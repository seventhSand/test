<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 10:28 AM
 */

namespace App\Http\Controllers\Panel\System;


use App\Http\Controllers\Panel\BaseController;

class DashboardController extends BaseController
{
    public function actionGetIndex()
    {
        $this->layout->{'rightSection'} = 'Put your content here ...';
    }
}