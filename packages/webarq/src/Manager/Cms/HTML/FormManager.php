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
     * Form input collections
     *
     * @var array
     */
    protected $collections = [];

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

    public function __construct(AdminManager $admin, array $options = [])
    {
        $this->admin = $admin;

        $this->setup($options);
    }

    protected function setup(array $options)
    {
        $this->module = array_pull($options, 'module');
        $this->panel = array_pull($options, 'panel');

        $this->setPropertyFromOptions($options);

        $this->prepareCollections($options);
    }

    /**
     * @param array $inputs
     * @todo Support row grouping, following [[path1 => array setting, path2 => array setting] ...] format
     */
    protected function prepareCollections(array $inputs)
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
                        if ($input->isMultilingual()) {
                            foreach (Wl::getCodes() as $code) {
                                if (Wl::getSystem() != $code) {
                                    $clone = clone $input;
                                    $clone->attribute()->setName($clone->name, $code);
                                    $clone->setTitle($clone->getTitle() . ' (' . strtoupper($code) . ')');
                                    $this->collections['multilingual-frm-input'][$input->name][$code]
                                            = $clone;
                                }
                            }
                        }

                        $this->collectInputValidator($input);

                        $this->collections[$input->name] = $input;
                    }

                    $this->pairs[$input->name] = $input;
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
        $attr = Arr::merge(Arr::merge($old, $column->getExtra('form')), $attr) + [
                        'module' => $module, 'table' => $table, 'column' => $column
                ];
        $attr['db-type'] = $old['type'];
        $attr['form-type'] = $this->type;
// Input type
        $type = isset($attr['file']) ? 'file' : array_get($attr, 'type', 'null');
// This is could be pain on the process, but due to laravel input form method behaviour is different
// one from another, we need class helper to enable us adding consistent parameter
        $input = Wa::load('manager.cms.HTML!.form.input.' . $type . ' input', $attr)
                ?: Wa::load('manager.cms.HTML!.form.input.default input', $attr);

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

    protected function collectInputValidator(AbstractInput $input)
    {
// Collect validator rules
        $this->validatorRules[$input->name] = $input->rules->toString();

        if ([] !== $input->errorMessages) {
            foreach ($input->errorMessages as $errType => $errMsg) {
                $this->validatorMessages[$input->name . '.' . $errType] = $errMsg;
            }
        }
    }

    /**
     * @param null|number $id
     */
    public function setValuesFromDB($id = null)
    {
        if (is_numeric($id) && 'edit' === $this->type) {
            if (true === $this->model) {
                $this->values = Wa::model(str_singular($this->panel))->formRowFinder($id);
            } elseif (is_array($this->model)) {
                $this->values = Wa::model($this->model[0])->{$this->model[1]}($id);
            } elseif (is_string($this->model)) {
                $this->values = Wa::model(str_singular($this->panel))->{$this->model}($id);
            } else {
                $this->values = Wa::load('manager.cms.HTML!.form.model$', $id, $this->collections, $this->master)
                        ->getData();
            }
        }
    }

    /**
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function getCollection($key = null, $default = null)
    {
        return array_get($this->collections, $key, $default);
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
     * Set post data form data
     *
     * @param array $values
     */
    public function setValues(array $values = [])
    {
        $this->values = Arr::merge($this->values, $values);
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
        if ([] !== $this->collections) {
// Pull out multilingual input
            $multilingual = array_pull($this->collections, 'multilingual-frm-input', []);

            foreach ($this->collections as $input) {
// Set input value
                $input->setValue(array_get($this->values, $input->getInputName()));

                $this->html .= $input->buildHtml();

// Print out multilingual input
                if ([] !== $multilingual && null !== ($inputs = array_get($multilingual, $input->name))) {
                    foreach ($inputs as $input) {
                        $input->setValue(array_get($this->values, $input->getInputName()));
                        $this->html .= $input->buildHtml();
                    }
                }
            }

// Putting back multilingual into collections
            $this->collections['multilingual-frm-input'] = $multilingual;
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
                'elements' => $this->collections,
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