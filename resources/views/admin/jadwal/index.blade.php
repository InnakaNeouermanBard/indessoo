{{-- index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <!-- Judul utama Jadwal Kerja -->
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Jadwal Kerja') }}
                </h2>
                <!-- Subjudul Karyawan Outsourcing -->
                <h3 class="text-lg text-gray-600 mt-2">
                    Karyawan Outsourcing
                </h3>
            </div>
            <!-- Tombol di sisi kanan -->
            <div class="flex gap-2">
                <a href="{{ route('jadwal-shift.create-massal') }}" class="btn btn-accent btn-sm">
                    Buat Jadwal Massal
                </a>
                <label class="btn btn-primary btn-sm" for="create_modal_toggle"
                    onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create_modal' }))">
                    Tambah Jadwal
                </label>
                <a href="{{ route('jadwalkerja.index') }}" class="btn btn-accent btn-sm">
                    Jadwal Excel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="container mx-auto px-5 py-4">
        <form action="{{ route('jadwal-shift.index') }}" method="get" class="mb-4 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium">NIK/Nama</label>
                    <input type="text" name="cari_nik" placeholder="Cari NIK/Nama" value="{{ $cari_nik }}"
                        class="input input-bordered w-full mt-1" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Bulan</label>
                    <select name="bulan" class="select select-bordered w-full mt-1">
                        @foreach ($bulanList as $key => $nama)
                            <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                                {{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Tahun</label>
                    <select name="tahun" class="select select-bordered w-full mt-1">
                        @foreach ($tahunList as $key => $nama)
                            <option value="{{ $key }}" {{ $tahun == $key ? 'selected' : '' }}>
                                {{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="btn btn-success w-full">Filter</button>
                </div>
            </div>
        </form>

        @if (session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-error mb-4">{{ session('error') }}</div>
        @endif

        <!-- Tampilan Karyawan dengan Jadwal -->
        <div class="bg-white rounded shadow p-4 mb-6">
            <h3 class="text-lg font-semibold mb-4">Karyawan dengan Jadwal Bulan {{ $bulanList[$bulan] }}
                {{ $tahun }}</h3>

            @if (isset($karyawanWithSchedules) && $karyawanWithSchedules->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($karyawanWithSchedules as $k)
                        <a href="{{ route('jadwal-shift.karyawan-detail', $k->nik) }}?bulan={{ $bulan }}&tahun={{ $tahun }}"
                            class="p-4 border rounded hover:bg-blue-50 transition flex items-center gap-3">
                            <div class="bg-blue-100 p-2 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium">{{ $k->nama_lengkap }}</div>
                                <div class="text-sm text-gray-600">{{ $k->nik }}</div>
                                @if ($k->departemen)
                                    <div class="text-xs text-gray-500">{{ $k->departemen->nama }}</div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    Tidak ada karyawan dengan jadwal di bulan ini
                </div>
            @endif
        </div>

        <!-- Daftar Semua Jadwal (Opsional - bisa disembunyikan jika terlalu banyak) -->
        <div class="bg-white rounded shadow overflow-hidden">
            <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold">Daftar Detail Jadwal</h3>
                <button id="toggleJadwalBtn" class="btn btn-sm btn-outline">
                    Sembunyikan Detail
                </button>
            </div>

            <div id="jadwalTableContainer">
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Shift</th>
                                <th>Libur?</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jadwal as $i => $item)
                                <tr>
                                    <td>{{ $jadwal->firstItem() + $i }}</td>
                                    <!-- Modify this line to handle pagination -->
                                    <td>
                                        <a href="{{ route('jadwal-shift.karyawan-detail', $item->karyawan_nik) }}?bulan={{ $bulan }}&tahun={{ $tahun }}"
                                            class="text-blue-600 hover:underline">
                                            {{ $item->karyawan_nik }}
                                        </a>
                                    </td>
                                    <td>{{ $item->karyawan->nama_lengkap }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                                    <td>
                                        @if ($item->shift)
                                            {{ $item->shift->nama }} ({{ $item->shift->waktu_mulai }} -
                                            {{ $item->shift->waktu_selesai }})
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->is_libur ? 'badge-error' : 'badge-success' }}">
                                            {{ $item->is_libur ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </td>
                                    <td>
                                        <label class="btn btn-warning btn-sm"
                                            onclick="edit_button('{{ $item->id }}')">Edit</label>
                                        <form action="{{ route('jadwal-shift.destroy', $item->id) }}" method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Yakin hapus data ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-error btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">Tidak ada data jadwal untuk periode ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="p-4">
                {{ $jadwal->links() }} <!-- Menampilkan pagination -->
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <x-modal name="create_modal">
        <h3 class="text-lg font-bold">Tambah Jadwal Shift</h3>
        <form action="{{ route('jadwal-shift.store') }}" method="POST">
            @csrf
            <label class="form-control w-full">
                <span>Karyawan</span>
                <select name="karyawan_nik" class="input input-bordered" required>
                    <option value="">Pilih</option>
                    @foreach ($karyawan as $k)
                        <option value="{{ $k->nik }}">{{ $k->nama_lengkap }} ({{ $k->nik }})</option>
                    @endforeach
                </select>
            </label>

            <label class="form-control w-full mt-2">
                <span>Tanggal</span>
                <input type="date" name="tanggal" class="input input-bordered" required />
            </label>

            <label class="form-control w-full mt-2">
                <span>Shift</span>
                <select name="shift_id" class="input input-bordered">
                    <option value="">-- Libur --</option>
                    @foreach ($shifts as $s)
                        <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->waktu_mulai }} -
                            {{ $s->waktu_selesai }})</option>
                    @endforeach
                </select>
            </label>

            <label class="form-control w-full mt-2">
                <div class="flex items-center">
                    <input type="checkbox" name="is_libur" value="1" class="checkbox" />
                    <span class="ml-2">Tandai sebagai libur</span>
                </div>
            </label>

            <button class="btn btn-success mt-4 w-full">Simpan</button>
        </form>
    </x-modal>

    <!-- Modal Edit -->
    <x-modal name="edit_button">
        <h3 class="text-lg font-bold mb-2">Edit Jadwal Shift</h3>
        <form id="form_edit" method="POST" action="">
            @csrf @method('PUT')
            <input type="hidden" name="id" id="edit_id" />
            <label class="form-control w-full">
                <span>Tanggal</span>
                <input type="date" name="tanggal" id="edit_tanggal" class="input input-bordered" required />
            </label>
            <label class="form-control w-full mt-2">
                <span>Shift</span>
                <select name="shift_id" id="edit_shift_id" class="input input-bordered">
                    <option value="">-- Libur --</option>
                    @foreach ($shifts as $s)
                        <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->waktu_mulai }} -
                            {{ $s->waktu_selesai }})</option>
                    @endforeach
                </select>
            </label>
            <label class="form-control mt-2">
                <div class="flex items-center">
                    <input type="checkbox" name="is_libur" id="edit_is_libur" value="1" class="checkbox" />
                    <span class="ml-2">Tandai sebagai libur</span>
                </div>
            </label>
            <button class="btn btn-warning mt-4 w-full">Perbarui</button>
        </form>
    </x-modal>

    <script>
        function edit_button(id) {
            fetch('/admin/jadwal-shift/' + id + '/edit')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('edit_id').value = data.id;
                    document.getElementById('edit_tanggal').value = data.tanggal;
                    document.getElementById('edit_shift_id').value = data.shift_id || '';
                    document.getElementById('edit_is_libur').checked = data.is_libur;
                    document.getElementById('form_edit').action = '/admin/jadwal-shift/' + data.id;

                    window.dispatchEvent(new CustomEvent('open-modal', {
                        detail: 'edit_button'
                    }));
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    alert('Terjadi kesalahan saat mengambil data');
                });
        }

        // Toggle untuk menampilkan/menyembunyikan tabel jadwal detail
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleJadwalBtn');
            const container = document.getElementById('jadwalTableContainer');

            toggleBtn.addEventListener('click', function() {
                if (container.style.display === 'none') {
                    container.style.display = 'block';
                    toggleBtn.textContent = 'Sembunyikan Detail';
                } else {
                    container.style.display = 'none';
                    toggleBtn.textContent = 'Tampilkan Detail';
                }
            });
        });
    </script>
</x-app-layout>
