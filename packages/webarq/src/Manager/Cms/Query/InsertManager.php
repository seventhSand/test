<?php
/**
 * Created by PhpStorm
 * Date: 29/12/2016
 * Time: 15:26
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Manager\Cms\Query;


use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Wa;
use Webarq\Manager\Cms\HistoryManager;

class InsertManager extends CrudAbstractManager
{
    protected $type = 'insert';

    /**
     * @return bool
     */
    public function execute()
    {
        if ([] !== $this->pairs && null !== ($row = array_pull($this->pairs, $this->master))) {
// Auto completion for create time column
            $this->checkForCreateUpdateTime($row, $this->master, 'create_on');

// Always insert master table in the first place
            if (null !== ($model = $this->model(str_singular($this->master)))) {
// Eloquent way
                $id = $model->insert($row);
            } else {
// Builder way
                $id = $this->makeInsert($this->master, $row);
            }

// Insert foreign data
            if (is_numeric($id)) {
                if ([] !== $this->pairs) {
                    $foreignKey = Wa::table($this->master)->getReferenceKeyName();
                    foreach ($this->pairs as $table => $row) {
                        if (is_array($row)) {
                            if (!Arr::isAssocAbs($row)) {
// Multi insertion
                                foreach ($row as $row1) {
                                    $row1[$foreignKey] = $id;
                                    $this->makeInsert($table, $row1);
                                }
                                unset($this->pairs[$table]);
                                continue;
                            } else {
                                $this->makeInsert($table, $row);
                            }
                        }
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @param $table
     * @param array $row
     * @return number
     */
    protected function makeInsert($table, array $row)
    {
        foreach ($row as $column => $value) {
            if (is_array($value)) {
                $row[$column] = Str::decodeSerialize($value);
            }
        }

        return DB::table($table)->insertGetId($row);
    }
}