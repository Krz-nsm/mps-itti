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
                                    {{-- <div class="col-xl-3 col-md-6 mb-3">
                                        <label for="no_item" class="form-label">Item </label>
                                        <select class="form-control select2" id="select2Code" name="no_item">
                                            <option value="">Pilih No. Item</option>
                                            @foreach ($filter as $item)
                                                <option value="{{ $item->hanger }}">{{ $item->hanger }} |
                                                    {{ $item->longdescription }}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}
                                    <div class="col-xl-3 col-md-6 mb-3">
                                        <label for="no_item" class="form-label">Item</label>
                                        <select class="form-control select2" id="select2Code" name="no_item"></select>
                                    </div>

                                    <div class="col-xl-3 col-md-6 mb-3">
                                        <label for="txQty" class="form-label">Qty / Kg</label>
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
                                    <div class="col-xl-4 col-md-6 mb-3">
                                        <label for="txDate" class="form-label">Hari Libur Selain Minggu</label>
                                        <input type="number" class="form-control" id="txRedDate">
                                    </div>
                                </div>
                                {{-- <div class="position-absolute bottom-0 end-0">
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm">
                                        Generate Report
                                    </a>
                                </div> --}}
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
                                    <div class="col-xl-12 col-md-12 mb-3 ">
                                        <button type="button" class="btn btn-primary " id="btSubmit">Submit</button>
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
                                <div class="col-xl-3 col-md-6 mb-4" onclick="moveToSelected(this)"
                                    data-id="{{ $machine->id }}"data-code="{{ $machine->mesin_code }}"
                                    data-item="{{ $machine->item_code }}"
                                    data-start="{{ $machine->start_date }}"data-end="{{ $machine->end_date }}">
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
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- Perhitungan libur dan machine nembak ke api / database --}}
    {{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectItem = $('#select2Code');
        const inputQty = document.getElementById('txQty');
        const inputDate1 = document.getElementById('tsDate');
        const inputDate2 = document.getElementById('txDate');

        const spanNoItem = document.getElementById('calc_no_item');
        const spanMcQty = document.getElementById('mc_qty');
        const spanQty = document.getElementById('calc_qty');
        const spanDate = document.getElementById('calc_date');
        const spanDays = document.getElementById('calc_days');
        const spanMachine = document.getElementById('calc_machine');

        selectItem.select2({
            placeholder: 'Pilih No. Item',
            ajax: {
                url: '{{ route("schedule.filterdata") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term || ''
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 0
        });

        $.ajax({
            url: '{{ route("schedule.filterdata") }}',
            data: { q: '' },
            success: function(data) {
                const initialItems = data.slice(0, 5);
                initialItems.forEach(function(item) {
                    const option = new Option(item.text, item.id, false, false);
                    selectItem.append(option);
                });
                selectItem.trigger('change');
            }
        });

        // Fungsi request kalkulasi
        function sendCalculationRequest() {
            const noItem = selectItem.val();
            const qty = inputQty.value;
            const date1 = inputDate1.value;
            const date2 = inputDate2.value;

            if (!noItem || !qty || !date1 || !date2) {
                return;
            }

            const url = new URL("{{ route('home.calculation') }}");
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

        // Event listener untuk semua input
        selectItem.on('change', sendCalculationRequest);
        inputQty.addEventListener('input', sendCalculationRequest);
        inputDate1.addEventListener('change', sendCalculationRequest);
        inputDate2.addEventListener('change', sendCalculationRequest);
    });
</script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectItem = $('#select2Code');
            const inputQty = document.getElementById('txQty');
            const inputDate1 = document.getElementById('tsDate');
            const inputDate2 = document.getElementById('txDate');
            const inputRedDate = document.getElementById('txRedDate');

            const spanNoItem = document.getElementById('calc_no_item');
            const spanMcQty = document.getElementById('mc_qty');
            const spanQty = document.getElementById('calc_qty');
            const spanDate = document.getElementById('calc_date');
            const spanDays = document.getElementById('calc_days');
            const spanMachine = document.getElementById('calc_machine');

            selectItem.select2({
                placeholder: 'Pilih No. Item',
                ajax: {
                    url: '{{ route('schedule.filterdata') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0
            });

            $.ajax({
                url: '{{ route('schedule.filterdata') }}',
                data: {
                    q: ''
                },
                success: function(data) {
                    const initialItems = data.slice(0, 5);
                    initialItems.forEach(function(item) {
                        const option = new Option(item.text, item.id, false, false);
                        selectItem.append(option);
                    });
                    selectItem.trigger('change');
                }
            });

        function sendCalculationRequest() {
            const noItem = selectItem.val();
            const qty = inputQty.value;
            const date1 = inputDate1.value;
            const date2 = inputDate2.value;
            const redDays = parseInt(inputRedDate.value || '0');

            if (!noItem || !qty || !date1 || !date2) return;

            // ✅ Reset mesin yang sudah dipilih
            resetSelectedMachines();

            const url = new URL("{{ route('home.calculation') }}");
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
                    const rawWorkdays = parseInt(data.workdays_until_delivery || 0);
                    const effectiveWorkdays = Math.max(rawWorkdays - redDays, 1);
                
                    spanDays.textContent = effectiveWorkdays;
                    const jumlahPerMesin = parseFloat(data.jumlah_1mesin || 0);
                    const mesinDibutuhkan = Math.ceil(jumlahPerMesin / effectiveWorkdays);
                
                    spanMachine.textContent = mesinDibutuhkan;
                
                    console.log(
                        `Workdays Asli: ${rawWorkdays}, Libur Tambahan: ${redDays}, Workdays Efektif: ${effectiveWorkdays}, Mesin: ${mesinDibutuhkan}`
                    );
                })
                .catch(error => {
                    console.error("Gagal fetch:", error);
                });
            }

            selectItem.on('change', sendCalculationRequest);
            inputQty.addEventListener('input', sendCalculationRequest);
            inputDate1.addEventListener('change', sendCalculationRequest);
            inputDate2.addEventListener('change', sendCalculationRequest);
            inputRedDate.addEventListener('input', sendCalculationRequest);
        });
    </script>
    <script>
        let selectedMachines = [];
        let machineRequired = 0;
        let usedMesin = 0;

        function parseDate(str) {
            return str ? new Date(str) : null;
        }

        function isMachineAvailable(machineStart, machineEnd, inputStart, inputEnd) {
            const ms = parseDate(machineStart);
            const me = parseDate(machineEnd);
            if (!ms || !me) return true;
            const isOverlap = inputStart <= me && inputEnd >= ms;

            return !isOverlap;
        }

        function refreshMachineAvailability() {
            const inputStart = parseDate(document.getElementById('tsDate').value);
            const inputEnd = parseDate(document.getElementById('txDate').value);

            const allMachines = document.querySelectorAll('#available-machines .col-xl-3');

            allMachines.forEach(card => {
                const start = card.dataset.start;
                const end = card.dataset.end;

                const isAvailable = isMachineAvailable(start, end, inputStart, inputEnd);

                const statusDiv = card.querySelector('.text-success, .text-danger');
                if (isAvailable) {
                    statusDiv.classList.remove('text-danger');
                    statusDiv.classList.add('text-success');
                    statusDiv.textContent = 'Available';
                } else {
                    statusDiv.classList.remove('text-success');
                    statusDiv.classList.add('text-danger');
                    statusDiv.textContent = 'Unavailable';
                }

                card.style.pointerEvents = isAvailable ? 'auto' : 'none';
                card.style.opacity = isAvailable ? '1' : '0.5';
            });
        }

        document.getElementById('tsDate').addEventListener('change', refreshMachineAvailability);
        document.getElementById('txDate').addEventListener('change', refreshMachineAvailability);

        function moveToSelected(card) {
            const machineId = card.dataset.id;
            const machineCode = card.dataset.code;

            const spanMachine = document.getElementById('calc_machine');
            let machineRequired = parseInt(spanMachine.textContent || '0');

            if (!machineId) {
                alert("Card tidak memiliki data-id!");
                return;
            }

            if (isNaN(machineRequired) || machineRequired <= 0) {
                alert("Jumlah mesin yang dibutuhkan sudah tercapai.");
                return;
            }

            if (selectedMachines.includes(machineId)) {
                alert("Mesin sudah dipilih!");
                return;
            }
            const clonedCard = card.cloneNode(true);
            clonedCard.onclick = null;
            const closeBtn = document.createElement('button');
            closeBtn.textContent = '×';
            closeBtn.className = 'btn btn-sm btn-danger';
            closeBtn.style.position = 'absolute';
            closeBtn.style.top = '5px';
            closeBtn.style.right = '5px';
            closeBtn.onclick = function() {
                cancelSelected(clonedCard, machineId, card);
            };

            clonedCard.style.position = 'relative';
            clonedCard.appendChild(closeBtn);
            document.getElementById('selected-machines').appendChild(clonedCard);
            selectedMachines.push(machineId);
            card.remove();
            usedMesin ++;
            machineRequired -= 1;
            spanMachine.textContent = machineRequired;
            console.log(usedMesin);

        }

        function cancelSelected(clonedCard, machineId, originalCard) {
            clonedCard.remove();
            document.getElementById('available-machines').appendChild(originalCard);
            selectedMachines = selectedMachines.filter(id => id !== machineId);
            const spanMachine = document.getElementById('calc_machine');
            let currentRequired = parseInt(spanMachine.textContent || '0');
            if (usedMesin > 0) {
              usedMesin--;
            }
            spanMachine.textContent = currentRequired + 1;
            console.log(`Mesin ${machineId} dikembalikan. Kebutuhan bertambah jadi: ${currentRequired + 1}`);
            refreshMachineAvailability();
            console.log(usedMesin);
        }

        function sendCalculationRequest() {
            const noItem = $('#select2Code').val();
            const qty = document.getElementById('txQty').value;
            const date1 = document.getElementById('tsDate').value;
            const date2 = document.getElementById('txDate').value;

            if (!noItem || !qty || !date1 || !date2) return;

            const url = new URL("{{ route('home.calculation') }}");
            url.searchParams.append("no_item", noItem);
            url.searchParams.append("txQty", qty);
            url.searchParams.append("date1", date1);
            url.searchParams.append("date2", date2);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('calc_machine').textContent = data.kebutuhan_mesin || '-';
                    machineRequired = parseInt(data.kebutuhan_mesin || 0);

                    // Reset selected machine jika terlalu banyak
                    if (selectedMachines.length > machineRequired) {
                        alert("Jumlah mesin yang dipilih melebihi kebutuhan. Silakan reset.");
                    }

                    refreshMachineAvailability();
                })
                .catch(error => {
                    console.error("Gagal fetch:", error);
                });
        }
        function resetSelectedMachines() {
            const selectedContainer = document.getElementById("selected-machines");
            const availableContainer = document.getElementById("available-machines");
            const selectedCards = selectedContainer.querySelectorAll(".col-xl-3"); // asumsi class card

            selectedCards.forEach(card => {
                // Hapus tombol close jika ada
                const closeBtn = card.querySelector('button');
                if (closeBtn) closeBtn.remove();

                // Hapus style posisi relatif
                card.style.position = '';

                availableContainer.appendChild(card);
            });

            selectedMachines = [];
            usedMesin = 0;
            document.getElementById('calc_machine').textContent = '-';
        }
    </script>
    <script>
        document.getElementById('btSubmit').addEventListener('click', function() {
            const noItem = $('#select2Code').val();
            const qty = document.getElementById('txQty').value;
            const startDate = document.getElementById('tsDate').value;
            const endDate = document.getElementById('txDate').value;
            const qtyPerDay = document.getElementById('calc_qty').textContent;

            const dayNeed = document.getElementById('mc_qty').textContent;
            const dayPlann = document.getElementById('calc_qty').textContent;

            const selectedMachineCards = document.querySelectorAll('#selected-machines .col-xl-3');
            const machines = Array.from(selectedMachineCards)
                .map(card => card.dataset.code)
                .filter(Boolean);

            if (!noItem || !qty || !startDate || !endDate || machines.length === 0 || !qtyPerDay) {
                alert('Pastikan semua input dan mesin telah dipilih.');
                return;
            }

            if(dayPlann - dayNeed <= 0){
                alert('Range tanggal antara kebutuhan hari produksi sama tanngal input works daynya tidak sesuai.');
                return;
            }

            fetch('{{ route('schedule.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        no_item: noItem,
                        qty: parseInt(qty),
                        qty_day: parseInt(qtyPerDay),
                        start_date: startDate,
                        delivery_date: endDate,
                        machines: machines
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Server error: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'Success') {
                        alert('✅ Data berhasil disimpan!');
                        location.reload();
                    } else {
                        alert('⚠️ Gagal menyimpan data.');
                        console.log('Server response:', data);
                    }
                })
                .catch(error => {
                    console.error('❌ Error:', error);
                    alert('Terjadi kesalahan saat mengirim data.');
                });
        });
    </script>
