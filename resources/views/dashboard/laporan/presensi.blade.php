@extends('dashboard.layouts.main')
@section('container')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Laporan Presensi') }}
            </h2>
        </div>
    </x-slot>

    <div class="container mx-auto px-5 pt-5">
        <div class="w-full rounded-md bg-slate-200 px-10 mb-10">
            <h1 class="py-3 font-bold text-xl">Laporan Presensi Saya</h1>
            <form action="{{ route('karyawan.laporan.presensi.karyawan') }}" method="post" target="_blank"
                enctype="multipart/form-data" class="pb-3">
                @csrf
                <div class="flex w-full flex-wrap gap-2 lg:flex-nowrap">
                    <input type="month" name="bulan" class="input input-bordered w-full"
                        value="{{ \Carbon\Carbon::now()->format('Y-m') }}" required />
                    <button type="submit" class="btn btn-error w-full lg:w-14">
                        <i class="ri-file-pdf-2-fill text-lg text-white"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- <div class="w-full rounded-md bg-slate-200 px-10">
        <h1 class="py-3 font-bold text-xl">Laporan Presensi Semua Karyawan</h1>
        <form action="{{ route('admin.laporan.presensi.semua-karyawan') }}" method="post" target="_blank"
            enctype="multipart/form-data" class="pb-3">
            @csrf
            <div class="flex w-full flex-wrap gap-2 lg:flex-nowrap">
                <input type="month" name="bulan" class="input input-bordered w-full"
                    value="{{ Carbon\Carbon::now()->format('Y-m') }}" required />
                <button type="submit" class="btn btn-error w-full lg:w-14">
                    <i class="ri-file-pdf-2-fill text-lg text-white"></i>
                </button>
            </div>
        </form>
    </div> --}}
    </div>
@endsection
