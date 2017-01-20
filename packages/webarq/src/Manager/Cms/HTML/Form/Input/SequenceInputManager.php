<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/19/2017
 * Time: 12:53 PM
 */

namespace Webarq\Manager\Cms\HTML\Form\Input;


use Webarq\Manager\Cms\HTML\Form\AbstractInput;

class SequenceInputManager extends AbstractInput
{
    /**
     * @inheritdoc
     */
    protected function buildInput()
    {
        return \Form::text($this->name, $this->value, $this->attribute->toArray());
    }
}