<?php
/**
 * Created by PhpStorm
 * Date: 05/02/2017
 * Time: 12:26
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Manager\Cms\HTML;


use Wa;
use Webarq\Manager\Cms\HTML\Form\AbstractInput;

class FormConfigManager extends FormManager
{

    /**
     * @param array $inputs
     */
    protected function prepareInputs(array $inputs)
    {
        if ([] !== $inputs) {
            $master = null;
            foreach ($inputs as $name => $attr) {
                if (!is_array($attr)) continue;
// Build input
                $input = $this->makeInput($name, $attr);
// Process valid input
                if ($input instanceof AbstractInput && $input->isValid()) {
                    if ($input->isPermissible()) {
                        $this->inputs[$input->getInputName()] = $input;
                    }
                }
            }
        }
    }

    /**
     * @param $name
     * @param array $attr
     * @return mixed
     */
    protected function makeInput($name, array $attr)
    {
// Set attribute name
        $attr['name'] = $name;
// Input type
        $type = isset($attr['file']) ? 'file' : array_get($attr, 'type', 'null');
// This is could be pain on the process, but due to laravel input form method behaviour is different
// one from another, we need class helper to enable us adding consistent parameter
        $input = Wa::load('manager.cms.HTML!.form.input.' . $type . ' input', $this->module, $this->panel, $attr)
                ?: Wa::load('manager.cms.HTML!.form.input.default input', $this->module, $this->panel, $attr);

        return $this->inputManagerDependencies($input);
    }
}