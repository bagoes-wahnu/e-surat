@extends('layout.layout')
@section('content')


<script>
    document.title = "Dasftar Surat Masuk - Dishub E-Surat";
        document.getElementById('daftarSuratMasukNav').classList.add('kt-menu__item--active');
</script>


<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile">
        <!-- begin:: Content -->
        <div class="kt-portlet__head kt-portlet__head--lg  kt-portlet__head--break-sm" id="kt_content">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Daftar Surat Masuk
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="kt-portlet__head-actions">
                        <a href="#" class="btn btn-green-cta btn-elevate btn-icon-sm" data-toggle="modal"
                            data-target="#modal-tambah">
                            <i class="la la-plus"></i>
                            Tambah Surat
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body kt-portlet__body--fit">
            <table class="table tabel-esurat table-responsive-lg table-hover" id="dttb-srt-masuk" style="padding: 20px;">
                <thead>
                    <tr style="background: #f9fefa;">
                        <th>No. Surat</th>
                        <th style="width: ">Judul</th>
                        <th style="width:">Tanggal</th>
                        <th style="width:">Pengirim</th>
                        <th style="width:" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2013147011</td>
                        <td>Surat Super Semar</td>
                        <td>10 Agustus 2019</td>
                        <td>Dishub Mojokerto</td>
                        <td class="text-center" nowrap>
                            <a href="#" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" title="Edit" data-togle="m-tooltip" data-toggle="modal" data-target="#modal-edit"> 
                          <i class="la la-edit"></i>
                        </a>
                        <a href="#" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" title="Lihat" data-togle="m-tooltip" data-toggle="modal" data-target="#modal-view">
                          <i class="la la-eye"></i>
                        </a>
                        <a href="#" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" title="Hapus" data-togle="m-tooltip" id="sweetHapusSurat">
                          <i class="fa fa-trash-alt"></i>
                        </a>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
    <!-- end:: Content -->
</div>


<div class="modal fade" id="modal-tambah" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg-medium modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="width: 400px">Tambah Surat Masuk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">File Surat</label>
                    <div class="col-9">
                        <div class="kt-dropzone dropzone" action="inc/api/dropzone/upload.php" id="m-dropzone-one">
                            <div class="kt-dropzone__msg dz-message needsclick">
                                <h3 class="kt-dropzone__msg-title">Drop files here or click to upload.</h3>
                                <span class="kt-dropzone__msg-desc">Hanya diijinkan mengunggah satu file</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">No. Surat</label>
                    <div class="col-9">
                        <input type="text" class="form-control" aria-describedby="" placeholder="No. Surat">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">Judul</label>
                    <div class="col-9">
                        <input type="text" class="form-control" aria-describedby="" placeholder="Masukkan Judul">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">Tanggal</label>
                    <div class="col-9">
                        <input type="text" class="form-control" id="tanggalsuratmasuk_modal" readonly
                            placeholder="Pilih Tanggal">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">Pengirim</label>
                    <div class="col-9">
                        <input type="text" class="form-control" aria-describedby="" placeholder="Masukkan Pengirim">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-clean" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-lg btn-green-cta ladda-button">Simpan</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg-medium modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="width: 400px">Edit Surat Masuk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">File Surat</label>
                    <div class="col-9">
                        <div class="kt-dropzone dropzone" action="inc/api/dropzone/upload.php" id="m-dropzone-one">
                            <div class="kt-dropzone__msg dz-message needsclick">
                                <h3 class="kt-dropzone__msg-title">Drop files here or click to upload.</h3>
                                <span class="kt-dropzone__msg-desc">Hanya diijinkan mengunggah satu file</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">No. Surat</label>
                    <div class="col-9">
                        <input type="text" class="form-control" aria-describedby="" placeholder="No. Surat">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">Judul</label>
                    <div class="col-9">
                        <input type="text" class="form-control" aria-describedby="" placeholder="Masukkan Judul">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">Tanggal</label>
                    <div class="col-9">
                        <input type="text" class="form-control" id="tanggalsuratmasuk" readonly
                            placeholder="Pilih Tanggal">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">Pengirim</label>
                    <div class="col-9">
                        <input type="text" class="form-control" aria-describedby="" placeholder="Masukkan Pengirim">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-clean" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-lg btn-green-cta ladda-button">Simpan</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-view" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg-medium modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="width: 400px">View Surat Masuk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">File Surat</label>
                    <div class="col-9">
                        <label for="" class="col-form-label main-fontts font-boldd"><a class="fancybox"
                                data-fancybox-type="iframe" target="_blank"
                                href="http://www.africau.edu/images/default/sample.pdf">e-surat-contoh.pdf</a></label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">No. Surat</label>
                    <div class="col-9">
                        <label for="" class="col-form-label main-fontts font-boldd">20131771889</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">Judul</label>
                    <div class="col-9">
                        <label for="" class="col-form-label main-fontts font-boldd">Surat Super
                            Semar</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">Tanggal</label>
                    <div class="col-9">
                        <label for="" class="col-form-label main-fontts font-boldd">12 Agustus
                            2019</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">Pengirim</label>
                    <div class="col-9">
                        <label for="" class="col-form-label main-fontts font-boldd">Rama bin
                            Fahmi</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-clean" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-lg btn-green-cta ladda-button">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!--end::Modal-->



<script type="text/javascript" src="{{aset_extends('js/page/surat-masuk-grid.js')}}" defer></script>

@endsection