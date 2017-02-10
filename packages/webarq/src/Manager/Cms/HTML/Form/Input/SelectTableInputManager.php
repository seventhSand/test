<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/18/2017
 * Time: 1:34 PM
 */

namespace Webarq\Manager\Cms\HTML\Form\Input;


use Illuminate\Support\Collection;
use Wa;
use Webarq\Model\NoModel;

class SelectTableInputManager extends SelectInputManager
{
    /**
     * Source config
     *
     * @var array
     */
    protected $sources = [];

    /**
     * @var
     */
    protected $traverse;

    /**
     * Select options
     *
     * @var array
     */
    protected $options = [];

    /**
     * @return mixed
     */
    protected function buildInput()
    {
        if (true === $this->traverse) {
            $this->traverse = 'parent_id';
        }

        $this->buildOptionsFromSource();

        return parent::buildInput();
    }

    /**
     * @return array
     */
    protected function buildOptionsFromSource()
    {
        if (!is_callable($this->options)) {
            $options = $this->sources;

            if (null === ($table = array_pull($options, 'table'))) {
                $table = $this->table->getName();
            }

            if (null === ($column = array_pull($options, 'column'))) {
                $column = $this->table->primaryColumn()->getName();
            }

            if (!is_array($column)) {
                $label = $column;
            } elseif (count($column) === 2) {
                list($column, $label) = $column;
            } else {
                $this->options = ['Column sources config should be an array, and expect to have 2 member'];

                return [];
            }

            $columns = [$column, $label];

            if (null !== $this->traverse) {
                $columns[] = $this->traverse;
            }
            $options['columns'] = $columns;

            $get = NoModel::instance($table)
                    ->optionQueryBuilder($options)
                    ->get();


            if (null === $this->traverse) {
                if ($get->count()) {
                    foreach ($get as $item) {
                        $this->options[$item->{$column}] = $item->{$label};
                    }
                }
            } else {
                $this->makeTraverse($get, $column, $label, $this->traverse);
            }
        }
    }

    /**
     * @param Collection $collection
     * @param mixed $value Column name for option value
     * @param string $label Column name for option label
     * @param string $parent Column name which used for traversing
     */
    protected function makeTraverse(Collection $collection, $value, $label, $parent)
    {
        if ($collection->count()) {
            $options = [];
            foreach ($collection as $row) {
                $options[$row->{$parent}][] = [$row->{$value}, $row->{$label}];
            }

            if ([] !== ($parents = array_pull($options, 0, []))) {
                foreach ($parents as $item) {
                    $this->options[$item[0]] = $item[1];

                    $this->getSubOption($options, $item[0], 1);
                }
            }
        }
    }

    /**
     * @param array $collections
     * @param $parent
     * @param int $level
     */
    protected function getSubOption(array $collections, $parent, $level = 1)
    {
        if ([] !== ($items = array_pull($collections, $parent, []))) {
            foreach ($items as $item) {
                $i = 1;
                while($i) {
                    $item[1] = ' -- ' . $item[1];
                    $i++;
                    if ($i > $level) {
                        break;
                    }
                }
                $this->options[$item[0]] = $item[1];

                $this->getSubOption($collections, $item[0], $level + 1);
            }
        }
    }
}