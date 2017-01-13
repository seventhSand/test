<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/12/2017
 * Time: 9:49 AM
 */ ?>

<h3>Your site name</h3>

@if ([] !== $menus)
    <ul class="navigation main">
        @foreach ($menus as $module => $panels)
            <li>
                <h3 style="text-transform: capitalize;margin-bottom: 0;">{{$module}}</h3>
                <ul class="navigation child">
                    @foreach ($panels as $info)
                        <li>
                            {!! Html::link($info[1], $info[0]) !!}
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
@endif