<?php

/**
 * Created by PhpStorm
 * Date: 14/01/2017
 * Time: 14:45
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
    <title>Please Login</title>

    <!-- Bootstrap -->
    <link href="{{URL::asset('gentella/vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{URL::asset('gentella/build/css/custom.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('default/css/custom.css')}}" rel="stylesheet">
</head>

<body class="nav-md" style="background-color: #f7f7f7;">
<div class="container body">
    <div class="main_container">
        <div class="col-md-12">
            <div class="row">
                <div class="mid_center" style="width: 50%;margin-top: 50px;">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Registered Area</h2>

                            <div class="clearfix"></div>
                        </div>
                        @if (isset($messages))
                            @foreach ($messages as $groups)
                                <label class="messages error">{{ current($groups) }}</label>
                            @endforeach
                        @endif
                        {!!
                        Form::open([
                                'url' => URL::panel('system/admins/auth/login'),
                                'class' => 'form-horizontal form-label-left']) !!}
                        <div class="form-group{{ isset($messages['username']) ? ' bad' : '' }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">
                                Username <span class="required">*</span>
                            </label>

                            <div class="col-md-6 col-sm-6 col-xs-12">
                                {!! Form::text('username', '', [
                                        'class' => 'form-control'
                                ]) !!}
                            </div>
                        </div>

                        <div class="form-group{{ isset($messages['password']) ? ' bad' : '' }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">
                                Password <span class="required">*</span>
                            </label>

                            <div class="col-md-6 col-sm-6 col-xs-12">
                                {!! Form::password('password', [
                                        'class' => 'form-control'
                                ]) !!}
                            </div>
                        </div>

                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-primary">Cancel</button>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>


