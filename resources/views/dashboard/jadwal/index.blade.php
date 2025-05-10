@extends('dashboard.layouts.main')

@section('js')
@endsection

@section('container')
    <div class="container mx-auto px-4 py-6 space-y-6">
        
        {{-- Filter berdasarkan bulan dan tahun --}}
        <form action="{{ route('karyawan.jadwalkerja.index') }}" method="get" class="mb-4 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium">Bulan</label>
                    <select name="bulan" class="select select-bordered w-full mt-1">
                        @foreach ($bulanList as $key => $nama)
                            <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Tahun</label>
                    <select name="tahun" class="select select-bordered w-full mt-1">
                        @foreach ($tahunList as $key => $nama)
                            <option value="{{ $key }}" {{ $tahun == $key ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
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
            
        <!-- Tampilan Jadwal Karyawan -->
        <div id="jadwalTableContainer" class="bg-white rounded shadow p-4 mb-6">
            <h3 class="text-lg font-semibold mb-4 text-center">
                Jadwal Bulan {{ $bulanList[$bulan] }} {{ $tahun }}
            </h3>

            @if (isset($jadwal) && count($jadwal) > 0)
                <div class="overflow-x-auto">
                    <table class="table w-full table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Shift</th>
                                <th>Libur?</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jadwal as $i => $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
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
                                            {{ $item->shift->nama }} ({{ $item->shift->waktu_mulai }} - {{ $item->shift->waktu_selesai }})
                                        @else
                                            - 
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->is_libur ? 'badge-error' : 'badge-success' }}">
                                            {{ $item->is_libur ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    Tidak ada data jadwal untuk periode ini
                </div>
            @endif
        </div>
    </div>
@endsection
