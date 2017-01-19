<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/18/2017
 * Time: 6:34 PM
 */

namespace Webarq\Manager\Cms\Query;


use DB;
use Illuminate\Support\Arr;
use Wa;
use Webarq\Info\TableInfo;

class InsertManager extends PostManager
{
    protected $formType = 'create';

    /**
     * @return bool
     */
    public function execute()
    {
        if ([] !== $this->post && !is_null($this->master)) {
// Master data should be inserted before another
            $row = array_pull($this->post, $this->master, []);

            if ([] !== $row) {
                $m = Wa::table($this->master);
// Initiate model
                $model = $this->initiateModel($this->master);
// Create time completion
                $this->addCreateTime($m, $row);
// Bind row in to model
                $this->rowBinder($model, $row);
// Save
                $model->save();
// Last inserted id
                $id = $model->{$model->getKeyName()};
// Translation
                $this->translation($id,
                        $m,
                        array_pull($this->post, 'translation.' . $m->getName(true), []));
// Support rows

                $this->supportData($id, $m, $this->post);
            }

            return true;
        }
    }

    /**
     * @param $id
     * @param TableInfo $table
     * @param array|null $rows
     */
    protected function translation($id, TableInfo $table, array $rows = null)
    {
        if ([] !== $rows) {
            foreach ($rows as $code => $row) {
                $row += ['create_on' => date('Y-m-d H:i:s'), 'lang_code' => $code];
                $row[$table->getReferenceKeyName()] = $id;
                $model = $this->initiateModel($table->getName(true), 'id');
                $this->rowBinder($model, $row);
                $model->save();
            }
        }
    }

    /**
     * @param $id
     * @param TableInfo $master
     * @param array $groups
     */
    protected function supportData($id, TableInfo $master, array $groups)
    {
        foreach ($groups as $table => $rows) {
            $t = Wa::table($table);
            if (Arr::isAssoc($rows)) {
                $rows = [$rows];
            }
            foreach ($rows as $row) {
// Initiate model
                $model = $this->initiateModel($table);
// Add create time column
                $this->addCreateTime($t, $row);
// Add foreign key column
                $row[$master->getReferenceKeyName()] = $id;
// Bind row in to model
                $this->rowBinder($model, $row);
// Save
                $model->save();
            }
        }
    }
}