{{-- create-massal.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Buat Jadwal Shift Massal</h2>
    </x-slot>

    <div class="container mx-auto px-5 py-4">
        <div class="bg-white p-6 rounded shadow">
            @if (session('error'))
                <div class="alert alert-error mb-4">{{ session('error') }}</div>
            @endif

            <form action="{{ route('jadwal-shift.store-massal') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Periode Jadwal -->
                    <div class="col-span-1 md:col-span-2 border-b pb-4 mb-4">
                        <h3 class="text-lg font-semibold mb-4">Periode Jadwal</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Bulan Mulai</label>
                                <select name="bulan_mulai" class="select select-bordered w-full" required>
                                    @foreach ($bulanList as $key => $nama)
                                        <option value="{{ $key }}" {{ date('m') == $key ? 'selected' : '' }}>
                                            {{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Tahun</label>
                                <select name="tahun_mulai" class="select select-bordered w-full" required>
                                    @foreach ($tahunList as $key => $nama)
                                        <option value="{{ $key }}" {{ date('Y') == $key ? 'selected' : '' }}>
                                            {{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Durasi (bulan)</label>
                                <select name="durasi" class="select select-bordered w-full" required>
                                    <option value="1">1 Bulan</option>
                                    <option value="2">2 Bulan</option>
                                    <option value="3">3 Bulan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Karyawan -->
                    <div class="border-b pb-4 mb-4">
                        <h3 class="text-lg font-semibold mb-2">Pilih Karyawan</h3>
                        <p class="text-sm text-gray-600 mb-4">Pilih karyawan yang akan dijadwalkan</p>

                        <div class="max-h-60 overflow-y-auto bg-gray-50 p-4 rounded border">
                            <div class="mb-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" id="selectAll" class="checkbox mr-2">
                                    <span>Pilih Semua</span>
                                </label>
                            </div>
                            <hr class="my-2">

                            @foreach ($karyawan as $k)
                                <div class="mb-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="karyawan_nik[]" value="{{ $k->nik }}"
                                            class="checkbox mr-2 karyawan-checkbox">
                                        <span>{{ $k->nama_lengkap }} ({{ $k->nik }})</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Pola Jadwal -->
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Pola Jadwal</h3>
                        <p class="text-sm text-gray-600 mb-4">Pilih pola rotasi shift</p>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Jenis Pola</label>
                            <select name="pattern_type" id="pattern_type" class="select select-bordered w-full"
                                required>
                                @foreach ($patternOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="shift_selection" class="mt-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Shift Pagi</label>
                                    <select name="shift_pagi" class="select select-bordered w-full">
                                        <option value="">Tidak Ada</option>
                                        @foreach ($shifts as $s)
                                            <option value="{{ $s->id }}">{{ $s->nama }}
                                                ({{ $s->waktu_mulai }} - {{ $s->waktu_selesai }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Shift Siang</label>
                                    <select name="shift_siang" class="select select-bordered w-full">
                                        <option value="">Tidak Ada</option>
                                        @foreach ($shifts as $s)
                                            <option value="{{ $s->id }}">{{ $s->nama }}
                                                ({{ $s->waktu_mulai }} - {{ $s->waktu_selesai }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Shift Malam</label>
                                    <select name="shift_malam" class="select select-bordered w-full">
                                        <option value="">Tidak Ada</option>
                                        @foreach ($shifts as $s)
                                            <option value="{{ $s->id }}">{{ $s->nama }}
                                                ({{ $s->waktu_mulai }} - {{ $s->waktu_selesai }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="custom_pattern" class="hidden mt-4">
                            <div class="border rounded p-4 bg-gray-50">
                                <h4 class="font-medium mb-2">Custom Pattern Builder</h4>
                                <p class="text-sm mb-4">Buat pola kustom dengan menambahkan hari kerja dan hari libur
                                    secara berurutan.</p>

                                <div id="pattern_container">
                                    <!-- Pola akan ditampilkan di sini -->
                                </div>

                                <div class="flex gap-2 mt-4">
                                    <button type="button" class="btn btn-sm btn-outline" id="add_work_day">+ Hari
                                        Kerja</button>
                                    <button type="button" class="btn btn-sm btn-outline" id="add_off_day">+ Hari
                                        Libur</button>
                                    <button type="button" class="btn btn-sm btn-outline btn-error"
                                        id="reset_pattern">Reset</button>
                                </div>

                                <input type="hidden" name="custom_pattern" id="custom_pattern_input" value="">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 border-t pt-4">
                    <div class="flex justify-between">
                        <a href="{{ route('jadwal-shift.index') }}" class="btn btn-outline">Batal</a>
                        <button type="submit" class="btn btn-primary">Buat Jadwal Massal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all checkbox functionality
            const selectAll = document.getElementById('selectAll');
            const karyawanCheckboxes = document.querySelectorAll('.karyawan-checkbox');

            selectAll.addEventListener('change', function() {
                karyawanCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });
            });

            karyawanCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(karyawanCheckboxes).every(c => c.checked);
                    const anyChecked = Array.from(karyawanCheckboxes).some(c => c.checked);

                    selectAll.checked = allChecked;
                    selectAll.indeterminate = anyChecked && !allChecked;
                });
            });

            // Pattern type handling
            const patternType = document.getElementById('pattern_type');
            const shiftSelection = document.getElementById('shift_selection');
            const customPattern = document.getElementById('custom_pattern');

            patternType.addEventListener('change', function() {
                if (this.value === 'custom') {
                    shiftSelection.classList.add('hidden');
                    customPattern.classList.remove('hidden');
                } else {
                    shiftSelection.classList.remove('hidden');
                    customPattern.classList.add('hidden');
                }
            });

            // Custom pattern builder
            const patternContainer = document.getElementById('pattern_container');
            const addWorkDay = document.getElementById('add_work_day');
            const addOffDay = document.getElementById('add_off_day');
            const resetPattern = document.getElementById('reset_pattern');
            const customPatternInput = document.getElementById('custom_pattern_input');

            let patternData = [];

            function updatePatternUI() {
                patternContainer.innerHTML = '';

                if (patternData.length === 0) {
                    patternContainer.innerHTML = '<p class="text-gray-500">Belum ada pola yang ditambahkan</p>';
                    return;
                }

                const patternDiv = document.createElement('div');
                patternDiv.className = 'flex flex-wrap gap-2 mb-2';

                patternData.forEach((item, index) => {
                    const dayElement = document.createElement('div');

                    if (item.is_libur) {
                        dayElement.className = 'p-2 bg-red-100 border border-red-300 rounded text-sm';
                        dayElement.textContent = `Hari ${index + 1}: Libur`;
                    } else {
                        dayElement.className = 'p-2 bg-green-100 border border-green-300 rounded text-sm';
                        const shiftText = item.shift_id ? `Shift ID: ${item.shift_id}` : 'Tanpa Shift';
                        dayElement.textContent = `Hari ${index + 1}: ${shiftText}`;
                    }

                    patternDiv.appendChild(dayElement);
                });

                patternContainer.appendChild(patternDiv);
                customPatternInput.value = JSON.stringify(patternData);
            }

            addWorkDay.addEventListener('click', function() {
                // Buat array shifts untuk digunakan dalam modal
                const shiftsArray = [];
                @foreach ($shifts as $s)
                    shiftsArray.push({
                        id: "{{ $s->id }}",
                        nama: "{{ $s->nama }}",
                        waktu: "{{ $s->waktu_mulai }} - {{ $s->waktu_selesai }}"
                    });
                @endforeach

                // Buat modal untuk pemilihan shift
                const modalDiv = document.createElement('div');
                modalDiv.className =
                    'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';

                let modalContent = `
                    <div class="bg-white p-4 rounded shadow-lg max-w-md w-full">
                        <h3 class="text-lg font-bold mb-4">Pilih Shift untuk Hari Kerja</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Shift</label>
                            <select id="shift_select" class="select select-bordered w-full">
                                <option value="">-- Tanpa Shift --</option>
                `;

                shiftsArray.forEach(shift => {
                    modalContent +=
                        `<option value="${shift.id}">${shift.nama} (${shift.waktu})</option>`;
                });

                modalContent += `
                            </select>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" id="cancel_shift" class="btn btn-outline">Batal</button>
                            <button type="button" id="confirm_shift" class="btn btn-primary">Tambahkan</button>
                        </div>
                    </div>
                `;

                modalDiv.innerHTML = modalContent;
                document.body.appendChild(modalDiv);

                // Event handlers untuk modal
                document.getElementById('cancel_shift').addEventListener('click', function() {
                    document.body.removeChild(modalDiv);
                });

                document.getElementById('confirm_shift').addEventListener('click', function() {
                    const shiftId = document.getElementById('shift_select').value;
                    patternData.push({
                        shift_id: shiftId || null,
                        is_libur: false
                    });
                    updatePatternUI();
                    document.body.removeChild(modalDiv);
                });
            });

            addOffDay.addEventListener('click', function() {
                patternData.push({
                    shift_id: null,
                    is_libur: true
                });
                updatePatternUI();
            });

            resetPattern.addEventListener('click', function() {
                patternData = [];
                updatePatternUI();
            });

            // Initialize UI
            updatePatternUI();
        });
    </script>
</x-app-layout>
