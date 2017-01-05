<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/15/2016
 * Time: 10:40 AM
 */

namespace App\Http\Controllers\Panel;


use App\Http\Controllers\Webarq;
use Auth;
use URL;

class BaseController extends Webarq
{
    /**
     * @var object Webarq\Manager\AdminManager
     */
    protected $admin;

    public function before()
    {
        $this->admin = Auth::user();

        if (!isset($this->admin)) {
            if ('login' !== $this->action && 'auth' !== $this->controller) {
                return redirect(URL::panel('system/admins/auth/login'));
            }
        } elseif (!$this->hasPermission()) {
            return $this->actionGetForbidden();
        }

        return parent::before();
    }

    protected function hasPermission()
    {
        return $this->admin->hasPermission($this->getModule() . '.' . $this->getPanel() . '.' . $this->action);
    }

    public function actionGetIndex()
    {
        return 'Index';
    }

    protected function useHelper()
    {
        $this->setModule(array_pull($this->params, 1));
        $this->setPanel(array_pull($this->params, 2));
    }
}