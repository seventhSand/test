<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/5/2017
 * Time: 12:45 PM
 */

namespace Webarq\Model;


use Illuminate\Database\Eloquent\Model;

class NoModel extends Model
{
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = null;

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = null;

    /*
     |
     | Start of table of model modification
     | Be wise to use this, lad :)
     |
     */
    protected static $_table;

    protected static $_primaryKey = 'id';

    /**
     * Create NoModel instance
     *
     * @param string $table Table name
     * @param string $key Table primary key column name
     * @return static
     */
    public static function instance($table, $key = 'id')
    {
        $instance = new static;
        $instance->setTable($table);
        $instance->setKeyName($key);
        return $instance;
    }

    /**
     * Set model table
     *
     * @param string $table
     */
    public function setTable($table)
    {
        static::$_table = $table;
    }

    /**
     * Get model table
     *
     * @return mixed
     */
    public function getTable()
    {
        return static::$_table;
    }

    /**
     * Set table primary key
     *
     * @param string $key
     */
    public function setKeyName($key)
    {
        static::$_primaryKey = $key;
    }

    /**
     * Get table primary key
     *
     * @return string
     */
    public function getKeyName()
    {
        return static::$_primaryKey;
    }
    /*
     | End of table of model modification
     */
}