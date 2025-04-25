{{-- karyawan-detail.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Detail Jadwal Shift: {{ $karyawan->nama_lengkap }}</h2>
            <a href="{{ route('jadwal-shift.index') }}?bulan={{ $bulan }}&tahun={{ $tahun }}"
                class="btn btn-outline btn-sm">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="container mx-auto px-5 py-4">
        <div class="bg-white p-6 rounded shadow mb-6">
            <!-- Informasi Karyawan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-600">NIK</p>
                    <p class="font-medium">{{ $karyawan->nik }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Nama Lengkap</p>
                    <p class="font-medium">{{ $karyawan->nama_lengkap }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Departemen</p>
                    <p class="font-medium">{{ $karyawan->departemen->nama ?? '-' }}</p>
                </div>
            </div>

            <!-- Filter Bulan & Tahun -->
            <form action="{{ route('jadwal-shift.karyawan-detail', $karyawan->nik) }}" method="get" class="mb-4">
                <div class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium mb-1">Bulan</label>
                        <select name="bulan" class="select select-bordered">
                            @foreach ($bulanList as $key => $nama)
                                <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                                    {{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tahun</label>
                        <select name="tahun" class="select select-bordered">
                            @foreach ($tahunList as $key => $nama)
                                <option value="{{ $key }}" {{ $tahun == $key ? 'selected' : '' }}>
                                    {{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        <!-- Calendar View -->
        <div class="bg-white p-6 rounded shadow">
            <div class="mb-4 flex justify-between items-center">
                <h3 class="text-lg font-semibold">Jadwal Bulan {{ $bulanList[$bulan] }} {{ $tahun }}</h3>
                <div class="text-sm">
                    <span class="inline-block w-4 h-4 bg-green-100 border border-green-300 rounded-sm mr-1"></span>
                    Kerja
                    <span class="inline-block w-4 h-4 bg-red-100 border border-red-300 rounded-sm ml-2 mr-1"></span>
                    Libur
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-7 gap-2">
                @foreach (['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                    <div class="bg-gray-100 p-2 text-center font-medium">{{ $day }}</div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-7 gap-2 mt-2">
                @php
                    $firstDay = \Carbon\Carbon::createFromDate($tahun, $bulan, 1);
                    $lastDay = $firstDay->copy()->endOfMonth();

                    // Add empty cells for days before the first day of month
                    $startOfGrid = $firstDay->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                    $endOfGrid = $lastDay->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);

                    $day = $startOfGrid->copy();
                @endphp

                @while ($day <= $endOfGrid)
                    @php
                        $isCurrentMonth = $day->month == $bulan;
                        $calendarDay = $calendarData[$day->day] ?? null;

                        $dayClasses = 'p-2 rounded border h-24 md:h-32 relative ';

                        if (!$isCurrentMonth) {
                            $dayClasses .= 'bg-gray-50 text-gray-400 ';
                        } elseif ($calendarDay && isset($calendarDay['jadwal'])) {
                            if ($calendarDay['is_libur']) {
                                $dayClasses .= 'bg-red-50 border-red-200 ';
                            } else {
                                $dayClasses .= 'bg-green-50 border-green-200 ';
                            }
                        }
                    @endphp

                    <div class="{{ $dayClasses }}" data-date="{{ $day->format('Y-m-d') }}">
                        <div class="flex justify-between items-start mb-1">
                            <span class="font-medium {{ $isCurrentMonth ? '' : 'text-gray-400' }}">
                                {{ $day->day }}
                            </span>

                            @if ($isCurrentMonth)
                                <button type="button" class="text-xs text-blue-600 hover:text-blue-800"
                                    onclick="editDay('{{ $karyawan->nik }}', '{{ $day->format('Y-m-d') }}')">
                                    Edit
                                </button>
                            @endif
                        </div>

                        @if ($isCurrentMonth && isset($calendarDay['jadwal']))
                            <div class="text-sm mt-1">
                                @if ($calendarDay['is_libur'])
                                    <span class="badge badge-error">Libur</span>
                                @elseif ($calendarDay['shift'])
                                    <span class="badge badge-success">{{ $calendarDay['shift']->nama }}</span>
                                    <div class="text-xs mt-1">
                                        {{ $calendarDay['shift']->waktu_mulai }} -
                                        {{ $calendarDay['shift']->waktu_selesai }}
                                    </div>
                                @else
                                    <span class="badge badge-warning">Tidak Ada Shift</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    @php
                        $day->addDay();
                    @endphp
                @endwhile
            </div>
        </div>
    </div>

    <!-- Modal Edit Hari -->
    <x-modal name="edit_day_modal">
        <h3 class="text-lg font-bold mb-2">Edit Jadwal <span id="edit_day_date"></span></h3>
        <form id="form_edit_day" method="POST">
            @csrf
            <input type="hidden" name="karyawan_nik" id="edit_karyawan_nik">
            <input type="hidden" name="tanggal" id="edit_day_tanggal">

            <label class="form-control w-full mt-2">
                <span>Shift</span>
                <select name="shift_id" id="edit_day_shift_id" class="input input-bordered">
                    <option value="">-- Tanpa Shift --</option>
                    @foreach ($shifts as $s)
                        <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->waktu_mulai }} -
                            {{ $s->waktu_selesai }})</option>
                    @endforeach
                </select>
            </label>

            <label class="form-control mt-2">
                <div class="flex items-center">
                    <input type="checkbox" name="is_libur" id="edit_day_is_libur" value="1" class="checkbox">
                    <span class="ml-2">Tandai sebagai libur</span>
                </div>
            </label>

            <div class="flex justify-between mt-4">
                <button type="button" class="btn btn-outline"
                    onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'edit_day_modal' }))">
                    Batal
                </button>
                <button type="button" id="save_day_button" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </x-modal>

    <script>
        // Perbaikan pada fungsi editDay di karyawan-detail.blade.php
        // Perbaikan pada fungsi editDay di karyawan-detail.blade.php
        function editDay(karyawanNik, tanggal) {
            // Pastikan tanggal hanya dalam format YYYY-MM-DD (tanpa komponen waktu)
            // untuk menghindari masalah timezone
            if (tanggal.length > 10) {
                tanggal = tanggal.substring(0, 10);
            }

            console.log("Edit jadwal untuk tanggal:", tanggal);

            // Format tanggal untuk display
            const displayDate = new Date(tanggal + "T00:00:00").toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });

            document.getElementById('edit_day_date').textContent = displayDate;
            document.getElementById('edit_karyawan_nik').value = karyawanNik;
            document.getElementById('edit_day_tanggal').value = tanggal;

            // Reset form
            document.getElementById('edit_day_shift_id').value = '';
            document.getElementById('edit_day_is_libur').checked = false;

            // Tambahkan parameter khusus untuk debugging
            const debugParam = new Date().getTime();

            // Fetch existing data if available
            fetch(
                    `/admin/jadwal-shift/get-day?karyawan_nik=${encodeURIComponent(karyawanNik)}&tanggal=${encodeURIComponent(tanggal)}&debug=${debugParam}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Data jadwal yang diterima:", data);

                    if (data && data.jadwal) {
                        document.getElementById('edit_day_shift_id').value = data.jadwal.shift_id || '';
                        document.getElementById('edit_day_is_libur').checked = !!data.jadwal.is_libur;
                        console.log("Jadwal ditemukan - Shift ID:", data.jadwal.shift_id, "Libur:", data.jadwal
                            .is_libur);
                    } else {
                        console.log("Tidak ada jadwal untuk tanggal ini");
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    alert('Terjadi kesalahan saat mengambil data jadwal');
                });

            // Open modal
            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'edit_day_modal'
            }));
        }

        // Perbaikan pada script di karyawan-detail.blade.php
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('save_day_button').addEventListener('click', function() {
                const karyawanNik = document.getElementById('edit_karyawan_nik').value;
                const tanggal = document.getElementById('edit_day_tanggal').value;
                const shiftId = document.getElementById('edit_day_shift_id').value;
                const isLibur = document.getElementById('edit_day_is_libur').checked;

                // Tambahkan validasi sederhana
                if (!karyawanNik || !tanggal) {
                    alert('NIK karyawan dan tanggal harus diisi!');
                    return;
                }

                // Pastikan tanggal hanya dalam format YYYY-MM-DD (tanpa komponen waktu)
                const formattedTanggal = tanggal.substring(0, 10);

                console.log("Menyimpan jadwal untuk tanggal:", formattedTanggal);

                const data = {
                    karyawan_nik: karyawanNik,
                    tanggal: formattedTanggal,
                    shift_id: shiftId || null, // Pastikan null jika kosong
                    is_libur: isLibur
                };

                console.log("Data yang akan dikirim:", data);

                fetch('/admin/jadwal-shift/update-single-day', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(result => {
                        console.log("Hasil dari server:", result);

                        if (result.success) {
                            // Tampilkan tanggal yang diupdate untuk memastikan tidak ada kesalahan
                            alert(
                                `Jadwal berhasil diperbarui untuk tanggal ${result.debug_tanggal || formattedTanggal}`
                                );

                            // Reload halaman setelah berhasil
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menyimpan jadwal');
                    });
            });
        });
    </script>
</x-app-layout>
