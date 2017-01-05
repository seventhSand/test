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
    public function hasPermission()
    {
        return true;
    }

    public function actionGetIndex()
    {
        return 'Dashboard Area';
    }

    public function actionGetCreate()
    {

        return 'Create Dashboard';
    }
}