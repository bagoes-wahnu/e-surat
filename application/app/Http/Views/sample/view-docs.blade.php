@extends('layout.layout')
@section('content')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!--Begin::Section-->
<div class="row">
    <div class="col-lg-12">
        <div class="top-view-button">
            <a href="{{url('/home')}}">
                <button type="button"  class="btn btn-lg btn-outline-hover-green-esurat btn-elevate btn-circle btn-icon">
                    <i
                    class="la la-arrow-left"></i>
                </button>
            </a>
            <div class="info-doc">
                <h2 style="margin-bottom: 0px;">{{helpEmpty($surat->judul)}}</h2>
                <p style="margin-bottom: 0px;">
                    {{helpDate(@$surat->tanggal, 'mi')}}
                </p>
            </div>
            <div class="float-right">
                <button type="button" class="float-right btn btn-lg btn-green-cta btn-elevate btn-pill">
                    <i class="la la-print"></i>
                    Cetak
                </button>
            </div>
        </div>
    </div>
    <!-- <div class="col-lg-12 display-button">
        <button type="button" class="float-right btn btn-md btn-outline-hover-green-esurat btn-elevate btn-pill" style="margin:0px 20px; height : 50px;">
            <i class="la la-arrow-left"></i> PREV
        </button>

        <form class="kt-form">
            <div class="form-group  metode" style="">
                <div style="display:block">
                    <label class="label-center">Page</label>
                </div>
                <input type="text" class="form-control page-number" aria-describedby="" value="1" placeholder="">
            </div>
        </form>

        <button type="button" class="float-right btn btn-md btn-outline-hover-green-esurat btn-elevate btn-pill" style="margin:0px 20px; height : 50px;">NEXT
            <i class="la la-arrow-right"></i>
        </button>
    </div> -->
    <div class="col-lg-12 display-pdf">
        <img class="page-ttd" id="ttd" src="{{url('watch/sample?ct=tanda_tangan&src='.$ttd->path_file)}}" style=" <?php echo (!empty($surat_ttd))? 'left:'.$surat_ttd->left.'px;top:'.$surat_ttd->top.'px' : ''; ?> ">
        <?php
        for($i=1; $i <= $surat->halaman;$i++){
            ?>
            <img class="pdf" src="{{url('watch/sample?ct=surat&src=page-'.$i.'.png')}}"/>
            <?php
        }
        ?>
        <img class="pdf hide" src="{{asset ('assets/extends/img/photos/doc1.jpg')}}"/>
    </div>
</div>

<style type="text/css">
    .page-ttd{
        width: 85px;
        position: absolute;
    }
</style>

<script type="text/javascript">
    $( function() {
        $("#ttd").draggable({
            addClasses: false,
            cursor: "grabbing"
        });

        $('#ttd').on('drag', function (e) {
            console.log('h');
        });

        $('#ttd').on('dragstop', function (e) {
            setPosition();
        });
    });

    function setPosition() {
        let left = $('#ttd').css('left').replace('px', '');
        let top = $('#ttd').css('top').replace('px', '');
        $.ajax({
            type : "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data : {id_stt:1, id_ttd:1, page:1, id_surat:1, left, top},
            url : baseUrl+'sample/save_position',
            success : function(response){
                console.log(response);
            }
        })
    }
</script>

<!--End::Section-->
@endsection