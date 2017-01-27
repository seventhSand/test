<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/6/2016
 * Time: 6:32 PM
 */

namespace Webarq\Manager\Installer;


use Wa;
use Webarq\Info\TableInfo;

class CreateManager extends InstallerAbstract
{
    protected function installation(TableInfo $table)
    {
        if (null === array_get($this->payload, 'installed.' . $table->getName() . '.create')
                && [] !== $table->getColumns()
        ) {
            $code = $this->openClass($strClass = 'create_' . $table->getName() . '_class');
            $code .= $this->migrationUp($table);
// Separate method with new line
            $code .= PHP_EOL;
            $code .= $this->migrationDown($table);
            $code .= $this->closeClass();
// Copy migration file
            $this->setMigrationFile($strClass, $code);
// Create model or not
            $this->createModelOrNot($table);
// Set payload
            array_set($this->payload, 'installed.' . $table->getName() . '.create', $table->getSerialize());
        }
    }

    /**
     * Migration up method
     *
     * @param TableInfo $table
     * @return string
     */
    private function migrationUp(TableInfo $table)
    {
        //DDL script
        $str = '    /**' . PHP_EOL;
        $str .= '     * Run the migrations.' . PHP_EOL;
        $str .= '     *' . PHP_EOL;
        $str .= '     * @return void' . PHP_EOL;
        $str .= '     */' . PHP_EOL;
        $str .= '    public function up()' . PHP_EOL;
        $str .= '    {' . PHP_EOL;
        $str .= '        Schema::create(\'' . $table->getName() . '\', function(Blueprint $table)' . PHP_EOL;
        $str .= '        {' . PHP_EOL;

        $uniqueItems = $uniquesItems = [];

        foreach ($table->getColumns() as $column) {
            $str .= Wa::manager('installer.definition', $column)->getDefinition();
            if (true === $column->isUnique()) {
                $uniqueItems[] = $column->getName();
            }
            if (true === $column->isUniques()) {
                $uniquesItems[] = $column->getName();
            }
        }
        $str .= Wa::manager('installer.UniqueDefinition!', $column)->getDefinitionUnique($uniqueItems);
        $str .= Wa::manager('installer.UniqueDefinition!', $column)->getDefinitionUniques($uniquesItems);
        $str .= '        });' . PHP_EOL;
// Create translation table
        $str .= $this->translationTable($table);
        $str .= '    }' . PHP_EOL;

        return $str;
    }

    private function translationTable(TableInfo $table)
    {
        if ($table->isMultiLingual()) {
            $str = PHP_EOL;
            $str .= '        Schema::create(\'' . \Wl::translateTableName($table->getName()) . '\', function(Blueprint $table)' . PHP_EOL;
            $str .= '        {' . PHP_EOL;
// Column ID
            $str .= Wa::manager('installer.definition', Wa::load('info.column', ['master' => 'bigId']))
                    ->getDefinition();
// Column language code
            $str .= Wa::manager('installer.definition', Wa::load('info.column', [
                    'name' => 'lang_code',
                    'type' => 'char',
                    'length' => 2,
                    'notnull' => true
            ]))->getDefinition();

            foreach ($table->getColumns() as $column) {
                if ($column->isPrimary()) {
                    $attrColumn = [
                            'name' => $table->getReferenceKeyName(),
                            'type' => $column->getType(),
                            'unsigned' => $column->getExtra('unsigned'),
                            'notnull' => true];
                    $str .= Wa::manager('installer.definition', Wa::load('info.column', $attrColumn))
                            ->getDefinition();
                } elseif ($column->getExtra('multilingual')) {
                    $column = clone $column;

                    $str .= Wa::manager('installer.definition', $column)->getDefinition();
                }
            }

            $str .= Wa::manager('installer.definition', Wa::load('info.column', ['master' => 'createOn']))
                    ->getDefinition();

            return $str . '        });' . PHP_EOL;
        }
    }

    /**
     * Migration  down method
     *
     * @param TableInfo $table
     * @return string
     */
    private function migrationDown(TableInfo $table)
    {
        $str = '    /**' . PHP_EOL;
        $str .= '     * Reverse the migrations.' . PHP_EOL;
        $str .= '     *' . PHP_EOL;
        $str .= '     * @return void' . PHP_EOL;
        $str .= '     */' . PHP_EOL;
        $str .= '    public function down()' . PHP_EOL;
        $str .= '    {' . PHP_EOL;
        $str .= '        Schema::drop(\'' . $table->getName() . '\');' . PHP_EOL;
// Drop translation table
        if ($table->isMultiLingual()) {
            $str .= PHP_EOL . '        Schema::drop(\'' . $table->getName() . '_i18n\');' . PHP_EOL;
        }
        $str .= '    }' . PHP_EOL;

        return $str;
    }

    private function createModelOrNot(TableInfo $table)
    {
        $class = $table->getModel();
        if (null !== $class) {
            $path = $table->getModelDir();
            $namespace = 'Webarq\Model';
            if ('Model' !== $path) {
                $dirs = explode('/', $path);
                $path = '';
                foreach ($dirs as $item) {
                    $dir = ucfirst(strtolower($item));
                    $path .= '/' . $dir;
                    $namespace .= '\\' . $dir;
                    if (!is_dir(__DIR__ . '/../../Model' . $path)) {
                        mkdir(__DIR__ . '/../../Model' . $path, 0755);
                    }
                }

                $path = 'Model' . $path;
            }
// Class Name
            $class = studly_case($class) . 'Model';
            if (!file_exists(__DIR__ . '/../../' . $path . '/' . $class . '.php')) {
                $str = '<?php' . PHP_EOL . PHP_EOL;
                $str .= 'namespace ' . $namespace . ';' . PHP_EOL . PHP_EOL . PHP_EOL;
                if ('Model' !== $path) {
                    $str .= 'use Webarq\Model\AbstractListingModel;' . PHP_EOL . PHP_EOL;
                }
                $str .= 'class ' . $class . ' extends AbstractListingModel' . PHP_EOL;
                $str .= '{' . PHP_EOL;
                $str .= '    protected $table = \'' . $table->getName() . '\';' . PHP_EOL;
                $str .= '}';

                $f = fopen(__DIR__ . '/../../' . $path . '/' . $class . '.php', 'w+');
                fwrite($f, $str);
                fclose($f);
            }
        }
    }
}