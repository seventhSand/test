<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/21/2016
 * Time: 3:18 PM
 */ ?>

@extends('html')

@section('css')
    @parent
    <link type="text/css" rel="stylesheet" href="{{URL::asset('manager/css/form.css')}}"/>
@stop

@section('content')
    {!! $html !!}
@endsection

