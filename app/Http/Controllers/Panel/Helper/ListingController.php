<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 7:06 PM
 */

namespace App\Http\Controllers\Panel\Helper;


use App\Http\Controllers\Panel\BaseController;

class ListingController extends BaseController
{
    public function before()
    {
        if (!is_object($this->module) || !is_object($this->panel)) {
            $this->setModule($this->getParam(1));
            $this->setPanel($this->getParam(2));
            if (!is_object($this->module) || !is_object($this->panel)) {
                return $this->actionGetForbidden();
            }
        }

        return parent::before();
    }

    public function actionGetIndex()
    {
        return \Wa::manager('cms.HTML!.table', $this->admin, $this->panel)
                ->toHtml();
    }
}