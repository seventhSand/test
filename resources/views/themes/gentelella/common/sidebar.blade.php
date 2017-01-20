<?php
/**
 * Created by PhpStorm
 * Date: 14/01/2017
 * Time: 12:06
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */ ?>
<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            <a href="#" class="site_title">
                <i class="fa fa-paw"></i> <span>{{config('webarq.site.name')}}</span></a>
        </div>

        <div class="clearfix"></div>

        <!-- menu profile quick info -->
        <div class="profile">
            <div class="profile_pic">
                <img src="{{URL::asset('gentelella/sample/images/img.jpg')}}" alt="..." class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <span>{{trans('webarq.title.welcome')}},</span>

                <h2>{{ $admin->username }}</h2>
            </div>
        </div>
        <!-- /menu profile quick info -->

        <br/>

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <p>&nbsp;</p>

                <h3>General</h3>
                <ul class="nav side-menu">
                    @if ([] !== Wa::panel()->getMenus())
                        @foreach (Wa::panel()->getMenus() as $module => $panels)
                            <li>
                                <a>
                                    {{--
                                        fa-home, fa-edit, fa-desktop, fa-table, fa-bar-chart-o,
                                        fa-bug, fa-windows, fa-laptop, fa-sitemap
                                    --}}
                                    <i class="fa fa-clone"></i> {{ $module }}
                                    <span class="fa fa-chevron-down"></span>
                                </a>
                                <ul class="nav child_menu">
                                    @foreach($panels as $panel)
                                        <li>
                                            {!! Html::link($panel[1], $panel[0]) !!}
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>

        </div>
        <!-- /sidebar menu -->

        <!-- /menu footer buttons -->
        <div class="sidebar-footer hidden-small">
            <a href="{{URL::panel('system/configurations')}}"
               data-toggle="tooltip" data-placement="top" title="Configurations">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
            </a>
            <!--
            <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
            </a>
            -->
            <a href="{{URL::panel('system/admins/auth/logout')}}"
               data-toggle="tooltip" data-placement="top" title="Logout">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
            </a>
            <a href="{{URL::to('tab')}}" data-toggle="tooltip" data-placement="top">
                <span class="glyphicon" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top">
                <span class="glyphicon" aria-hidden="true"></span>
            </a>
        </div>
        <!-- /menu footer buttons -->
    </div>
</div>
