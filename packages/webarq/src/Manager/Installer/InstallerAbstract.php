<?php
/**
 * Created by PhpStorm
 * Date: 25/10/2016
 * Time: 11:46
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Manager\Installer;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Wa;
use Webarq\Info\ModuleInfo;
use Webarq\Info\TableInfo;

abstract class InstallerAbstract
{
    /**
     * @var string
     */
    protected $response = '';

    /**
     * @var array of object Webarq\Info\TableInfo
     */
    protected $tables = [];

    /**
     * Table payload
     *
     * @var array
     */
    protected $payload = [];

    public function __construct($module = null)
    {
        $this->payload = Wa::config('payload', []);

        if (isset($module)) {
            $module = explode(str_contains($module, ']') ? ']' : ',', $module);
        } else {
            $module = Wa::modules();
        }

        $this->makeCollection($module);
    }

    /**
     * @param array $module
     */
    private function makeCollection(array $module)
    {
        if ([] !== $module) {
            foreach ($module as $item) {
                $item = trim($item, ',');
// Get intended table onlye
                if (str_contains($item, '[')) {
                    list($module, $table) = explode('[', $item, 2);
                    if (null !== ($module = Wa::module($module))) {
                        $this->collectTable($module, explode(',', $table));
                    }
                } else {
                    if (null !== ($module = Wa::module($item))) {
                        $this->collectTable($module);
                    }
                }
            }
        }
    }

    /**
     * @param ModuleInfo $module
     * @param array $tables
     */
    private function collectTable(ModuleInfo $module, array $tables = [])
    {
        if ([] !== $tables) {
            foreach ($tables as $table) {
                if ($module->hasTable($table)) {
                    $this->tables[$table] = $module->getTable($table);
                }
            }
        } else {
            $this->tables = Arr::merge($this->tables, $module->getTables());
        }
    }

    /**
     *
     */
    public function install()
    {
        if ([] !== $this->tables) {
            foreach ($this->tables as $table) {
// Install table (and related object)
                $this->installation($table);
            }

            $this->setPayload();
        }
        return 'done';
    }

    abstract protected function installation(TableInfo $table);

    private function setPayload()
    {
// Update payload data
        $f = fopen('config-module/payload.php', 'w+');
        fwrite($f, '<?php return '
                . PHP_EOL
                . '       '
                . $this->var_export_array($this->payload, '       ') . ';');
        fclose($f);
    }

    protected function var_export_array($var, $indent = "")
    {
        switch (gettype($var)) {
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                            . ($indexed ? "" : $this->var_export_array($key) . " => ")
                            . $this->var_export_array($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            default:
                return var_export($var, TRUE);
        }
    }

    public function getResponse()
    {
        return $this->response;
    }

    protected function setMigrationFile($strClass, $lineCode)
    {
        $f = fopen('database/migrations/' . date('Y_m_d_His') . '_' . $strClass . '.php', 'w+');
        fwrite($f, $lineCode);
        fclose($f);
    }

    /**
     * @param string $class
     * @return string
     */
    protected function openClass($class)
    {
        $str = '<?php ' . PHP_EOL . PHP_EOL;
        $str .= 'use Illuminate\Database\Schema\Blueprint;' . PHP_EOL;
        $str .= 'use Illuminate\Database\Migrations\Migration;' . PHP_EOL . PHP_EOL . PHP_EOL;
        return $str . 'class ' . Str::studly($class) . ' extends Migration {' . PHP_EOL;
    }

    /**
     * @return string
     */
    protected function closeClass()
    {
        return '}';
    }
}