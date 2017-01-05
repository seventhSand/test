<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/8/2016
 * Time: 4:37 PM
 */

namespace Webarq\Manager\HTML\Form;


use Illuminate\Contracts\Support\Htmlable;
use Webarq\Manager\HTML\ElementManager;

class InputManager implements Htmlable
{
    protected $input;

    /**
     * @var object ContainerManager
     */
    protected $label;

    /**
     * @var object ContainerManager
     */
    protected $info;

    /**
     * Default container
     *
     * @var array
     */
    protected $containers = [
            'input' => '<div class="form-group"></div>',
            'info' => '<small class="control-info form-text text-muted"></small>',
            'label' => '<label class="control-label"></label>'
    ];

    /**
     * @param array $args
     * @param null $label
     * @param null $info
     * @param string|array $container
     */
    public function __construct(array $args, $label = null, $info = null, $container = '<div class="form-group"></div>')
    {
        foreach ($args as $i => $arg) {
            if (!is_string($arg) && is_callable($arg)) {
// Remove callback from $args
                unset($args[$i]);
// $args should only contain one callback item
                break;
            }
        }
// Shift input type
        $type = array_shift($args);
// Set input by calling laravel form type method
        $this->input = call_user_func_array(array(app('form'), $type), $args);
// Warning!!!
// Do not change code sequence
        $this->setContainer($container, 'input');
        $this->setLabel($label ? : array_get($args, 0));
        $this->setInfo($info);
        if (is_callable($arg)) {
            $arg($this);
        }
    }

    /**
     * Decoration function.
     * Set container decoration
     *
     * @param mixed $value
     * @param string $key
     * @return InputManager
     */
    public function setContainer($value, $key = 'input')
    {
        if (!is_null($value)) {
            if (!is_array($value)) {
                $this->containers[$key] = $value;
            } else {
                $this->containers = $value + $this->containers;
            }
        }

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Decoration function.
     * Set label decoration
     *
     * @param mixed $value
     * @param null|string $container Html tag name or full html tag (with any attributes)
     * @return InputManager
     */
    public function setLabel($value, $container = null)
    {
        $this->label = new ElementManager($value, $container ? : $this->containers['label']);

        return $this;
    }

    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Decoration function.
     * Set info decoration
     *
     * @param mixed $value
     * @param string $container Html tag name or full html tag (with any attributes)
     * @return InputManager
     */
    public function setInfo($value, $container = null)
    {
        $this->info = new ElementManager($value, $container ? : $this->containers['info']);

        return $this;
    }

    public function toHtml()
    {
        if (isset($this->label) && $this->label instanceof ElementManager) {
            $this->label = $this->label->toHtml();
        }
        if (isset($this->info) && $this->info instanceof ElementManager) {
            $this->info = $this->info->toHtml();
        }
        $input = $this->input->toHtml();
        if (starts_with($this->containers['input'], ':')) {
            return view(substr($this->containers['input'], 1), [
                    'label' => $this->label,
                    'info' => $this->info,
                    'input' => $input
            ]);
        } else {
            return (new ElementManager($this->label . $input . $this->info, $this->containers['input']))
                    ->toHtml();
        }
    }
}