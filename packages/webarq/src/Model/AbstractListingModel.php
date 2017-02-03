<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/14/2016
 * Time: 5:58 PM
 */

namespace Webarq\Model;


use Illuminate\Database\Eloquent\Model;

class AbstractListingModel extends Model
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

}