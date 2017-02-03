<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/1/2017
 * Time: 4:30 PM
 */

namespace Webarq\Manager\Cms\Query;


use DB;
use Wa;
use Webarq\Info\TableInfo;

class DeleteManager
{
    /**
     * @var TableInfo
     */
    protected $table;

    /**
     * Mime columns
     *
     * @var array
     */
    protected $columns = [];

    /**
     * @var number
     */
    protected $id;

    /**
     * Column where
     *
     * @var string
     */
    protected $where;

    public function __construct(TableInfo $table, array $columns = [], $id, $where = 'id')
    {
        $this->table = $table;
        $this->columns = $columns;
        $this->id = $id;
        $this->where = $where;
    }

    /**
     * @param bool|true $history Log history or not
     * @return mixed
     */
    public function delete($history = true)
    {
        if (true === $history) {
            $rows = $this->findPrimaryRow();

            if (false !== $rows) {
// Delete row from primary table
                $del = $this->deleteRow($this->table->getName(), [$this->where => $this->id]);

                if ([] !== $rows && $del) {
// History record
                Wa::instance('manager.cms.history')->record(\Auth::user(), 'delete', $this->table, $rows[0]);
// Unlink file(s)
                    $this->unlinkFile($this->columns, $rows[0]);
// Check for translation table
                    $this->translationDeletion();
                }

                return 1;
            }
        } else {
            return $this->deleteRow($this->table->getName(), [$this->where => $this->id]);
        }

        return false;
    }

    /**
     * @return array|bool
     */
    protected function findPrimaryRow()
    {
        $columns = $this->columns;

        if ([] !== ($histories = $this->table->getHistories())) {
            $histories = array_pull($histories, 'delete', $histories);

            if (isset($histories['item'])) {
                $columns[] = array_get($histories, 'item');
                $columns[] = $this->table->primaryColumn()->getName();
            }
        }

        if ([] !== $columns) {
            $rows = $this->getRow($this->table->getName(), $columns, [$this->where => $this->id]);

            return [] === $rows ? false : $rows;
        }

        return [];
    }

    /**
     * Select row from table
     *
     * @param $table
     * @param array $columns
     * @param array $where
     * @return mixed
     */
    protected function getRow($table, array $columns, array $where)
    {
        $g = DB::table($table)
                ->select($columns);
        foreach ($where as $column => $value) {
            $g->where($column, $value);
        }

        return $g->get()->toArray();
    }

    /**
     * Delete row from table
     *
     * @param $table
     * @param array $where
     */
    protected function deleteRow($table, array $where)
    {
        $d = DB::table($table);

        foreach ($where as $column => $value) {
            $d->where($column, $value);
        }

        return $d->delete();
    }

    /**
     * @param array $columns
     * @param $item
     */
    protected function unlinkFile(array $columns, $item)
    {
        if (is_object($item)) {
            foreach ($columns as $column) {
                if (file_exists($item->{$column})) {
                    unlink($item->{$column});
                }
            }
        }
    }

    /**
     *
     */
    protected function translationDeletion()
    {
        if ($this->table->isMultiLingual()) {
// Translate table name
            $tt = \Wl::translateTableName($this->table->getName());
// Where condition
            $w = [$this->table->getReferenceKeyName() => $this->id];
// Check if table has translated mime column
            if ([] !== $this->columns) {
                $columns = [];
                foreach ($this->columns as $column) {
                    if ($this->table->getColumn($column)->isMultilingual()) {
                        $columns[] = $column;
                    }
                }

                if ([] !== $columns) {
                    $columns[] = $this->table->primaryColumn()->getName();

                    $rows = $this->getRow($tt, $columns, $w);
                }
            }

// Delete translation row
            $this->deleteRow($tt, $w);
// Unlink file(s)
            if (isset($columns) && isset($rows) && [] !== $rows) {
                foreach ($rows as $row) {
                    $this->unlinkFile($columns, $row);
                }
            }
        }
    }
}