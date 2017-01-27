<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/26/2017
 * Time: 11:21 AM
 */

namespace App\Http\Controllers\Panel\Helper;


use App\Http\Controllers\Panel\BaseController;

class DeleteController extends BaseController
{
    public function actionGetIndex()
    {
//        dd($this->module, $this->panel->getAction('delete'));
        $this->layout->{'rightSection'} = 'Delete ...';
    }
}