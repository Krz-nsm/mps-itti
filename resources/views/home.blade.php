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
        <div class="col-9 mb-4">
          <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <h4 class="mb-2 font-weight-bold text-primary">Form Input</h4>
                  <!-- <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"> Form Input</div> -->
                  <div class="row">
                    <div class="col-xl-3 col-md-6 mb-3">
                    <label for="txCode" class="form-label">No. Item</label>
                    <input type="text" class="form-control" id="txCode" placeholder="No. Item">
                  </div>
                  <div class="col-xl-3 col-md-6 mb-3">
                    <label for="txQty" class="form-label">Qty</label>
                    <input type="number" class="form-control" id="txQty" placeholder="0">
                  </div>
                  <div class="col-xl-3 col-md-6 mb-3">
                    <label for="txDate" class="form-label">Tanggal Delivery</label>
                    <input type="date" class="form-control" id="txDate" placeholder="mm-dd-yyyy">
                  </div>
                  <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                  </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-3 mb-4">
          <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <h4 class="mb-2 font-weight-bold text-primary">Calculation</h4>
                  <div class="row">
                    <div class="col-xl-6 col-md-6 mb-3">
                    <label for="txCode" class="form-label">No Item : </label>
                    <label for="txQty" class="form-label">Qty Summary : </label>
                    <label for="txDate" class="form-label">Delivery Date : </label>
                    <!-- Total mesin jika dikerjakan dalam sehari -->
                    <label for="txDate" class="form-label">Working Days : </label>
                    <label for="txDate" class="form-label">Machine Required : </label>
                    <!-- <input type="text" class="form-control" id="txCode" placeholder="No. Item"> -->
                  </div>
                  <div class="col-xl-3 col-md-6 mb-3">
                    <!-- <label for="txQty" class="form-label">Qty Summary : </label> -->
                    <!-- <input type="number" class="form-control" id="txQty" placeholder="0"> -->
                  </div>
                  <div class="col-xl-3 col-md-6 mb-3">
                    <!-- <label for="txDate" class="form-label">Delivery Date : </label> -->
                    <!-- <input type="date" class="form-control" id="txDate" placeholder="mm-dd-yyyy"> -->
                  </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Area Chart -->
        <div class="col-xl-6 col-lg-7">
          <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Mechine List</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
              <div class="row" id="available-machines">
                @foreach ($mesin as $machine)
                @php
                    $isAvailable = is_null($machine->end_date) || Carbon::now()->gt(Carbon::parse($machine->end_date));
                @endphp
                  <div class="col-xl-3 col-md-6 mb-4" onclick="moveToSelected(this)">
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
                              <h6 class="mb-2 fs-6">Until {{ \Carbon\Carbon::parse($machine->end_date)->format('Y-m-d') }}</h6>
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
              <!-- Card Header - Dropdown -->
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Selected Mechine</h6>
              </div>
              <!-- Card Body -->
              <div class="card-body">
                <div class="row" id="selected-machines">
                
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  <!-- /.container-fluid -->
@endsection

<script>
  function moveToSelected(cardElement) {
    const selectedContainer = document.getElementById("selected-machines");

    if (!selectedContainer.contains(cardElement)) {
      // Tambahkan tombol X
      const closeButton = document.createElement("button");
      closeButton.innerHTML = "&times;";
      closeButton.className = "btn btn-sm btn-danger position-absolute";
      closeButton.style.top = "5px";
      closeButton.style.right = "10px";
      closeButton.onclick = function (event) {
        event.stopPropagation(); // cegah trigger click card
        moveToAvailable(cardElement);
      };

      // Sisipkan ke dalam card-body
      const cardBody = cardElement.querySelector(".card-body");
      cardBody.appendChild(closeButton);

      selectedContainer.appendChild(cardElement);
    }
  }

  function moveToAvailable(cardElement) {
    const availableContainer = document.getElementById("available-machines");

    // Hapus tombol X
    const closeButton = cardElement.querySelector("button");
    if (closeButton) {
      closeButton.remove();
    }

    availableContainer.appendChild(cardElement);
  }
</script>

<style>
  .btn-danger {
    font-weight: bold;
    padding: 0 8px;
    line-height: 1;
  }
</style>
