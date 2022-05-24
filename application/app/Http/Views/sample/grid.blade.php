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
                        <a href="#" class="btn btn-green-cta btn-elevate btn-icon-sm"
                        data-toggle="modal" data-target="#modal_surat">
                        <i class="la la-plus"></i>
                        Tambah
                    </a>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body kt-portlet__body--fit">
            <!-- begin my datatable -->
            <table class="table tabel-esurat table-responsive-lg table-hover" id="table1">

                <thead class="text-center">
                    <tr style="background: #f9fefa;">
                        <th width="20px" style="padding-left:25px">No.</th>
                        <th>Tanggal</th>
                        <th class="align-left">Nama Surat</th>
                        <th>Pejabat</th>
                        <th>Status</th>
                        <!-- <th>Lihat Tenant</th> -->
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr>
                        <td>1. </td>
                        <td class="">12 Januari 2019</td>
                        <td class="fokus align-left">Surat tugas perbaikan jalan</td>
                        <td class="float-left">
                            <span class="kt-badge kt-badge--warning kt-badge--dot"></span>
                            &nbsp;
                            <span class="kt-font-bold">
                                Sekretaris
                            </span>
                        </td>
                        <td>
                            <span
                            class="kt-badge bg-orange-dark-esurat kt-badge--inline kt-badge--pill">
                            Rejected
                        </span>
                    </td>
                    <td nowrap></td>
                </tr>

                <tr>
                    <td>20. </td>
                    <td class="">12 Januari 2019</td>
                    <td class="fokus align-left">Surat Keputusan Tentang kebijakan
                    stasiun</td>
                    <td class="float-left">
                        <span class="kt-badge bg-green-custom kt-badge--dot"></span>
                        &nbsp;
                        <span class="kt-font-bold">
                            Kadis
                        </span>
                    </td>
                    <td>
                        <span
                        class="kt-badge bg-green-custom kt-badge--inline kt-badge--pill">
                        Approved
                    </span>
                </td>
                <td nowrap></td>
            </tr>
            <tr>
                <td>20. </td>
                <td class="">12 Januari 2019</td>
                <td class="fokus align-left">Surat Keputusan Tentang kebijakan
                stasiun</td>
                <td class="float-left">
                    <span class="kt-badge bg-green-custom kt-badge--dot"></span>
                    &nbsp;
                    <span class="kt-font-bold">
                        Kadis
                    </span>
                </td>
                <td>
                    <span
                    class="kt-badge bg-green-custom kt-badge--inline kt-badge--pill">
                    Approved
                </span>
            </td>
            <td nowrap></td>
        </tr>
        <tr>
            <td>20. </td>
            <td class="">12 Januari 2019</td>
            <td class="fokus align-left">Surat Keputusan Tentang kebijakan
            stasiun</td>
            <td class="float-left">
                <span class="kt-badge bg-green-custom kt-badge--dot"></span>
                &nbsp;
                <span class="kt-font-bold">
                    Kadis
                </span>
            </td>
            <td>
                <span
                class="kt-badge bg-green-custom kt-badge--inline kt-badge--pill">
                Approved
            </span>
        </td>
        <td nowrap></td>
    </tr>
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
            <div class="kt-portlet kt-portlet--height-fluid-half kt-portlet--border-bottom-brand bg-green-custom"
            style="height: 150px;">
            <div class="kt-portlet__body kt-portlet__body--fluid">
                <div class="kt-widget26">
                    <div class="kt-widget26__content">
                        <span class="kt-widget26__number text-light">570</span>
                        <span class="kt-widget26__desc text-light">APPROVED</span>
                    </div>
                            <!-- <div class="kt-widget26__chart" style="height:100px; width: 230px;">
                                <canvas id="kt_chart_quick_stats_1"></canvas>
                            </div> -->
                        </div>
                    </div>
                </div>
                <!-- <div class="kt-space-20"></div> -->
                <div class="kt-portlet kt-portlet--height-fluid-half kt-portlet--border-bottom-danger"
                style="height: 150px;">
                <div class="kt-portlet__body kt-portlet__body--fluid">
                    <div class="kt-widget26">
                        <div class="kt-widget26__content">
                            <span class="kt-widget26__number">640</span>
                            <span class="kt-widget26__desc">REJECTED</span>
                        </div>
                            <!-- <div class="kt-widget26__chart" style="height:100px; width: 230px;">
                                <canvas id="kt_chart_quick_stats_2"></canvas>
                            </div> -->
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
<div class="modal fade" id="modal_surat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

       <form class="kt-form">
          <div class="modal-header">
             <h5 class="modal-title" id="exampleModalLabel">Form tambah surat</h5>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
             </button>
         </div>
         <div class="modal-body">

             <div class="col-lg-6">
                <div class="form-group  metode" style="">
                   <label class="label-tipe" style=" margin-bottom: 5px !important;">Pilih Pejabat
                      <span class="text-danger">*</span></label>
                      <ul class="pilihan-tipe metode">
                          <li>
                             <input type="radio" id="metodeLelang" name="radio-metode" value="general"
                             unchecked />
                             <label for="metodeLelang">SEKRETARIS</label>
                         </li>
                         <li>
                             <input type="radio" id="metodePL" name="radio-metode" value="lokasi"
                             unchecked />
                             <label for="metodePL">KADIS</label>
                         </li>
                     </ul>
                 </div>

             </div>

             <div class="col-lg-12">

                <div class="form-group row">
                   <label class="label-tipe col-form-label col-lg-12 col-sm-12">Upload berkas</label>
                   <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="kt-dropzone dropzone dropzone-success m-dropzone--primary"
                      action="inc/api/dropzone/upload.php" id="m-dropzone-two">
                      <div class="kt-dropzone__msg dz-message needsclick">
                        <h3 class="text-green-custom kt-dropzone__msg-title">Drop files here or
                        click to upload.</h3>
                        <span class="text-green-cta kt-dropzone__msg-desc">Upload up to 10
                        files</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
<div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
 <button type="button" class="btn btn-lg btn-green-cta">Simpan</button>
</div>

</form>
</div>
</div>
</div>

<!--end::Modal-->
@endsection