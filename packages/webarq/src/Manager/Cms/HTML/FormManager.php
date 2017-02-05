<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/16/2017
 * Time: 2:36 PM
 */
namespace Webarq\Manager\Cms\HTML;


use Form;
use Illuminate\Support\Arr;
use Wa;
use Webarq\Manager\AdminManager;
use Webarq\Manager\Cms\HTML\Form\AbstractInput;
use Webarq\Manager\Cms\HTML\Form\RulesManager;
use Webarq\Manager\SetPropertyManagerTrait;
use Wl;


/**
 * Panel form generator
 *
 * Generate form based on configuration module files
 *
 * Class FormManager
 * @package Webarq\Manager\Cms\HTML
 */
class FormManager
{
    use SetPropertyManagerTrait;

    /**
     * Current login admin
     *
     * @var AdminManager
     */
    protected $admin;

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $panel;

    /**
     * Form model, useful to get input values
     *
     * @var true|array [class name, method name]|string method name|
     */
    protected $model;

    /**
     * Form transaction type, create or edit
     *
     * @var string
     */
    protected $type;

    /**
     * Form title
     *
     * @var string
     */
    protected $title;

    /**
     * Form action
     *
     * @var string
     */
    protected $action;

    /**
     * Validator messages
     *
     * @var array
     */
    protected $validatorMessages = [];

    /**
     * Inputs rule in laravel format
     *
     * @var array
     */
    protected $validatorRules = [];

    /**
     * Transaction pairs, used on insert|update processing
     *
     * @var array
     */
    protected $pairs = [];

    /**
     * Form error message
     *
     * @var string HTML element
     */
    protected $message = [];

    /**
     * Input values
     *
     * @var array
     */
    protected $values = [];

    /**
     * Form inputs
     *
     * @var array
     */
    protected $inputs = [];

    /**
     * @var array
     * @todo Support media tab on form
     */
    protected $media = [];

    /**
     * @var string
     */
    protected $view = 'manager.cms.form.index';

    /**
     * HTML structure;
     *
     * @var string
     */
    protected $html;

    /**
     * Form attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $alerts = [];

    /**
     * Transaction master table.
     * Usable for multiple table transaction
     *
     * @var
     */
    protected $master;

    /**
     * Master row id
     *
     * @var
     */
    protected $editingRowId;

    public function __construct(AdminManager $admin, array $options = [])
    {
        $this->admin = $admin;

        $this->setup($options);
    }

    protected function setup(array $options)
    {
        $this->setPropertyFromOptions($options);

        $this->prepareInputs($options);
    }

    /**
     * @param array $inputs
     * @todo Support row grouping, following [[path1 => array setting, path2 => array setting] ...] format
     */
    protected function prepareInputs(array $inputs)
    {
        if ([] !== $inputs) {
            $master = null;
            foreach ($inputs as $path => $attr) {
                if (is_numeric($path)) {
                    $path = $attr;
                    $attr = [];
                }
// Build input
                $input = $this->makeInput($path, $attr);
// Input not found
                if (null === $input) continue;
// Check for master table
                if (null === $master || 0 === strpos($master, str_singular($input->{'table'}->getName()))) {
                    $master = $input->{'table'}->getName();
                }
// Process valid input
                if ($input->isValid()) {
                    if ($input->isPermissible()) {
                        $this->inputs[$input->getInputName()] = $input;
                        if ($input->isMultilingual()) {
                            foreach (Wl::getCodes() as $code) {
                                if (Wl::getSystem() != $code) {
                                    $clone = clone $input;
                                    $clone->attribute()->setName($clone->name, $code);
                                    $clone->setTitle($clone->getTitle() . ' (' . strtoupper($code) . ')');
                                    $this->inputs[$clone->getInputName()] = $clone;
                                    $this->pairs['multilingual'][$input->name][$code] = $clone;
                                }
                            }
                        }
                    }

                    $this->pairs[$input->getInputName()] = $input;
                }
            }

            if (null === $this->master) {
                $this->master = $master;
            }
        }
    }

    /**
     * @param $path
     * @param array $attr
     * @return mixed
     */
    protected function makeInput($path, array $attr)
    {
        list($module, $table, $column) = explode('.', $path, 3);
// load info class manager
        $module = Wa::module($module);
        if (!$module->hasTable($table)) {
            return null;
        }
        $table = $module->getTable($table);
        $column = $table->getColumn($column);
        $old = $column->unserialize();
        $attr = Arr::merge(Arr::merge($old, $column->getExtra('form')), $attr);
        $attr = ['table' => $table, 'column' => $column, 'db-type' => $old['type'], 'form-type' => $this->type] + $attr;
// Input type
        $type = isset($attr['file']) ? 'file' : array_get($attr, 'type', 'null');
// This is could be pain on the process, but due to laravel input form method behaviour is different
// one from another, we need class helper to enable us adding consistent parameter
        $input = Wa::load('manager.cms.HTML!.form.input.' . $type . ' input', $module, $module->getPanel($this->panel), $attr)
                ?: Wa::load('manager.cms.HTML!.form.input.default input', $module, $module->getPanel($this->panel), $attr);

        return $this->inputManagerDependencies($input);
    }

