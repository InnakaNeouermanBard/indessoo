{{-- form-lembur index --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Form Lembur') }}
            </h2>
            <label class="btn btn-primary btn-sm" for="create_modal">Tambah</label>
        </div>
    </x-slot>

    <div class="container mx-auto px-5 pt-5">
        <form action="{{ route('form-lembur.index') }}" method="get" class="my-3">
            <div class="flex w-full flex-wrap gap-2 md:flex-nowrap">
                <input type="text" name="cari_nik" placeholder="Cari NIK" class="input input-bordered w-full"
                    value="{{ request()->cari_nik }}" />
                <button type="submit" class="btn btn-success w-full md:w-14">
                    <i class="ri-search-2-line text-lg text-white"></i>
                </button>
            </div>
        </form>

        <div class="w-full overflow-x-auto rounded-md bg-slate-200 px-10">
            <table class="table mb-4 w-full text-dark">
                <thead class="text-black">
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Tanggal</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($karyawan as $value => $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nik }}</td>
                            <td>{{ $item->nama_karyawan }}</td>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->jam_mulai }}</td>
                            <td>{{ $item->jam_selesai }}</td>
                            <td>
                                @if ($item->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($item->status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @else
                                    <span class="badge badge-error">Rejected</span>
                                @endif
                            </td>
                            <td>
                                <label class="btn btn-info btn-sm" for="detail_button"
                                    onclick="return detail_button('{{ $item->id }}')">
                                    <i class="ri-eye-fill"></i>
                                </label>
                                <label class="btn btn-warning btn-sm" for="edit_button"
                                    onclick="return edit_button('{{ $item->id }}')">
                                    <i class="ri-pencil-fill"></i>
                                </label>
                                <label class="btn btn-error btn-sm"
                                    onclick="return delete_button('{{ $item->id }}', '{{ $item->nama_karyawan }}')">
                                    <i class="ri-delete-bin-line"></i>
                                </label>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Lembur -->
    <input type="checkbox" id="create_modal" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box relative">
            <!-- Tombol Batal di pojok kanan -->
            <label for="create_modal"
                class="absolute top-0 right-0 mt-2 mr-2 cursor-pointer text-lg btn btn-secondary text-white">
                Batal
            </label>

            <h3 class="text-lg font-bold">Tambah Lembur</h3>

            <form action="{{ route('form-lembur.store') }}" method="POST">
                @csrf

                <label class="form-control w-full">
                    <span class="label-text">Pilih Karyawan</span>
                    <select name="nik" class="input input-bordered w-full" required>
                        <option value="">Pilih Karyawan</option>
                        @foreach ($niks as $karyawan)
                            <option value="{{ $karyawan->nik }}">
                                {{ $karyawan->nama_lengkap }} ({{ $karyawan->nik }})
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="form-control w-full mt-2">
                    <span class="label-text">Tanggal</span>
                    <input type="date" name="tanggal" class="input input-bordered w-full" required />
                </label>

                <div class="grid grid-cols-2 gap-2 mt-2">
                    <label class="form-control w-full">
                        <span class="label-text">Jam Mulai</span>
                        <input type="time" name="jam_mulai" id="jam_mulai" class="input input-bordered w-full"
                            required onchange="hitungOvertime()" />
                    </label>

                    <label class="form-control w-full">
                        <span class="label-text">Jam Selesai</span>
                        <input type="time" name="jam_selesai" id="jam_selesai" class="input input-bordered w-full"
                            required onchange="hitungOvertime()" />
                    </label>
                </div>

                <label class="form-control w-full mt-2">
                    <span class="label-text">Overtime (Jam)</span>
                    <input type="number" name="overtime" id="overtime" class="input input-bordered w-full"
                        step="0.01" readonly required />
                </label>

                <button type="submit" class="btn btn-success mt-3 w-full">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Modal Detail -->
    <input type="checkbox" id="detail_button" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box relative">
            <label for="detail_button"
                class="absolute top-0 right-0 mt-2 mr-2 cursor-pointer text-lg btn btn-secondary text-white">
                Tutup
            </label>
            <h3 class="text-lg font-bold mb-4">Detail Lembur</h3>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <tbody>
                        <tr>
                            <td class="font-bold">NIK</td>
                            <td id="detail_nik"></td>
                        </tr>
                        <tr>
                            <td class="font-bold">Nama Karyawan</td>
                            <td id="detail_nama_karyawan"></td>
                        </tr>
                        <tr>
                            <td class="font-bold">Tanggal</td>
                            <td id="detail_tanggal"></td>
                        </tr>
                        <tr>
                            <td class="font-bold">Jam Mulai</td>
                            <td id="detail_jam_mulai"></td>
                        </tr>
                        <tr>
                            <td class="font-bold">Jam Selesai</td>
                            <td id="detail_jam_selesai"></td>
                        </tr>
                        <tr>
                            <td class="font-bold">Overtime (Jam)</td>
                            <td id="detail_overtime"></td>
                        </tr>
                        <tr>
                            <td class="font-bold">Status</td>
                            <td id="detail_status"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <input type="checkbox" id="edit_button" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box relative">
            <label for="edit_button"
                class="absolute top-0 right-0 mt-2 mr-2 cursor-pointer text-lg btn btn-secondary text-white">
                Batal
            </label>
            <h3 class="text-lg font-bold mb-2">Ubah Lembur</h3>

            <form id="form_edit" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">

                <label class="form-control w-full">
                    <span class="label-text">NIK</span>
                    <input type="text" name="nik" id="edit_nik" class="input input-bordered w-full" readonly
                        required />
                </label>

                <label class="form-control w-full mt-2">
                    <span class="label-text">Nama Karyawan</span>
                    <input type="text" name="nama_karyawan" id="edit_nama_karyawan"
                        class="input input-bordered w-full" readonly required />
                </label>

                <label class="form-control w-full mt-2">
                    <span class="label-text">Tanggal</span>
                    <input type="date" name="tanggal" id="edit_tanggal" class="input input-bordered w-full"
                        required />
                </label>

                <div class="grid grid-cols-2 gap-2 mt-2">
                    <label class="form-control w-full">
                        <span class="label-text">Jam Mulai</span>
                        <input type="time" name="jam_mulai" id="edit_jam_mulai"
                            class="input input-bordered w-full" required onchange="hitungEditOvertime()" />
                    </label>

                    <label class="form-control w-full">
                        <span class="label-text">Jam Selesai</span>
                        <input type="time" name="jam_selesai" id="edit_jam_selesai"
                            class="input input-bordered w-full" required onchange="hitungEditOvertime()" />
                    </label>
                </div>

                <label class="form-control w-full mt-2">
                    <span class="label-text">Overtime (Jam)</span>
                    <input type="number" name="overtime" id="edit_overtime" class="input input-bordered w-full"
                        step="0.01" readonly required />
                </label>

                <label class="form-control w-full mt-2">
                    <span class="label-text">Status</span>
                    <select name="status" id="edit_status" class="input input-bordered w-full" required>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </label>

                <button type="submit" class="btn btn-warning mt-4 w-full">Perbarui</button>
            </form>
        </div>
    </div>

    <script>
        // Fungsi untuk menghitung overtime (form tambah)
        function hitungOvertime() {
            const jamMulai = document.getElementById('jam_mulai').value;
            const jamSelesai = document.getElementById('jam_selesai').value;

            if (jamMulai && jamSelesai) {
                // Convert jam ke Date objects untuk perhitungan
                const [mulaiJam, mulaiMenit] = jamMulai.split(':').map(Number);
                const [selesaiJam, selesaiMenit] = jamSelesai.split(':').map(Number);

                let jamMulaiDate = new Date();
                jamMulaiDate.setHours(mulaiJam, mulaiMenit, 0);

                let jamSelesaiDate = new Date();
                jamSelesaiDate.setHours(selesaiJam, selesaiMenit, 0);

                // Jika jam selesai lebih kecil dari jam mulai, berarti melewati tengah malam
                if (jamSelesaiDate < jamMulaiDate) {
                    jamSelesaiDate.setDate(jamSelesaiDate.getDate() + 1);
                }

                // Hitung selisih dalam milidetik dan konversi ke jam
                const selisihMilidetik = jamSelesaiDate - jamMulaiDate;
                const selisihJam = selisihMilidetik / (1000 * 60 * 60);

                // Set nilai overtime dengan 2 angka desimal
                document.getElementById('overtime').value = selisihJam.toFixed(2);
            }
        }

        // Fungsi untuk menghitung overtime (form edit)
        function hitungEditOvertime() {
            const jamMulai = document.getElementById('edit_jam_mulai').value;
            const jamSelesai = document.getElementById('edit_jam_selesai').value;

            if (jamMulai && jamSelesai) {
                // Convert jam ke Date objects untuk perhitungan
                const [mulaiJam, mulaiMenit] = jamMulai.split(':').map(Number);
                const [selesaiJam, selesaiMenit] = jamSelesai.split(':').map(Number);

                let jamMulaiDate = new Date();
                jamMulaiDate.setHours(mulaiJam, mulaiMenit, 0);

                let jamSelesaiDate = new Date();
                jamSelesaiDate.setHours(selesaiJam, selesaiMenit, 0);

                // Jika jam selesai lebih kecil dari jam mulai, berarti melewati tengah malam
                if (jamSelesaiDate < jamMulaiDate) {
                    jamSelesaiDate.setDate(jamSelesaiDate.getDate() + 1);
                }

                // Hitung selisih dalam milidetik dan konversi ke jam
                const selisihMilidetik = jamSelesaiDate - jamMulaiDate;
                const selisihJam = selisihMilidetik / (1000 * 60 * 60);

                // Set nilai overtime dengan 2 angka desimal
                document.getElementById('edit_overtime').value = selisihJam.toFixed(2);
            }
        }

        function detail_button(id) {
            $.ajax({
                type: "GET",
                url: "{{ url('admin/form-lembur') }}/" + id,
                success: function(response) {
                    // Set data ke modal detail
                    $('#detail_nik').text(response.nik);
                    $('#detail_nama_karyawan').text(response.nama_karyawan);
                    $('#detail_tanggal').text(response.tanggal);
                    $('#detail_jam_mulai').text(response.jam_mulai);
                    $('#detail_jam_selesai').text(response.jam_selesai);
                    $('#detail_overtime').text(response.overtime);

                    // Set status dengan format yang sesuai
                    let statusText = '';
                    if (response.status === 'pending') {
                        statusText = '<span class="badge badge-warning">Pending</span>';
                    } else if (response.status === 'approved') {
                        statusText = '<span class="badge badge-success">Approved</span>';
                    } else {
                        statusText = '<span class="badge badge-error">Rejected</span>';
                    }
                    $('#detail_status').html(statusText);

                    // Tampilkan modal
                    document.getElementById('detail_button').checked = true;
                },
                error: function(xhr) {
                    alert('Gagal mengambil data detail lembur.');
                }
            });

            return false;
        }

        function edit_button(id) {
            $.ajax({
                type: "GET",
                url: "{{ url('admin/form-lembur') }}/" + id + "/edit",
                success: function(response) {
                    // Set data ke form input
                    $('#edit_id').val(response.id);
                    $('#edit_nik').val(response.nik);
                    $('#edit_nama_karyawan').val(response.nama_karyawan);
                    $('#edit_tanggal').val(response.tanggal);
                    $('#edit_jam_mulai').val(response.jam_mulai);
                    $('#edit_jam_selesai').val(response.jam_selesai);
                    $('#edit_overtime').val(response.overtime);
                    $('#edit_status').val(response.status);

                    // Set action form update
                    $('#form_edit').attr('action', '/admin/form-lembur/' + response.id);

                    // Tampilkan modal
                    document.getElementById('edit_button').checked = true;
                },
                error: function(xhr) {
                    alert('Gagal mengambil data lembur.');
                }
            });

            return false;
        }

        function delete_button(id, name) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data lembur " + name + " akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ route('form-lembur.delete') }}", {
                        _token: "{{ csrf_token() }}",
                        id: id
                    }, function(response) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        location.reload();
                    });
                }
            });
        }
    </script>
</x-app-layout>
