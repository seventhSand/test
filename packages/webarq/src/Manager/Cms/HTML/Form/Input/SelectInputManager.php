<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/17/2017
 * Time: 4:01 PM
 */

namespace Webarq\Manager\Cms\HTML\Form\Input;


use Illuminate\Support\Arr;
use Webarq\Manager\Cms\HTML\Form\AbstractInput;

class SelectInputManager extends AbstractInput
{
    /**
     * @var array|callable $options
     */
    protected $options = ['0' => 'On', '1' => 'Off'];

    protected function buildInput()
    {
        if (is_callable($this->options)) {
            $method = $this->options;
            $this->options = $method();
        }

        $this->blankOption();

        return \Form::select($this->name, $this->options, $this->value, $this->attributes);
    }

    protected function blankOption()
    {
        if (null !== ($labelOption = array_get($this->settings, 'blankOption'))) {
            if (true === $labelOption || 1 === $labelOption || '1' === $labelOption) {
                $this->options = Arr::merge(['' => config('webarq.system.input.blank-option-label')], $this->options);
            } else {
                if (!is_array($labelOption)) {
                    $labelOption = ['' => $labelOption];
                }

                $this->options = Arr::merge($labelOption, $this->options);
            }
        }
    }
}