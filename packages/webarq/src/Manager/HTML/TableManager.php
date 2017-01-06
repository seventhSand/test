<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/14/2016
 * Time: 1:46 PM
 */

namespace Webarq\Manager\HTML;


use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Wa;
use Webarq\Manager\HTML\Table\BodyManager;
use Webarq\Manager\HTML\Table\Driver\DriverAbstractManager;

class TableManager implements Htmlable
{
    /**
     * @var object Webarq\Manager\HTML\ContainerManager
     */
    protected $title;

    /**
     * @var object Webarq\Manager\HTML\Table\BodyManager
     */
    protected $head;

    /**
     * @var object Webarq\Manager\HTML\Table\BodyManager
     */
    protected $body;

    /**
     * @var object Webarq\Manager\HTML\Table\BodyManager
     */
    protected $foot;

    /**
     * Default containers
     *
     * @var array
     */
    protected $containers = [
            'table' => ['table', []],
            'head' => ['thead', []],
            'body' => ['tbody', []],
            'foot' => ['tfoot', []]
    ];

    /**
     * Data driver
     *
     * @var mixed
     */
    protected $driver;

    public function __construct($title = null, array $heads = [])
    {
        if (isset($title)) {
            if (is_array($title)) {
                $heads = $title;
                $title = null;
            } else {
                $this->setTitle($title);
            }
        }

        if ([] !== $heads) {
            $manager = $this->addHead()->addRow();
            foreach ($heads as $key => $setting) {
                if (is_numeric($key)) {
                    $key = $setting;
                    $setting = [];
                }
                $manager->addCell($key, $setting);
            }
        }
    }

    /**
     * @param $title
     * @param string $container
     * @param array $attr
     * @return $this
     */
    public function setTitle($title, $container = 'h3', array $attr = [])
    {
        $this->title = Wa::html('element', $title, $container, $attr);

        return $this;
    }

    /**
     * @param string $container
     * @param array $attributes
     * @return mixed
     */
    public function addHead($container = 'thead', $attributes = [])
    {
        return $this->addRows(array_merge(['head'], func_get_args()));
    }

    /**
     * @param array $args Array of [$type, $container, $attributes] or [$type, $callback, $container, $attributes]
     * @return mixed
     */
    public function addRows(array $args = [])
    {
        $type = $args[0];
        $container = array_get($args, 1);
        $attributes = array_get($args, 2, []);

        if (is_callable($container) && !is_string($container)) {
            $callback = $container;
            $container = array_get($args, 2, 't' . $type);
            $attributes = array_get($args, 3, []);
        }

        if (is_array($container)) {
            $attributes = $container;
            $container = 't' . $type;
        }

        if (!isset($this->{$type})) {
            if (isset($container)) {
                $this->setContainer($type, $container, $attributes);
            }

            $this->{$type} = Wa::html('table.body', $type);
        }

        if (isset($callback)) {
            $callback($this->{$type});

            return $this;
        }

        return $this->{$type};
    }

    /**
     * @param $key
     * @param $value
     * @param array $attributes
     * @return $this
     */
    public function setContainer($key, $value, array $attributes = [])
    {
        if (!is_array($key)) {
            $this->containers[$key] = [$value, $attributes];
        } else {
            $this->containers = $key + $this->containers;
        }

        return $this;
    }

    /**
     * To use driver sampling data give bool true in to $arg
     *
     * @param string $type
     * @param mixed $args [, ... $args]
     * @return mixed
     */
    public function driver($type, $args = null)
    {
        if (isset($args)) {
            $args = func_get_args();
            $type = array_shift($args);
        }

        return $this->driver = Wa::load('manager.html.table.driver.' . $type, $args, Wa::getGhost());
    }

    /**
     * @param string $container
     * @param array $attributes
     * @return mixed
     */
    public function addFoot($container = 'tfoot', $attributes = [])
    {
        return $this->addRows(array_merge(['foot'], func_get_args()));
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if (null !== $this->driver && $this->driver instanceof DriverAbstractManager) {
            $this->compileDriver();
        }

        $str = '';
        if (isset($this->title)) {
            $str .= $this->title->toHtml();
        }

        $rowHtml = '';
        foreach (['head', 'body', 'foot'] as $key) {
            $rowHtml .= $this->makeRows($key);
        }
        $str .= Wa::html('element', $rowHtml, array_get($this->containers, 'table.0', 'table'),
                array_get($this->containers, 'table.1', []))->toHtml();

        return $str;
    }

    /**
     * Set head, body, and footer based on driver data
     */
    protected function compileDriver()
    {
// Reset all data
        if ([] !== $this->driver->getData('head')) {
            $head = $this->addHead()->addRow();
            foreach ($this->driver->getData('head') as $column => $attributes) {
                if (is_numeric($column)) {
                    $column = $attributes;
                    $attributes = [];
                } elseif (!is_array($attributes)) {
                    $attributes = [];
                }
                $head->addCell($column, $attributes);
            }


            $this->addBody();
            if ([] !== $this->driver->getData('rows')) {
// Provided data could be in disordered state, so we should found the column value by its column name
                if (Arr::isAssocAbs($this->driver->getData('head'))) {
                    foreach ($this->driver->getData('rows') as $row) {
                        $handler = $this->body->addRow();
                        foreach ($this->driver->getData('head') as $column) {
                            $handler->addCell(array_get($row, $column));
                        }
                    }
                } else {
                    foreach ($this->driver->getData('rows') as $row) {
                        $handler = $this->body->addRow();
                        foreach ($row as $value) {
                            $handler->addCell($value);
                        }
                    }
                }
            } else {
                $this->body->addRow()->addCell('No Data', ['colspan' => count($this->driver->getData('head'))]);
            }
        }
    }

    /**
     * @param string $container
     * @param array $attributes
     * @return mixed
     */
    public function addBody($container = 'tbody', $attributes = [])
    {
        return $this->addRows(array_merge(['body'], func_get_args()));
    }

    /**
     * @param $type
     * @return string
     */
    private function makeRows($type)
    {
        if (isset($this->{$type}) && $this->{$type} instanceof BodyManager) {
            return Wa::html(
                    'element',
                    $this->{$type}->toHtml(),
                    array_get($this->containers, $type . '.0', 't' . $type),
                    array_get($this->containers, $type . '.1', [])
            )->toHtml();
        }
    }
}