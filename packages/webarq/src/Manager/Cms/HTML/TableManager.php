<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/22/2016
 * Time: 12:41 PM
 */

namespace Webarq\Manager\Cms\HTML;


use Wa;
use Webarq\Info\ModuleInfo;
use Webarq\Info\PanelInfo;
use Webarq\Manager\AdminManager;
use Webarq\Manager\Cms\HTML\Table\Driver\AbstractManager;
use Webarq\Manager\SetPropertyManagerTrait;

class TableManager extends \Webarq\Manager\HTML\TableManager
{
    use SetPropertyManagerTrait;

    /**
     * @var AdminManager
     */
    protected $admin;

    /**
     * @var ModuleInfo
     */
    protected $module;

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
    protected $pagination = 10;

    /**
     * @var string
     * @todo
     */
    protected $themes = 'default';

    /**
     * @var
     */
    protected $table;

    /**
     * Create CMS\TableManager instance
     *
     * @param AdminManager $admin
     * @param ModuleInfo $module
     * @param PanelInfo $panel
     */
    public function __construct(AdminManager $admin, ModuleInfo $module, PanelInfo $panel)
    {
        $this->admin = $admin;
        $this->module = $module;
        $this->panel = $panel;
        $this->actions = $panel->getActions();

        $settings = $panel->getListing();
        $this->setPropertyFromOptions($settings);

        $this->collectPagination();

        $this->collectActions();
    }

    protected function collectPagination()
    {
// Pagination property should be array
        if (!is_array($this->pagination)) {
            $this->pagination = [
                    $this->pagination,
                    Wa::getThemesView(config('webarq.system.themes', 'default'), 'common.pagination', false)
            ];
        }

    }

    /**
     * Collect actions and grouping them according to placement setting
     */
    protected function collectActions()
    {
        if ([] !== $this->actions) {
// Create action should be on listing header
            $groups['header']['create'] = array_pull($this->actions, 'create', []);

            foreach ($this->actions as $name => $setting) {
                if (is_numeric($name)) {
                    $name = $setting;
                    $setting = [];
                }
// By default, action will be put on listing row item column action
                $placement = array_pull($setting, 'placement', 'listing');
                if (is_array($placement)) {
                    foreach ($placement as $key) {
                        $groups[$key][$name] = $setting;
                    }
                } else {
                    $groups[$placement][$name] = $setting;
                }
            }
            $this->actions = $groups;
        }
    }

    /**
     * Build table HTML element
     *
     * @return string
     */
    public function toHtml()
    {
// Build header action, while create actions should be on header;
        $html = Wa::panel()->generateActionButton(
                array_get($this->actions, 'header', []), $this->module, $this->panel);

        $this->buildHeader(array_get($this->headers, 'columns', []), $headers, $columns);

        $this->loadDriver($columns);

        $this->buildBody($headers);

        $html .= parent::toHtml();

        $html .= $this->driver->paginate($this->pagination[1]);

        return $html;
    }

    /**
     * @param array $groups
     * @param array|null $headers
     * @param array|null $columns
     */
    protected function buildHeader(array $groups = [], array &$headers = null, array &$columns = null)
    {
        $columns = [];
        if ([] !== $groups) {
// All header should be grouped in case we need to split one header into smaller column
            if (!isset($groups[0]) || !is_array($groups[0])) {
                $groups = [$groups];
            }

            $this->addHead(array_get($this->headers, 'container'));

            foreach ($groups as $i => $items) {
                $row = $this->head->addRow(array_pull($items, 'container'));

                foreach ($items as $column => $attr) {
                    if (is_numeric($column)) {
                        $column = $attr;
                        $attr = [];
                    }

// Guarded column should not be shown on listing
                    if (null === array_pull($attr, 'guarded')) {
                        $headers[$column] = $attr;

                        $row->addCell(Wa::trans('webarq.title.' . $column), array_pull($attr, 'container'), $attr);
                    }

                    $columns[] = $column;
                }

// Add action column
                if (0 === $i && [] !== array_get($this->actions, 'listing', [])) {
                    $row->addCell(Wa::trans('webarq.title.actionButton'), ['rowspan' => count($groups)]);
                }
            }
        }
    }

    protected function loadDriver(array $columns)
    {
// Got a data driver
        if (null === $this->driver) {
            $this->driver = Wa::manager('cms.HTML!.table.driver.paginate',
                    Wa::table($this->table ?: $this->panel->getTable()), $columns, $this->pagination[0])
                    ->buildSequence($this->sequence);
        } else {
            $args = $this->driver;
            if (!is_array($args)) {
                $driver = $args;
                $args = [];
            } else {
                $driver = array_shift($args);
            }

            $sampling = false;

            if (true === array_get($args, 0)) {
                $sampling = array_pull($args, 0);
            }

            $this->setDriver(Wa::load('manager.html.table.driver.' . $driver, $args, Wa::getGhost()), $sampling);
        }
    }

    /**
     * @param array|null $headers
     */
    protected function buildBody(array $headers = null)
    {
        if ($this->driver instanceof AbstractManager && [] !== ($rows = $this->driver->getRows())) {
            if (null === $headers) {
                foreach ($rows[0] as $key => $value) {
                    $headers[$key] = [];
                }

                $this->buildHeader($headers);
            }

            $this->addBody();

            foreach ($rows as $item) {
                $row = $this->body->addRow();
                foreach ($headers as $key => $setting) {
                    if (null !== ($modifier = array_get($setting, 'modifier'))) {
                        $row->addCell(Wa::modifier($modifier, $item->{$key}));
                    } else {
                        $row->addCell($item->{$key});
                    }
                }

                if ([] !== ($actions = array_get($this->actions, 'listing', []))) {
                    $row->addCell(Wa::panel()->generateActionButton(
                            $actions, $this->module, $this->panel, (array)$item)
                    );
                }
            }
        }
    }
}