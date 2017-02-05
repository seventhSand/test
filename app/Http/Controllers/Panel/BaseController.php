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
use Wa;
use Webarq\Info\ModuleInfo;
use Webarq\Info\PanelInfo;

class BaseController extends Webarq
{
    /**
     * @var \Webarq\Manager\AdminManager
     */
    protected $admin;

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->themes = config('webarq.system.themes', 'default');

        parent::__construct($params);

        $this->admin = Auth::user();
        view()->share(['admin' => $this->admin, 'shareBreadCrumbAction' => $this->getParam('action')]);
    }

    /**
     * Called from routing file
     *
     * @return mixed
     */
    public function escape()
    {
        if (null === $this->admin) {
            if ('login' !== $this->action && 'auth' !== $this->controller) {
                return redirect(URL::panel('system/admins/auth/login'));
            }
        } elseif (null !== \Request::segment(2)
                && (
                        !$this->isAccessible() || !is_object($this->module) || !is_object($this->panel))
        ) {
            return $this->actionGetForbidden();
        }

        view()->share(['shareModule' => $this->module, 'sharePanel' => $this->panel]);

        return parent::escape();
    }

    /**
     * @return mixed
     */
    protected function isAccessible()
    {
        return $this->module instanceof ModuleInfo && $this->panel instanceof PanelInfo
        && Wa::panel()->isAccessible($this->module, $this->panel, $this->action);
    }

    /**
     * @return string
     */
    public function actionGetIndex()
    {
        return $this->actionGetForbidden();
    }

    public function after()
    {
// Send session transaction in to layout
        $this->layout->with('alerts', \Session::get('transaction', []));

        return parent::after();
    }

    protected function setTransactionMessage($message, $type = 'warning')
    {
        \Session::flash('transaction', is_array($message) ? $message : [$message, $type]);
    }
}