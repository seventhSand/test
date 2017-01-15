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
use Request;
use URL;

class AuthController extends BaseController
{
    protected $layout = 'login';

    public function before()
    {
        if (isset($this->admin) && 'logout' !== $this->action) {
            return redirect(URL::panel('system/dashboard'));
        }
    }

    public function actionGetLogin()
    {

    }

    public function actionPostLogin()
    {
        $validator = \Validator::make(
                ['username' => Request::input('username'), 'password' => Request::input('password'),],
                ['username' => 'required', 'password' => 'required']
        );

        if ($validator->fails()) {
            $this->layout->messages = $validator->errors()->getMessages();
        } else {
            Auth::attempt(['username' => Request::input('username'), 'password' => Request::input('password')]);

            if (Auth::user()) {
                return redirect(URL::panel('system/dashboard'));
            } else {
                $this->layout->messages = [['Please check your username and password']];
            }
        }
    }

    public function actionGetLogout()
    {
        Auth::logout();
    }

    protected function hasPermission()
    {
        return true;
    }
}