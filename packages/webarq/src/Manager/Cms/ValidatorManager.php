<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/12/2017
 * Time: 2:52 PM
 */

namespace Webarq\Manager\Cms;


use Webarq\Manager\AdminManager;

class ValidatorManager
{
    /**
     * @var AdminManager
     */
    protected $admin;

    public function __construct(AdminManager $admin)
    {
        $this->admin = $admin;
    }

}