@extends('dashboard.layouts.main')
{{-- resources/views/karyawan/form-lembur/index.blade.php --}}
@section('js')
    <script>
        // Fungsi untuk menghitung overtime
        function hitungOvertime() {
            const jamMulai = document.getElementById('jam_mulai').value;
            const jamSelesai = document.getElementById('jam_selesai').value;

            if (jamMulai && jamSelesai) {
                const [mulaiJam, mulaiMenit] = jamMulai.split(':').map(Number);
                const [selesaiJam, selesaiMenit] = jamSelesai.split(':').map(Number);

                let jamMulaiDate = new Date();
                jamMulaiDate.setHours(mulaiJam, mulaiMenit, 0);

                let jamSelesaiDate = new Date();
                jamSelesaiDate.setHours(selesaiJam, selesaiMenit, 0);

                if (jamSelesaiDate < jamMulaiDate) {
                    jamSelesaiDate.setDate(jamSelesaiDate.getDate() + 1);
                }

                const selisihMilidetik = jamSelesaiDate - jamMulaiDate;
                const selisihJam = selisihMilidetik / (1000 * 60 * 60);

                document.getElementById('overtime').value = selisihJam.toFixed(2);
            }
        }



        @if (session()->has('success'))
            Swal.fire({
                title: 'Berhasil',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#6419E6',
                confirmButtonText: 'OK',
            });
        @endif

        @if (session()->has('error'))
            Swal.fire({
                title: 'Gagal',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#6419E6',
                confirmButtonText: 'OK',
            });
        @endif
    </script>
@endsection

