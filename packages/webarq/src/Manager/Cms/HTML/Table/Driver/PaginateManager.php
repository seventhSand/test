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
use Webarq\Info\TableInfo;

class PaginateManager extends AbstractManager
{

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
    protected $sequence = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Create builder instance
     *
     * @param TableInfo $table
     * @param array $columns
     * @param int|number $limit
     */
    public function __construct(TableInfo $table, array $columns = [], $limit = 2)
    {
        $this->limit = $limit;

        $this->builder = DB::table($table->getName())->select([] === $columns ? '*' : $columns)
                ->addSelect($table->primaryColumn()->getName());
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
     * @param array $columns
     * @param $query
     * @return $this
     */
    public function buildSearch(array $columns, $query)
    {
        if (null !== $query) {
            foreach ($columns as $column) {
                if (is_array($column)) {

                } else {
                    $this->builder->where($column, 'like', '%' . $query . '%');
                }
            }
        }
        return $this;
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

    /**
     * @inheritdoc
     */
    public function getRows()
    {
        $this->get = $this->builder->paginate($this->limit);

        if ($this->get->count()) {
            return array_get($this->get->toArray(), 'data', []);
        }

        return [];
    }
}