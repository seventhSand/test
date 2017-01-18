<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/17/2017
 * Time: 2:28 PM
 */ ?>
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{URL::asset('admin-lte/dist/img/user2-160x160.jpg')}}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{$admin->username}}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            @if ([] !== Wa::panel()->getMenus())
                @foreach (Wa::panel()->getMenus() as $module => $panels)
                    <li class="active treeview">
                        <a>
                            <i class="fa fa-dashboard"></i> <span>{{ $module }}</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @foreach($panels as $panel)
                                <li>
                                    <a href="{{$panel[1]}}">
                                        <i class="fa fa-circle-o"></i> {{$panel[0]}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            @endif
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
