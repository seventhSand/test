<?php
/**
 * Created by PhpStorm
 * Date: 19/10/2016
 * Time: 13:39
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq;


use Webarq\Info\ModuleInfo;
use Webarq\Info\TableInfo;
use Webarq\Manager\ConfigManager;

/**
 * Helper class. This is break the SOLID Principals but i can not do anything about that. I think this is the most
 * suitable way
 *
 * Class Wa
 * @package Webarq
 */
class Wa
{
    protected $instances = [];

    protected $space = 'Webarq';

    /**
     * @param $name
     * @param null $module
     * @param array $columns
     * @return null
     */
    public function table($name, $module = null, array $columns = [])
    {
// Ups, this is something unusual
        if (is_array($module)) {
            $columns = $module;
            $module = null;
        }
// Table should have module
        if (!isset($module)) {
            if ([] !== ($modules = $this->modules())) {
                foreach ($modules as $item) {
                    $manager = $this->module($item);
                    if ($manager->hasTable($name)) {
                        $module = $manager->getName();
                        break;
                    }
                }
            }
        }

        if (!isset($module) || !Wa::module($module)->hasTable($name)) {
            return null;
        }

        return TableInfo::getInstance($name, $module, $columns);
    }

    /**
     * Get config modules
     *
     * @return array
     */
    public function modules()
    {
        return config('webarq.modules', []);
    }

    /**
     * Load config-module configuration
     *
     * @param $name
     * @return object Webarq\Info\ModuleInfo
     */
    public function module($name)
    {
        if (in_array($name, $this->modules())) {
            return ModuleInfo::getInstance($name, $this->config($name, []));
        }
    }

    /**
     * A shortcut method to access Webarq\Manager\ConfigManager instances
     *
     * @param string $file
     * @param mixed $default
     * @return mixed
     */
    public function config($file, $default = null)
    {
        return ConfigManager::get($file, $default);
    }

    /**
     * Manager class loader
     *
     * @param $class
     * @param array $args , ... [$args]
     * @return object
     */
    public function manager($class, $args = [])
    {
        $args = func_get_args();
// Remove class argument
        array_shift($args);
        return $this->load('manager.' . $class, $args, $this->getGhost());
    }

    /**
     * Load new given class name. To load class without normalize class name, set "false" as first parameter,
     * following by intended parameter,
     *
     * @param string|false $class Class name
     * @param null|mixed $args [, mixed $args ...]
     * @return object|null
     */
    public function load($class, $args = null)
    {
        $args = func_get_args();
// Remove class argument
        array_shift($args);
        if (false === $class) {
            $class = array_shift($args);
        } else {
            $class = $this->normalizeClass($class);
        }

        if ($this->getGhost() === array_get($args, 1)) {
            $args = $args[0];
        }
// Prioritize app layer
        if (class_exists('App\\' . $class)) {
            $class = 'App\\' . $class;
        } elseif (!class_exists($class)) {
            return null;
        }

        switch (count($args)) {
            case 0:
                return new $class();
            case 1:
                return new $class($args[0]);
            case 2:
                return new $class($args[0], $args[1]);
            case 3:
                return new $class($args[0], $args[1], $args[2]);
            case 4:
                return new $class($args[0], $args[1], $args[2], $args[3]);
            case 5:
                return new $class($args[0], $args[1], $args[2], $args[3], $args[4]);
            case 6:
                return new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
            default:
                $o = new \ReflectionClass($class);
                $i = $o->newInstanceArgs($args);
                return $i;
        }
    }

    /**
     * Normally all class using studly case string format
     * ".", "\", and "/" will be use as directory separator
     *
     * @param string $path
     * @return string Full class path
     */
    public function normalizeClass($path)
    {
        $path = str_replace(['\\', '/', '_'], '.', $path);
        $path = str_replace('-', ' ', $path);
        $path = explode('.', $path);
        if (count($path) > 1) {
            $path = $this->compilePathName($path);
            $class = implode('\\', $path);
// Suffixed class with root name space
            if (!ends_with($class, '$')) {
                $class .= $path[0];
            } else {
                $class = substr($class, 0, -1);
            }
        } else {
            $class = ucfirst(strtolower(current($path)));
        }

        return $this->space . '\\' . $class;
    }

