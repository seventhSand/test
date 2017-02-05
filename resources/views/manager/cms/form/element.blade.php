<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/17/2017
 * Time: 10:57 AM
 */ ?>
<div{!! Html::attributes($attribute) !!}>
    <label for="{{ $title or '' }}">{{ $title or '' }}</label>
    {!! $input or '...' !!}
    @if (!empty($info))
        <span class="help-block">{{ $info }}</span>
    @endif
</div>