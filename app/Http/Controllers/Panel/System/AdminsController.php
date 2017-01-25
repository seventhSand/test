<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 4:51 PM
 */

namespace App\Http\Controllers\Panel\System;


use App\Http\Controllers\Panel\Helper\ListingController;

class AdminsController extends ListingController
{
    public function actionGetIndex()
    {
        return parent::actionGetIndex();
    }
}