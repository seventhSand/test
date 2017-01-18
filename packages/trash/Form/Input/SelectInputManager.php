<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/21/2016
 * Time: 5:06 PM
 */

namespace Webarq\Manager\Cms\HTML\Form\Input\trash;


use Illuminate\Support\Arr;
use Webarq\Manager\Cms\HTML\Form\InputManager;

class SelectInputManager extends InputManager
{
    /**
     * Select option
     *
     * @var array
     */
    protected $options = [];


    public function buildInput()
    {
        if (null !== ($labelOption = array_pull($this->attributes, 'blankOption'))) {
            if (true === $labelOption || 1 === $labelOption || '1' === $labelOption) {
                $this->options = Arr::merge(['' => config('webarq.system.input.blank-option-label')], $this->options);
            } elseif (is_string($labelOption)) {
                $this->options = Arr::merge(['' => $labelOption], $this->options);
            }
        }
        return $this->form->addCollection(
                [$this->type, $this->name, $this->options, $this->value, $this->attributes],
                $this->title, $this->info
        );
    }
}