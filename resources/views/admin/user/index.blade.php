{{-- Admin CRUD View --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Data Admin') }}
            </h2>
            <label class="btn btn-primary btn-sm" for="create_modal">Tambah Admin</label>
        </div>
    </x-slot>

    <div class="container mx-auto px-5 pt-5">
        <div>
            <form action="{{ route('admin-management') }}" method="get" enctype="multipart/form-data" class="my-3">
                <div class="flex w-full flex-wrap gap-2 md:flex-nowrap">
                    <input type="text" name="cari_admin" placeholder="Pencarian" class="input input-bordered w-full"
                        value="{{ request()->cari_admin }}" />
                    <button type="submit" class="btn btn-success w-full md:w-14">
                        <i class="ri-search-2-line text-lg text-white"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="w-full overflow-x-auto rounded-md bg-slate-200 px-10">
            <table id="tabelAdmin"
                class="table mb-4 w-full border-collapse items-center border-gray-200 align-top dark:border-white/40">
                <thead class="text-sm text-black">
                    <tr>
                        <th></th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($admins as $value => $admin)
                        <tr class="hover">
                            <td>{{ $loop->iteration }}</td>
                            {{-- <td class="font-bold">{{ $admins->firstItem() + $value }}</td> --}}
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>
                                <label class="btn btn-warning btn-sm" for="edit_button"
                                    onclick="return edit_button('{{ $admin->id }}')">
                                    <i class="ri-pencil-fill"></i>
                                </label>
                                <label class="btn btn-error btn-sm"
                                    onclick="return delete_button('{{ $admin->id }}', '{{ $admin->name }}')">
                                    <i class="ri-delete-bin-line"></i>
                                </label>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mx-3 mb-5">
                {{-- {{ $admins->links() }} --}}
            </div>
        </div>
    </div>

    {{-- Awal Modal Create --}}
    <input type="checkbox" id="create_modal" class="modal-toggle" />
    <div class="modal" role="dialog">
        <div class="modal-box">
            <div class="mb-3 flex justify-between">
                <h3 class="text-lg font-bold">Tambah {{ $title }}</h3>
                <label for="create_modal" class="cursor-pointer">
                    <i class="ri-close-large-fill"></i>
                </label>
            </div>
            <div>
                <form action="{{ route('admin-management.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <button type="reset" class="btn btn-neutral btn-sm">Reset</button>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Nama<span class="text-red-500">*</span></span>
                            </span>
                        </div>
                        <input type="text" name="name" placeholder="Nama"
                            class="input input-bordered w-full text-blue-700" value="{{ old('name') }}" required />
                        @error('name')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Email<span class="text-red-500">*</span></span>
                            </span>
                        </div>
                        <input type="email" name="email" placeholder="Email"
                            class="input input-bordered w-full text-blue-700" value="{{ old('email') }}" required />
                        @error('email')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Password<span
                                        class="text-red-500">*</span></span>
                            </span>
                        </div>
                        <input type="password" name="password" placeholder="Password"
                            class="input input-bordered w-full text-blue-700" required />
                        @error('password')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Konfirmasi Password<span
                                        class="text-red-500">*</span></span>
                            </span>
                        </div>
                        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password"
                            class="input input-bordered w-full text-blue-700" required />
                    </label>
                    <button type="submit" class="btn btn-success mt-3 w-full text-white">Simpan</button>
                </form>
            </div>
        </div>
    </div>
    {{-- Akhir Modal Create --}}

    {{-- Awal Modal Edit --}}
    <input type="checkbox" id="edit_button" class="modal-toggle" />
    <div class="modal" role="dialog">
        <div class="modal-box">
            <div class="mb-3 flex justify-between">
                <h3 class="text-lg font-bold">Ubah {{ $title }}</h3>
                <label for="edit_button" class="cursor-pointer">
                    <i class="ri-close-large-fill"></i>
                </label>
            </div>
            <div>
                <form action="{{ route('admin-management.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="text" name="id" hidden>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">Nama<span class="text-red-500">*</span></span>
                            <span class="label-text-alt" id="loading_edit1"></span>
                        </div>
                        <input type="text" name="name" placeholder="Nama"
                            class="input input-bordered w-full text-blue-700" required />
                        @error('name')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">Email<span class="text-red-500">*</span></span>
                            <span class="label-text-alt" id="loading_edit2"></span>
                        </div>
                        <input type="email" name="email" placeholder="Email"
                            class="input input-bordered w-full text-blue-700" required />
                        @error('email')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">Password</span>
                            <span class="label-text-alt" id="loading_edit3"></span>
                        </div>
                        <input type="password" name="password"
                            placeholder="Kosongkan jika tidak ingin mengubah password"
                            class="input input-bordered w-full text-blue-700" />
                        @error('password')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">Konfirmasi Password</span>
                        </div>
                        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password"
                            class="input input-bordered w-full text-blue-700" />
                    </label>
                    <button type="submit" class="btn btn-warning mt-3 w-full text-slate-700">Perbarui</button>
                </form>
            </div>
        </div>
    </div>
    {{-- Akhir Modal Edit --}}

    <script>
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

        function edit_button(id) {
            // Loading effect start
            let loading = `<span class="loading loading-dots loading-md text-purple-600"></span>`;
            $("#loading_edit1").html(loading);
            $("#loading_edit2").html(loading);
            $("#loading_edit3").html(loading);

            $.ajax({
                type: "get",
                url: "{{ route('admin-management.edit') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id
                },
                success: function(data) {
                    // console.log(data);
                    let items = [];
                    $.each(data, function(key, val) {
                        items.push(val);
                    });

                    $("input[name='id']").val(items[0]);
                    $("input[name='name']").val(items[1]);
                    $("input[name='email']").val(items[2]);

                    // Loading effect end
                    loading = "";
                    $("#loading_edit1").html(loading);
                    $("#loading_edit2").html(loading);
                    $("#loading_edit3").html(loading);
                }
            });
        }

        function delete_button(id, name) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                html: "<p>Data yang dihapus tidak dapat dipulihkan kembali!</p>" +
                    "<div class='divider'></div>" +
                    "<div class='flex flex-col'>" +
                    "<b>Admin: " + name + "</b>" +
                    "</div>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6419E6',
                cancelButtonColor: '#F87272',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "{{ route('admin-management.delete') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": id
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#6419E6',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        },
                        error: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.responseJSON.message
                            })
                        }
                    });
                }
            })
        }
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
</x-app-layout>
