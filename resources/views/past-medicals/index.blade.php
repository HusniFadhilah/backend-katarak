@extends('layouts.template.admin')

@section('title', 'Kelola Riwayat Penyakit')

@php $role = Fungsi::getRoleSession(); @endphp

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <h1 class="h3 mb-4 text-gray-800">Kelola Riwayat Penyakit</h1>
            </div>
            <div class="col-lg-3">
                <small>
                    <ol class="breadcrumb">
                        <li><a href="{{ route('dashboard') }}" class="text-decoration-none text-gray-800 mr-2"><i class="icon-dashboard"></i> Home</a></li>
                        <li class="active"><i class="ion-ios-arrow-forward mr-2"></i>Riwayat Penyakit</li>
                    </ol>
                </small>
            </div>
        </div>
        <div class="row">
            <div class="col-lg">
                <div class="d-flex flex-column flex-md-row">
                    <a href="{{ route('past-medical.create') }}" class="btn btn-primary mb-3 mr-1"><span class="fa fa-plus mr-2"></span>Tambah Riwayat Penyakit</a>
                </div>
                @if (count($errors) > 0)
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Error saat import excel!</h5>
                    <p>Silahkan perbaiki beberapa data yang ditunjukkan oleh pesan di bawah ini, pada file excel Anda, lalu silahkan upload ulang file yang telah diperbaiki</p>
                    <ol class="pl-3">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ol>
                </div>
                @endif
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-secondary">Data Riwayat Penyakit</h6>
                        <div>
                            <span type="button" onclick="fillDataTable()" class="mr-2"><i class="fas fa-sync-alt text-gray-500"></i></span>
                            <span class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink" style="">
                                    <a class="dropdown-item tombol-bulk-konfirmasi-custom" href="{{ route('past-medical.bulkdestroy') }}" data-message="Riwayat penyakit yang dipilih ini akan dihapus" data-id-form="bulk-past-medical-form">Hapus <span class="text-info">riwayat penyakit</span> yang dipilih</a>
                                </div>
                            </span>
                        </div>
                    </div>
                    <form id="bulk-past-medical-form" action="" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="past-medicaldata">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="select_all" value="">
                                            </th>
                                            <th>#</th>
                                            <th>Riwayat Penyakit</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" action="" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importExcelLabel">Import Excel for Riwayat Penyakit</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Upload File Excel *</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    fillDataTable();

    function fillDataTable() {
        $('#past-medicaldata').DataTable().destroy();
        let s_name = $('#s_name').val();

        //datatables
        const table = $('#past-medicaldata').DataTable({
            drawCallback: function() {
                $('[data-toggle="popover"]').popover();
                selectAllChecked()
                $('.btn-copy').on('click', function() {
                    copyDivToClipboard(this)
                })
            }
            , "processing": true
            , "serverSide": true,

            "ajax": {
                "url": "{{ route('past-medical') }}"
                , "dataType": "json"
                , "type": "GET"
                , "data": {
                    _token: "{{csrf_token()}}"
                    , s_name: s_name
                }
                , error: function(xhr, status, error) {
                    if (error = 'error') {
                        error = xhr.responseJSON != null ? xhr.responseJSON.message : xhr.responseText
                    }
                    triggerSweetalert("Gagal!", "Terjadi kegagalan, silahkan coba beberapa saat lagi! Error: " + error, "error");
                    return false;
                }
            },

            "columns": [{
                className: "text-center"
                , data: '#'
                , orderable: false
                , searchable: false
                , width: "1%"
            }, {
                className: "text-center"
                , data: 'DT_RowIndex'
                , width: "1%"
            }, {
                className: "text-sm"
                , data: 'name'
            }, {
                className: "text-center"
                , data: 'action'
                , orderable: false
                , searchable: false
            }]
        });

    };
    $('#filter').on('change', '.filter', function() {
        fillDataTable();
    });

</script>

@endpush
