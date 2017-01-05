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

class RowManager implements Htmlable
{
    public static $isHead = false;

    protected $cells = [];

    public function addCell($value, $container = null, $attributes = [])
    {
        if (is_array($container)) {
            $attributes = $container;
            $container = null;
        }
        if (!isset($container)) {
            $container = self::$isHead ? 'th' : 'td';
        }


        $this->cells[] = Wa::html('element', $value, $container, $attributes);

        return $this;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if ([] !== $this->cells) {
            $s = '';
            foreach ($this->cells as $cell) {
                $s .= $cell->toHtml();
            }
            return $s;
        }
    }
}