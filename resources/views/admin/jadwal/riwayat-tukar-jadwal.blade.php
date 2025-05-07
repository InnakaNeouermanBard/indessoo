<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Riwayat Pertukaran Jadwal') }}
            </h2>
        </div>
    </x-slot>

    <div class="container mx-auto px-5 pt-5">
        <!-- Card Statistik -->
        <div class="mb-6 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
            <!-- Card Jumlah Tukar Jadwal Hari Ini -->
            <div class="rounded-xl bg-white p-5 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500">Pertukaran Jadwal Hari Ini</p>
                        <h5 class="mt-1 text-2xl font-bold">{{ $countTukarJadwalToday }}</h5>
                    </div>
                    <div class="rounded-full bg-blue-100 p-3">
                        <i class="ri-calendar-todo-line text-xl text-blue-500"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter dan Pencarian -->
        <div class="mb-6 rounded-xl bg-white p-5 shadow-lg">
            <h3 class="mb-4 text-lg font-semibold">Filter Riwayat</h3>
            <form action="{{ route('tukar-jadwal.riwayat') }}" method="GET">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Cari Karyawan</label>
                        <input type="text" name="search" class="input input-bordered w-full"
                            placeholder="Nama atau NIK karyawan" value="{{ request('search') }}">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" class="input input-bordered w-full"
                            value="{{ request('tanggal_awal') }}">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="input input-bordered w-full"
                            value="{{ request('tanggal_akhir') }}">
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <a href="{{ route('tukar-jadwal.riwayat') }}" class="btn btn-outline mr-2">Reset</a>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>

        <!-- Tabel Riwayat -->
        <div class="overflow-x-auto rounded-xl bg-white p-5 shadow-lg">
            <h3 class="mb-4 text-lg font-semibold">Riwayat Pertukaran Jadwal</h3>

            @if ($tukarJadwal->count() > 0)
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Karyawan Pengaju</th>
                            <th>Karyawan Penerima</th>
                            <th>Status</th>
                            <th>Tgl. Pertukaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tukarJadwal as $index => $item)
                            <tr class="{{ request('highlight') == $item->id ? 'bg-yellow-100' : '' }} hover">
                                <td class="font-bold">{{ $tukarJadwal->firstItem() + $index }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i') }}</td>
                                <td>
                                    <div class="font-medium">{{ $item->pengaju->nama_lengkap ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">NIK: {{ $item->nik_pengaju }}</div>
                                </td>
                                <td>
                                    <div class="font-medium">{{ $item->penerima->nama_lengkap ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">NIK: {{ $item->nik_penerima }}</div>
                                </td>
                                <td>
                                    @if ($item->status == 'approved')
                                        <span class="badge badge-success">Disetujui</span>
                                    @elseif($item->status == 'rejected')
                                        <span class="badge badge-error">Ditolak</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
                                <td>
                                    <button class="btn btn-info btn-sm"
                                        onclick="detailTukarJadwal({{ $item->id }})">
                                        <i class="ri-eye-line mr-1"></i> Detail
                                    </button>

                                    @if ($item->status == 'pending')
                                        <form action="{{ route('admin.tukar-jadwal.terima', $item->id) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="ri-check-line mr-1"></i> Terima
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.tukar-jadwal.tolak', $item->id) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="btn btn-error btn-sm">
                                                <i class="ri-close-line mr-1"></i> Tolak
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $tukarJadwal->links() }}
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-8">
                    <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <i class="ri-inbox-line text-3xl text-gray-400"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-500">Tidak ada data pertukaran jadwal</h4>
                    <p class="text-gray-400">Belum ada pertukaran jadwal yang diajukan</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Detail Tukar Jadwal -->
    <div id="detailModal" class="modal">
        <div class="modal-box max-w-2xl">
            <div class="flex justify-between mb-4">
                <h3 class="text-lg font-bold">Detail Pertukaran Jadwal</h3>
                <button onclick="closeDetailModal()" class="btn btn-sm btn-circle">
                    <i class="ri-close-line"></i>
                </button>
            </div>

            <div id="modalContent" class="space-y-4">
                <!-- Content akan diisi via AJAX -->
                <div class="skeleton h-10 w-full"></div>
                <div class="skeleton h-20 w-full"></div>
                <div class="skeleton h-32 w-full"></div>
            </div>

            <div class="modal-action">
                <button onclick="closeDetailModal()" class="btn btn-outline">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        // Tampilkan notifikasi jika ada
        @if (session()->has('success'))
            Swal.fire({
                title: 'Berhasil',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#6419E6',
            });
        @endif

        @if (session()->has('error'))
            Swal.fire({
                title: 'Gagal',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#6419E6',
            });
        @endif

        @if (session()->has('info'))
            Swal.fire({
                title: 'Informasi',
                text: '{{ session('info') }}',
                icon: 'info',
                confirmButtonColor: '#6419E6',
            });
        @endif

        // Fungsi untuk menampilkan modal detail
        function detailTukarJadwal(id) {
            // Tampilkan modal
            document.getElementById('detailModal').classList.add('modal-open');

            // Tampilkan loading di modal content
            document.getElementById('modalContent').innerHTML = `
                <div class="flex justify-center items-center py-8">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                </div>
            `;

            // <p><span class="font-medium">Departemen:</span> ${data.pengaju.departemen ? data.pengaju.departemen.nama : 'N/A'}</p>
            // Ambil data detail via AJAX
            fetch('{{ url('admin/tukar-jadwal/detail') }}/' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let html = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="border rounded-lg p-4">
                                    <h4 class="font-semibold mb-3">Karyawan Pengaju</h4>
                                    <p><span class="font-medium">Nama:</span> ${data.pengaju.nama_lengkap}</p>
                                    <p><span class="font-medium">NIK:</span> ${data.pengaju.nik}</p>
                                </div>
                                
                                <div class="border rounded-lg p-4">
                                    <h4 class="font-semibold mb-3">Karyawan Penerima</h4>
                                    <p><span class="font-medium">Nama:</span> ${data.penerima.nama_lengkap}</p>
                                    <p><span class="font-medium">NIK:</span> ${data.penerima.nik}</p>
                                    
                                </div>
                            </div>
                            
                            <div class="border rounded-lg p-4 mt-4">
                                <h4 class="font-semibold mb-3">Informasi Pertukaran</h4>
                                <p><span class="font-medium">Tanggal Pengajuan:</span> ${data.waktu_pengajuan}</p>
                                <p><span class="font-medium">Alasan:</span> ${data.alasan}</p>
                            </div>
                            
                            <div class="border rounded-lg p-4 mt-4">
                                <h4 class="font-semibold mb-3">Detail Jadwal yang Dipertukarkan</h4>
                                <div class="overflow-x-auto">
                                    <table class="table w-full">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Hari</th>
                                                <th>Jadwal Pengaju</th>
                                                <th>Jadwal Penerima</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;

                        if (data.jadwal.length > 0) {
                            data.jadwal.forEach(jadwal => {
                                html += `
                                    <tr>
                                        <td>${jadwal.tanggal}</td>
                                        <td>${jadwal.hari}</td>
                                        <td>${jadwal.jadwal_pengaju}</td>
                                        <td>${jadwal.jadwal_penerima}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            html += `
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data jadwal yang tersedia</td>
                                </tr>
                            `;
                        }

                        html += `</tbody>
                                    </table>
                                </div>
                            </div>
                        `;

                        document.getElementById('modalContent').innerHTML = html;
                    } else {
                        document.getElementById('modalContent').innerHTML = `
                            <div class="alert alert-error">
                                <i class="ri-error-warning-line mr-2"></i>
                                Gagal memuat detail pertukaran jadwal
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    document.getElementById('modalContent').innerHTML = `
                        <div class="alert alert-error">
                            <i class="ri-error-warning-line mr-2"></i>
                            Terjadi kesalahan saat memuat data: ${error}
                        </div>
                    `;
                });
        }

        // Fungsi untuk menutup modal detail
        function closeDetailModal() {
            document.getElementById('detailModal').classList.remove('modal-open');
        }

        // Jika ada parameter highlight, scroll ke row yang di-highlight
        document.addEventListener('DOMContentLoaded', function() {
            @if (request()->has('highlight'))
                const highlightedRow = document.querySelector('.bg-yellow-100');
                if (highlightedRow) {
                    highlightedRow.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    // Tambahkan efek highlight yang menghilang setelah beberapa detik
                    setTimeout(() => {
                        highlightedRow.style.transition = 'background-color 1s ease';
                        highlightedRow.style.backgroundColor = '';
                    }, 3000);
                }
            @endif
        });
    </script>
</x-app-layout>
