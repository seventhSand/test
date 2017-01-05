<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 6:22 PM
 */

namespace App\Http\Controllers\Site;


class BaseController
{

    public function actionGetNoMethod()
    {
//        return view('webarq.errors.405');
        return $this->forbidden();
    }

    protected function forbidden()
    {
        return view('webarq.errors.403');
    }
}