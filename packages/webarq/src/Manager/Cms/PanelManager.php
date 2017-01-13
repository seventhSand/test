<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/10/2017
 * Time: 1:39 PM
 */

namespace Webarq\Manager\Cms;


use Wa;
use Webarq\Info\PanelInfo;
use Webarq\Manager\AdminManager;

/**
 * Class PanelManager
 *
 * @package Webarq\Manager\Cms
 */
class PanelManager
{
    /**
     * @var AdminManager
     */
    protected $admin;

    /**
     * @var
     */
    protected $menus = [];

    /**
     * Create PanelManager instance
     *
     * @param null|AdminManager $admin
     */
    public function __construct(AdminManager $admin = null)
    {
        $this->admin = $admin;

        $this->collectMenus();
    }

    /**
     * Collect menus
     */
    protected function collectMenus()
    {
        $modules = Wa::modules();

        if (is_array($modules) && [] !== $modules) {
            foreach ($modules as $module) {
                $module = Wa::load('info.module', $module);
                if ([] !== ($panels = $module->getPanels())) {
                    foreach ($panels as $panel) {
                        if ($this->isAccessible($module->getName(), $panel->getName())) {
                            $this->menus[$module->getName()][$panel->getName()] = [
                                    $panel->getLabel(),
                                    $this->generateURL($panel->getPermalink(), $module->getName(), $panel->getName(),
                                            $this->getAction($panel))
                            ];
                        }
                    }
                }
            }
        }
    }

    /**
     * Check if given module, panel, action path is accessible by current admin
     *
     * @param $module
     * @param null $panel
     * @param null|string|array $action
     * @return true
     */
    public function isAccessible($module, $panel = null, $action = null)
    {
        if (!is_null($action)) {
            if (!is_array($action)) {
                $action = [$action];
            }

            foreach ($action as &$item) {
                if (is_bool($item)) {
                    continue;
                }
                $item = trim($item, '.');

                switch (substr_count($item, '.')) {
                    case 1 :
                        $item = $module . '.' . $item;
                        break;
                    case 0 :
                        $item = $module . '.' . $panel . '.' . $item;
                        break;
                }
            }
        } else {
            $action = $module . (isset($panel) ? '.' . $panel : '');
        }

        return ('system' === $module && 'dashboard' === $panel)
        || $this->admin->hasPermission($action);
    }

    /**
     * @param $permalink
     * @param $module
     * @param $panel
     * @param string $action
     * @param array $rows
     * @return string
     */
    public function generateURL($permalink, $module, $panel, $action = '', array $rows = [])
    {
        list($permalink, $params) = $this->getPermalinkAndParams($permalink, $action, $panel);

        return \URL::panel(\URL::detect($permalink, $module, $panel, $this->suffixed($action)))
        . $this->suffixedParam($params, $rows);
    }

    /**
     * @param string $permalink
     * @param string $action
     * @param string $panel
     * @return array
     */
    protected function getPermalinkAndParams($permalink, $action, $panel)
    {
        $params = [];
// Injected params
        if (is_string($permalink)) {
            if (starts_with($permalink, '.')) {
                $params = explode(',', substr($permalink, 1));
                $permalink = true;
            } elseif (starts_with($permalink, '?')) {
                $params = explode(',', substr($permalink, 1));
                $permalink = null;
            }
        }

// Default params
        if ([] === $params && null !== ($panel = Wa::table($panel))) {
            $params[] = $panel->primaryColumn()->getName();
            switch ($action) {
                case 'activeness':
                    $params[] = 'is_active';
                    break;
            }
        }

        return [$permalink, $params];
    }

    /**
     * @param $action
     * @return string
     */
    protected function suffixed($action)
    {
        switch ($action) {
            case 'create':

            case 'edit':
                return 'form/' . $action;

            default:
                return $action;

        }
    }

    /**
     * @param array $params
     * @param array $rows
     * @return string
     */
    protected function suffixedParam(array $params, array $rows)
    {
        $str = '';

        if ([] !== $params && $rows !== []) {
            foreach ($params as $param) {
                $str .= '/' . array_get($rows, $param);
            }
        }

        return $str;

    }

    /**
     * @param PanelInfo $panel
     * @return string
     */
    protected function getAction(PanelInfo $panel)
    {
        if (false !== $panel->getListing() && null !== $panel->getListing()) {
            $action = 'listing/';
        } else {
            $action = '';
        }

        return $action . config('webarq.system.default-action');
    }

    /**
     * @return null|AdminManager
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Generate completed panel html element
     *
     * @param null|string $view
     * @return string
     */
    public function generateMenu($view = null)
    {
        $html = '';

        if (is_null($view)) {
            if ([] !== $this->menus) {
                $html = '<ul class="navigation main">';
                foreach ($this->menus as $module => $items) {
                    $html .= '<li>';
                    $html .= '<h3 style="text-transform: capitalize;margin-bottom: 0;">' . $module . '</h3>';
                    $html .= '<ul class="navigation child">';
                    foreach ($items as $item) {
                        $html .= '<li>' . \Html::link($item[1], $item[0]) . '</li>';
                    }
                    $html .= '</ul>';
                    $html .= '</li>';
                }
            }
            return $html . '</ul>';
        }

        return view($view, ['menus' => $this->menus, 'html' => $html]);
    }

    /**
     * @param array $actions
     * @param $module
     * @param $panel
     * @param array $row
     * @return string
     */
    public function generateActionButton($actions = [], $module, $panel, array $row = [])
    {
// Keep actions as array
        if (!is_array($actions)) {
            $actions = [$actions];
        }

        $html = '';

        if ([] !== $actions) {
            foreach ($actions as $action => $setting) {
// Button permissions
                $permissions = array_pull($setting, 'permissions', []);
                if (!is_array($permissions)) {
                    $permissions = [$permissions];
                }
                $permissions = array_merge([$action], $permissions);

                if ($this->isAccessible($module, $panel, $permissions)) {
// Pull out rules from button settings
                    $rules = array_pull($setting, 'rules', []);

                    if (Wa::manager('cms.rule', $this->admin, $rules, $row)->isValid()) {
                        $setting['permalink'] = $this->generateURL(array_get($setting, 'permalink'),
                                $module, $panel, $action, $row);

                        $html .= Wa::manager('cms.HTML!.table.button', $setting + ['type' => $action])->toHtml();
                    }
                }
            }
        }

        return $html;
    }
}