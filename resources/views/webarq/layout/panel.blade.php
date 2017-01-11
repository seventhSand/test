<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/10/2017
 * Time: 12:48 PM
 */ ?>
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel build my CMS</title>

    @section('css')
        <link type="text/css" href="{{URL::asset('webarq/css/panel.css')}}" rel="stylesheet"/>
        <link type="text/css" href="{{URL::asset('webarq/css/grid.css')}}" rel="stylesheet"/>
    @show

</head>
<body>
<div class="dark-line" style="position: fixed; top:0px; width:100%;">&nbsp;</div>
<div class="section group panel">
    <div class="span_fl_left">
        <h3>Your site name</h3>
        {!! $panel or 'Panel not available right now' !!}
    </div>
    <div class="span_fl_right">
        {!! $right or 'Content not available right now' !!}
    </div>
</div>
<div class="dark-line" style="position: fixed; bottom:0px; width:100%;">&nbsp;</div>
</body>

</html>
