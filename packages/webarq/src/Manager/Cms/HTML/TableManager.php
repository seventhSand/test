<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/22/2016
 * Time: 12:41 PM
 */

namespace Webarq\Manager\Cms\HTML;


use Wa;
use Webarq\Info\PanelInfo;
use Webarq\Manager\AdminManager;
use Webarq\Manager\SetPropertyManagerTrait;

class TableManager extends \Webarq\Manager\HTML\TableManager
{
    use SetPropertyManagerTrait;

    /**
     * @var AdminManager
     */
    protected $admin;

    /**
     * @var PanelInfo
     */
    protected $panel;

    /**
     * Table headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Table sequential
     *
     * @var array
     */
    protected $sequence = [];

    /**
     * @var
     */
    protected $actions;

    /**
     * @var
     */
    protected $src;

    public function __construct(AdminManager $admin, PanelInfo $panel)
    {
        $this->admin = $admin;
        $this->panel = $panel;
        $this->actions = $panel->getActions();

        $this->setUp($panel->getListing());
    }

    protected function setUp(array $attributes = [])
    {
        $this->setPropertyFromOptions($attributes);
        dd($this);
    }

    protected function getColumns()
    {

    }

}