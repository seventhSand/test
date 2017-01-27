<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/9/2017
 * Time: 4:01 PM
 */

namespace Webarq\Manager\Cms\HTML\Table\Driver;


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
    protected $sequence = [];

    protected $headers = [];

    /**
     * Create builder instance
     *
     * @param AdminManager $admin
     * @param string $module
     * @param string $panel
     * @param array $headers
     * @param int|number $limit
     * @param array $actions
     */
    public function __construct(AdminManager $admin, $module, $panel, $headers = [], $limit = 2, array $actions = [])
    {
        $this->admin = $admin;
        $this->module = $module;
        $this->panel = $panel;
        $this->limit = $limit;
        $this->actions = $actions;
        $this->headers = $headers;

        $columns = $this->getColumns();

        $this->builder = DB::table($panel)->select([] === $columns ? '*' : $columns)
                ->addSelect(Wa::table($panel)->primaryColumn()->getName());
    }

    /**
     * @return array
     */
    protected function getColumns()
    {
        $columns = [];
        if ([] !== $this->headers ) {
            foreach ($this->headers as $key => $value) {
                $columns[] = is_numeric($key) ? $value : $key;
            }
        }

        return $columns;
    }

    /**
     * @param $sequences
     * @return $this
     */
    public function buildSequence($sequences)
    {
        if (!is_array($sequences)) {
            $sequences = [$sequences];
        }

        foreach ($sequences as $column => $direction) {
            if (is_numeric($column)) {
                $column = $direction;
                $direction = 'asc';
            }

            $this->builder->orderBy($column, $direction);
        }

        return $this;
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
                $item = $this->buildItem($item);
                $item['actionButton'] = $this->buildActions($item);
            }

            return $data['data'];
        }

        return [];
    }

    /**
     * @param array $row
     * @return array
     */
    protected function buildItem($row)
    {
        if (!is_array($row)) {
            $row = (array) $row;
        }
        foreach ($this->headers as $column => $setting) {
            if (is_numeric($column)) continue;
            if (null !== ($modifier = array_get($setting, 'modifier'))) {
                $row[$column] = Wa::modifier($modifier, $row[$column]);
            }
        }

        return $row;
    }

    /**
     * @param array $item
     * @return string
     */
    protected function buildActions(array $item)
    {
        $m = Wa::module($this->module);
        $p = $m->getPanel($this->panel);
        return Wa::panel()->generateActionButton($this->actions, $m, $p, $item);
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