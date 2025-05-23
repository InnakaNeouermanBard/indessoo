@extends('dashboard.layouts.main')
{{-- presensi/izin/create.blade.php  --}}
@section('js')
    <script>
        $(document).ready(function() {
            // Sembunyikan info sisa kuota cuti awalnya
            $("#info-sisa-kuota").hide();
            $("#peringatan-kuota").hide();

            // Ketika pilihan status berubah
            $("select[name='status']").change(function() {
                // Jika pilihan adalah cuti (C)
                if ($(this).val() == 'C') {
                    $("#info-sisa-kuota").show();

                    // Jika kuota cuti habis, tampilkan peringatan
                    if (parseInt($("#sisa-kuota").text()) <= 0) {
                        $("#peringatan-kuota").show();
                        $("button[type='submit']").prop('disabled', true);
                    } else {
                        $("#peringatan-kuota").hide();
                        $("button[type='submit']").prop('disabled', false);
                    }
                } else {
                    $("#info-sisa-kuota").hide();
                    $("#peringatan-kuota").hide();
                    $("button[type='submit']").prop('disabled', false);
                }
            });
        });
    </script>
@endsection

@section('container')
    <div class="-mx-3 flex flex-wrap">
        <div class="w-full max-w-full flex-none px-3">
            {{-- Awal Form Tambah --}}
            <div
                class="dark:bg-slate-850 dark:shadow-dark-xl relative mb-6 flex min-w-0 flex-col break-words rounded-2xl border-0 border-solid border-transparent bg-white bg-clip-border shadow-xl">
                <div
                    class="border-b-solid mb-0 flex items-center justify-between rounded-t-2xl border-b-0 border-b-transparent p-6 pb-3">
                    <div class="mb-3">
                        <h6 class="font-bold dark:text-white">{{ $title }}</h6>
                    </div>
                    <div>
                        <a href="{{ route('karyawan.izin') }}"
                            class="bg-150 active:opacity-85 tracking-tight-rem bg-x-25 mb-0 inline-block cursor-pointer rounded-lg border border-solid border-slate-500 dark:border-white bg-transparent px-4 py-1 text-center align-middle text-sm font-bold leading-normal text-slate-500 dark:text-white shadow-none transition-all ease-in hover:-translate-y-px hover:opacity-75 md:px-8 md:py-2">
                            <i class="ri-arrow-left-line"></i>
                            Kembali
                        </a>
                    </div>
                </div>
                <div class="flex-auto px-6 pb-6 pt-0">
                    <form action="{{ route('karyawan.izin.store') }}" method="POST" enctype="multipart/form-data"
                        id="pengajuanPresensiStore" class="mx-auto w-full lg:w-3/4 xl:w-1/2">
                        @csrf
                        <label class="form-control w-full">
                            <div class="label">
                                <span class="label-text font-semibold dark:text-slate-100">
                                    <p>Jenis Pengajuan <span class="text-red-500">*</span></p>
                                </span>
                            </div>
                            <select name="status"
                                class="select select-bordered text-base text-blue-700 dark:bg-slate-100 w-full" required>
                                <option disabled selected>Izin / Sakit / Cuti</option>
                                @foreach ($statusPengajuan as $item)
                                    <option value="{{ $item->value }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('jenis_pembayaran')
                                <div class="label">
                                    <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                                </div>
                            @enderror
                        </label>

                        {{-- Info Sisa Kuota Cuti --}}
                        <div id="info-sisa-kuota" class="my-3 p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <i class="ri-information-line text-blue-500"></i>
                                <span class="text-blue-700">Sisa kuota cuti Anda: <strong
                                        id="sisa-kuota">{{ $sisaKuota }}</strong> hari</span>
                            </div>
                        </div>

                        {{-- Peringatan jika kuota habis --}}
                        <div id="peringatan-kuota" class="my-3 p-3 bg-red-50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <i class="ri-alert-line text-red-500"></i>
                                <span class="text-red-700">Kuota cuti Anda tahun ini sudah habis!</span>
                            </div>
                        </div>

                        <label class="form-control w-full">
                            <div class="label">
                                <span class="label-text font-semibold dark:text-slate-100">
                                    <p>Tanggal Mulai <span class="text-red-500">*</span></p>
                                </span>
                            </div>
                            <input type="date" name="tanggal_mulai"
                                class="input input-bordered w-full text-blue-700 dark:bg-slate-100" required />
                        </label>

                        <label class="form-control w-full">
                            <div class="label">
                                <span class="label-text font-semibold dark:text-slate-100">
                                    <p>Tanggal Selesai <span class="text-red-500">*</span></p>
                                </span>
                            </div>
                            <input type="date" name="tanggal_selesai"
                                class="input input-bordered w-full text-blue-700 dark:bg-slate-100" required />
                        </label>

                        <div class="my-5 flex flex-wrap justify-center gap-2">
                            <button type="submit" class="btn btn-success w-full text-white">Simpan Pengajuan</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- Akhir Form Tambah --}}
        </div>
    </div>
@endsection
