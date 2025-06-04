@extends('layouts.main')
@section('title', 'Schedule')

@section('content')

  @php
    use Carbon\Carbon;
  @endphp

  <ul class="nav nav-tabs mb-3">
    <li class="nav-item">
      <a class="nav-link" href="{{ route('poList') }}">PO With Greige Delivery Date</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('scheList') }}">Schedule List Machine</a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" aria-current="page" href="{{ route('forecastList') }}">Detail Product</a>
    </li>
    <!-- <li class="nav-item">
      <a class="nav-link" href="{{ route('view') }}">Shcedule Plann</a>
    </li> -->
  </ul>

  <div class="row mb-3">
    <div class="col-6">
      <input type="text" id="searchInput" class="form-control" placeholder="Cari Item Code...">
    </div>
  </div>
  
  <div id="resultContainer" class="mb-4"></div>

  @foreach($itemCode as $item)
    <div class="card mb-4">
      <div class="card-body" id="schedule-{{ $item->item_code }}">
        <div>Loading data...</div>
      </div>
    </div>
  @endforeach

  <!-- Modal -->
  <div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 90%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="dataModalLabel">Input Schedule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row">
              <div class="col-8 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                  <div class="card-body">
                    <h4 class="mb-2 font-weight-bold text-primary">Form Input</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  const items = {!! json_encode($itemCode) !!};

  $(document).ready(function () {
    items.forEach(function (item) {
      const itemCode = item.item_code;
      const $container = $('#schedule-' + itemCode);
      $container.html('<div>Loading data...</div>');

      $.ajax({
        url: '/schedule/' + itemCode,
        method: 'GET',
        dataType: 'json',
        success: function (data) {
          renderForecastTable($container, itemCode, data);
        },
        error: function () {
          $container.html('<div class="text-danger">Error loading data.</div>');
        }
      });
    });
  });

  $('#searchInput').on('keyup', function () {
    const keyword = $(this).val().toUpperCase().trim();

    if (keyword.length !== 0) {
      $.ajax({
        url: '/search/' + encodeURIComponent(keyword),
        method: 'GET',
        dataType: 'json',
        success: function (data) {
          if (data.exists_in_schedule) {
            $('#resultContainer').html('<div class="card"><div class="card-body text-muted">Item sudah ada dalam jadwal.</div></div>');
            $('#resultContainer').show();
            $('.card').hide();
            return;
          }

          $('.card').remove();

          const containerId = 'schedule-' + keyword;
          const newCardHtml = `
            <div class="card mb-4">
              <div class="card-body" id="${containerId}">
                <div>Loading data...</div>
              </div>
            </div>
          `;
          $('#resultContainer').html(newCardHtml).show();

          // Tambahan jika ingin langsung load forecast item tersebut
          $.ajax({
            url: '/schedule/' + keyword,
            method: 'GET',
            dataType: 'json',
            success: function (data) {
              renderForecastTable($('#' + containerId), keyword, data);
            },
            error: function () {
              $('#' + containerId).html('<div class="text-danger">Error loading data.</div>');
            }
          });
        },
        error: function () {
          $('#resultContainer').html('<div class="card"><div class="card-body text-danger">Gagal mengambil data.</div></div>');
          $('#resultContainer').show();
        }
      });

    } else {
      $('#resultContainer').hide();
      $('.card').show();

      items.forEach(function (item) {
        const itemCode = item.item_code;
        const $container = $('#schedule-' + itemCode);
        $container.html('<div>Loading data...</div>');

        $.ajax({
          url: '/schedule/' + itemCode,
          method: 'GET',
          dataType: 'json',
          success: function (data) {
            console.log(data);
            renderForecastTable($container, itemCode, data);
          },
          error: function () {
            $container.html('<div class="text-danger">Error loading data.</div>');
          }
        });
      });
    }
  });

  $(document).on('click', '#initialStockCell', function () {
    const stockValue = $(this).data('stock');
    const itemCode = $(this).data('item');
  
    $.ajax({
      url: "{{ route('getDetailStock') }}",
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      data: {
        item_code: itemCode,
        stock: stockValue
      },
      success: function (response) {
        console.log(response);

        let html = '<table class="table table-bordered table-sm">';
        html += '<thead><tr><th>Lotcode</th><th>No. Project</th><th>Season</th><th>Keterangan Lot</th><th>Warehouse</th><th>Qty (Kg)</th></tr></thead><tbody>';

        if (response.length > 0) {
          response.forEach(row => {
            html += `<tr>
                      <td>${row.lotcode}</td>
                      <td>${row.origdlvsalordlinesalordercode}</td>
                      <td>${row.statisticalgroupcode}</td>
                      <td>${row.externalreference}</td>
                      <td>${row.logicalwarehousecode}</td>
                      <td>${parseFloat(row.stock).toFixed(2)}</td>
                    </tr>`;
          });
        } else {
          html += '<tr><td colspan="3" class="text-center">Data tidak ditemukan</td></tr>';
        }

        html += '</tbody></table>';

        // Masukkan HTML ke dalam modal
        $('#dataModal .modal-body').html(html);

        // Tampilkan modal
        $('#dataModal').modal('show');
      },
      error: function (xhr, status, error) {
        $('#dataModal .modal-body').html('<div class="alert alert-danger">Gagal memuat data.</div>');
        $('#dataModal').modal('show');
      }
    });
  });


  function renderForecastTable($container, itemCode, data) {
    const sqlData = data.schedules;
    const db2Datas = data.db2_data;
    const forecast = data.forecast;
    const stockDatas = data.stock_data;
    let initialStock = 0;

    if (stockDatas.length > 0) {
      initialStock = parseFloat(stockDatas[0].stock || 0);
    }

    function normalizeDate(date) {
      return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }

    const today = normalizeDate(new Date());
    const dates = [];

    // Hari kemarin
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    dates.push(normalizeDate(yesterday));

    // Next 365 days
    for (let i = 0; i <= 365; i++) {
      let d = new Date(today);
      d.setDate(d.getDate() + i);
      dates.push(normalizeDate(d));
    }

    // Qty Plann
    let qtyPlannMap = {};
    sqlData.forEach(function (row) {
      let start = new Date(row.start_date);
      let end = new Date(row.end_date);
      let dailyQty = parseFloat(row.qty_day || 0);

      for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
        let currentDate = normalizeDate(new Date(d));

        // Skip Minggu (jika perlu)
        if (currentDate.getDay() === 0) continue;
      
        let dateKey = currentDate.toISOString().split("T")[0];
        qtyPlannMap[dateKey] = (qtyPlannMap[dateKey] || 0) + dailyQty;
      }
    });

    // PO Map (Greige Delivery Date)
    let greigeDeliveryMap = {};
    db2Datas.forEach(function (row) {
      let to = normalizeDate(new Date(row.rmp_req_to));
      let dateKey = to.toISOString().split("T")[0];
      let qty = parseFloat(row.qty_total || 0);
      greigeDeliveryMap[dateKey] = (greigeDeliveryMap[dateKey] || 0) + qty;
    });

    // Forecast Map
    const forecastMap = {};
    const currentYear = today.getFullYear();
    dates.forEach(function (d) {
      if (d.getDate() === 1) {
        const match = forecast.find(f =>
          parseInt(f.buy_month) === (d.getMonth() + 1) && currentYear === d.getFullYear()
        );
        if (match) {
          forecastMap[d.toISOString().split("T")[0]] = parseFloat(match.total_qty_kg || 0);
        }
      }
    });

    // Project Qty Map
    let projectQtyMap = {};
    sqlData.forEach(row => {
      if (row.end_date && row.qty) {
        let key = normalizeDate(new Date(row.end_date)).toISOString().split("T")[0];
        projectQtyMap[key] = parseFloat(row.qty || 0);
      }
    });

    // Balance Map calculation
    let lastBalance = initialStock;
    const balanceMap = {};
    dates.forEach(d => {
      const key = d.toISOString().split("T")[0];
      const poQty = greigeDeliveryMap[key] || 0;
      const forecastQty = forecastMap[key] || 0;
      const projectQty = projectQtyMap[key] || 0;

      let totalQtyDay = 0;
      sqlData.forEach(row => {
        const start = normalizeDate(new Date(row.start_date));
        const end = normalizeDate(new Date(row.end_date));
        if (d >= start && d <= end && d.getDay() !== 0) { // exclude Sundays
          totalQtyDay += parseFloat(row.qty_day || 0);
        }
      });
    });

    // Build HTML table
    let html = '<div class="table-responsive scrollable-table-container">';
    html += '<table class="table table-bordered table-sm scrollable-table">';
    html += '<thead><tr>';
    html += '<th>' + itemCode + '</th>';
    html += '<th></th>';

    dates.forEach(d => {
      const label = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
      const isSunday = d.getDay() === 0;
      html += `<th class="${isSunday ? 'sunday-column' : ''}">${label}</th>`;
    });

    html += '</tr></thead><tbody>';

    // Planning
    html += '<tr><td><strong>Qty Planning</strong></td><td></td>';
    dates.forEach(function (d) {
      let dateKey = d.toISOString().split("T")[0];
      const isSunday = d.getDay() === 0;
      const val = qtyPlannMap[dateKey];

      html += `<td class="${isSunday ? 'sunday-column' : ''}">${val ? val.toFixed(2) : ''}</td>`;
    });
    html += '</tr>';

    // PO With Greige Delivery Date
    html += '<tr><td><strong>PO With Greige Delivery Date</strong></td><td></td>';
    dates.forEach(d => {
      const key = d.toISOString().split("T")[0];
      const val = greigeDeliveryMap[key];
      const isSunday = d.getDay() === 0;
      html += `<td class="${isSunday ? 'sunday-column' : ''}">${val ? val.toFixed(2) : ''}</td>`;
    });
    html += '</tr>';

    // Forecast
    html += '<tr><td><strong>Forecast</strong></td><td></td>';
    dates.forEach(d => {
      const key = d.toISOString().split("T")[0];
      const val = forecastMap[key];
      const isSunday = d.getDay() === 0;
      html += `<td class="${isSunday ? 'sunday-column' : ''}">${val ? val.toFixed(2) : ''}</td>`;
    });
    html += '</tr>';

    // Balance
    html += `<tr><td><strong>Balance</strong></td>
            <td style="background-color:rgb(190, 229, 255);">
              <strong 
                id="initialStockCell" 
                data-stock="${initialStock.toFixed(2)}" 
                data-item="${itemCode}" 
                style="cursor:pointer;">
                ${initialStock.toFixed(2)}
              </strong>
            </td>
            `;

    dates.forEach((d, i) => {
      const currentDate = normalizeDate(d);
      const key = currentDate.toISOString().split("T")[0];
                
      if (currentDate.getDay() === 0) {
        html += '<td class="sunday-column"></td>';
        return;
      }

      const poQty = greigeDeliveryMap[key] || 0;
      const forecastQty = 0;
      const projectQty = projectQtyMap[key] || 0;

      let totalQtyDay = 0;
      sqlData.forEach(function(row) {
        const start = normalizeDate(new Date(row.start_date));
        const end = normalizeDate(new Date(row.end_date));
        if (currentDate >= start && currentDate <= end && currentDate.getDay() !== 0) {
          totalQtyDay += parseFloat(row.qty_day || 0);
        }
      });

      lastBalance = (lastBalance - poQty - forecastQty - projectQty) + totalQtyDay;
      
      html += '<td class="balance-cell" data-date="' + key + '"><strong>' + lastBalance.toFixed(2) + '</strong></td>';
    });
    html += '</tr>';

    html += '</tbody></table></div>';

    $container.html(html);
  }

  function buildForecastTable(itemCode, data) {
    const sqlData = data.schedules;
    const db2Datas = data.db2_data;
    const forecast = data.forecast;
    const stockDatas = data.stock_data;
    let initialStock = 0;

    if (stockDatas.length > 0) {
      initialStock = parseFloat(stockDatas[0].stock || 0);
    }

    function normalizeDate(date) {
      return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }

    const today = normalizeDate(new Date());
    const dates = [];

    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    dates.push(normalizeDate(yesterday));

    for (let i = 0; i <= 365; i++) {
      let d = new Date(today);
      d.setDate(d.getDate() + i);
      dates.push(normalizeDate(d));
    }

    let greigeDeliveryMap = {};
    db2Datas.forEach(function (row) {
      let to = normalizeDate(new Date(row.rmp_req_to));
      let dateKey = to.toISOString().split("T")[0];
      let qty = parseFloat(row.qty_total || 0);
      greigeDeliveryMap[dateKey] = (greigeDeliveryMap[dateKey] || 0) + qty;
    });

    const forecastMap = {};
    const currentYear = today.getFullYear();
    dates.forEach(function (d) {
      if (d.getDate() === 1) {
        const match = forecast.find(f =>
          parseInt(f.buy_month) === (d.getMonth() + 1) && currentYear === d.getFullYear()
        );
        if (match) {
          forecastMap[d.toISOString().split("T")[0]] = parseFloat(match.total_qty_kg || 0);
        }
      }
    });

    let lastBalance = initialStock;
    const balanceMap = {};
    dates.forEach(d => {
      const key = d.toISOString().split("T")[0];
      const poQty = greigeDeliveryMap[key] || 0;
      const forecastQty = forecastMap[key] || 0;

      let totalQtyDay = 0;

      lastBalance = (lastBalance - poQty - forecastQty) + totalQtyDay;
      balanceMap[key] = lastBalance;
    });

    let html = '<div class="table-responsive scrollable-table-container">';
    html += '<table class="table table-bordered table-sm scrollable-table">';
    html += '<thead><tr>';
    html += '<th>' + itemCode + '</th>';
    html += '<th></th>';

    dates.forEach(d => {
      const label = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
      const isSunday = d.getDay() === 0;
      html += `<th class="${isSunday ? 'sunday-column' : ''}">${label}</th>`;
    });

    html += '</tr></thead><tbody>';

    html += '<tr><td><strong>PO With Greige Delivery Date</strong></td><td></td>';
    dates.forEach(d => {
      const key = d.toISOString().split("T")[0];
      const val = greigeDeliveryMap[key];
      const isSunday = d.getDay() === 0;
      html += `<td class="${isSunday ? 'sunday-column' : ''}">${val ? val.toFixed(2) : ''}</td>`;
    });
    html += '</tr>';

    html += '<tr><td><strong>Forecast</strong></td><td></td>';
    dates.forEach(d => {
      const key = d.toISOString().split("T")[0];
      const val = forecastMap[key];
      const isSunday = d.getDay() === 0;
      html += `<td class="${isSunday ? 'sunday-column' : ''}">${val ? val.toFixed(2) : ''}</td>`;
    });
    html += '</tr>';

    html += '<tr><td><strong>Balance</strong></td><td><strong>' + initialStock.toFixed(2) + '</strong></td>';
    dates.forEach((d, i) => {
      const key = d.toISOString().split("T")[0];
      let val = '';
      if (i === 0) {
        val = initialStock;
      } else {
        val = balanceMap[key];
      }
      const isSunday = d.getDay() === 0;
      html += `<td class="${isSunday ? 'sunday-column' : ''}"><strong>${val ? val.toFixed(2) : ''}</strong></td>`;
    });
    html += '</tr>';

    html += '</tbody></table></div>';

    return html;
  }
</script>

<style>
  .scrollable-table-container {
    overflow-x: auto;
  }
  .scrollable-table {
    white-space: nowrap;
  }
  .sunday-column {
    background-color: #ffe6e6;
  }
</style>
@endpush
