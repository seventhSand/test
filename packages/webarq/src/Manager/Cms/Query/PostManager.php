<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/18/2017
 * Time: 3:47 PM
 */

namespace Webarq\Manager\Cms\Query;


use Illuminate\Database\Eloquent\Model;
use Wa;
use Webarq\Info\TableInfo;
use Webarq\Manager\AdminManager;
use Webarq\Model\NoModel;

class PostManager
{
    protected $admin;

    /**
     * @var array
     */
    protected $post = [];

    /**
     * Transaction type, create|edit
     *
     * @var
     */
    protected $formType;

    /**
     * Transaction master table
     *
     * @var string
     */
    protected $master;

    /**
     * @param AdminManager $admin
     * @param array $post
     * @param null $master
     */
    public function __construct(AdminManager $admin, array $post, $master = null)
    {
        $this->admin = $admin;
        $this->post = $post;

        $this->setMaster($master);
    }

    /**
     * @param $master
     */
    protected function setMaster($master)
    {
        if ([] !== $this->post) {
            foreach ($this->post as $table => $rows) {
                if (!isset($master) || 0 === strpos($master, str_singular($table))) {
                    $master = $table;
                }
            }
        }

        $this->master = $master;
    }

    /**
     * @param TableInfo $table
     * @param array $row
     */
    protected function addCreateTime(TableInfo $table, array &$row)
    {
        if (null !== $table->getCreateTimeColumn() && !isset($row[$table->getCreateTimeColumn()])) {
            $row[$table->getCreateTimeColumn()] = date('Y-m-d H:i:s');
        }
    }

    /**
     * @param TableInfo $table
     * @param array $row
     * @return array
     */
    protected function addUpdateTime(TableInfo $table, array &$row)
    {
        if (null !== $table->getUpdateTimeColumn() && !isset($row[$table->getUpdateTimeColumn()])) {
            $row[$table->getUpdateTimeColumn()] = date('Y-m-d H:i:s');
        }
    }

    protected function initiateModel($table, $primary = null)
    {
        return NoModel::instance($table, $primary ?: Wa::table($table)->primaryColumn()->getName());
    }

    protected function rowBinder(Model $model, array $row)
    {
        foreach ($row as $column => $value) {
            $model->{$column} = $value;
        }
    }
}