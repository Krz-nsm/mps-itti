@extends('layouts.main')
@section('title', 'Schedule')

@section('content')
  <div class="card shadow-sm">
    <div class="card-body table-responsive">
      <table id="itemTable" class="table table-bordered">
        <thead>
          <tr id="tableHead">
            <th>Mesin</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function () {
      function normalizeDate(date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
      }

      $.ajax({
        url: "{{ route('loadData') }}",
        method: 'GET',
        dataType: 'json',
        success: function (response) {
          let today = new Date();
          let startDate = new Date(today);
          startDate.setDate(startDate.getDate() - 1);
          let endDate = new Date(today);
          endDate.setFullYear(endDate.getFullYear() + 1);

          let currentDate = new Date(startDate);
          let theadRow = $('#tableHead');
          let dates = [];

          while (currentDate <= endDate) {
            let day = currentDate.getDate().toString().padStart(2, '0');
            let month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
            let year = currentDate.getFullYear();
            let formattedDate = `${day}-${month}-${year}`;
            let dayOfWeek = currentDate.getDay();

            if (dayOfWeek === 0) {
              theadRow.append(`<th style="color: red;">${formattedDate}</th>`);
            } else {
              theadRow.append(`<th>${formattedDate}</th>`);
            }

            dates.push(new Date(currentDate));
            currentDate.setDate(currentDate.getDate() + 1);
          }

          var dataDB2 = response.dataDB2;
          var greigeDeliveryMap = {};

          dataDB2.forEach(function (row) {
            let to = normalizeDate(new Date(row.rmp_req_to));
            let dateKey = to.toISOString().split("T")[0];
            let subcode = (row.subcode02 || '') + '-' + (row.subcode03 || '');
            let dailyQty = parseFloat(row.qty_total || 0);

            if (!greigeDeliveryMap[dateKey]) {
              greigeDeliveryMap[dateKey] = {};
            }

            greigeDeliveryMap[dateKey][subcode] = (greigeDeliveryMap[dateKey][subcode] || 0) + dailyQty;
          });

          let html = '<tr style="border-top: 3px solid black;"><td><strong>PO With Greige Delivery Date</strong></td>';

          dates.forEach(function (d) {
            let currentDate = normalizeDate(d);
            let key = currentDate.toISOString().split("T")[0];

            if (currentDate.getDay() === 0) {
              html += '<td style="background-color:#f8d7da;"></td>';
            } else {
              let combos = greigeDeliveryMap[key];
              if (combos) {
                let cellContent = '';
                for (const [subcode, qty] of Object.entries(combos)) {
                  cellContent += `
                    <div class="cell-item" data-subcode="${subcode}" data-qty="${qty.toFixed(2)}">
                      <strong>${subcode}</strong><br>
                      <small>${qty.toFixed(2)}</small>
                    </div>
                    <hr style="margin: 4px 0;">
                  `;
                }
                html += `<td>${cellContent}</td>`;
              } else {
                html += '<td></td>';
              }
            }
          });

          html += '</tr>';

          $('#itemTable tbody').append(html);

          // Tambahkan event klik
          $('.cell-item').on('click', function () {
            let subcode = $(this).data('subcode');
            let qty = $(this).data('qty');
            alert(`Subcode: ${subcode}\nQty: ${qty}`);
          });
        },
        error: function (xhr, status, error) {
          alert('Gagal mengambil data: ' + error);
          console.error(error);
        }
      });
    });
  </script>

  <style>
    table th, table td {
      text-align: left;
      vertical-align: middle;
      white-space: nowrap;
    }

    .table-sm td, .table-sm th {
      padding: 2px 4px !important;
      font-size: 11px !important;
    }

    .table-scroll-wrapper {
      overflow-x: auto;
      max-height: 500px;
      overflow-y: auto;
    }

    .wide-table {
      width: 3000%;
      min-width: 1500px;
      table-layout: fixed;
    }

    .cell-item {
      cursor: pointer;
    }

    .cell-item:hover {
      background-color: #e9ecef;
    }
  </style>
@endpush
