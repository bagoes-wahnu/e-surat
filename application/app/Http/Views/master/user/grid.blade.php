@extends('layout.layout')
@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
            <div class="kt-portlet__head kt-portlet__head--lg  kt-portlet__head--break-sm">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Master User
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-actions">
                        &nbsp;
                        <!-- <a href="javascript:;" class="btn btn-green-cta btn-elevate btn-icon-sm" onclick="tambah()">
                            <i class="la la-plus"></i>
                            Tambah
                        </a> -->
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div class="row">
                    <div class="col-md-12">
                        <p style="margin-left: 15px;">Default password : <b>{{helpEmpty($default_password)}}</b></p>
                    </div>
                </div>
                <!-- begin my datatable -->
                <table class="table tabel-esurat table-responsive-lg table-hover" id="table1">

                    <thead class="text-center">
                        <tr style="background: #f9fefa;">
                            <th data-orderable="false" width="20px" style="padding-left:25px">No.</th>
                            <th>Nama</th>
                            <th class="align-left">Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th data-orderable="false">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                    </tbody>
                </table>
                <!-- end my datatable -->
            </div>
        </div>
    </div>
</div>

<!--begin::Modal-->
<div class="modal fade" id="modal_setting" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle" style="width: 75%">Form Konfigurasi</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </button>
        </div>
        <div class="modal-body">
            <form id="formSetting" action="" method="post" onsubmit="return false;">
                <input type="hidden" name="id_user" id="id_user_setting" value="">
                <div class="row">

                    <div class="col-lg-6">
                        <div class="form-group  metode" style="">
                            <label class="label-tipe" style=" margin-bottom: 5px !important;">Izin Akses Android</label>
                            <br>
                            <span class="kt-switch kt-switch--success kt-switch--icon"><label><input type="checkbox" name="allow_access" id="allow_access" value="1"><span></span></label></span>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group  metode" style="">
                            <label class="label-tipe" style=" margin-bottom: 5px !important;">Batas Akses Simultan
                                <span class="text-danger">*</span>
                            </label>
                            <div class="kt-checkbox-inline">
                                <label class="kt-checkbox kt-checkbox--success">
                                    <input type="checkbox" name="limit_access" id="limit_access" value="1" onchange="check_limit()"> Tidak terbatas
                                    <span></span>
                                </label>
                            </div>

                            <input type="text" class="form-control" name="total_limit_access" id="total_limit_access" placeholder="e.g : 10" style="width: 33%">
                        </div>
                    </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-lg btn-green-cta ladda-button" data-style="zoom-in"
                id="btn_save_setting">Simpan</button>
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
    var arr_role_user = <?php echo json_encode($role_user); ?>;
</script>
<script type="text/javascript" src="{{aset_extends('js/page/master-user-grid.js')}}"></script>

<!--end::Modal-->
@endsection