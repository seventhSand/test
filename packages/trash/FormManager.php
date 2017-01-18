<?php
/**
 * Created by PhpStorm
 * Date: 18/12/2016
 * Time: 10:29
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Manager\Cms\HTML\trash;


use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Wa;
use Webarq\Manager\AdminManager;
use Webarq\Manager\Cms\HTML\Form\InputManager;
use Webarq\Manager\setPropertyManagerTrait;

/**
 * Panel form generator
 *
 * Generate form based on configuration module files
 *
 * Class FormManager
 * @package Webarq\Manager\Cms\HTML
 */
class FormManager implements Htmlable
{
    use SetPropertyManagerTrait;

    /**
     * Current login admin
     *
     * @var AdminManager
     */
    protected $admin;

    /**
     * Form transaction type, create or edit
     *
     * @var string
     */
    protected $type;

    /**
     * Module name
     *
     * @var string
     */
    protected $module;

    /**
     * Panel name
     *
     * @var string
     */
    protected $panel;

    /**
     * Table master parent
     *
     * @var string
     */
    protected $master;

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
     * Form input collections
     *
     * @var array
     */
    protected $inputs = [];

    /**
     * Form attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Form builder
     *
     * @var object Webarq\Manager\HTML\FormManager
     */
    protected $builder;

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

    protected $paths = [];

    /**
     * FormManager create instance
     *
     * @param AdminManager $admin
     * @param array $options
     */
    public function __construct(AdminManager $admin, array $options = [])
    {
        $this->admin = $admin;

        $this->setPropertyFromOptions($options, false);

        $this->compileOptions($options);

        $this->builder = new \Webarq\Manager\HTML\FormManager($this->action, $this->attributes);
    }


    /**
     * Extract options in to class property
     *
     * @param array $options
     */
    protected function compileOptions(array $options)
    {
        if ([] !== $options) {
            foreach ($options as $key => $value) {
                if (is_numeric($key)) {
// Multiple inputs in one container
                    if (is_array($value)) {
                        $group = [];
                        foreach ($value as $key1 => $value1) {
                            $group[$key1] = $value1;
                        }
                        $this->inputs[count($this->inputs) + 1] = $group;
                    } else {
                        $this->inputs[$value] = [];
                    }
                } elseif (str_contains($key, '.')) {
                    $this->inputs[$key] = $value;
                } else {
                    $this->attributes[$key] = $value;
                }
            }
        }
    }

    /**
     * @param $path
     * @param null $index
     * @return mixed
     */
    protected function getPath($path, $index = null)
    {
        if (!isset($this->paths[$path])) {
            $this->paths[$path] = explode('.', $path, 3);
        }

        return !is_null($index) ? array_get($this->paths[$path], $index) : $this->paths[$path];
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

    /**
     * Compile form configuration
     *
     * @return $this
     */
    public function compile()
    {
        $this->compileInputs();

        return $this;
    }

    /**
     * Add collections in to $builder by compiling $inputs property
     */
    protected function compileInputs()
    {
        if ([] !== $this->inputs) {
            foreach ($this->inputs as $path => $setting) {
// Add collection group
                if (is_numeric($path)) {

                } else {
                    $this->addInput($path, $setting);
                }
            }
        }
    }

    /**
     * Add input in to builder
     *
     * @param $path
     * @param array $attr
     */
    protected function addInput($path, array $attr = [])
    {
        list($module, $table, $column) = explode('.', $path);
// Merge extended input attributes with pre-defined attributes in column information
        $attr = Arr::merge(Wa::module($module)->getTable($table)->getColumn($column)->getInputAttribute(), $attr);
// Attribute type and name should not be empty
        $type = array_pull($attr, 'type');
        $name = array_pull($attr, 'name');
        if (null === $type || null === $name) {
            abort('405', config('webarq.system.error-message.configuration'));
        }
// Only add unguarded input
        if (null === array_get($attr, 'guarded')) {
// Following laravel way, value should not be not in attributes
            $value = $this->pullValueFromAttributes($name, $attr);

            $attr += ['table' => $table, 'column' => $column, 'module' => $module];

// This is could be pain on the process, but due to laravel input form method behaviour is different
// one from another, we need class helper to enable us adding collection with proper arguments
// @todo Build own form builder to simplify the logic
            $input = Wa::load('manager.cms.HTML!.form.input.' . $type . ' input',
                    $this->builder, $type, $name, $value, $attr)
                    ?: Wa::load('manager.cms.HTML!.form.input', $this->builder, $type, $name, $value, $attr);

// Pairing input name with the manager, while each input set as impermissible
            $this->pairs[$name] = [$input, true];

            if (null === array_get($attr, 'protected')
                    && ([] === $input->getPermissions()
                            || Wa::panel()->isAccessible($this->module, $this->panel, $input->getPermissions()))
            ) {
                $input->buildInput();

                $this->setValidatorMessage($name, $input);
                if ([] !== $input->getErrorMessage()) {
                    foreach ($input->getErrorMessage() as $errType => $errMsg) {
                        $this->validatorMessages[$name . '.' . $errType] = $errMsg;
                    }
                }

                $this->validatorRules[$name] = $input->getRules()->toString();
// Set input permission in to true
                $this->pairs[$name][1] = false;
            }
        }
    }

    /**
     * @param mixed $name Input name
     * @param array $attr Input attributes
     * @return mixed
     */
    protected function pullValueFromAttributes($name, array &$attr = [])
    {
        return array_pull($attr, 'value', [] !== $this->post
                ? array_get($this->post, $name)
                : ('create' === $this->type ? array_get($attr, 'default') : ''));
    }

    protected function setValidatorMessage($inputName, InputManager $manager)
    {

    }

    /**
     * @param array $messages
     * @param string $level
     * @param string $container
     */
    public function setMessages($messages = [], $level = 'warning', $container = ':webarq.form.cms.message')
    {
        if (is_array($messages)) {
            foreach ($messages as &$message) {
                if (is_array($message)) {
                    $message = current($message);
                }
            }
        }
        $this->builder->setMessages($messages, $level, $container);
    }

    /**
     * Set form inputs
     *
     * While inputs already exist and options not empty,
     * merge inputs options with the previous one
     *
     * @param $name
     * @param array $options
     * @return $this
     */
    public function setInput($name, array $options = [])
    {
        if (isset($this->inputs[$name]) && [] !== $options) {
            $this->inputs[$name] = Arr::merge($this->inputs[$name], $options);
        } else {
            $this->inputs[$name] = $options;
        }

        return $this;
    }

    /**
     * Get validator messages
     *
     * @return array
     */
    public function getValidatorMessages()
    {
        return $this->validatorMessages;
    }

    /**
     * Get validator rules
     *
     * @return array
     */
    public function getValidatorRules()
    {
        return Arr::filter($this->validatorRules);
    }

    /**
     * Get column and input pair
     *
     * @return array
     */
    public function getPairs()
    {
        return $this->pairs;
    }

    /**
     * Get master table name
     *
     * @return string
     */
    public function getMaster()
    {
        return $this->master;
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
        if (isset($this->title)) {
            $this->builder->setTitle($this->title);
        }

        return $this->builder->toHtml();
    }
}