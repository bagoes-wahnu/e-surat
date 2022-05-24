@extends('layout.layout')
@section('content')
<!--Begin::Section-->
<div class="row">
    <div class="col-lg-12">
        <div class="top-view-button">
            <a href="{{url('/home')}}">
            <button type="button"  class="btn btn-lg btn-outline-hover-green-esurat btn-elevate btn-circle btn-icon"><i
                    class="la la-arrow-left"></i></button></a>
            <div class="info-doc">
                <h2 style="margin-bottom: 0px;">
                    Surat Keputusan Tentang kebijakan </h2>
                <p style="margin-bottom: 0px;">
                    12 Januari 2019
                </p>
            </div>
            <div class="float-right">

                {{-- <button type="button" class="float-right btn btn-lg btn-outline-hover-green-esurat btn-elevate btn-pill"
                    style="margin:0px 20px;">
                    <i class="la la-print"></i>
                    Cetak
                </button> --}}

                <button type="button" class="float-right btn btn-lg btn-green-cta btn-elevate btn-pill">
                        <i class="la la-print"></i>
                        Cetak
                </button>

            </div>
        </div>
    </div>
    <div class="col-lg-12 display-button">
            <button type="button" class="float-right btn btn-md btn-outline-hover-green-esurat btn-elevate btn-pill"
            style="margin:0px 20px; height : 50px;">
            <i class="la la-arrow-left"></i>
            PREV
        </button>

        <form class="kt-form">
                <div class="form-group  metode" style="">
                    <div style="display:block">
                            <label class="label-center">Page</label>
                    </div>
                        <input type="text" class="form-control page-number" aria-describedby="" value="2" placeholder="">
                </div>
        </form>

        <button type="button" class="float-right btn btn-md btn-outline-hover-green-esurat btn-elevate btn-pill"
        style="margin:0px 20px; height : 50px;">
        NEXT
        <i class="la la-arrow-right"></i>
    </button>
    </div>
    <div class="col-lg-12 display-pdf">
        <img class="pdf" src="{{asset ('assets/extends/img/photos/doc1.jpg')}}"/>
        {{-- <iframe src="{{asset ('assets/extends/img/photos/doc1.jpg')}}" width="100%" height="700px" style="border: none"></iframe> --}}
    </div>
</div>

<!--End::Section-->
@endsection