@endpush


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


{{-- Script event listener --}}
<script>
    // document.addEventListener('DOMContentLoaded', function () {
    //     const selectItem = document.getElementById('select2Code');
    //     const inputQty = document.getElementById('txQty');
    //     const inputDate1 = document.getElementById('tsDate');
    //     const inputDate2 = document.getElementById('txDate');

    //     // Target span elements for output
    //     const spanNoItem = document.getElementById('calc_no_item');
    //     const spanMcQty = document.getElementById('mc_qty');
    //     const spanQty = document.getElementById('calc_qty');
    //     const spanDate = document.getElementById('calc_date');
    //     const spanDays = document.getElementById('calc_days');
    //     const spanMachine = document.getElementById('calc_machine');

    //     function sendCalculationRequest() {
    //         const noItem = selectItem.value;
    //         const qty = inputQty.value;
    //         const date1 = inputDate1.value;
    //         const date2 = inputDate2.value;

    //         if (!noItem || !qty || !date1 || !date2) {
    //             return;
    //         }

    //         const url = new URL("{{ route('home.calculation') }}");
    //         url.searchParams.append("no_item", noItem);
    //         url.searchParams.append("txQty", qty);
    //         url.searchParams.append("date1", date1);
    //         url.searchParams.append("date2", date2);

    //         fetch(url)
    //             .then(response => response.json())
    //             .then(data => {
    //                 spanNoItem.textContent = data.no_item || '-';
    //                 spanMcQty.textContent = data.jumlah_1mesin || '-';
    //                 spanQty.textContent = data.calculation || '-';
    //                 spanDate.textContent = data.delivery_date || '-';
    //                 spanDays.textContent = data.workdays_until_delivery || '-';
    //                 spanMachine.textContent = data.kebutuhan_mesin || '-';
    //             })
    //             .catch(error => {
    //                 console.error("Gagal fetch:", error);
    //             });
    //     }

    //     // Event listeners
    //     selectItem.addEventListener('change', sendCalculationRequest);
    //     inputQty.addEventListener('input', sendCalculationRequest);
    //     inputDate1.addEventListener('change', sendCalculationRequest);
    //     inputDate2.addEventListener('change', sendCalculationRequest);
    // });
</script>

{{-- Script pindahin mesin ke kanan --}}
{{-- <script>
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
</script> --}}

<style>
    .btn-danger {
        font-weight: bold;
        padding: 0 8px;
        line-height: 1;
    }
</style>
