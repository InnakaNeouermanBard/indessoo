<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Manajemen Kuota Cuti Karyawan') }}
            </h2>
            <a href="{{ route('admin.administrasi-presensi') }}" class="btn btn-primary flex items-center gap-2">
                <i class="ri-arrow-left-line"></i>
                <span>Kembali ke Perizinan</span>
            </a>
        </div>
    </x-slot>

    <div class="container mx-auto px-5 pt-5">
        <div class="w-full overflow-x-auto rounded-md bg-white shadow-lg px-10 py-6">
            <table class="table mb-4 w-full border-collapse items-center border-gray-200 align-top">
                <thead class="text-sm text-black">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">NIK</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Departemen</th>
                        <th class="px-4 py-3">Kuota Cuti</th>
                        <th class="px-4 py-3">Cuti Terpakai</th>
                        <th class="px-4 py-3">Sisa Kuota</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($karyawan as $index => $item)
                        @php
                            // Hitung jumlah cuti yang sudah dipakai tahun ini
                            $cutiTerpakai = DB::table('pengajuan_presensi')
                                ->where('nik', $item->nik)
                                ->where('status', 'C')
                                ->where('status_approved', 2)
                                ->whereYear('tanggal_pengajuan', date('Y'))
                                ->count();

                            $sisaKuota = $item->kuota_cuti - $cutiTerpakai;
                        @endphp
                        <tr class="hover">
                            <td class="px-4 py-3 font-bold">{{ $karyawan->firstItem() + $index }}</td>
                            <td class="px-4 py-3">{{ $item->nik }}</td>
                            <td class="px-4 py-3">{{ $item->nama_lengkap }}</td>
                            <td class="px-4 py-3">{{ $item->departemen->nama }}</td>
                            <td class="px-4 py-3">{{ $item->kuota_cuti }} hari</td>
                            <td class="px-4 py-3">{{ $cutiTerpakai }} hari</td>
                            <td class="px-4 py-3">
                                <span
                                    class="{{ $sisaKuota <= 0 ? 'text-red-500 font-bold' : 'text-green-500 font-bold' }}">
                                    {{ $sisaKuota }} hari
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button class="edit-kuota-btn btn btn-info btn-sm" data-nik="{{ $item->nik }}"
                                    data-nama="{{ $item->nama_lengkap }}" data-kuota="{{ $item->kuota_cuti }}">
                                    Edit Kuota
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $karyawan->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Edit Kuota -->
    <div id="editKuotaModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Edit Kuota Cuti Karyawan</h3>
            <form action="" id="formEditKuota" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-nik" name="nik">

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nama Karyawan</label>
                    <div class="p-2 bg-gray-100 rounded" id="edit-nama"></div>
                </div>

                <div class="mb-4">
                    <label for="edit-kuota" class="block text-sm font-medium mb-2">Kuota Cuti (Hari)</label>
                    <input type="number" id="edit-kuota" name="kuota_cuti" class="input input-bordered w-full"
                        min="0" required>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" class="btn close-modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
        <label class="modal-backdrop close-modal" for="editKuotaModal"></label>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Edit kuota modal
                $(".edit-kuota-btn").click(function() {
                    const nik = $(this).data('nik');
                    const nama = $(this).data('nama');
                    const kuota = $(this).data('kuota');

                    $("#edit-nik").val(nik);
                    $("#edit-nama").text(nama);
                    $("#edit-kuota").val(kuota);

                    // Update action URL form
                    $("#formEditKuota").attr('action', `/admin/kuota-cuti/${nik}`);

                    $("#editKuotaModal").addClass("modal-open");
                });

                // Tutup modal
                $(".close-modal").click(function() {
                    $("#editKuotaModal").removeClass("modal-open");
                });
            });
        </script>
    @endpush
</x-app-layout>
