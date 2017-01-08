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
     * Pagination settings, array [limit, view]
     *
     * @var array|number
     */
    protected $paginator = [2, 'webarq.listing.cms.paginator'];

    /**
     * Create CMS\TableManager instance
     *
     * @param AdminManager $admin
     * @param PanelInfo $panel
     */
    public function __construct(AdminManager $admin, PanelInfo $panel)
    {
        $this->admin = $admin;
        $this->panel = $panel;
        $this->actions = $panel->getActions();

// Set class property from settings
        $settings = $panel->getListing();
        $this->setPropertyFromOptions($settings);

        if (!is_array($this->paginator)) {
            $this->paginator = [$this->paginator, 'webarq.listing.cms.paginator'];
        }
    }

    /**
     * Build table HTML element
     *
     * @return string
     */
    public function toHtml()
    {
        $this->setup();

        $s = parent::toHtml();

        return $s . $this->driver->paginator($this->paginator[1]);
    }

    /**
     * Table setup
     */
    protected function setup()
    {
        $this->makeHeader();

        if (!isset($this->driver)) {
            $this->driver = ['paginator', $this->panel->getName(), $this->columns, $this->paginator[0]];
        }

        $this->setupDriver();
    }

    protected function makeHeader()
    {
        if ([] !== ($groups = array_get($this->headers, 'columns', []))) {
            $container = array_get($this->headers, 'container');

            if (!is_array($groups[0])) {
                $groups = [$groups];
            }

            $head = $this->addHead($container);

            foreach ($groups as $columns) {
                $row = $head->addRow(array_pull($columns, 'container'));
                foreach ($columns as $column => $attr) {
                    if (is_numeric($column)) {
                        $column = $attr;
                        $attr = [];
                    }

                    $this->columns[] = $column;

                    $row->addCell(trans('webarq.'. $column), array_pull($attr, 'container'), $attr);
                }
            }
        }
    }

    protected function setupDriver()
    {
        if (is_array($this->driver) && [] !== ($driver = $this->driver)) {
            $type = array_shift($driver);

            $this->driver($type, $driver, Wa::getGhost());
        }
    }

}