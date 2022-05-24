@extends('layout.layout')
@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
            <div class="kt-portlet__head kt-portlet__head--lg  kt-portlet__head--break-sm">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Arsip Surat
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-actions">
                        &nbsp;
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <!-- begin my datatable -->
                <table class="table tabel-esurat table-responsive-lg table-hover" id="table1">

                    <thead class="text-center">
                        <tr style="background: #f9fefa;">
                            <th width="20px" style="padding-left:25px">No.</th>
                            <th class="align-left">Judul Surat</th>
                            <th>Jenis Surat</th>
                            <th>Tanggal Surat</th>
                            <th>Pegawai</th>
                            <th>Pejabat</th>
                            <th>Status</th>
                            <th>Action</th>
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


<style type="text/css">
    .bg-gray-custom {
        color: #fff !important;
        background-color: #adb1c7 !important;
    }
</style>

<script type="text/javascript">
    lastGrade = <?php echo $last_level; ?>;
</script>
<script type="text/javascript" src="{{aset_extends('js/page/surat-arsip-grid.js')}}"></script>

<!--end::Modal-->
@endsection