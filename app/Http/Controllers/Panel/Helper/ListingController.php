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
    /**
     * @var \Webarq\Manager\Cms\HTML\TableManager
     */
    protected $builder;

    protected $layout = 'webarq.tcl-panel.layout.listing';

    public function actionGetIndex()
    {
        $this->builder = \Wa::manager('cms.HTML!.table', $this->admin, $this->module, $this->panel);
    }

    public function after()
    {
        $this->layout->right = $this->builder->toHtml();

        return parent::after();
    }
}