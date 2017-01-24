<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/20/2017
 * Time: 10:05 AM
 */

namespace Webarq\Manager\Cms\HTML\Form\Input;


use Illuminate\Support\Str;

class AttributeInputManager
{
    protected $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * Insert new class
     *
     * @param $str
     * @return $this
     */
    public function insertClass($str)
    {
        $this->set('class', $str, true);

        return $this;
    }

    /**
     * Set attribute name
     *
     * @param $name
     * @param null $langCode
     */
    public function setName($name, $langCode = null)
    {
        if (null === $langCode) {
            $this->items['name'] = $name;
        } else {
            $names = explode('[', $name, 2);
            $names[0] .= '_' . $langCode;
            if (isset($names[1])) {
                $names[0] .= '[';
            }
            $this->items['name'] = implode('', $names);
        }
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_get($this->items, $key, $default);
    }

    /**
     * Set or Overwrite attribute item
     *
     * @param mixed $key
     * @param mixed $value
     * @param bool $join
     * @return $this
     */
    public function set($key, $value, $join = false)
    {
        if (true === $join && isset($this->items[$key]) && !is_object($this->items[$key])) {
            if (is_array($value)) {
                $this->items[$key] = Arr::merge((array) $this->items[$key], $value, 'join');
            } else {
                $this->items[$key] .= ' ' . $value;
            }
        } else {
            $this->items[$key] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $this->finalize();

        return $this->items;
    }

    /**
     * Finalize attributes
     */
    protected function finalize()
    {
        if (isset($this->items['class'])) {
            $this->items['class'] = Str::filter($this->items['class']);
        }

        if (null !== ($referrer = array_pull($this->items, 'referrer'))) {
            $this->insertClass('referrer')
                ->set('data-referrer-target', $referrer);
        }

// Attribute value should not be array
        foreach ($this->items as $key => &$item) {
            if(is_array($item)) {
                $item = base64_encode(serialize($item));
            } elseif (null === $item || false === $item) {
                unset($this->items[$key]);
            }
        }
    }
}