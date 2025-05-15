<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">
                {{ __('Jadwal Excel') }}
            </h2>
            <label for="import_modal" class="btn btn-primary btn-sm">
                <i class="ri-upload-cloud-2-line mr-1"></i> Import Excel
            </label>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6 space-y-6">
        <a href="{{ route('jadwal-shift.index') }}" class="btn btn-accent btn-sm">
            Kembali
        </a>

        {{-- Fallback jika $files belum ada --}}
        @php
            if (!isset($files)) {
                $files = \App\Models\ExcelFile::latest()->paginate(10);
            }
        @endphp

        {{-- Modal Import --}}
        <input type="checkbox" id="import_modal" class="modal-toggle" />
        <div class="modal">
            <div class="modal-box relative">
                <label for="import_modal" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
                <h3 class="text-lg font-semibold mb-4">Upload File Excel</h3>

                <form action="{{ route('jadwalkerja.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-control mb-4">
                        <input type="file" name="file" accept=".xlsx,.xls"
                            class="file-input file-input-bordered w-full" required>
                        @error('file')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="modal-action">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-file-add-line mr-1"></i> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Daftar File --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold">Uploaded Files</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>#</th>
                            <th>Nama File</th>
                            <th>Tanggal Upload
                            </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($files as $idx => $file)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 whitespace-nowrap">{{ $files->firstItem() + $idx }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $file->original_name }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $file->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-center space-x-2">
                                    {{-- Download --}}
                                    <a href="{{ route('jadwalkerja.download', $file->id) }}"
                                        class="btn btn-sm btn-outline btn-info" title="Download">
                                        <i class="ri-download-2-line"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('jadwalkerja.destroy', $file->id) }}" method="POST"
                                        class="inline-block delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline btn-error" title="Hapus">
                                            <i class="ri-delete-bin-6-line"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                    Belum ada file di-upload.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 bg-gray-50">
                {{ $files->links() }}
            </div>
        </div>
    </div>

    {{-- Notifikasi SweetAlert2 --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ $errors->first() }}'
            });
        </script>
    @endif

    <script>
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "File ini akan hilang selamanya!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#000000',
                    cancelButtonColor: '#000000',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    <style>
        .swal2-confirm {
    background-color: #007bff !important; /* Warna biru untuk tombol OK */
    color: white !important; /* Teks tombol OK menjadi putih */
}
.swal2-cancel {
    background-color: #007bff !important; /* Warna biru untuk tombol OK */
    color: white !important; /* Teks tombol OK menjadi putih */
    border-color: #007bff !important; /* Border tombol OK menjadi biru */
}
</style>
    {{-- Pastikan SweetAlert2, DaisyUI & Alpine.js di-include di layout utama --}}
</x-app-layout>
