<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/22/2016
 * Time: 12:41 PM
 */

namespace Webarq\Manager\Cms\HTML;


use Illuminate\Support\Arr;
use Wa;
use Webarq\Info\ModuleInfo;
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
    protected $pagination = [2, 'webarq.listing.cms.pagination'];

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

// Set class property from settings
        $settings = $panel->getListing();
        $this->setPropertyFromOptions($settings);

// Pagination property should be array
        if (!is_array($this->pagination)) {
            $this->pagination = [$this->pagination, 'webarq.listing.cms.pagination'];
        }

        $this->groupingActions();
    }

    /**
     * Grouping actions by placement setting
     */
    protected function groupingActions()
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
// Build header action, while create actions should be on header
        $html = $this->buildHeaderActions(array_get($this->actions, 'header', []));

        $this->setup();

        $html .= parent::toHtml();

        return $html . $this->driver->paginate($this->pagination[1]);
    }

    /**
     * @param array $actions
     * @return string
     */
    protected function buildHeaderActions(array $actions)
    {
        $string = '';

        foreach ($actions as $type => $setting) {
            $class = Wa::manager(
                    'cms.HTML!.table.' . $type,
                    $this->admin,
                    $this->module->getName(),
                    $this->panel->getName(),
                    [],
                    $setting
            ) ?: Wa::manager(
                    'cms.HTML!.table.button',
                    $this->admin,
                    $this->module->getName(),
                    $this->panel->getName(),
                    [],
                    $setting + ['type' => $type]);

            $string .= $class ? $class->toHtml() : '';
        }

        return $string;
    }

    /**
     * Table setup
     */
    protected function setup()
    {
        $this->makeHeader();

        $this->setupDriver();
    }

    protected function makeHeader()
    {
        if ([] !== ($groups = array_get($this->headers, 'columns', []))) {
            if (!is_array($groups[0])) {
                $groups = [$groups];
            }

            $this->addHead(array_get($this->headers, 'container'));

            foreach ($groups as $i => $columns) {
                $row = $this->head->addRow(array_pull($columns, 'container'));

                foreach ($columns as $column => $attr) {
                    if (is_numeric($column)) {
                        $column = $attr;
                        $attr = [];
                    }

// Guarded column should not be shown on listing
                    if (null === array_pull($attr, 'guarded')) {
                        $this->columns[] = $column;
                        $row->addCell(trans('webarq.' . $column), array_pull($attr, 'container'), $attr);
                    } else {
                        $this->columns[$column] = $column;
                    }
                }

// Add action column
                if (0 === $i) {
                    $row->addCell(trans('webarq.actionButton'), ['rowspan' => count($groups)]);
                }
            }
            $this->columns[] = 'actionButton';
        }
    }

    protected function setupDriver()
    {
        if (!isset($this->driver)) {
            $this->driver = Wa::manager(
                    'cms.HTML!.table.driver.paginate',
                    $this->admin,
                    $this->module->getName(),
                    $this->panel->getName(),
                    $this->columns,
                    $this->pagination[0],
                    array_get($this->actions, 'listing', [])
            )->buildSequence($this->sequence);

// Remove guarded columns
            $this->columns = Arr::unsetAssocKey($this->columns);
        } elseif (is_array($this->driver) && [] !== ($driver = $this->driver)) {
            $type = array_shift($driver);

            $this->driver($type, $driver, Wa::getGhost());
        }
    }

    protected function buildAction($type)
    {
        $attr = array_pull($this->actions, $type, []) + [
                        'module' => $this->panel->getModule(),
                        'panel' => $this->panel->getName(),
                        'type' => $type
                ];

        return Wa::manager('cms.HTML!.table.' . $type, $this->admin, $attr)
                ?: Wa::manager('cms.HTML!.table.button', $this->admin, $attr);
    }
}