<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/9/2017
 * Time: 4:01 PM
 */

namespace Webarq\Manager\Cms\HTML\Table\Driver;


use Illuminate\Support\Arr;
use Wa;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Webarq\Manager\AdminManager;
use Webarq\Manager\HTML\Table\Driver\DriverAbstractManager;

class PaginateManager extends DriverAbstractManager
{
    /**
     * @var AdminManager
     */
    protected $admin;

    /**
     * @var string
     */
    protected $panel;

    /**
     * @var string
     */
    protected $module;

    /**
     * @var \Illuminate\Database\Query\Builder
     */
    protected $builder;

    /**
     * @var null|number
     */
    protected $limit;

    /**
     * @var LengthAwarePaginator
     */
    protected $get;

    /**
     * @var array
     */
    protected $actions = [];

    /**
     * @var array
     */
    protected $head = [];

    /**
     * Create builder instance
     *
     * @param AdminManager $admin
     * @param string $module
     * @param string $panel
     * @param array $columns
     * @param int|number $limit
     * @param array $actions
     */
    public function __construct(AdminManager $admin, $module, $panel, $columns = [], $limit = 2, array $actions = [])
    {
        $this->admin = $admin;
        $this->module = $module;
        $this->panel = $panel;
        $this->limit = $limit;
        $this->actions = $actions;

// Remove action button from columns
        array_pop($columns);
        $this->head = Arr::unsetAssocKey($columns);
        $this->builder = DB::table($panel)->select($columns);
    }

    /**
     * @inheritdoc
     */
    public function sampling()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getRows()
    {
        $this->get = $this->builder->paginate($this->limit);

        if ($this->get->count()) {
            $data = $this->get->toArray();
            foreach ($data['data'] as &$item) {
                $item = (array)$item;
                $tmp = array_intersect_key($item, array_flip($this->head));
                $tmp['actionButton'] = $this->buildActions($item);
                $item = $tmp;
            }

            return $data['data'];
        }

        return [];
    }

    /**
     * @param array $item
     * @return string
     */
    protected function buildActions(array $item)
    {
        $string = '';

        foreach ($this->actions as $type => $setting) {
            $class = Wa::manager(
                    'cms.HTML!.table.' . $type,
                    $this->admin,
                    $this->module,
                    $this->panel,
                    $item,
                    $setting
            ) ?: Wa::manager(
                    'cms.HTML!.table.button',
                    $this->admin,
                    $this->module,
                    $this->panel,
                    $item,
                    $setting + ['type' => $type]);

            $string .= $class ? $class->toHtml() : '';
        }

        return $string;
    }

    /**
     * @param null|string $view
     * @return string
     */
    public function paginate($view = null)
    {
        if ($this->get instanceof LengthAwarePaginator) {
            return $this->get->render($view);
        }
    }
}