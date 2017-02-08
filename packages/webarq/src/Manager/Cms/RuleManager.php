<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/9/2017
 * Time: 6:14 PM
 */

namespace Webarq\Manager\Cms;


use Wa;
use Webarq\Manager\AdminManager;
use Webarq\Model\NoModel;

class RuleManager
{
    /**
     * @var AdminManager
     */
    protected $admin;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var array
     */
    protected $items;

    protected $operator = [
            '===' => 'isIdentical',
            '==' => 'isEqual',
            '!==' => 'isNotIdentical',
            '!=' => 'isNotEqual',
            '>=' => 'isGreaterEqual',
            '>' => 'isGreater',
            '<=' => 'isLowerEqual',
            '<>' => 'isLowerGreater',
            '<' => 'isLower',
    ];

    protected $table;

    /**
     * @param AdminManager $admin
     * @param array $rules
     * @param array $items
     * @param mixed $table
     */
    public function __construct(AdminManager $admin, $rules = [], $items = [], $table = null)
    {
        $this->admin = $admin;
        $this->rules = $rules;
        $this->items = $items;
        $this->table = $table;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if ([] === $this->rules || [] === $this->items) {
            return true;
        } elseif (is_callable($this->rules)) {
            return call_user_func_array($this->rules, [$this->admin, $this->items]);
        } elseif (is_array($this->rules) && [] !== $this->rules) {
            $and = false;
            if (true === last($this->rules)) {
                $and = true;
                array_pop($this->rules);
            }

            $valid = $this->checkChildRow($this->rules);

            if (!$valid && true === $and) {
                return false;
            }

            if ([] !== $this->rules) {
                foreach ($this->rules as $key => $value) {
                    $valid = $this->compareValue($this->getValue($key), $this->getValue($value));

                    if (true === $and && !$valid) {
// All rules should be valid
                        return false;
                    } elseif ($valid) {
// One is enough for us
                        return true;
                    }
                }
            } else {
                return $valid;
            }
        }

        return false;
    }

    /**
     * Check for child rules
     *
     * @param array $rules
     * @return bool
     */
    protected function checkChildRow(array &$rules)
    {
        $child = array_pull($rules, 'has-child');
        $parent = array_pull($rules, 'parent-column');

        if (true !== $child && null !== $parent && null !== $this->table) {
            $id = array_pull($rules, 'primary-column', Wa::table($this->table)->primaryColumn()->getName());
            return null === NoModel::instance($this->table, $id)
                    ->where($parent, array_get($this->items, $id))
                    ->first();
        }
        return true;
    }

    protected function compareValue($left, $right, $operator = '===')
    {
        if (is_array($right) && isset($this->operator[$right[0]])) {
            $operator = array_pull($right, 0);
            if (is_array($right) && 1 === count($right)) {
                $right = array_shift($right);
            }
        }
        if (!is_array($left) && is_array($right)) {
            return in_array($left, $right);
        } else {
            return $this->{$this->operator[$operator]}($left, $right);
        }
    }

    protected function getValue($value)
    {
        if (is_callable($value)) {
            $value = call_user_func_array($this->rules, [$this->admin, $this->items]);
        } elseif (is_string($value) && str_contains($value, '.')) {
            list($property, $path) = explode('.', $value, 2);
            $method = 'get' . ucfirst(strtolower($property));
            if (method_exists($this, $method)) {
                $value = $this->{$method}($path);
            }
        }

        return is_numeric($value) ? (int)$value : $value;
    }

    /**
     * @param $left
     * @param $right
     * @return bool
     */
    protected function isEqual($left, $right)
    {
        return $left == $right;
    }

    protected function isNotEqual($left, $right)
    {
        return $left != $right;
    }

    protected function isIdentical($left, $right)
    {
        return $left === $right;
    }

    protected function isNotIdentical($left, $right)
    {
        return $left !== $right;
    }

    protected function isGreater($left, $right)
    {
        return $left > $right;
    }

    protected function isGreaterEqual($left, $right)
    {
        return $left >= $right;
    }

    protected function isLower($left, $right)
    {
        return $left < $right;
    }

    protected function isLowerEqual($left, $right)
    {
        return $left <= $right;
    }

    protected function isLowerGreater($left, $right)
    {
        return $left <> $right;
    }

    /**
     * @param $key
     * @return array|mixed|number
     */
    protected function getAdmin($key)
    {
        if ('level' === $key) {
            return $this->admin->getLevel(true);
        } else {
            return $this->admin->getProfile($key);
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function getItem($key)
    {
        return array_get($this->items, $key);
    }

}