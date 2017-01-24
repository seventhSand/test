<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/24/2017
 * Time: 1:03 PM
 */

namespace Webarq\Manager\Cms\HTML\Form;


use DB;
use Wa;

class Model
{
    /**
     * @var array
     */
    protected $inputs = [];

    /**
     * @var
     */
    protected $master;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Master row id
     *
     * @var number
     */
    protected $id;

    public function __construct($id, array $inputs, $master)
    {
        $this->id = $id;
        $this->inputs = $inputs;
        $this->master = $master;

        $this->compile();
    }

    /**
     * @todo simplify the logic
     */
    protected function compile()
    {
        if ([] !== $this->inputs) {
            $multilingual = array_pull($this->inputs, 'multilingual-frm-input');

            foreach ($this->inputs as $name => $input) {
                $columns[$input->{'table'}->getName()][$name] = $input->{'column'}->getName();
                if (isset($multilingual[$name]) && $input->isMultilingual()) {
                    
                }
            }

// Master data
            $this->masterData(array_pull($columns, $this->master, []));
// Translation data
        }
    }

    protected function masterData(array $columns)
    {
        $data = $this->rowFinder($this->master, Wa::table($this->master)->primaryColumn()->getName(), $columns);
        if (null !== $data) {
            $this->doPairing($data->first(), $columns);
        }
    }

    protected function rowFinder($table, $whereColumn, array $columns)
    {
        if ([] !== $columns) {
            return DB::table($table)
                    ->select($columns)
                    ->where($whereColumn, $this->id)
                    ->get();
        }
    }

    protected function doPairing($row, array $pairs)
    {
        foreach ($pairs as $input => $column) {
            $this->data[$input] = $row->{$column};
        }
    }

    protected function translationData()
    {

    }

    public function getData()
    {
        return $this->data;
    }
}