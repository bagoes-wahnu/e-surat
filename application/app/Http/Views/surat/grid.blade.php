@extends('layout.layout')
@section('content')
<div class="row">
    <div class="col-xl-10 col-lg-10">
        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
            <div class="kt-portlet__head kt-portlet__head--lg  kt-portlet__head--break-sm">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Daftar Surat
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-actions">
                        &nbsp;
                        <?php
                        // echo $last_level.'-';
                        // echo $grade;
                        // if($role == 4){
                        if ($role == 4 && $grade == $last_level) {
                        ?>
                            <a href="javascript:;" class="btn btn-green-cta btn-elevate btn-icon-sm" onclick="tambah()">
                                <i class="la la-plus"></i>
                                Tambah
                            </a>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <!-- begin my datatable -->
                <table class="table tabel-esurat table-responsive-lg table-hover" id="table1">

                    <thead class="text-center">
                        <tr style="background: #f9fefa;">
                            <th width="10%" style="padding-left:25px">No.</th>
                            <th width="20%" class="align-left">Judul Surat</th>
                            <th width="15%">Jenis Surat</th>
                            <th width="15%">Tanggal Surat</th>
                            <th width="10%">Pegawai</th>
                            <th width="10%">Pejabat</th>
                            <th width="10%">Status</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                    </tbody>
                </table>
                <!-- end my datatable -->
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-2">
        <!--begin:: Widgets/Quick Stats-->
        <div class="row row-full-height">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="kt-portlet kt-portlet--height-fluid-half kt-portlet--border-bottom-brand bg-green-custom" style="height: 150px;">
                    <div class="kt-portlet__body kt-portlet__body--fluid">
                        <div class="kt-widget26">
                            <div class="kt-widget26__content">
                                <span class="kt-widget26__number text-light" id="stats_approved">0</span>
                                <span class="kt-widget26__desc text-light">APPROVED</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="kt-space-20"></div> -->
                <div class="kt-portlet kt-portlet--height-fluid-half kt-portlet--border-bottom-danger" style="height: 150px;">
                    <div class="kt-portlet__body kt-portlet__body--fluid">
                        <div class="kt-widget26">
                            <div class="kt-widget26__content">
                                <span class="kt-widget26__number" id="stats_rejected">0</span>
                                <span class="kt-widget26__desc">ROLLBACK</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kt-portlet kt-portlet--height-fluid-half kt-portlet--border-bottom-danger" style="height: 150px;">
                    <div class="kt-portlet__body kt-portlet__body--fluid">
                        <div class="kt-widget26">
                            <div class="kt-widget26__content">
                                <span class="kt-widget26__number" id="stats_waiting">0</span>
                                <span class="kt-widget26__desc">WAITING</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12">
            </div>
        </div>
        <!--end:: Widgets/Quick Stats-->
    </div>
    <!-- here -->
    <div class="col-xl-6">

    </div>
</div>

<!--begin::Modal-->
<div class="modal fade" id="modal_form" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form tambah surat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form id="form1" action="" method="post" onsubmit="return false;">
                    <input type="hidden" name="action" id="action" value="add">
                    <input type="hidden" name="id_surat" id="id_surat" value="">

                    <div class="col-lg-6 form-text">
                        <div class="form-group  metode">
                            <label class="label-tipe" style=" margin-bottom: 5px !important;">Nama Pegawai
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" name="pegawai" id="pegawai" placeholder="Masukkan nama pegawai" data-show-subtext="true" data-live-search="true">
                                <option value="" data-hidden="true">-- Pilih pegawai --</option>
                                <?php
                                foreach ($pegawai as $key => $value) {
                                ?>
                                    <option value="<?php echo $value->id_pegawai; ?>"><?php echo $value->nama_pegawai; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 form-text">
                        <div class="form-group  metode">
                            <label class="label-tipe" style=" margin-bottom: 5px !important;">Judul Surat
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-surat" name="judul" id="judul" placeholder="Masukkan judul surat" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-lg-6 form-text">
                        <div class="form-group  metode">
                            <label class="label-tipe" style=" margin-bottom: 5px !important;">Jenis Surat
                                <span class="text-danger">*</span>
                            </label>
                            <br>
                            <select class="form-control m-select2" data-live-search="true" name="jenis_surat" id="jenis_surat">
                                <option value="">-- Pilih Jenis Surat --</option>
                                <?php
                                foreach ($jenis_surat as $key => $value) {
                                ?>
                                    <option value="{{$value->id_jenis}}">{{$value->jenis}}</option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 form-text">
                        <div class="form-group  metode">
                            <label class="label-tipe" style=" margin-bottom: 5px !important;">Tanggal Surat
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-surat" name="tanggal" id="tanggal" placeholder="Masukkan tanggal surat" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-lg-6 form-text">
                        <div class="form-group  metode">
                            <label class="label-tipe" style=" margin-bottom: 5px !important;">Pilih Pejabat
                                <span class="text-danger">*</span>
                            </label>
                            <ul class="pilihan-tipe metode">
                                <?php
                                foreach ($tanda_tangan as $key => $value) {

                                ?>
                                    <li>
                                        <input type="radio" id="ttd-<?php echo $value->id_ttd; ?>" class="option-ttd" name="pejabat" value="<?php echo $value->id_ttd; ?>" unchecked />
                                        <label for="ttd-<?php echo $value->id_ttd; ?>" class="uppercase"><?php echo $value->nama; ?></label>
                                    </li>
                                <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>

                    <!-- start : revisi rollback -->
                    <div class="col-lg-6 form-keterangan">
                        <div class="form-group  metode">
                            <label class="label-tipe" style=" margin-bottom: 5px !important;">Direvisi oleh</label>
                            <div id="user_revisi">Sekretaris Dinas</div>
                        </div>
                    </div>

                    <div class="col-lg-6 form-keterangan">
                        <div class="form-group  metode">
                            <label class="label-tipe" style=" margin-bottom: 5px !important;">Keterangan</label>
                            <div id="keterangan"></div>
                            <br>
                            <div id="file-rollback"></div>
                        </div>
                    </div>
                    <!-- end : revisi rollback -->
                </form>

                <div class="col-lg-12">

                    <div class="form-group row">
                        <label class="label-tipe col-form-label col-lg-12 col-sm-12">Upload berkas</label>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="kt-dropzone dropzone dropzone-success m-dropzone--primary" action="inc/api/dropzone/upload.php" id="dropzone_file">
                                <div class="kt-dropzone__msg dz-message needsclick">
                                    <h3 class="text-green-custom kt-dropzone__msg-title">Drop files here or
                                        click to upload.</h3>
                                    <span class="text-green-cta kt-dropzone__msg-desc">Upload 1 file (Max. 4 MB)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-lg btn-green-cta ladda-button" data-style="zoom-in" id="btn_save">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_rollback" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form Rollback</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form id="formRollback" action="" method="post" onsubmit="return false;">
                    <input type="hidden" name="id_surat" id="id_surat_rollback" value="">
                    <div class="col-lg-6">
                        <div class="form-group  metode">
                            <label class="label-tipe" style=" margin-bottom: 5px !important;">Judul Surat
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-surat" name="judul" id="judul_rollback" placeholder="Masukkan judul surat" autocomplete="off" disabled>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group  metode">
                            <label class="label-tipe" style=" margin-bottom: 5px !important;">Keterangan</label>
                            <textarea name="keterangan" id="keterangan_rollback" class="form-control"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-lg btn-green-cta ladda-button" data-style="zoom-in" id="btn_rollback">Rollback</button>
            </div>
        </div>
    </div>
</div>

<!-- modal Timeline -->
<div class="modal fade" id="modal_timeline" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Timeline Surat</h5>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button> -->
            </div>
            <div class="modal-body">
                <div class="kt-timeline-v2">
                    <div class="kt-timeline-v2__items  kt-padding-top-25 kt-padding-bottom-30">
                        <div id="batas_timeline"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-clean" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- start : advance mode -->
<div class="modal fade" id="modal_upload" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form update file surat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">

                    <div class="form-group row">
                        <label class="label-tipe col-form-label col-lg-12 col-sm-12">Upload berkas</label>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="kt-dropzone dropzone dropzone-success m-dropzone--primary" action="" id="dropzone_file_update">
                                <div class="kt-dropzone__msg dz-message needsclick">
                                    <h3 class="text-green-custom kt-dropzone__msg-title">Drop files here or
                                        click to upload.</h3>
                                    <span class="text-green-cta kt-dropzone__msg-desc">Upload 1 file</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-lg btn-green-cta ladda-button" data-style="zoom-in" id="btn_upload">Upload</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_history_rollback" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">History Rollback</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table tabel-esurat table-responsive-lg table-hover" style="width: 100%;" id="table-history-rollback">
                            <thead class="text-center">
                                <tr style="background: #f9fefa;">
                                    <th width="20px" style="padding-left:25px">No.</th>
                                    <th>Pejabat</th>
                                    <th class="align-left">Keterangan</th>
                                    <th>Tanggal</th>
                                    <th>Batch</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">tutup</button>
            </div>
        </div>
    </div>
</div>

<div id="list-file-history" style="display: none;"></div>
<!-- end : advance mode -->

<!-- start : modal surat selesai -->
<div class="modal fade" id="modal_surat_selesai" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Unggah Surat Selesai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <div class="form-group row">
                        <label class="label-tipe col-form-label col-lg-12 col-sm-12">Upload berkas</label>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="kt-dropzone dropzone dropzone-success m-dropzone--primary" action="inc/api/dropzone/upload.php" id="dropzone_surat_selesai">
                                <div class="kt-dropzone__msg dz-message needsclick">
                                    <h3 class="text-green-custom kt-dropzone__msg-title">Drop files here or click to upload.</h3>
                                    <span class="text-green-cta kt-dropzone__msg-desc">Upload 1 file</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-lg btn-green-cta ladda-button" data-style="zoom-in" id="btn_upload_selesai">Upload</button>
            </div>
        </div>
    </div>
</div>

<!--begin::Modal Detail Penomoran Surat-->
<div class="modal fade" id="modal_detail_penomoran" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nomor Surat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="row" style="margin: 10px 10px;">
                    <div class="col-md-4" style="margin-bottom: 15px;">
                        <label>No. Surat</label>
                    </div>
                    <div class="col-md-8" style="margin-bottom: 15px;">
                        <b id="xa-nomor-surat">-</b><a id="xa-edit-nomor-surat" href="javascript:;" onclick=""><i style="font-size: 1.5rem !important; color: #4eb96e; margin-left: 7px;" class="la la-pencil"></i></a>
                    </div>
                    <div class="col-md-4" style="margin-bottom: 15px;">
                        <label>Tanggal</label>
                    </div>
                    <div class="col-md-8">
                        <b id="xa-tanggal">-</b>
                    </div>
                    <div class="col-md-4" style="margin-bottom: 15px;">
                        <label>Judul</label>
                    </div>
                    <div class="col-md-8" style="margin-bottom: 15px;">
                        <b id="xa-judul-surat">-</b>
                    </div>
                    <div class="col-md-4" style="margin-bottom: 15px;">
                        <label>Pengirim</label>
                    </div>
                    <div class="col-md-8" style="margin-bottom: 15px;">
                        <b id="xa-pengirim">-</b>
                    </div>
                    <div class="col-md-4" style="margin-bottom: 15px;">
                        <label>File Surat</label>
                    </div>
                    <div class="col-md-8" style="margin-bottom: 15px;">
                        <b id="xa-file-surat">-</b>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--begin::Modal Penomoran Surat-->
<div class="modal fade" id="modal_penomoran" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nomor Surat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form id="form-penomoran" action="" method="post" onsubmit="return false;">
                    <input type="hidden" name="penomoran_action" id="penomoran-action" value="add">
                    <input type="hidden" name="penomoran_id_surat" id="penomoran-id-surat" value="">
                    <div class="form-group col-md-6">
                        <label class="label-tipe" style=" margin-bottom: 5px !important;">Tipe
                            <span class="text-danger">*</span>
                        </label><br/>
                        <input type="hidden" id="val-tipe" value="2">
                        <button type="button" id="btn-tipe-hari-ini" onClick="pilihTipe(2)" class="btn btn-success">HARI INI</button>
                        <button type="button" id="btn-tipe-pilih-nomor" onClick="pilihTipe(1)" class="btn btn-outline-success">PILIH NOMOR</button>
                    </div>
                    <div class="col-lg-6 form-text show-form-today" style="">
                        <div class="form-group  metode">
                            <label class="label-tipe" style=" margin-bottom: 5px !important;">Nomor Surat
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="penomoran_nomor_surat" id="penomoran-nomor-surat" placeholder="Tidak tersedia" disabled>
                        </div>
                    </div>
                    <div class="col-lg-6 form-text show-form-number" style="display:none;">
                        <div class="form-group  metode">
                            <label class="label-tipe" style=" margin-bottom: 5px !important;">Tanggal
                                <span class="text-danger">*</span>
                            </label>
                            <br>
                            <input type="text" class="form-control input-surat" name="penomoran_tanggal" id="penomoran-tanggal" placeholder="Masukkan tanggal" autocomplete="off">
                        </div>
                    </div>
                    <!-- <div class="col-lg-6 form-text show-form-number" style="display:none;">
                        <div class="form-group  metode">
                            <label class="label-tipe" style=" margin-bottom: 5px !important;">Sektor
                                <span class="text-danger">*</span>
                            </label>
                            <br>
                            <select class="form-control m-select2 list_sector" placeholder="Pilih Sektor" name="sector" id="sector" style="width:100%">
                            </select>
                        </div>
                    </div> -->
                    <div class="col-lg-6 form-text show-form-number" style="display:none;">
                        <div class="form-group  metode">
                            <label class="label-tipe" style=" margin-bottom: 5px !important;">Nomor Surat
                                <span class="text-danger">*</span>
                            </label>
                            <br>
                            <select class="form-control m-select2 list_nomor_penomoran" placeholder="Pilih Sumber Daya" name="nomor_penomoran" id="nomor-penomoran" style="width:100%">
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-lg btn-green-cta ladda-button" id="save-penomoran" data-style="zoom-in" onclick="saveSuratPenomoran()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    .bg-gray-custom {
        color: #fff !important;
        background-color: #adb1c7 !important;
    }
</style>

<script type="text/javascript">
    lastGrade = <?php echo $last_level; ?>;
    let advanceMode = '<?php echo $advance; ?>';

    console.log(advanceMode);
</script>
<script type="text/javascript" src="{{aset_extends('js/page/surat-grid.js')}}"></script>

<!--end::Modal-->
@endsection
