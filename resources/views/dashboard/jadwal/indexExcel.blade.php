@extends('dashboard.layouts.main')

@section('container')
    <div class="container mx-auto px-4 py-6 space-y-6">
        <a href="{{ route('karyawan.jadwalkerja.index') }}" class="btn btn-accent btn-sm">
            Kembali
        </a>

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
                            <th>Tanggal Upload</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($files as $idx => $file)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 whitespace-nowrap">{{ $files->firstItem() + $idx }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $file->original_name }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $file->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-center space-x-2">
                                    <a href="{{ route('karyawan.jadwalkerja.download', $file->id) }}"
                                        class="btn btn-sm btn-outline btn-info" title="Download">
                                        <i class="ri-download-2-line"></i> Download
                                    </a>
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
@endsection
