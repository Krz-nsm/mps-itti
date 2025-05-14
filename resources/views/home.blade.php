@extends('layouts.main')

@section('title', 'Dashboard')
<!-- Select2 CSS -->
<!-- Select2 CSS (di <head>) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery & Select2 JS (sebelum </body>) -->
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
                                        <label for="no_item" class="form-label">Item </label>
                                        <select class="form-control select2" id="select2Code" name="no_item">
                                            <option value="">Pilih No. Item</option>
                                            @foreach ($filter as $item)
                                                <option value="{{ $item->hanger }}">{{ $item->hanger }} |
                                                    {{ $item->longdescription }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-3 col-md-6 mb-3">
                                        <label for="txQty" class="form-label">Qty</label>
                                        <input type="number" class="form-control" id="txQty" placeholder="0">
                                    </div>
                                    <div class="col-xl-3 col-md-6 mb-3">
                                        <label for="tsDate" class="form-label">Tanggal Start</label>
                                        <input type="date" class="form-control" id="tsDate" placeholder="mm-dd-yyyy">
                                    </div>
                                    <div class="col-xl-3 col-md-6 mb-3">
                                        <label for="txDate" class="form-label">Tanggal Delivery</label>
                                        <input type="date" class="form-control" id="txDate" placeholder="mm-dd-yyyy">
                                    </div>
                                </div>
                                <div class="position-absolute bottom-0 end-0">
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm">
                                        Generate Report
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
                                    <div class="col-xl-12 col-md-12 mb-3">
                                        <p>No Item: <span id="calc_no_item">-</span></p>
                                        <p>Delivery Date: <span id="calc_date">-</span></p>
                                        <p>Qty Calculation Each Day: <span id="calc_qty">-</span></p>
                                        <p>Qty Machine Each Day: <span id="mc_qty">-</span></p>
                                        <p>Working Days: <span id="calc_days">-</span></p>
                                        <p>Machine Required: <span id="calc_machine">-</span></p>
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
                                    $isAvailable =
                                        is_null($machine->end_date) ||
                                        Carbon::now()->gt(Carbon::parse($machine->end_date));
                                @endphp
                                <div class="col-xl-3 col-md-6 mb-4" onclick="moveToSelected(this)">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="h6 font-weight-bold text-primary text-uppercase mb-1 fs-6">
                                                        {{ $machine->mesin_code }} <!-- Nomor mesin -->
                                                    </div>
                                                    @if (!$isAvailable)
                                                        <h6 class="card-subtitle mb-2 text-body-secondary fs-6">
                                                            {{ $machine->item_code }}</h6>
                                                    @endif
                                                    <div
                                                        class="h5 mb-0 font-weight-bold {{ $isAvailable ? 'text-success' : 'text-danger' }}">
                                                        {{ $isAvailable ? 'Available' : 'Unavailable' }}
                                                    </div>
                                                    @if (!$isAvailable)
                                                        <h6 class="mb-2 fs-6">Until
                                                            {{ \Carbon\Carbon::parse($machine->end_date)->format('Y-m-d') }}
                                                        </h6>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectItem = document.getElementById('select2Code');
        const inputQty = document.getElementById('txQty');
        const inputDate1 = document.getElementById('tsDate');
        const inputDate2 = document.getElementById('txDate');

        // Target span elements for output
        const spanNoItem = document.getElementById('calc_no_item');
        const spanMcQty = document.getElementById('mc_qty');
        const spanQty = document.getElementById('calc_qty');
        const spanDate = document.getElementById('calc_date');
        const spanDays = document.getElementById('calc_days');
        const spanMachine = document.getElementById('calc_machine');

        function sendCalculationRequest() {
            const noItem = selectItem.value;
            const qty = inputQty.value;
            const date1 = inputDate1.value;
            const date2 = inputDate2.value;

            if (!noItem || !qty || !date1 || !date2) {
                return;
            }

            const url = new URL("http://127.0.0.1:8000/calculation");
            url.searchParams.append("no_item", noItem);
            url.searchParams.append("txQty", qty);
            url.searchParams.append("date1", date1);
            url.searchParams.append("date2", date2);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    spanNoItem.textContent = data.no_item || '-';
                    spanMcQty.textContent = data.jumlah_1mesin || '-';
                    spanQty.textContent = data.calculation || '-';
                    spanDate.textContent = data.delivery_date || '-';
                    spanDays.textContent = data.workdays_until_delivery || '-';
                    spanMachine.textContent = data.kebutuhan_mesin || '-';
                })
                .catch(error => {
                    console.error("Gagal fetch:", error);
                });
        }

        // Event listeners
        selectItem.addEventListener('change', sendCalculationRequest);
        inputQty.addEventListener('input', sendCalculationRequest);
        inputDate1.addEventListener('change', sendCalculationRequest);
        inputDate2.addEventListener('change', sendCalculationRequest);
    });
</script>


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
