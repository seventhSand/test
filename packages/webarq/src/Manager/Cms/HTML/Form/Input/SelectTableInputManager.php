<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/18/2017
 * Time: 1:34 PM
 */

namespace Webarq\Manager\Cms\HTML\Form\Input;


class SelectTableInputManager extends SelectInputManager
{
    protected $sourceTable = [];

    protected $traverse = [];

    protected $options = [];

    protected function buildInput()
    {
        $this->buildOptionsFromSource();

        return parent::buildInput();
    }

    protected function buildOptionsFromSource()
    {
        if (null === ($table = array_get($this->sourceTable, 'name'))) {
            $table = $this->table->getName();
        }

        if (null === ($column = array_get($this->sourceTable, 'column'))) {
            $column = $this->table->primaryColumn()->getName();
        }

        if (!is_array($column)) {
            $label = $column;
        } else {
            list($column, $label) = $column;
        }

        $get = \DB::table($table)
                ->select($column, $label);

// Check for order column
        $get = $get->get();

        if ($get->count()) {
            foreach ($get as $item) {
                $this->options[$item->{$column}] = $item->{$label};
            }
        }
    }
}