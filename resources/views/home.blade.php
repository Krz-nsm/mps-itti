@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')

@php 
  use Carbon\Carbon;
@endphp
  <!-- Begin Page Content -->
    <div class="container-fluid">
      <!-- Page Heading -->
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
      </div>

      <!-- Content Row -->
      <div class="row">
        <div class="col-7 mb-4">
          <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <h4 class="mb-2 font-weight-bold text-primary">Form Input</h4>
                  <div class="row position-relative">
                    <div class="col-xl-4 col-md-6 mb-3">
                      <label for="txCode" class="form-label">No. Item</label>
                      <input type="text" class="form-control" id="txCode">
                    </div>
                    <div class="col-xl-4 col-md-6 mb-3">
                      <label for="txQty" class="form-label">Qty</label>
                      <input type="number" class="form-control" id="txQty">
                    </div>
                    <div class="col-xl-4 col-md-6 mb-3">
                      <label for="txStartDate" class="form-label">Tanggal Start</label>
                      <input type="date" class="form-control" id="txStartDate">
                    </div>
                    <div class="col-xl-4 col-md-6 mb-3">
                      <label for="txDate" class="form-label">Tanggal Delivery</label>
                      <input type="date" class="form-control" id="txDate">
                    </div>
                    <div class="col-xl-4 col-md-6 mb-3">
                      <label for="txDate" class="form-label">Hari Libur Selain Minggu</label>
                      <input type="number" class="form-control" id="txRedDate">
                    </div>
                    <div class="col-xl-12 col-md-12 mb-3 ">
                      <button type="button" class="btn btn-primary " id="btGenerate">Generate</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-5 mb-4">
          <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <h4 class="mb-2 font-weight-bold text-primary">Calculation</h4>
                  <div class="row">
                    <div class="col-xl-12 col-md-12 mb-2">
                      <label id="txNoItemCode" for="txCode" class="form-label">No Item : </label>
                    </div>
                    <div class="col-xl-12 col-md-12 mb-2">
                      <label id="txQtySum" for="txQty" class="form-label">Jumlah Mesin 1 Hari : </label>
                    </div>
                    <div class="col-xl-12 col-md-12 mb-2">
                      <label id="txDelivDate" for="txDate" class="form-label">Delivery Date : </label>
                    </div>
                    <div class="col-xl-12 col-md-12 mb-2">
                      <label id="txWorkDays" for="txDate" class="form-label">Hari Kerja : </label>
                    </div>
                    <div class="col-xl-12 col-md-12 mb-2">
                      <label id="txMechineNeed" for="txDate" class="form-label">Kebutuhan Mesin : </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xl-6 col-lg-7">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Mechine List</h6>
            </div>
            <div class="card-body">
              <div class="row" id="available-machines">
                @foreach ($mesin as $machine)
                @php
                    $isAvailable = is_null($machine->end_date) || Carbon::now()->gt(Carbon::parse($machine->end_date));
                @endphp
                  <div class="col-xl-3 col-md-6 mb-4" onclick="moveToSelected(this)" data-enddate="{{ $machine->end_date }}">
                    <div class="card border-left-primary shadow h-100 py-2">
                      <div class="card-body">
                        <div class="row no-gutters align-items-center">
                          <div class="col mr-2">
                            <div class="h6 font-weight-bold text-primary text-uppercase mb-1 fs-6">
                                {{ $machine->mesin_code }} <!-- Nomor mesin -->
                            </div>
                            @if (! $isAvailable)
                              <h6 class="card-subtitle mb-2 text-body-secondary fs-6">{{ $machine->item_code }}</h6>
                            @endif
                            <div class="h5 mb-0 font-weight-bold {{ $isAvailable ? 'text-success' : 'text-danger' }}">
                              {{ $isAvailable ? 'Available' : 'Unavailable' }}
                            </div>
                             @if (! $isAvailable)
                              <h6 class="mb-2 fs-6 untilMess">Until {{ \Carbon\Carbon::parse($machine->end_date)->format('Y-m-d') }}</h6>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-6 col-lg-7">
          <div class="card shadow mb-4">
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Selected Mechine</h6>
              </div>
              <div class="card-body">
                <div class="row" id="selected-machines">
                
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  let totalMesin = 0;
  let usedMesin = 0;
  let needed = 0;

  function moveToSelected(cardElement) {
    if (!totalMesin || isNaN(totalMesin) || totalMesin <= 0) {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Silakan hitung dan pastikan jumlah mesin lebih dari 0 terlebih dahulu!",
      });
      return;
    }

    const statusText = cardElement.querySelector(".text-danger, .text-success");
    if (statusText && statusText.textContent.trim().toLowerCase() === "unavailable") {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Mesin ini sedang tidak tersedia dan tidak bisa dipilih!",
      });
      return;
    }

    const selectedContainer = document.getElementById("selected-machines");

    if (!selectedContainer.contains(cardElement)) {
      if (usedMesin >= totalMesin) {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Jumlah mesin yang dipilih sudah maksimal!",
        });
        return;
      }

      usedMesin++;
      needed = totalMesin-usedMesin;
      updateRemainingDisplay();

      const closeButton = document.createElement("button");
      closeButton.innerHTML = "&times;";
      closeButton.className = "btn btn-sm btn-danger position-absolute";
      closeButton.style.top = "5px";
      closeButton.style.right = "10px";
      closeButton.onclick = function (event) {
        event.stopPropagation();
        moveToAvailable(cardElement);
      };

      const cardBody = cardElement.querySelector(".card-body");
      cardBody.appendChild(closeButton);

      selectedContainer.appendChild(cardElement);
    }
  }
  function moveToAvailable(cardElement) {
    const availableContainer = document.getElementById("available-machines");

    const closeButton = cardElement.querySelector("button");
    if (closeButton) {
      closeButton.remove();
    }

    if (usedMesin > 0) {
      usedMesin--;
      updateRemainingDisplay();
    }

    availableContainer.appendChild(cardElement);
  }
  function calculateWorkingDays(startDateStr, endDateStr, tglMerah) {
    const startDate = new Date(startDateStr);
    const endDate = new Date(endDateStr);

    startDate.setHours(0, 0, 0, 0);
    endDate.setHours(0, 0, 0, 0);

    let count = 0;
    let current = new Date(startDate);

    while (current <= endDate) {
      const day = current.getDay();
      if (day !== 0) { // 0 = Sunday
        count++;
      }
      current.setDate(current.getDate() + 1);
    }
    const redDays = parseInt(tglMerah) || 0;
    const workingDays = count - redDays;
    return workingDays >= 0 ? workingDays : 0;
  }
  function updateRemainingDisplay() {
    $('#txMechineNeed').text("Machine Required : " + (totalMesin - usedMesin));
  }
  function updateMachineAvailability(tglStartStr) {
    const tglStart = new Date(tglStartStr);

    document.querySelectorAll('#available-machines > .col-xl-3').forEach(card => {
      const endDateStr = card.getAttribute('data-enddate');

      if (endDateStr) {
        const endDate = new Date(endDateStr);
        const statusDiv = card.querySelector('.text-danger, .text-success');
        const itemCode = card.querySelector('.card-subtitle');
        const untilText = card.querySelector('.end-date-text');
        const untilMessage = card.querySelector('.untilMess');

        if (tglStart > endDate) {
          statusDiv.classList.remove('text-danger');
          statusDiv.classList.add('text-success');
          statusDiv.textContent = 'Available';

          if (itemCode) itemCode.style.display = 'none';
          if (untilText) untilText.style.display = 'none';
          if (untilMessage) untilMessage.style.display = 'none';

        } else {
          statusDiv.classList.remove('text-success');
          statusDiv.classList.add('text-danger');
          statusDiv.textContent = 'Unavailable';

          if (itemCode) itemCode.style.display = '';
          if (untilText) untilText.style.display = '';
        }
      }
    });
  }


  $(document).ready(function () {
    $('#btGenerate').on('click', function () {
      const noItem = $('#txCode').val().trim();
      const inptQty = parseFloat($('#txQty').val().trim());
      const tglStart = $('#txStartDate').val().trim();
      const tglDeliv = $('#txDate').val().trim();
      const tglMerah = $('#txRedDate').val().trim();
      const today = new Date().setHours(0, 0, 0, 0);

      if (!noItem || !inptQty || !tglDeliv || !tglMerah) {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Semua field harus diisi!",
        });
        return;
      }
      if (isNaN(inptQty) || parseInt(inptQty) <= 0) {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Qty belum di isi atau bernilai 0!",
        });
        return;
      }
      if (isNaN(tglMerah) || parseInt(tglMerah) < 0) {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Jumlah tanggal merah selain minggu belum di isi!",
        });
        return;
      }
      
      updateMachineAvailability(tglStart);

      const totalQty = inptQty/200;
      const workingDays = calculateWorkingDays(tglStart, tglDeliv, tglMerah);
      totalMesin = Math.ceil(totalQty / workingDays);
      needed = totalMesin;

      const date = new Date(tglDeliv);
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = date.getFullYear();
      const formattedDate = day + '-' + month + '-' + year;

      $('#txNoItemCode').text("No Item : " + noItem);
      $('#txQtySum').text("Qty Summary : " + totalQty);
      $('#txDelivDate').text("Delivery Date : " + formattedDate);
      $('#txWorkDays').text("Working Days : " + workingDays);
      $('#txMechineNeed').text("Machine Required : " + needed);

    });
  });
</script>

<style>
  .btn-danger {
    font-weight: bold;
    padding: 0 8px;
    line-height: 1;
  }
</style>
