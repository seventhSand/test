<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 4:51 PM
 */

namespace App\Http\Controllers\Panel\System;


use App\Http\Controllers\Panel\BaseController;

class AdminsController extends BaseController
{
    public function actionGetIndex()
    {
        return 'Admins listing`';
    }

    public function actionGetPublish()
    {
        return 'Create';
    }
}