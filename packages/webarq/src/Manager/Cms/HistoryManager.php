<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/5/2017
 * Time: 4:11 PM
 */

namespace Webarq\Manager\Cms;


use Webarq\Info\TableInfo;
use Webarq\Manager\AdminManager;

class HistoryManager
{
    public static function record($type, AdminManager $admin, TableInfo $table, $id, array $row)
    {
//        ['assigned', 'admins.username', 'roles.title'] : user assigned role "a" in to admin "b"
        if ([] !== $table->getHistories()) {
            dd($table->getHistories());
        }
        dd(func_get_args());
    }
}