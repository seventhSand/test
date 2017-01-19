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
     * @var
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
     * Post data
     *
     * @var array
     */
    protected $post = [];

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
            foreach ($inputs as $path => $attr) {
                if (is_numeric($path)) {
                    $path = $attr;
                    $attr = [];
                }

                $input = $this->makeInput($path, $attr);

                if ($input->isValid()) {
                    if ($input->isPermissible()) {
                        if (true === $input->multilingual && $input->table->isMultilingual()) {
                            foreach (app('Wlang\Lang')->getCodes() as $code) {
                                if (app('Wlang\Lang')->getSystem() != $code) {
                                    $clone = clone $input;
                                    $clone->setAttributeName($clone->name, $code);
                                    $clone->setTitle($clone->getTitle() . ' (' . strtoupper($code) . ')');
                                    $this->collections['s-y-s-t-e-m-m-u-l-t-i-l-i-n-g-u-a-l'][$input->name][$code]
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
        $table = $module->getTable($table);
        $column = $table->getColumn($column);
        $old = $column->unserialize();
        $attr = Arr::merge(Arr::merge($old, $column->getExtra('form')), $attr) + [
                        'module' => $module, 'table' => $table, 'column' => $column
                ];
        $attr['db-type'] = $old['type'];
        $attr['form-type'] = $this->type;

// This is could be pain on the process, but due to laravel input form method behaviour is different
// one from another, we need class helper to enable us adding consistent parameter
        $input = Wa::load('manager.cms.HTML!.form.input.' . array_get($attr, 'type', 'null') . ' input', $attr)
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
     * @param array $post
     */
    public function setPost(array $post = [])
    {
        $this->post = $post;
    }

    public function finalizePost()
    {
        $pairs = [];
        if ([] !== $this->pairs) {
// Multilingual inputs
            $multilingual = array_get($this->collections, 's-y-s-t-e-m-m-u-l-t-i-l-i-n-g-u-a-l', []);

            foreach ($this->pairs as $name => $input) {
                $value = $input->getValue();

                if (!is_array($value)) {
                    $pairs[$input->table->getName()][$input->column->getName()] = $value;
// Set translation row
                    if (null !== ($inputs = array_get($multilingual, $name))) {
                        foreach ($inputs as $code => $input) {
                            $pairs['translation'][$input->table->getName(true)][$code][$input->column->getName()]
                                    = $input->getValue();
                        }
                    }
                } else {
                    foreach ($value as $key => $str) {
                        $pairs[$input->table->getName()][$key][$input->column->getName()] = $str;
// @todo check translation for array row
                    }
                }
            }
        }

        return $pairs;
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
            $multilingual = array_pull($this->collections, 's-y-s-t-e-m-m-u-l-t-i-l-i-n-g-u-a-l', []);

            foreach ($this->collections as $input) {
// Set input value
                $input->setValue(array_get($this->post, $input->name));

                $this->html .= $input->buildHtml();

// Print out multilingual input
                if ([] !== $multilingual && null !== ($inputs = array_get($multilingual, $input->name))) {
                    foreach ($inputs as $input) {
                        $input->setValue(array_get($this->post, $input->getAttribute('name')));
                        $this->html .= $input->buildHtml();
                    }
                }
            }

// Putting back multilingual into collections
            $this->collections['s-y-s-t-e-m-m-u-l-t-i-l-i-n-g-u-a-l'] = $multilingual;
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