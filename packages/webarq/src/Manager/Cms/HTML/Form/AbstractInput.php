<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/17/2017
 * Time: 3:37 PM
 */

namespace Webarq\Manager\Cms\HTML\Form;


use Illuminate\Support\Arr;
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
    protected $protected = false;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Force inherited class to use getAttributes method
     *
     * @var array
     */
    private $attributes = [];

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        array_forget($options, ['form', 'master']);

        $this->setRule($options);

        $this->setPropertyFromOptions($options);

        $this->fixAttributes();

        $this->settings = $options;
    }

    protected function setRule(array &$options)
    {
        $this->rules = Wa::manager('cms.HTML!.form.rules', $options);

        array_forget($options, 'rules');
    }

    protected function fixAttributes()
    {
        if (!is_array($this->permissions)) {
            $this->permissions = [$this->permissions];
        }
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
                'attribute' => Arr::merge((array)$attr, ['class' => 'form-group'], 'join')
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
     * Set or Overwrite attribute item
     *
     * @param $key
     * @param $value
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Set attribute name
     *
     * @param $name
     * @param null $langCode
     */
    public function setAttributeName($name, $langCode = null)
    {
        if (null === $langCode) {
            $this->attributes['name'] = $name;
        } else {
            $names = explode('[', $name, 2);
            $names[0] .= '_' . $langCode;
            if (isset($names[1])) {
                $names[0] .= '[';
            }
            $this->attributes['name'] = implode('', $names);
        }
    }

    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Check if input is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return !$this->guarded && null !== $this->name && $this->isPermissible();
    }

    /**
     * Check if input is permissible to print out
     *
     * @return bool
     */
    public function isPermissible()
    {
        return [] === $this->permissions
        || Wa::panel()->isAccessible($this->module->getName(), $this->table->getName(), $this->permissions);
    }

    /**
     * @return bool
     */
    public function isProtected()
    {
        return true === $this->protected;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        return array_get($this->attributes, $key);
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

    /**
     * Build input attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        if ([] !== $this->settings) {
            $this->attributes = Arr::merge($this->settings, $this->attributes);
            foreach ($this->attributes as $key => $value) {
                if (is_object($value)) {
                    unset($this->attributes[$key]);
                } elseif (is_array($value)) {
                    $this->attributes[$key] = base64_encode(serialize($value));
                }
            }
        }

        return Arr::merge($this->attributes, [
                'class' => 'form-control'
        ], 'join');
    }
}