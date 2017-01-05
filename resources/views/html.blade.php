<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/21/2016
 * Time: 3:10 PM
 */ ?>
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel build my CMS</title>

    @section('css')
        <link type="text/css" href="{{URL::asset('css/panel.css')}}" rel="stylesheet"/>
    @show

</head>
<body>
<div class="flex-center position-ref full-height">
    @yield('content')
</div>
</body>
</html>

