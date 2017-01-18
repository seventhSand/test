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
    protected $defaultModule;

    /**
     * @var string
     */
    protected $defaultPanel;

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

    public function __construct(AdminManager $admin, array $options = [])
    {
        $this->admin = $admin;

        $this->setup($options);
    }

    protected function setup(array $options)
    {
        $this->defaultModule = array_pull($options, 'module');
        $this->defaultPanel = array_pull($options, 'panel');

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
// Multilingual input
                    if (true === $input->multilingual && $input->table->isMultilingual()) {
                        $this->collections['content']['multilingual'][$input->name] = clone $input;
                    }

                    $this->collectInputValidator($input);

                    $this->collections['content'][$input->name] = $input;

                    $this->pairs[$path] = $input;
                }
            }
        }
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
        $attr = Arr::merge(Arr::merge($column->unserialize(), $column->getExtra('form')), $attr) + [
                        'module' => $module, 'table' => $table, 'column' => $column
                ];

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

    /**
     * Compile form inputs
     */
    public function compile()
    {
        if (isset($this->collections['content'])) {
            $multilingual = array_pull($this->collections, 'content.multilingual', []);
            foreach ($this->collections['content'] as $input) {
// Set input value
                $input->setValue(array_get($this->post, $input->name));

                $this->html .= $input->buildHtml();

// Print out multilingual input
                if ([] !== $multilingual && null !== ($input = array_get($multilingual, $input->name))) {
                    foreach (app('Wlang\Lang')->getCodes() as $code) {
                        if (app('Wlang\Lang')->getSystem() != $code) {
                            $input->setAttributeName($input->name, $code);
// Set input value
                            $input->setValue(array_get($this->post, $input->getAttribute('name')));

                            $input->setTitle($input->getTitle(). ' (' . strtoupper($code) .')');

                            $this->html .= $input->buildHtml();
                        }
                    }
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
                        ['item' => studly_case($this->defaultPanel)]
                ),
                'attributes' => $this->attributes,
                'html' => $this->html,
// In case you want to build your own elements html structure
                'elements' => $this->collections,
                'alerts' => $this->alerts
        ]);
    }
}