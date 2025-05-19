@extends('layouts.main')
@section('title', 'Schedule')

@section('content')

  @foreach($itemCode as $item)
    <div class="mb-3" style="width: 100%;">
      <!-- Tombol kecil dengan lebar tetap -->
      <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $item->item_code }}" aria-expanded="false" aria-controls="collapse-{{ $item->item_code }}" style="width: 150px;">
        {{ $item->item_code }}
      </button>

      <!-- Collapse yang full lebar container -->
      <div class="collapse mt-2" id="collapse-{{ $item->item_code }}">
        <div class="card card-body" style="width: 100%;">
          <!-- Isi datatable atau konten -->
          Konten untuk {{ $item->item_code }}
        </div>
      </div>
    </div>
  @endforeach
@endsection

@push('scripts')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

  <script>
    $(document).ready(function() {
      $('[data-bs-toggle="collapse"]').on('click', function() {
        var itemCode = $.trim($(this).text());
        var $collapseDiv = $('#collapse-' + itemCode);
        var $cardBody = $collapseDiv.find('.card-body');

        // Cek apakah data sudah dimuat
        if ($cardBody.data('loaded')) {
          return; // kalau sudah load, jangan load ulang
        }

        // Tampilkan loading
        $cardBody.html('<div>Loading data...</div>');

        $.ajax({
          url: '/schedule/' + itemCode,
          method: 'GET',
          dataType: 'json',
          success: function(data) {
            if (data.length === 0) {
              $cardBody.html('<div>No data found for ' + itemCode + '</div>');
              $cardBody.data('loaded', true);
              return;
            }

            function normalizeDate(date) {
              return new Date(date.getFullYear(), date.getMonth(), date.getDate());
            }

            // 1. Buat array tanggal dari kemarin sampai 1 tahun ke depan
            var startDate = new Date();
            startDate.setDate(startDate.getDate() - 1);
            var dates = [];
            for (var i = 0; i <= 365; i++) {
              var d = new Date(startDate);
              d.setDate(startDate.getDate() + i);
              dates.push(new Date(d)); // simpan sebagai object Date
            }

            // 2. Header tabel
            var html = '<div style="overflow-x:auto;"><table class="table table-bordered table-sm">';
            html += '<thead><tr><th>Mesin</th>';
            dates.forEach(function(date) {
              var options = { day: 'numeric', month: 'short', year: 'numeric' };
              var dateStr = date.toLocaleDateString('id-ID', options);
              var isSunday = date.getDay() === 0;
              html += '<th' + (isSunday ? ' style="color:red;"' : '') + '>' + dateStr + '</th>';
            });
            html += '</tr></thead><tbody>';

            // 3. Ambil list mesin unik
            var mesinList = [...new Set(data.map(row => row.mesin_code))];
            // 4. Render tiap baris mesin
            mesinList.forEach(function(mesin) {
              html += '<tr><td>' + mesin + '</td>';
            
              dates.forEach(function(d) {
                var currentDate = normalizeDate(d); // penting!
              
                // Jika hari Minggu, skip qty
                if (currentDate.getDay() === 0) {
                  html += '<td style="background-color:#f8d7da;"></td>';
                  return;
                }

                var found = false;

                for (var i = 0; i < data.length; i++) {
                  var row = data[i];
                
                  if (row.mesin_code === mesin) {
                    var start = normalizeDate(new Date(row.start_date));
                    var end = normalizeDate(new Date(row.end_date));
                    
                    if (currentDate >= start && currentDate <= end) {
                      html += '<td>' + row.qty_day + '</td>';
                      found = true;
                      break;
                    }
                  }
                }

                if (!found) {
                  html += '<td></td>';
                }
              });
              html += '</tr>';
            });

            var poDeliveryMap = {};
            data.forEach(function(row) {
              var end = normalizeDate(new Date(row.end_date));
              var key = end.toISOString().split("T")[0];
              poDeliveryMap[key] = (poDeliveryMap[key] || 0) + parseFloat(row.qty || 0);
            });

            html += '<tr><td><strong>PO Delivery Date</strong></td>';
            dates.forEach(function(d) {
              var currentDate = normalizeDate(d);
              var key = currentDate.toISOString().split("T")[0];

              if (currentDate.getDay() === 0) {
                html += '<td style="background-color:#f8d7da;"></td>';
              } else {
                var qty = poDeliveryMap[key] || '';
                html += '<td><strong>' + (qty || '') + '</strong></td>';
              }
            });
            html += '</tr>';

            html += '<tr><td><strong>Forecast</strong></td>';
            dates.forEach(function(d) {
              var currentDate = normalizeDate(d);
              if (currentDate.getDay() === 0) {
                html += '<td style="background-color:#f8d7da;"></td>';
              } else {
                html += '<td><input type="number" class="form-control form-control-sm forecast-input" data-date="' + d.toISOString().split("T")[0] + '" style="width: 100px;"></td>';
              }
            });
            html += '</tr>';

            let cumulativeTotal = 0;
            let projectQtyMap = {};
            html += '<tr><td><strong>Project Qty Plan</strong></td>';
            dates.forEach(function (d) {
              var currentDate = normalizeDate(d);
              var key = currentDate.toISOString().split("T")[0];
            
              if (currentDate.getDay() === 0) {
                html += '<td style="background-color:#f8d7da;"></td>';
                return;
              }

              var dailyTotal = 0;
              data.forEach(function (row) {
                if (!row.mesin_code) return;
                var start = normalizeDate(new Date(row.start_date));
                var end = normalizeDate(new Date(row.end_date));
                if (currentDate >= start && currentDate <= end) {
                  if (currentDate.getDay() !== 0) {
                    dailyTotal += parseFloat(row.qty_day || 0);
                  }
                }
              });
            
              if (dailyTotal > 0) {
                cumulativeTotal += dailyTotal;
                html += '<td><strong>' + cumulativeTotal + '</strong></td>';
              } else {
                html += '<td></td>';
              }
            });
            html += '</tr>';
            
            html += '<tr><td><strong>Balance</strong></td>';
            dates.forEach(function(d) {
              if (d.getDay() === 0) {
                html += '<td style="background-color:#f8d7da;"></td>';
              } else {
                html += '<td class="balance-cell" data-date="' + d.toISOString().split("T")[0] + '"></td>';
              }
            });
            html += '</tr>';

            html += '</tbody></table></div>';
            $cardBody.html(html);
            $cardBody.data('loaded', true);

            $cardBody.on('input', '.forecast-input', function () {
              var forecastMap = {};
              $('.forecast-input').each(function () {
                var date = $(this).data('date');
                var val = parseFloat($(this).val() || 0);
                forecastMap[date] = val;
              });
            
              dates.forEach(function (d) {
                var key = d.toISOString().split("T")[0];
                if (d.getDay() === 0) return;
              
                var projectQty = projectQtyMap[key] || 0;
                var poQty = poDeliveryMap[key] || 0;
                var forecastQty = forecastMap[key] || 0;
                var balance = projectQty - poQty - forecastQty;
              
                var $cell = $cardBody.find('.balance-cell[data-date="' + key + '"]');
                if (projectQty || poQty || forecastQty) {
                  $cell.html('<strong>' + balance + '</strong>');
                } else {
                  $cell.html('');
                }
              });
            });
          },
          error: function() {
            $cardBody.html('<div class="text-danger">Error loading data.</div>');
          }
        });
      });
    });
  </script>

  <style>
    table th, table td {
      text-align: left;
      vertical-align: middle;
      white-space: nowrap;
    }

    .rotate-date {
      writing-mode: vertical-rl;
      transform: rotate(180deg);
      white-space: nowrap;
      font-size: 12px;
    }

    .table-sm input {
      max-width: 80px;
      margin: auto;
      display: block;
    }
  </style>

@endpush