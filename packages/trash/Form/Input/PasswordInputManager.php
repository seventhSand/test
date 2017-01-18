<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/5/2017
 * Time: 1:30 PM
 */

namespace Webarq\Manager\Cms\HTML\Form\Input\trash;


use Webarq\Manager\Cms\HTML\Form\InputManager;

class PasswordInputManager extends InputManager
{
    /**
     * @doc inherit
     * @return null|\Webarq\Manager\HTML\Form\InputManager
     */
    public function buildInput()
    {
        return $this->form->addCollection([$this->type, $this->name, $this->attributes],
                $this->title, $this->info);
    }
}