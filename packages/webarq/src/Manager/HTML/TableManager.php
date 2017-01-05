<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/14/2016
 * Time: 1:46 PM
 */

namespace Webarq\Manager\HTML;


use Illuminate\Contracts\Support\Htmlable;
use Wa;
use Webarq\Manager\HTML\Table\BodyManager;
use Webarq\Manager\HTML\Table\RowManager;

class TableManager implements Htmlable
{
    /**
     * @var object Webarq\Manager\HTML\ContainerManager
     */
    protected $title;

    protected $head;

    protected $body;

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
// Row type
        RowManager::$isHead = 'head' === $type;
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

            $this->{$type} = Wa::html('table.body');
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
     * @param string $container
     * @param array $attributes
     * @return mixed
     */
    public function addBody($container = 'tbody', $attributes = [])
    {
        return $this->addRows(array_merge(['body'], func_get_args()));
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