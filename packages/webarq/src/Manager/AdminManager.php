<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/15/2016
 * Time: 4:06 PM
 */

namespace Webarq\Manager;


use DB;
use Illuminate\Database\Eloquent\Collection;
use Wa;
use Webarq\Model\AdminModel;

class AdminManager extends WatchdogAbstractManager
{
    /**
     * @var AdminModel
     */
    protected $model;

    /**
     * Admin role
     *
     * @var array
     */
    protected $roles;

    /**
     * @var
     */
    protected $permissions;

    /**
     * Admin role levels
     *
     * @var array
     */
    protected $levels = [];

    public function __construct()
    {
        $this->setTable('admins');

        $this->model = new AdminModel();
    }

    /**
     * Identify admin
     * @param array|number $credentials
     * @return $this
     */
    public function identify($credentials = [])
    {
        if (is_array($credentials)) {
// Remove password from credential checking, it will check later
            unset($credentials['password']);
            if (null !== ($admin = $this->model->getByCredential($credentials))) {
                $credentials = $admin->id;
            }
        }

        if (is_numeric($credentials)) {
            $admin = $this->model->find($credentials);
            $this->setRolesAndLevels($admin->roles);
            $this->setPermissions($admin->permissions);
            $this->setProfile($admin->toArray());
        }
        return $this;
    }

    /**
     * @param Collection $roles
     */
    protected function setRolesAndLevels(Collection $roles)
    {
        $this->roles = $roles->toArray();
// Set levels
        if ([] !== $this->roles) {
            foreach ($this->roles as $role) {
                $this->levels[] = $role['level'];
            }
        }
    }

    /**
     * Set admin permissions
     *
     * @param Collection $permission
     */
    protected function setPermissions(Collection $permission)
    {
        if ($permission->count()) {
            foreach ($permission->toArray() as $item) {
                array_set($this->permissions, implode('.', $item), true);
            }
        }
    }

    /**
     * @param array $data
     */
    protected function setProfile(array $data)
    {
// Password should not be in data profile
        $this->password = array_pull($data, 'password');
// Set profile
        $this->profile = $data;
    }

    public function getProfiles()
    {
        return $this->profile;
    }

    /**
     * Check if admin has some role
     *
     * @param $str
     * @param string $key
     * @return bool
     */
    public function hasRole($str, $key = 'title')
    {
        if ([] !== $this->roles) {
            foreach ($this->roles as $role) {
                if ($str === array_get($role, $key)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if admin has permission
     *
     * @param string|array $path Permission path in {module}.{panel?}.{action?} format
     * @return true
     */
    public function hasPermission($path)
    {
        if (is_array($path)) {
// Get operator
            if (is_bool(last($path))) {
                $and = array_pop($path);
            } else {
                $and = false;
            }
// Everyone is suspected as fraud
            $hasPermission = false;
            foreach ($path as $path1) {
                $hasPermission = $this->hasPermission($path1);
// All permissions should be authorized
                if (!$hasPermission && $and) {
                    return false;
// One of the permissions should be enough
                } elseif ($hasPermission && !$and) {
                    return true;
                }
            }
            return $hasPermission;
        } else {
            return null !== $this->getPermission($path);
        }
    }

    /**
     * Get permissions
     *
     * @param $path
     * @return array|null
     */
    public function getPermission($path)
    {
        return array_get($this->permissions, $path);
    }

    /**
     * @param $level
     * @return bool
     */
    public function hasLevel($level)
    {
        return in_array($level, $this->levels);
    }

    /**
     * Get admin levels
     *
     * @param bool|false $highest Set to true to get highest levels
     * @return array|number
     */
    public function getLevel($highest = false)
    {
        if (true === $highest) {
            return min($this->levels);
        }

        return $this->levels;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getProfile($key);
    }

    /**
     * Get admin profile
     *
     * @param mixed $key
     * @param null $default
     * @return mixed
     */
    public function getProfile($key, $default = null)
    {
        return array_get($this->profile, $key, $default);
    }
}