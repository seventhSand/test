<?php
/**
 * Created by PhpStorm
 * Date: 30/12/2016
 * Time: 12:51
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Manager\Cms\HTML\Form;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class RulesManager
 *
 * Generate input rules from input attributes
 *
 * @package Webarq\Manager\Cms\HTML\Form
 */
class RulesManager
{
    /**
     * Input rules item
     *
     * @var array
     */
    protected $items = [];

    /**
     * Input attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Input value
     *
     * @var mixed
     */
    protected $value;

    /**
     * Create RulesManager instance
     *
     * Calling all methods which are ended with "Rule" string
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;

        foreach (get_class_methods($this) as $method) {
            if ('Rule' === substr($method, -4)) {
                $this->{$method}();
            }
        }
        $this->finalize();
    }

    /**
     * Finalize collected rule, do auto correction
     */
    protected function finalize()
    {
// Check if rules attributes is given
        if (null !== ($rule = array_get($this->attributes, 'rules'))) {
            $items = explode('|', $rule);
            foreach ($items as $item) {
                $item = explode(':', $item, 2);
                $this->items[$item[0]] = array_get($item, 1, '');
            }
        }
// @todo find an eloquent way to handle input array validation
        if (isset($this->attributes['multiple']) || Arr::inArray($this->attributes, 'multiple')) {

            foreach (['numeric', 'integer', 'max', 'min'] as $key) {
                if (null !== ($x = array_pull($this->items, $key))) {
                    $this->items[$key . 'Array'] = $x;
                }
            }
        }
    }

    /**
     * Rule item getter
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getItem($key, $default = null)
    {
        return array_get($this->items, $key, $default);
    }

    /**
     * Rule item setter
     *
     * @param $key
     * @param $str
     */
    public function setItem($key, $str)
    {
        $this->items[$key] = $str;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Convert rule items into laravel string format
     *
     * @param string $separator
     * @return string
     */
    public function toString($separator = '|')
    {
        $string = 'bail';
        foreach ($this->items as $key => $option) {
            $string .= $separator . (is_numeric($key) ? '' : $key);
            if ('' !== $option) {
                if (!is_numeric($key)) {
                    $string .= ':';
                }
                $string .= $option;
            }
        }
        return trim($string, $separator);
    }

    /**
     * Collect require rule
     */
    protected function requireRule()
    {
        if (true === $this->getAttribute('notnull')) {
            $this->items['required'] = '';
        }
    }

    /**
     * Get attribute item by given key
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getAttribute($key, $default = null)
    {
        return array_get($this->attributes, $key, $default);
    }

    /**
     * Collect numeric rule
     */
    protected function numericRule()
    {
        if (null !== $this->getAttribute('numeric')
                || str_contains($this->getAttribute('db-type', 'not-found'), 'int')
        ) {
            $this->items['numeric'] = '';
        }
    }

    /**
     * Collect max rule
     */
    protected function maxRule()
    {
        if (null !== ($max = $this->getAttribute('max', $this->getAttribute('length'))) && is_numeric($max)) {
            $this->items['max'] = $max;
        }
    }

    /**
     * Collect min rule
     */
    protected function minRule()
    {
        if (null !== ($min = $this->getAttribute('min')) && is_numeric($min)) {
            $this->items['min'] = $min;
        }
    }

    /**
     * Collect unique rule
     */
    protected function uniqueRule()
    {
        if (null !== ($unique = $this->getAttribute('unique'))) {
            if (true === $unique) {
                $unique = $this->getAttribute('table')->getName() . ',' . $this->getAttribute('column')->getName();
                if (null !== $this->value) {
                    $unique .= ',' . $this->value;
                    dd($unique);
                }
            }

            $this->items['unique'] = $unique;
        }
    }

    /**
     * Collect uniques rule
     */
    protected function uniquesRule()
    {

    }

    protected function fileRule()
    {
        if (null !== ($files = $this->getAttribute('file')) && is_array($files)) {
            array_forget($files, ['upload-dir', 'resize', 'prefix']);

            foreach ($files as $key => $value) {
                if (is_numeric($key) || 'type' === $key) {
                    $this->items[] = $value;
                } else {
                    $this->items[$key] = (is_array($value) ? implode(',', $value) : $value);
                }
            }
        }
    }
}