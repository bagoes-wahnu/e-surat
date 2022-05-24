@extends('layout.layout')
@section('content')

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
                        <button type="button" class="btn btn-green-cta btn-elevate btn-icon-sm" onclick="tambah()">
                            <i class="la la-plus"></i>
                            Tambah Surat
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body kt-portlet__body--fit">
            <table class="table tabel-esurat table-responsive-lg table-hover" id="table1" style="padding: 20px;">
                <thead>
                    <tr style="background: #f9fefa;">
                        <th>#</th>
                        <th>No. Surat</th>
                        <th style="width: ">Judul</th>
                        <th style="width:">Tanggal</th>
                        <th style="width:">Pengirim</th>
                        <th style="width:" class="text-center">Action</th>
                    </tr>
                </thead>
                <!-- <tbody>
                    <tr>
                        <td>1</td>
                        <td>2013147011</td>
                        <td>Surat Super Semar</td>
                        <td>10 Agustus 2019</td>
                        <td>Dishub Mojokerto</td>
                        <td class="text-center" nowrap></td>
                    </tr>
                </tbody> -->
            </table>

        </div>
    </div>
    <!-- end:: Content -->
</div>


<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg-medium modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="width: 400px">Tambah Surat Masuk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form id="form1" action="" method="post" onsubmit="return false;">
                    <input type="hidden" name="action" id="action" value="add">
                    <input type="hidden" name="id_surat_masuk" id="id_surat_masuk" value="">

                    <div class="form-group row">
                        <label for="" class="col-3 col-form-label" style="font-weight: 400; font-size: 1.2rem !important;">No. Surat</label>
                        <div class="col-9">
                            <input type="text" class="form-control input-surat-masuk" aria-describedby="" name="no_surat" id="no_surat" placeholder="No. Surat">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-3 col-form-label" style="font-weight: 400; font-size: 1.2rem !important;">Judul</label>
                        <div class="col-9">
                            <input type="text" class="form-control input-surat-masuk" name="judul" id="judul" aria-describedby="" placeholder="Masukkan Judul">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-3 col-form-label" style="font-weight: 400; font-size: 1.2rem !important;">Tanggal</label>
                        <div class="col-9">
                            <input type="text" class="form-control input-surat-masuk" name="tanggal" id="tanggal" readonly placeholder="Pilih Tanggal">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-3 col-form-label"
                        style="font-weight: 400; font-size: 1.2rem !important;">Pengirim</label>
                        <div class="col-9">
                            <input type="text" class="form-control input-surat-masuk" name="pengirim" id="pengirim" aria-describedby="" placeholder="Masukkan Pengirim">
                        </div>
                    </div>
                </form>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label" style="font-weight: 400; font-size: 1.2rem !important;">File Surat</label>
                    <div class="col-9">
                        <div class="kt-dropzone dropzone" action="inc/api/dropzone/upload.php" id="dropzone_file">
                            <div class="kt-dropzone__msg dz-message needsclick">
                                <h3 class="kt-dropzone__msg-title">Drop files here or click to upload.</h3>
                                <span class="kt-dropzone__msg-desc">Hanya diijinkan mengunggah satu file</span>
                            </div>
                        </div>
                        <div class="field-file">
                            <br>
                            <br>
                            <a target="_blank" href="ji" class="kt-link kt--font-boldest kt-link--state kt-link--primary fancybox fancybox-effects-a" data-fancybox="file-penggunaan" data-caption="sd" style="font-size: 1.3em !important;">File saat ini</a>
                            <button type="button" class="btn btn-danger btn-icon btn-sm" style="border-color: #f4516c;background-color: #f4516c;"><i class="la la-trash-o"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-clean" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-lg btn-green-cta ladda-button" data-style="zoom-in" id="btn_save">Simpan</button>
            </div>
        </div>
    </div>
</div>


<!-- <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
</div> -->


<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg-medium modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="width: 400px">View Surat Masuk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label" style="font-weight: 400; font-size: 1.2rem !important;">No. Surat</label>
                    <div class="col-9">
                        <label for="" class="col-form-label main-fontts font-boldd field-detail" id="detail_no_surat">20131771889</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label" style="font-weight: 400; font-size: 1.2rem !important;">Judul</label>
                    <div class="col-9">
                        <label for="" class="col-form-label main-fontts font-boldd field-detail" id="detail_judul">Surat Super Semar</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                    style="font-weight: 400; font-size: 1.2rem !important;">Tanggal</label>
                    <div class="col-9">
                        <label for="" class="col-form-label main-fontts font-boldd field-detail" id="detail_tanggal">12 Agustus 2019</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label"
                    style="font-weight: 400; font-size: 1.2rem !important;">Pengirim</label>
                    <div class="col-9">
                        <label for="" class="col-form-label main-fontts font-boldd field-detail" id="detail_pengirim">Rama bin Fahmi</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-3 col-form-label" style="font-weight: 400; font-size: 1.2rem !important;">File Surat</label>
                    <div class="col-9">
                        <label for="" class="col-form-label main-fontts font-boldd field-detail" id="detail_file"></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-clean" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal-->


<script type="text/javascript">
    lastGrade = <?php echo $last_level; ?>;
</script>
<script type="text/javascript" src="{{aset_extends('js/page/surat-masuk-grid.js')}}"></script>

@endsection