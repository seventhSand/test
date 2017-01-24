<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/23/2017
 * Time: 9:12 AM
 */

namespace Webarq\Manager\Cms\HTML\Form\Input;


use Webarq\Manager\Cms\HTML\Form\AbstractInput;

class FileInputManager extends AbstractInput
{
    protected $file;

    public function buildInput()
    {
        return \Form::file($this->name, $this->attribute()->toArray());
    }

}