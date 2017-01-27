<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 7:03 PM
 */

namespace App\Http\Controllers\Panel\System\Admins;


class FormController extends \App\Http\Controllers\Panel\Helper\FormController
{
    protected $idSegment = 1;

    public function actionPostCreate()
    {
        parent::actionPostCreate();

        $this->ids();
    }
}