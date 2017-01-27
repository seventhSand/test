<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/27/2017
 * Time: 1:21 PM
 */

namespace App\Http\Controllers\Panel\System\Admins;


use App\Http\Controllers\Panel\BaseController;

class ProfileController extends BaseController
{
    public function actionGetIndex()
    {
        $this->layout->{'rightSection'} = 'Profile';
    }
}