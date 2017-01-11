<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/24/2016
 * Time: 2:45 PM
 */

namespace Webarq\Info;


use Wa;
use Webarq\Manager\SingletonManagerTrait as Singleton;

/**
 * Helper class
 *
 * Class ModuleInfo
 * @package Webarq\Info
 */
class ModuleInfo
{
    use Singleton;

    /**
     * Module configuration
     *
     * @var array
     */
    protected $configs = [];

    /**
     * Module name
     *
     * @var string
     */
    protected $name;

    /**
     * Module registered panel menu
     *
     * @var array
     */
    protected $panels = [];

    /**
     * Module registered tables
     *
     * @var array object Webarq\Info\TableInfo
     */
    protected $tables = [];

    /**
     * Create ModuleInfo instance
     *
     * @param $name
     * @param array $configs
     */
    public function __construct($name, array $configs = [])
    {
        $this->name = $name;

        if ([] === $configs) {
            $configs = Wa::config($name, []);
        }

        $this->setup($configs);
    }

    /**
     * Setup module by given options
     *
     * @param array $options
     */
    protected function setup(array $options)
    {
        if ([] !== $options) {
            $this->configs = array_get($options, 'configs', []);

            $this->setupTables(array_get($options, 'tables', []));

            $this->setupPanels(array_get($options, 'panels', []));
        }
    }

    /**
     * Set module tables information
     *
     * @param array $tables
     */
    private function setupTables(array $tables)
    {
        if ([] !== $tables) {
            foreach ($tables as $name) {
                $this->tables[$name] = TableInfo::getInstance(
                        $name, $this->name, Wa::config($this->name . '.tables.' . $name, []));
            }
        }
    }

    /**
     * Set module panels
     *
     * @param array $options
     */
    private function setupPanels(array $options)
    {
        if ([] !== $options) {
            foreach ($options as $key => $value) {
                if (is_numeric($key)) {
                    $key = $value;
                    $value = Wa::config($this->name . '.panel.' . $key, []);
                }
                $this->panels[$key] = Wa::load('info.panel', $key, $this->name, $value);
            }
        }
    }

    /**
     * Get module configuration item, by given $key
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getConfig($key, $default = null)
    {
        return array_get($this->configs, $key, $default);
    }

    /**
     * Get all module configuration
     *
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * Get module name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get module table item by given $name
     *
     * @param $name
     * @return object Webarq\Info\TableInfo
     */
    public function getTable($name)
    {
        return array_get($this->tables, $name, TableInfo::getInstance(
                $name, $this->name, Wa::config($this->name . '.tables.' . $name, [])));
    }

    /**
     * Get all module tables
     *
     * @return array
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * Determine if a table registered in module
     *
     * @param $tableName
     * @return bool
     */
    public function hasTable($tableName)
    {
        return isset($this->tables[$tableName]);
    }

    /**
     * Get module panel item, by given $key
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed|object Webarq\Info\PanelInfo
     */
    public function getPanel($key, $default = null)
    {
        return array_get($this->panels, $key, $default);
    }

    /**
     * @return array
     */
    public function getPanels()
    {
        return $this->panels;
    }
}