    private function compilePathName(array $path)
    {
        foreach ($path as &$item) {
// Do not modified item value
            if (ends_with($item, '!')) {
                $item = substr($item, 0, -1);
            } else {
                $item = studly_case($item);
            }
        }
        return $path;
    }

    public function getGhost()
    {
        return config('webarq.system.ghost');
    }

    public function element($content, $container, array $attr = [])
    {
        if (is_bool($attr)) {
            $toHtml = $attr;
            $attr = [];
        }
        return $this->html('element', $content, $container, $attr)->toHtml();
    }

    /**
     * HTML Manager class loader
     *
     * @param $class
     * @param array $args , ... [$args]
     * @return object
     */
    public function html($class, $args = [])
    {
        $args = func_get_args();
// Remove class argument
        array_shift($args);
        return $this->load('manager.HTML!.' . $class, $args, $this->getGhost());
    }

    /**
     * Formatted permission into acceptable format
     *
     * @param array $permissions
     * @param $module
     * @param $panel
     * @return array
     */
    public function formatPermissions($permissions = [], $module, $panel)
    {
// Permissions should be array
        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }

        foreach ($permissions as &$permission) {
            if (is_bool($permission)) {
                continue;
            }
            $permission = trim($permission, '.');
            switch (substr_count($permission, '.')) {
                case 1 :
                    $permission = $module . '.' . $permission;
                    break;
                case 0 :
                    $permission = $module . '.' . $panel . '.' . $permission;
                    break;
            }
        }

        return $permissions;
    }

    /**
     * Shortcut for calling modifier manager
     *
     * @param mixed $pattern
     * @param mixed $value
     * @return mixed
     */
    public function modifier($pattern = null, $value = null)
    {
        $class = $this->instance('manager.value modifier');

        if (null !== $pattern) {
            $params = explode(':', $pattern);
            $method = array_pull($params, 0);
            array_unshift($params, $value);

            return call_user_func_array([$class, $method], $params);
        }

        return $class;
    }

    /**
     * Load class instance
     *
     * @param $class
     * @param mixed $arg [,... $arg, $arg] Uncountable argument
     * @return mixed
     */
    public function instance($class, $arg = [])
    {
// Get loaded class
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        } else {
// Load new instances
// Get actual arguments
            $args = func_get_args();
// Remove class argument
            array_shift($args);
            if ($this->getGhost() === array_get($args, 1)) {
                $args = $args[0];
            }
// Get class full path
            $fp = $this->normalizeClass($class);

            if (class_exists($fp)) {
                if (method_exists($fp, 'getInstance')) {
                    $this->instances[$class] = $fp::getInstance($args, $this->getGhost());
                } else {
                    $this->instances[$class] = $this->load(false, $fp, $args, $this->getGhost());
                }
                return $this->instances[$class];
            } else {
                abort(500, 'Class ' . $class . ' not found on this system');
            }
        }
    }

    /**
     * Shortcut for calling cms panel manager
     *
     * @return mixed
     */
    public function panel()
    {
        return $this->instance('manager.cms.panel', \Auth::user());
    }

    /**
     * @param $themes
     * @param $view
     * @param bool|true $object
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getThemesView($themes, $view, $object = true)
    {
        $path = view()->exists('themes.' . $themes . '.' . $view)
                ? ('themes.' . $themes . '.' . $view)
                : ('themes.default.' . $view);

        return $object ? view($path) : $path;
    }

    /**
     * @param string $name
     * @return null|object
     */
    public function model($name)
    {
        return $this->load('model.' . $name);
    }

    /**
     * @param $str
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function trans($str)
    {
        $trans = trans($str);
        if ($str === $trans) {
            if (false !== strpos($str, '.')) {
                $str = trim(strrchr($str, '.'), '.');
            }
            $trans = title_case(str_replace(['_', '-'], ' ', $str));
        }
        return $trans;
    }
}