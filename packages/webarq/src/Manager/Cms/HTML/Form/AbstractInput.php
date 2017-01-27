<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/17/2017
 * Time: 3:37 PM
 */

namespace Webarq\Manager\Cms\HTML\Form;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Wa;
use Webarq\Manager\SetPropertyManagerTrait;

abstract class AbstractInput
{
    use SetPropertyManagerTrait;

    /**
     * Input type
     *
     * @var string
     */
    protected $type;

    /**
     * Input information
     *
     * @var mixed
     */
    protected $info;

    /**
     * Input title
     *
     * @var string
     */
    protected $title;

    /**
     * Input name
     *
     * @var string
     */
    protected $name;

    /**
     * Input value
     *
     * @var null|mixed
     */
    protected $value;

    /**
     * Input permissions
     *
     * @var
     */
    protected $permissions = [];

    /**
     * Input rules
     *
     * @var object Webarq\Manager\Cms\HTML\Form\RulesManager
     */
    protected $rules;

    /**
     * Input error message when value is not match
     *
     * @var array
     */
    protected $errorMessages = [];

    /**
     * Input container
     *
     * @var string
     */
    protected $container = 'manager.cms.form.element';

    /**
     * Input macros
     *
     * @var array
     */
    protected $macros = [];

    /**
     * Input is multilingual
     *
     * @var null|bool
     */
    protected $multilingual;

    /**
     * Input table pair
     *
     * @var string
     */
    protected $table;

    /**
     * Input column pair
     *
     * @var string
     */
    protected $column;

    /**
     * Input module
     *
     * @var string
     */
    protected $module;

    /**
     * Input default value
     *
     * @var mixed
     */
    protected $default;

    /**
     * Input value modifier. Will be use on table insert|update
     *
     * @var string
     */
    protected $modifier;

    /**
     * Impermissible default value
     *
     * @var
     */
    protected $impermissible;

    /**
     * @var
     */
    protected $notnull;

    /**
     * @var bool
     */
    protected $guarded = false;

    /**
     * @var bool
     */
    protected $ignored = false;

    /**
     * @var bool
     */
    protected $invisible = false;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Form type, create or edit
     * @var string
     */
    protected $formType;

    /**
     * Db column type, eg. int, char, varchar, ... etc
     *
     * @var
     */
    protected $dbType;

    /**
     * @var \Webarq\Manager\Cms\HTML\Form\Input\AttributeInputManager
     */
    protected $attribute;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        array_forget($options, ['form', 'master']);

        $this->setRule($options);

        $this->setPropertyFromOptions($options);

        $this->settings = $options;

        $this->attribute = Wa::manager('cms.HTML!.form.input.attribute input', $this->settings)
                ->insertClass('form-control');

        if (Arr::inArray($this->settings, 'multiple')) {
            $this->attribute->setName($this->name . '[]');
        }
    }

    protected function setRule(array &$options = [])
    {
        $options['notnull'] = array_pull($options, 'required', array_get($options, 'notnull'));

        $this->rules = Wa::manager('cms.HTML!.form.rules', $options);

        array_forget($options, 'rules');
    }

    /**
     * Build input attributes
     */
    public function attribute()
    {
        return $this->attribute;
    }

    /**
     * Build input html element
     *
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    public function buildHTML()
    {
        $view = $this->container;
        $attr = [];
        if (is_array($this->container)) {
            list($view, $attr) = $this->container;
        }

        return view($view, [
                'title' => $this->getTitle(),
                'input' => $this->buildInput(),
                'attribute' => Arr::merge((array)$attr, ['class' => 'form-group'], 'join'),
                'info' => $this->info
        ])->render();
    }

    /**
     * Get input title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title ?: studly_case($this->name);
    }

    /**
     * Set input title
     *
     * @param $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    abstract protected function buildInput();

    /**
     * Get impermissible value, or return default value while not set
     *
     * @return mixed
     */
    public function getImpermissible()
    {
        return $this->impermissible ?: $this->default;
    }

    /**
     * @return mixed
     */
    public function getInputName()
    {
        $name = $this->attribute->get('name', $this->name);
        if (false !== ($pos = strpos($name, '['))) {
            return substr($name, 0, $pos);
        }

        return $name;
    }

    /**
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->rules->setValue($value);

        $this->value = $value;
    }

    /**
     * Check if input is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return !$this->guarded && null !== $this->name;
    }

    /**
     * Check if input is permissible to print out
     *
     * @return bool
     */
    public function isPermissible()
    {
        return !$this->invisible && (
                [] === $this->permissions || Wa::panel()->isAccessible(
                        $this->module, $this->module->getPanel($this->table->getName()), $this->permissions));
    }

    /**
     * Check if input is guarded
     *
     * @return bool
     */
    public function isGuarded()
    {
        return $this->guarded;
    }

    /**
     * @return bool
     */
    public function isIgnored()
    {
        return true === $this->ignored;
    }

    public function isMultilingual()
    {
        return !empty($this->multilingual) && $this->table->isMultilingual();
    }

    /**
     * Get input property
     *
     * @param $key
     * @return null
     * @todo Completion set, get function to hinder unnecessary flaw
     */
    public function __get($key)
    {
        return property_exists($this, $key) ? $this->{$key} : null;
    }

    public function __clone()
    {
        $this->attribute = clone $this->attribute;

        if (true === $this->multilingual) {
            $this->setRule();
        } elseif (!is_numeric($this->multilingual)) {
            $options = is_array($this->multilingual) ? $this->multilingual : ['rules' => $this->multilingual];
            $this->setRule($options);

            $this->setPropertyFromOptions($options);
        }
    }
}