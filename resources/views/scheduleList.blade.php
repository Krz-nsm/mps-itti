@extends('layouts.main')
@section('title', 'Schedule')

@section('content')

@php
use Carbon\Carbon;
@endphp

<ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link" href="{{ route('poList') }}">PO With Greige Delivery Date</a>
  </li>
  <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="{{ route('scheList') }}">Schedule List Machine</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{ route('forecastList') }}">Detail Product</a>
  </li>
</ul>

<div class="card shadow-sm mt-3">
  <div class="card-body table-responsive">
    <table id="itemTable" class="table table-bordered">
      <thead>
        <tr id="tableHead">
          <th>Mesin</th>
        </tr>
      </thead>
      <tbody id="tableBody">
        <!-- Collapsible rows will be injected here -->
      </tbody>
    </table>
  </div>
</div>

<!-- Modal (Optional) -->
<div class="modal fade" id="dataModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Machine Detail</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Detail goes here...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
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
      const typeColorMap = {
        'Po Greige': '#cce5ff',
        'Sche Plann': '#d4edda',
        'Forecast': '#fce5cd',
      };


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

        theadRow.append(`<th${dayOfWeek === 0 ? ' style="color:red;"' : ''}>${formattedDate}</th>`);
        dates.push(new Date(currentDate));
        currentDate.setDate(currentDate.getDate() + 1);
      }

      const dataMesin = response.dataMesin || [];
      const groupedByJenis = {};

      dataMesin.forEach(function (row) {
        const mesin = row.mesin_code;
        const type = row.type;
        const jenis = row.jenis;
        const start = normalizeDate(new Date(row.start_date)).toISOString().split("T")[0];
        const end = normalizeDate(new Date(row.end_date)).toISOString().split("T")[0];

        if (!groupedByJenis[jenis]) groupedByJenis[jenis] = {};
        if (!groupedByJenis[jenis][mesin]) groupedByJenis[jenis][mesin] = [];

        groupedByJenis[jenis][mesin].push({
          item_code: row.item_code,
          start: start,
          end: end,
          type: type
        });
      });

      Object.keys(groupedByJenis).forEach(function (jenis) {
        const collapseId = 'collapse_' + jenis.replace(/\s+/g, '_');

        $('#itemTable').append(`
          <tbody>
            <tr class="bg-light">
              <td colspan="${dates.length + 1}">
                <button class="btn btn-sm btn-link toggle-section" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="true">
                  <strong>${jenis}</strong>
                </button>
              </td>
            </tr>
          </tbody>
        `);

        $('#itemTable').append(`<tbody id="${collapseId}" class="collapse show"></tbody>`);

        const mesinMap = groupedByJenis[jenis];
        Object.keys(mesinMap).forEach(function (mesin) {
          let rowHtml = `<tr><td><strong>${mesin}</strong></td>`;

          dates.forEach(function (d) {
            let matchedJobs = mesinMap[mesin].filter(job => {
              let jobStart = normalizeDate(new Date(job.start));
              let jobEnd = normalizeDate(new Date(job.end));
              let current = normalizeDate(d);
              return current >= jobStart && current <= jobEnd;
            });

            if (d.getDay() === 0) {
              rowHtml += '<td style="background-color:#f8d7da;"></td>';
            } else if (matchedJobs.length > 0) {
              matchedJobs.forEach(matchedJob => {
                let bgColor = typeColorMap[matchedJob.type] || '#dee2e6';
              rowHtml += `<td style="background-color:${bgColor}">
                <div class="cell-item">
                  <strong>${matchedJob.item_code}</strong><br>
                  <small>${matchedJob.type}</small>
                </div>
              </td>`;
              });
              rowHtml += `</td>`;
            } else {
              rowHtml += '<td></td>';
            }
          });

          rowHtml += '</tr>';
          $(`#${collapseId}`).append(rowHtml);
        });
      });

      // Click cell item (optional event)
      $('.cell-item').on('click', function () {
        // Bisa tampilkan modal, tooltip, dll
        // $('#dataModal').modal('show');
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
  .cell-item {
    cursor: pointer;
    font-size: 12px;
    line-height: 1.1;
  }
  .cell-item:hover {
    background-color: #f8f9fa;
  }
  .toggle-section {
    text-decoration: none;
    font-size: 14px;
  }
  .toggle-section::before {
    content: "▼ ";
  }
  .toggle-section.collapsed::before {
    content: "► ";
  }
</style>
@endpush