@section('container')
    <div>
        <div class="-mx-3 mt-6 flex flex-wrap">
            <div class="mb-6 mt-0 w-full max-w-full px-3">
                <div
                    class="dark:bg-slate-850 dark:shadow-dark-xl border-black-125 relative flex min-w-0 flex-col break-words rounded-2xl border-0 border-solid bg-white bg-clip-border shadow-xl">
                    <div class="rounded-t-4 mb-0 p-4 pb-0">
                        <div class="flex justify-between items-center">
                            <h6 class="mb-2 font-bold dark:text-white">Form Lembur</h6>
                            <label class="btn btn-primary btn-sm" for="create_modal">
                                <i class="ri-add-fill"></i>
                                Ajukan Lembur
                            </label>
                        </div>
                    </div>

                    <div class="mt-3 flex w-full flex-col gap-y-5">
                        {{-- Input Filter --}}
                        <div class="flex flex-wrap">
                            <div class="md:flex-0 w-full max-w-full shrink-0 px-3 md:w-1/2">
                                <div class="mb-4">
                                    <label for="bulan"
                                        class="mb-2 ml-1 inline-block text-xs font-bold text-slate-700 dark:text-white/80">Bulan</label>
                                    <select name="bulan" id="bulan"
                                        class="focus:shadow-primary-outline dark:bg-slate-850 leading-5.6 ease select select-bordered block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 text-sm font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none dark:text-white"
                                        required>
                                        <option disabled selected>Pilih Bulan!</option>
                                        @foreach ($bulan as $value => $item)
                                            <option value="{{ $value + 1 }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="md:flex-0 w-full max-w-full shrink-0 px-3 md:w-1/2">
                                <div class="mb-4">
                                    <label for="tahun"
                                        class="mb-2 ml-1 inline-block text-xs font-bold text-slate-700 dark:text-white/80">Tahun</label>
                                    <select name="tahun" id="tahun"
                                        class="focus:shadow-primary-outline dark:bg-slate-850 leading-5.6 ease select select-bordered block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 text-sm font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none dark:text-white"
                                        required>
                                        <option disabled selected>Pilih Tahun!</option>
                                        @php
                                            $tahunMulai =
                                                $formLembur->count() > 0
                                                    ? date('Y', strtotime($formLembur[0]->tanggal))
                                                    : date('Y');
                                        @endphp
                                        @for ($tahun = $tahunMulai; $tahun <= date('Y'); $tahun++)
                                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-5 flex w-full max-w-full justify-center px-3">
                            <button type="button" id="searchButton" class="btn btn-warning btn-block">Search</button>
                        </div>

                        {{-- Tabel Riwayat Form Lembur --}}
                        <div id="searchLembur" class="w-full overflow-x-auto px-10">
                            <table id="tabelLembur"
                                class="table mb-4 w-full border-collapse items-center border-gray-200 align-top dark:border-white/40">
                                <thead class="text-sm text-gray-800 dark:text-gray-300">
                                    <tr>
                                        <th></th>
                                        <th>Hari</th>
                                        <th>Tanggal</th>
                                        <th>Jam Mulai</th>
                                        <th>Jam Selesai</th>
                                        <th>Overtime</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($formLembur as $value => $item)
                                        <tr class="hover">
                                            <td class="font-bold">{{ $value + 1 }}</td>
                                            <td class="text-slate-500 dark:text-slate-300">
                                                {{ date('l', strtotime($item->tanggal)) }}</td>
                                            <td class="text-slate-500 dark:text-slate-300">
                                                {{ date('d-m-Y', strtotime($item->tanggal)) }}</td>
                                            <td class="text-slate-500 dark:text-slate-300">
                                                {{ $item->jam_mulai }}</td>
                                            <td class="text-slate-500 dark:text-slate-300">
                                                {{ $item->jam_selesai }}</td>
                                            <td class="text-slate-500 dark:text-slate-300">
                                                {{ $item->overtime }} Jam</td>
                                            <td class="text-slate-500 dark:text-slate-300">
                                                @if ($item->status == 'pending')
                                                    <div class="badge badge-neutral dark:bg-slate-300 dark:text-slate-700">
                                                        Pending</div>
                                                @elseif ($item->status == 'approved')
                                                    <div class="badge badge-success">Disetujui</div>
                                                @else
                                                    <div class="badge badge-error">Ditolak</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mx-3 mb-5">
                                {{ $formLembur->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajukan Lembur -->
    <input type="checkbox" id="create_modal" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box relative">
            <label for="create_modal"
                class="absolute top-0 right-0 mt-2 mr-2 cursor-pointer text-lg btn btn-secondary text-white">
                Batal
            </label>

            <h3 class="text-lg font-bold">Ajukan Lembur</h3>

            <form action="{{ route('karyawan.form-lembur.store') }}" method="POST">
                @csrf

                <label class="form-control w-full mt-2">
                    <span class="label-text">Tanggal</span>
                    <input type="date" name="tanggal" class="input input-bordered w-full" required />
                </label>

                <div class="grid grid-cols-2 gap-2 mt-2">
                    <label class="form-control w-full">
                        <span class="label-text">Jam Mulai</span>
                        <input type="time" name="jam_mulai" id="jam_mulai" class="input input-bordered w-full" required
                            onchange="hitungOvertime()" />
                    </label>

                    <label class="form-control w-full">
                        <span class="label-text">Jam Selesai</span>
                        <input type="time" name="jam_selesai" id="jam_selesai" class="input input-bordered w-full"
                            required onchange="hitungOvertime()" />
                    </label>
                </div>

                <label class="form-control w-full mt-2">
                    <span class="label-text">Overtime (Jam)</span>
                    <input type="number" name="overtime" id="overtime" class="input input-bordered w-full" step="0.01"
                        readonly required />
                </label>

                <button type="submit" class="btn btn-success mt-3 w-full">Ajukan</button>
            </form>
        </div>
    </div>

    <style>
        .swal2-confirm {
            background-color: #007bff !important;
            /* Warna biru untuk tombol OK */
            color: white !important;
            /* Teks tombol OK menjadi putih */
            border-color: #007bff !important;
            /* Border tombol OK menjadi biru */
        }
    </style>
@endsection
