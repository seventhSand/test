<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/14/2016
 * Time: 2:22 PM
 */

namespace Webarq\Manager\HTML\Table;


use Illuminate\Contracts\Support\Htmlable;
use Wa;

class BodyManager implements Htmlable
{
    protected $rows = [];

    protected $containers = [];

    public function addRow($container = 'tr', $attributes = [])
    {
        if (is_array($container)) {
            $attributes = $container;
            $container = 'tr';
        }
        $this->containers[] = [$container, $attributes];

        return $this->rows[] = Wa::html('table.row');
    }

    public function toHtml()
    {
        if ([] !== $this->rows) {
            $s = '';
            foreach ($this->rows as $i => $row) {
                $s .= Wa::html('element', $row->toHtml() ,
                        array_get($this->containers, $i . '0' , 'tr'),
                        array_get($this->containers, $i . '1' , []))->toHtml();
            }
            return $s;
        }
    }
}