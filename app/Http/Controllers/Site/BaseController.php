<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 6:22 PM
 */

namespace App\Http\Controllers\Site;


use App\Http\Controllers\Webarq;

class BaseController extends Webarq
{
    public function actionGetTab()
    {
        return view('samples.tab');
    }
}