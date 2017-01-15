<?php
/**
 * Created by PhpStorm
 * Date: 14/01/2017
 * Time: 11:54
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */ ?>

        <!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gentella</title>

    @section('css')
        <link href="{{URL::asset('gentella/vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{URL::asset('gentella/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
        <link href="{{URL::asset('gentella/vendors/nprogress/nprogress.css')}}" rel="stylesheet">
        <link href="{{URL::asset('gentella/vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">
        <link href="{{URL::asset('gentella/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css')}}"
              rel="stylesheet">
        <link href="{{URL::asset('gentella/vendors/jqvmap/dist/jqvmap.min.css')}}" rel="stylesheet"/>
        <link href="{{URL::asset('gentella/build/css/custom.min.css')}}" rel="stylesheet">
    @show
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">
        {!! view('themes.gentella.common.sidebar') !!}

        {!! view('themes.gentella.common.top') !!}

        <div class="right_col" role="main">
            <div class="x_panel">
                @section('right')
                    {!! $rightSection or 'Welcome' !!}
                @show
            </div>
        </div>
        <!-- footer content -->
        <footer>
            <div class="pull-right">
                Powered by Gentella
            </div>
            <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
    </div>
</div>

@section('script')
    <script src="{{URL::asset('gentella/vendors/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{URL::asset('gentella/vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    <script src="{{URL::asset('gentella/vendors/fastclick/lib/fastclick.js')}}"></script>
    <script src="{{URL::asset('gentella/vendors/nprogress/nprogress.js')}}"></script>
    <script src="{{URL::asset('gentella/vendors/dropzone/dist/min/dropzone.min.js')}}"></script>
    <script src="{{URL::asset('gentella/build/js/custom.min.js')}}"></script>
@show
</body>
</html>

