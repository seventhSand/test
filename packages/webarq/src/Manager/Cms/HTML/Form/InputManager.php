<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/22/2016
 * Time: 12:55 PM
 */

namespace Webarq\Manager\Cms\HTML\Form;


use Illuminate\Support\Arr;
use Wa;
use Webarq\Manager\HTML\FormManager;
use Webarq\Manager\setPropertyManagerTrait;

class InputManager
{
    use SetPropertyManagerTrait;

    /**
     * FormManager instance
     *
     * @var object FormManager
     *
     * /**
     * Input type
     *
     * @var string
     */
    protected $type;

    /**
     * Input attributes
     *
     * @var array
     */
    protected $attributes = [];

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
    protected $errorMessage = [];

    /**
     * Input container
     *
     * @var string
     */
    protected $container;

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
     * Input attribute keys that should be an array
     *
     * @var array
     */
    protected $couldBeArray = ['options'];

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
     * Create InputManager instance
     *
     * @param FormManager $form
     * @param string $type
     * @param string $name
     * @param null $value
     * @param array $attributes
     */
    public function __construct(FormManager $form, $type, $name, $value = null, array $attributes = [])
    {
        $this->rules = Wa::manager('cms.HTML!.form.rules', $attributes);
        array_forget($attributes, ['rules']);

        $this->setPropertyFromOptions($attributes, true);

        $this->form = $form;
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;

        $this->setup($attributes);
    }

    protected function setup(array $attributes)
    {
        $this->generateClassAttribute($attributes);

        $this->stringifyArrayAttributes($attributes);

        $this->attributes = $attributes;

        if (true === array_get($this->attributes, 'multiple') || Arr::inArray($this->attributes, 'multiple')) {
            $this->attributes['name'] = $this->name . '[]';
        }
    }

    /**
     * Generate class attribute
     *
     * @param array $attributes
     */
    protected function generateClassAttribute(array &$attributes)
    {
        if (!isset($attributes['class'])) {
            $attributes['class'] = '';
        }

        $keys = config('webarq.system.input.class-member-attribute');

        foreach ($keys as $key => $value) {
// For numeric keys assume key and value has the same value
            if (is_numeric($key)) {
                $key = $value;
            }

            if (null !== ($pull = array_pull($attributes, $key))) {
                $attributes['class'] .= ' ' . $value;
            }
        }

        $attributes['class'] = implode(' ', array_flip(array_flip(array_filter(explode(' ', $attributes['class'])))));
    }

    /**
     * Any value other than the specified attributes key should not be an array
     *
     * @param array $attributes
     * @using $couldBeArray
     */
    protected function stringifyArrayAttributes(array &$attributes)
    {
        if ([] !== $attributes) {
            foreach ($attributes as $key => $value) {
                if (is_array($value) && !in_array($key, $this->couldBeArray)) {
                    $attributes[$key] = base64_encode(serialize($value));
                }
            }
        }
    }

    /**
     * @return null|\Webarq\Manager\HTML\Form\InputManager
     */
    public function buildInput()
    {
        return $this->form->addCollection([$this->type, $this->name, $this->value, $this->attributes],
                $this->title, $this->info);
    }

    /**
     * Get input column
     *
     * @param bool $manager
     * @return string
     */
    public function getColumn($manager = true)
    {
        return true === $manager ? $this->getTable()->getColumn($this->column) : $this->column;
    }

    /**
     * Get input table
     *
     * @param bool $manager
     * @return mixed
     */
    public function getTable($manager = true)
    {
        return true === $manager ? Wa::table($this->table) : $this->table;
    }

    /**
     * Get input default value
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Get input error message
     *
     * @return array
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Get input module
     *
     * @param bool $manager
     * @return mixed
     */
    public function getModule($manager = true)
    {
        return true === $manager ? Wa::module($this->module) : $this->module;
    }

    /**
     * Get input name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get input permissions
     *
     * @return array
     */
    public function getPermissions()
    {
        return (array)$this->permissions;
    }

    /**
     * @param mixed $key
     * @return object
     */
    public function getRules($key = null)
    {
        return isset($key) ? (is_object($this->rules) ? $this->rules->getAttribute($key) : false) : $this->rules;
    }

    /**
     * @return string
     */
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * @return mixed
     */
    public function getImpermissible()
    {
        return $this->impermissible ?: $this->default;
    }
}