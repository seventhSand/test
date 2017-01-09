<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/9/2017
 * Time: 10:46 AM
 */

namespace Webarq\Manager\Cms\HTML\Table;


use Html;
use Illuminate\Contracts\Support\Htmlable;
use URL;
use Wa;
use Webarq\Manager\AdminManager;
use Webarq\Manager\SetPropertyManagerTrait;

class ButtonManager implements Htmlable
{
    use SetPropertyManagerTrait;

    /**
     * @var
     */
    protected $html;

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $panel;

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var AdminManager
     */
    protected $admin;

    /**
     * Button type
     *
     * @var
     */
    protected $type;


    protected $permalink;

    protected $rows = [];

    /**
     * @param AdminManager $admin
     * @param string $module
     * @param string $panel
     * @param array $rows
     * @param array $settings
     */
    public function __construct(AdminManager $admin, $module, $panel, array $rows = [], array $settings = [])
    {
        $this->admin = $admin;
        $this->module = $module;
        $this->panel = $panel;
        $this->rows = $rows;

        $this->setPropertyFromOptions($settings);
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if ($this->permitted()) {
            $suffix = 'create' === $this->type || 'edit' === $this->type ? 'form/' : '';
            return Html::link(
                    URL::panel(URL::detect($this->permalink, $this->module, $this->panel, $suffix . $this->type)),
                    $this->type);
        }
        return '';
    }

    /**
     * @return bool
     */
    protected function permitted()
    {
        if ($this->admin->hasPermission(Wa::formatPermissions($this->type, $this->module, $this->panel, $this->type))
         && Wa::manager('cms.rule', $this->admin, $this->rules, $this->rows)->isValid()) {
            return true;
        }
        return false;
    }
}