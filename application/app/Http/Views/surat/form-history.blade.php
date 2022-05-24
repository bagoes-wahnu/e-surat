@extends('layout.layout')
@section('content')
<div class="row">
    <div class="col-xl-10 col-lg-10">
        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
            <div class="kt-portlet__head kt-portlet__head--lg  kt-portlet__head--break-sm">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Form Upload File History
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <?php
                $total_halaman = @$surat->halaman;
                ?>

                <form id="form1" action="" method="post" onsubmit="return false" enctype="multipart/form-data">
                    <input type="hidden" name="id_history" value="{{@$history->id_history}}">

                    <div class="col-lg-6">
                        <table class="table tabel-esurat table-responsive-lg table-hover" id="table1">
                            <?php

                            for ($i = 0; $i < $total_halaman; $i++) {
                            ?>
                                <tr style="vertical-align: middle;">
                                    <td>
                                        <label for="">Halaman {{($i + 1)}}</label>
                                    </td>
                                    <td>
                                        <input type="hidden" name="page[]" value="{{($i + 1)}}">
                                        <input type="file" name="file[]" class="form-control">
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>

                            <tr>
                                <td colspan="2" class="text-center">
                                    <button type="button" class="btn btn-secondary" onclick="window.history.back();">Batal</button>
                                    <button type="button" class="btn btn-green-cta ladda-button" data-style="zoom-in" id="btn_upload">Upload</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
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

<script type="text/javascript" src="{{aset_extends('js/page/form-history-grid.js')}}"></script>

<!--end::Modal-->
@endsection