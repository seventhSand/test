<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/5/2017
 * Time: 4:11 PM
 */

namespace Webarq\Manager\Cms;


use Illuminate\Support\Arr;
use Webarq\Info\TableInfo;
use Webarq\Manager\AdminManager;
use Webarq\Model\HistoryModel;

class HistoryManager
{
    /**
     * @param AdminManager $admin
     * @param string $action Transaction action (delete, edit, create, ... etc)
     * @param TableInfo $table
     * @param object $row
     * @param int|number $parentId
     * @return bool
     */
    public function record(AdminManager $admin, $action, TableInfo $table, $row, $parentId = 0)
    {
        $property = $table->getHistories();
        $property['group'] = $table->getName();

        foreach ($property as $key => &$value) {
            if (isset($row->{$value})) {
                $value = $row->{$value};
            }
        }

        $model = new HistoryModel();
        $model->{'parent_id'} = $parentId;
        $model->{'role_level'} = $admin->getLevel(true);
        $model->{'action'} = $action;
        $model->{'actor'} = $admin->getProfile('username');
        $model->{'properties'} = Arr::serialize($property);
        $model->{'create_on'} = date('Y-m-d H:i:s');

        return $model->save();
    }
}