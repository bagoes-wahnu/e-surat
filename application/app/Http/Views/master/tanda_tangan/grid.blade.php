@extends('layout.layout')
@section('content')

<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content -->
    <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile" id="kt_content">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Daftar Tanda Tangan
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body kt-portlet__body--fit">
            <table class="table tabel-esurat table-responsive-lg table-hover" id="table1">
                <thead class="text-center">
                    <tr style="background: #f9fefa;">
                        <th>Jabatan</th>
                        <th>Tanda tangan</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                    foreach ($tanda_tangan as $key => $value) {
                        ?>
                        <tr>
                            <td><?php echo $value->nama; ?></td>
                            <td>
                                <button type="button" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" title="Edit Tanda Tangan" data-toggle="m-tooltip" onclick="edit('<?php echo $value->id_ttd; ?>')">
                                    <i class="fa fa-signature"></i>
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- end:: Content -->
</div>

<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel" style="width: 75%">Tanda Tangan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="kt-portlet__body">
                    <form id="form1" class="kt-form" action="" method="post" onsubmit="false">
                        <input type="hidden" name="action" id="action" value="edit">
                        <input type="hidden" name="id_tanda_tangan" id="id_tanda_tangan" value="">

                        <div class="form-group row">
                            <label for="example-jabatan-input" class="col-3 col-form-label" style="font-weight: 400; font-size: 1.2rem !important;">Jabatan</label>
                            <div class="col-9">
                                <input class="form-control" type="text" value="Kadis" name="jabatan" id="jabatan" disabled="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="example-jabatan-input" class="col-3 col-form-label" style="font-weight: 400; font-size: 1.2rem !important;">TTD Hitam</label>
                            <div class="col-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="kt-dropzone dropzone" action="" id="dropzone_file_hitam">
                                            <div class="kt-dropzone__msg dz-message needsclick">
                                                <h3 class="kt-dropzone__msg-title">Drop files here or click to upload.</h3>
                                                <span class="kt-dropzone__msg-desc">Silahkan upload file tanda tangan <strong>Hitam</strong></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="field-ttd-hitam">
                                        <img id="ttd-hitam-exist" class="image-extend" style="width: auto !important;" src="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="example-jabatan-input" class="col-3 col-form-label" style="font-weight: 400; font-size: 1.2rem !important;">TTD Biru</label>
                            <div class="col-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="kt-dropzone dropzone" action="" id="dropzone_file_biru">
                                            <div class="kt-dropzone__msg dz-message needsclick">
                                                <h3 class="kt-dropzone__msg-title">Drop files here or click to upload.
                                                </h3>
                                                <span class="kt-dropzone__msg-desc">Silahkan upload file tanda tangan <strong>Biru</strong></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="field-ttd-biru">
                                        <img id="ttd-biru-exist" class="image-extend" style="width: auto !important;" src="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-clean" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-lg btn-green-cta ladda-button" data-style="zoom-in"
                id="btn_save">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{aset_extends('js/page/tanda-tangan-grid.js')}}"></script>
@endsection