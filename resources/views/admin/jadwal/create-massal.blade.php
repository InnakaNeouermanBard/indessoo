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
                        <h3 class="text-lg font-semibold mb-2">Pola Jadwal Mingguan</h3>
                        <p class="text-sm text-gray-600 mb-4">Tentukan shift untuk setiap minggu (Senin-Jumat,
                            Sabtu-Minggu libur)</p>

                        <!-- Pattern type selector -->
                        <input type="hidden" name="pattern_type" value="mingguan">

                        <div class="bg-blue-50 p-3 mb-4 rounded border border-blue-200">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                Jadwal akan dibuat dengan ketentuan:
                            <ul class="list-disc ml-5 mt-1">
                                <li>Jadwal dimulai dari hari Senin pertama dalam bulan</li>
                                <li>Jadwal dibuat untuk semua hari kerja (Senin-Jumat) dalam bulan</li>
                                <li>Hari Sabtu dan Minggu selalu libur</li>
                                <li>Jika bulan berakhir di tengah minggu (Senin-Kamis), jadwal akan dilanjutkan ke bulan
                                    berikutnya sampai Jumat</li>
                                <li>Hari di bulan berikutnya tersebut akan menggunakan input minggu ke-5 (opsional)</li>
                            </ul>
                            </p>
                        </div>

                        <div id="weekly_pattern" class="mt-4">
                            <div class="grid grid-cols-1 gap-3">
                                <div class="border rounded p-3 bg-gray-50">
                                    <h4 class="font-medium mb-2">Minggu ke-1</h4>
                                    <select name="shift_minggu1" class="select select-bordered w-full">
                                        <option value="">Libur</option>
                                        @foreach ($shifts as $s)
                                            <option value="{{ $s->id }}">{{ $s->nama }}
                                                ({{ $s->waktu_mulai }} - {{ $s->waktu_selesai }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="border rounded p-3 bg-gray-50">
                                    <h4 class="font-medium mb-2">Minggu ke-2</h4>
                                    <select name="shift_minggu2" class="select select-bordered w-full">
                                        <option value="">Libur</option>
                                        @foreach ($shifts as $s)
                                            <option value="{{ $s->id }}">{{ $s->nama }}
                                                ({{ $s->waktu_mulai }} - {{ $s->waktu_selesai }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="border rounded p-3 bg-gray-50">
                                    <h4 class="font-medium mb-2">Minggu ke-3</h4>
                                    <select name="shift_minggu3" class="select select-bordered w-full">
                                        <option value="">Libur</option>
                                        @foreach ($shifts as $s)
                                            <option value="{{ $s->id }}">{{ $s->nama }}
                                                ({{ $s->waktu_mulai }} - {{ $s->waktu_selesai }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="border rounded p-3 bg-gray-50">
                                    <h4 class="font-medium mb-2">Minggu ke-4</h4>
                                    <select name="shift_minggu4" class="select select-bordered w-full">
                                        <option value="">Libur</option>
                                        @foreach ($shifts as $s)
                                            <option value="{{ $s->id }}">{{ $s->nama }}
                                                ({{ $s->waktu_mulai }} - {{ $s->waktu_selesai }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="border rounded p-3 bg-gray-50">
                                    <h4 class="font-medium mb-2">Minggu ke-5 (opsional)</h4>
                                    <select name="shift_minggu5" class="select select-bordered w-full">
                                        <option value="">Libur</option>
                                        @foreach ($shifts as $s)
                                            <option value="{{ $s->id }}">{{ $s->nama }}
                                                ({{ $s->waktu_mulai }} - {{ $s->waktu_selesai }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Digunakan untuk hari kerja lanjutan di bulan
                                        berikutnya jika bulan berakhir di tengah minggu kerja</p>
                                </div>
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
        });
    </script>
</x-app-layout>
