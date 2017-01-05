<?php
/**
 * Created by PhpStorm
 * Date: 29/12/2016
 * Time: 15:27
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Manager\Cms\Query;


use Wa;
use Webarq\Manager\AdminManager;
use Webarq\Manager\Cms\HTML\Form\InputManager;

abstract class CrudAbstractManager
{
    /**
     * Database transaction type: insert, update, delete, or select
     *
     * @var
     */
    protected $type;

    /**
     * Current login user
     *
     * @var object AdminManager
     */
    protected $admin;

    /**
     * Post data
     *
     * @var array
     */
    protected $post = [];

    /**
     * Data pairing
     *
     * @var array
     */
    protected $pairs = [];

    /**
     * Master table
     *
     * @var null|string
     */
    protected $master;

    /**
     * Create QueryManager instance
     *
     * @param AdminManager $admin
     * @param array $pairs
     * @param array $post
     * @param null $master
     */
    public function __construct(AdminManager $admin, array $post, array $pairs, $master = null)
    {
        $this->admin = $admin;
        $this->post = $post;
        $this->master = $master;

        $this->pairData($pairs);

        $this->setMaster($master);
    }

    /**
     * Bind post data with pair table
     *
     * @param array $pairs
     */
    protected function pairData(array $pairs)
    {
        foreach ($pairs as $input => $options) {
            list($manager, $guarded) = $options;
            if (null !== ($column = $manager->getColumn()) && !$column->isGuarded()) {
                $this->setPair($manager, $guarded);
            }
        }
    }

    /**
     * @param InputManager $input
     * @param bool $guarded
     */
    protected function setPair(InputManager $input, $guarded = true)
    {
// While inserting row, all guarded input should not have post value
        $value = true === $guarded && 'insert' === $this->type
                ? $input->getRules()->getAttribute('guarded-value', $input->getDefault())
                : array_get($this->post, $input->getName());
        if (!is_array($value)) {
            $this->pairs[$input->getTable(false)][$input->getColumn(false)] =
                    $this->modifyValue($input->getModifier(), $value);
        } else {
            if (!isset($this->pairs[$input->getTable(false)])) {
                $this->pairs[$input->getTable(false)] = [];
            }

            $items = &$this->pairs[$input->getTable(false)];

            foreach ($value as $i => $item) {
                $items[$i][$input->getColumn(false)] = $this->modifyValue($input->getModifier(), $item);
            }
        }
    }

    /**
     * Modify value based on modifier config
     *
     * @param $modifier
     * @param $string
     * @return mixed
     */
    protected function modifyValue($modifier, $string)
    {
        if (isset($modifier)) {
            return Wa::load('manager.value modifier')->{$modifier}($string);
        }
        return $string;
    }

    protected function setMaster($master)
    {
        if (!isset($master)) {
            foreach (array_keys($this->pairs) as $table) {
                if (!isset($master) || 0 === strpos($master, str_singular($table))) {
                    $master = $table;
                }
            }
        }

        $this->master = $master;
    }

    abstract public function execute();

    /**
     * Get table model when available
     *
     * @param $table
     * @return mixed
     */
    protected function model($table)
    {
        $model = Wa::load('model.' . $table);

        if (!is_null($model) && method_exists($model, 'insert')) {
            return $model;
        }
    }

    protected function checkForCreateUpdateTime(array &$row, $table, $column)
    {
        if (null !== ($column = Wa::table($table)->getColumn($column))) {
            switch ($column->getType()) {
                case 'date':
                    $value = date('Y-m-d');
                    break;
                case 'time':
                    $value = date('H:i:s');
                    break;
                default:
                    $value = date('Y-m-d H:i:s');
                    break;
            }
            $row[$column->getName()] = $value;
        }
    }
}