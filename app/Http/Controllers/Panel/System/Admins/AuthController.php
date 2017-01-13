<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 12:59 PM
 */

namespace App\Http\Controllers\Panel\System\Admins;


use App\Http\Controllers\Panel\BaseController;
use Auth;
use URL;

class AuthController extends BaseController
{
    protected $layout = 'webarq.tcl-panel.layout.login';

    protected function hasPermission()
    {
        return true;
    }

    public function actionGetLogin()
    {
        Auth::attempt(['username' => 'superadmin', 'password' => 'superadmin']);

        if (Auth::user()) {
            return redirect(URL::panel('system/dashboard'));
        } else {
            return 'Ups no login found';
        }
    }
}