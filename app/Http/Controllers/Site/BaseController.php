<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 6:22 PM
 */

namespace App\Http\Controllers\Site;


use App\Http\Controllers\Webarq;

class BaseController extends Webarq
{

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->themes = config('webarq.site.themes', 'default');

        parent::__construct($params);
    }

    public function actionGetIndex()
    {

    }
}