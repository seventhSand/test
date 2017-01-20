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

    <title>Default - Gentelella</title>

    @section('css')
        <link href="{{URL::asset('gentelella/vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{URL::asset('gentelella/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
        <link href="{{URL::asset('gentelella/vendors/nprogress/nprogress.css')}}" rel="stylesheet">
        <link href="{{URL::asset('gentelella/vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">
        <link href="{{URL::asset('gentelella/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css')}}"
              rel="stylesheet">
        <link href="{{URL::asset('gentelella/vendors/jqvmap/dist/jqvmap.min.css')}}" rel="stylesheet"/>
        <link href="{{URL::asset('gentelella/build/css/custom.min.css')}}" rel="stylesheet">
    @show
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">
        {!! Wa::getThemesView($themes, 'common.sidebar') !!}

        {!! Wa::getThemesView($themes, 'common.top') !!}

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
    <script src="{{URL::asset('gentelella/vendors/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{URL::asset('gentelella/vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    <script src="{{URL::asset('gentelella/vendors/fastclick/lib/fastclick.js')}}"></script>
    <script src="{{URL::asset('gentelella/vendors/nprogress/nprogress.js')}}"></script>
    <script src="{{URL::asset('gentelella/vendors/dropzone/dist/min/dropzone.min.js')}}"></script>
    <script src="{{URL::asset('gentelella/build/js/custom.min.js')}}"></script>
@show
</body>
</html>