    /**
     * @param AbstractInput $input
     * @return AbstractInput
     */
    protected function inputManagerDependencies(AbstractInput $input)
    {
        return $input;
    }

    /**
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function getValue($key = null, $default = null)
    {
        return array_get($this->values, $key, $default);
    }

    /**
     * Set post data form data
     *
     * @param array $values
     */
    public function setValues(array $values = [])
    {
        $this->values = Arr::merge($this->values, $values);
    }

    /**
     * @param null|number $id
     */
    public function dataModeling($id = null)
    {
        if (true === $this->model) {
            $this->values = Wa::table($this->panel)->model()->formRowFinder($id);
        } elseif (is_array($this->model)) {
            $this->values = Wa::model($this->model[0])->{$this->model[1]}($id);
        } elseif (is_string($this->model)) {
            $this->values = Wa::table($this->panel)->model()->{$this->model}($id);
        } else {
            $this->values = Wa::load('manager.cms.HTML!.form.model$', $id, $this->pairs, $this->master)
                    ->getData();
        }

        if (is_array($this->values)) {
            $this->html .= Form::hidden('remote-value', base64_encode(serialize($this->values)));
        }

        $this->setEditingRowId($id);
    }

    /**
     * @param $id
     */
    public function setEditingRowId($id)
    {
        $this->editingRowId = $id;
    }

    /**
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function getInput($key = null, $default = null)
    {
        return array_get($this->inputs, $key, $default);
    }

    /**
     * Get pairs
     *
     * @return array
     */
    public function getPairs()
    {
        return $this->pairs;
    }

    /**
     * @return array
     */
    public function getValidatorMessages()
    {
        return $this->validatorMessages;
    }

    /**
     * @return array
     */
    public function getValidatorRules()
    {
        return $this->validatorRules;
    }

    /**
     * @param array $messages
     * @param string $level
     */
    public function setAlert($messages = [], $level = 'warning')
    {
        if (is_array($messages)) {
            foreach ($messages as &$message) {
                if (is_array($message)) {
                    $message = current($message);
                }
            }
            $this->alerts = [$messages, $level];
        }
    }

    /**
     * @return mixed
     */
    public function getMaster()
    {
        return $this->master;
    }

    /**
     * Compile form inputs
     */
    public function compile()
    {
        if ([] !== $this->inputs) {

            foreach ($this->inputs as $input) {
// Set input value
                $input->setValue(array_get($this->values, $input->getInputName()));
// Modify unique rule
                if ('edit' === $this->type && $input->attribute()->has('unique')) {
                    $input->rules->setItem('unique', $input->rules->getItem('unique') . ',' . $this->editingRowId);
                }
// Collect input validator
                $this->collectInputValidator($input);

                $this->html .= $input->buildHtml();
            }
        }

        if (null !== \Request::input('remote-value')) {
            $this->html .= Form::hidden('remote-value', \Request::input('remote-value'));
        }
    }

    /**
     * Get input rules
     *
     * @param AbstractInput $input
     */
    protected function collectInputValidator(AbstractInput $input)
    {
        if ($input->{'rules'} instanceof RulesManager) {
// Collect validator rules
            $this->validatorRules[$input->getInputName()] = $input->rules->toString();

            if ([] !== $input->errorMessages) {
                foreach ($input->errorMessages as $errType => $errMsg) {
                    $this->validatorMessages[$input->name . '.' . $errType] = $errMsg;
                }
            }
        }
    }

    /**
     * Convert $builder into well formatted HTML element
     *
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    public function toHtml()
    {
        $this->attributes['url'] = $this->action;

        return view($this->view, [
                'title' => $this->title ?: trans('webarq.title.' . $this->type,
                        ['item' => studly_case($this->panel)]
                ),
                'attributes' => $this->attributes,
                'html' => $this->html,
// In case you want to build your own elements html structure
                'elements' => $this->inputs,
                'alerts' => $this->alerts
        ]);
    }


    /**
     * Modify value based on modifier config
     *
     * @param $modifier
     * @param $string
     * @return mixed
     */
    protected function modifyValue($modifier, $string)
    {
        if (null !== $modifier) {
            return Wa::load('manager.value modifier')->{$modifier}($string);
        }
        return $string;
    }
}