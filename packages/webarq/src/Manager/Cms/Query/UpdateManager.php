<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/25/2017
 * Time: 12:19 PM
 */

namespace Webarq\Manager\Cms\Query;


use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Wa;
use Webarq\Info\TableInfo;

class UpdateManager extends QueryManager
{
    /**
     * @var string
     */
    protected $formType = 'edit';

    /**
     * @var
     */
    protected $id;

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        $count = 0;

        if ([] !== $this->post && !is_null($this->master) && is_numeric($this->id)) {
// Master data should be inserted before another
            $row = array_pull($this->post, $this->master, []);

            if ([] !== $row) {
                $m = Wa::table($this->master);
// Total updating row
                $count = 0;
// Initiate model
                $model = $this->initiateModel($this->master);
// Update master
                $update = $this->update($model, $row, [$m->primaryColumn()->getName() => $this->id]);
                if (true === $update || (is_numeric($update) && $update > 0)) {
                    if (null !== $m->getUpdateTimeColumn()) {
                        $this->addUpdateTime($m, $row);
                        $this->update($model, $row, [$m->primaryColumn()->getName() => $this->id]);
                    }
                    $count += 1;
                }

// Translation
                $tr = array_pull($this->post, 'translation', []);
                $update = $this->translation($this->id, $m, $tr);
                if (true === $update || (is_numeric($update) && $update > 0)) {
                    $count += 1;
                }
// Support rows
                $update = $this->supportData($this->id, $m, $this->post);
                if (true === $update || (is_numeric($update) && $update > 0)) {
                    $count += 1;
                }
            }
        }

        return $count;
    }

    protected function update(Model $model, array $row, array $where)
    {
        $model = $this->buildWhere($model, $where);

        return $model->update($row);
    }

    /**
     * @param $id
     * @param TableInfo $table
     * @param array $rows
     * @return bool|number
     */
    protected function translation($id, TableInfo $table, array $rows = [])
    {
        if ($table->isMultiLingual() && [] !== $rows) {
// Table name translation
            $t = \Wl::translateTableName($table->getName());
            $rows = array_get($rows, $t, []);

            if ([] !== $rows) {
                foreach ($rows as $code => $row) {
// Check for translation row
                    $find = DB::table($t)
                            ->where(\Wl::getLangCodeColumn('name'), $code)
                            ->where($table->getReferenceKeyName(), $id)
                            ->get();
                    $model = $this->initiateModel($t, 'id');
                    if ($find->count()) {
                        return $this->update($model, $row, [
                                        \Wl::getLangCodeColumn('name') => $code,
                                        $table->getReferenceKeyName() => $id]
                        );
                    } else {
// Translation row completion
                        $row += [
                                'create_on' => date('Y-m-d H:i:s'),
                                \Wl::getLangCodeColumn('name') => $code,
                                $table->getReferenceKeyName() => $id
                        ];

                        $this->rowBinder($model, $row);
                        $model->save();

                        return $model->id;
                    }
                }
            }
        }
    }

    /**
     * @param $id
     * @param TableInfo $master
     * @param array $groups
     * @return number
     */
    protected function supportData($id, TableInfo $master, array $groups)
    {
        $count = 0;
        if ([] !== $groups) {
            foreach ($groups as $table => $rows) {
                $t = Wa::table($table);
                if (Arr::isAssoc($rows)) {
                    $rows = [$rows];
                }

                $f = $t->getForeignColumn() ?: [$master->getReferenceKeyName()];

                if ($t->isFlushUpdate()) {
                    $delete = \DB::table($table)->where($master->getReferenceKeyName(), $this->id)->delete();
                    if (true === $delete || (is_numeric($delete) && $delete > 0)) {
                        $count += 1;
                    }
                }

                foreach ($rows as $row) {
// Default value
                    $where = [];
                    $find = null;
// Initiate model
                    $model = $this->initiateModel($table);
                    if (!$t->isFlushUpdate()) {
                        $find = DB::table($table);
                        foreach ($f as $column) {
                            if ($column === $master->getReferenceKeyName()) {
                                $val = $this->id;
                            } else {
                                $val = array_get($row, $column);
                            }
                            $find->where($column, $val);
                            $where[$column] = $val;
                        }
// Trying to find row
                        $find = $find->get();
                    }

                    if (null !== $find && $find->count()) {
                        $update = $this->update($model, $row, $where);
                        if (true === $update || (is_numeric($update) && $update > 0)) {
                            $count += 1;
                        }

                    } else {
// Add create time column
                        $this->addCreateTime($t, $row);
// Add foreign key column
                        $row[$master->getReferenceKeyName()] = $id;
// Bind row in to model
                        $this->rowBinder($model, $row);
// Save
                        $model->save();

                        if (is_int($model->id) && $model->id > 0) {
                            $count += 1;
                        }
                    }
                }
            }
        }
        return $count;
    }

}