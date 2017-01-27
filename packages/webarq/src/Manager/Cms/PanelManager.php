<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/10/2017
 * Time: 1:39 PM
 */

namespace Webarq\Manager\Cms;


use Wa;
use Webarq\Info\ModuleInfo;
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
                        if ($this->isAccessible($module, $panel)) {
                            $action = false !== $panel->getListing() && null !== $panel->getListing() ? 'listing' : '';
                            $link = $this->generateURL($panel->getPermalink(),
                                    $module->getName(), $panel->getName(), $action
                            );
                            $this->menus[$module->getTitle()][$panel->getName()] = [$panel->getTitle(), $link];
                        }
                    }
                }
            }
        }
    }

    /**
     * Check if given module, panel, action path is accessible by current admin
     *
     * @param ModuleInfo $module
     * @param PanelInfo $panel
     * @param null|string|array $action
     * @return true
     */
    public function isAccessible(ModuleInfo $module, PanelInfo $panel = null, $action = null)
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
                        $item = $module->getName() . '.' . $item;
                        break;
                    case 0 :
                        $item = $module->getName() . '.' . $panel->getName() . '.' . $item;
                        break;
                }
            }
        } else {
            $action = $module->getName() . (null !== $panel->getName() ? '.' . $panel->getName() : '');
        }

        return !$panel->isGuarded() || $this->admin->hasPermission($action);
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

        return \URL::panel(\URL::detect($permalink, $module, $panel, $this->makeAction($action, $permalink)))
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
     * @param mixed $action
     * @param mixed $permalink
     * @return string
     */
    protected function makeAction($action, $permalink)
    {
        switch ($action) {
            case 'create':

            case 'edit':
                return 'form/' . $action;

            default:
                if (null === $permalink) {
                    $action .= '/' . config('webarq.system.default-action');
                }
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
     * @return null|AdminManager
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @return array
     */
    public function getMenus()
    {
        return $this->menus;
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
     * @param ModuleInfo $module
     * @param PanelInfo $panel
     * @param array $row
     * @return string
     */
    public function generateActionButton($actions = [], ModuleInfo $module, PanelInfo $panel, array $row = [])
    {
// Keep actions as array
        if (!is_array($actions)) {
            $actions = [$actions];
        }

        $html = '';

        if ([] !== $actions) {
            foreach ($actions as $action => $options) {
// Button permissions
                $permissions = array_pull($options, 'permissions', []);
                if (!is_array($permissions)) {
                    $permissions = [$permissions];
                }
                $permissions = array_merge([$action], $permissions);

                if ($this->isAccessible($module, $panel, $permissions)) {
// Pull out rules from button settings
                    $rules = array_pull($options, 'rules', []);

                    if (Wa::manager('cms.rule', $this->admin, $rules, $row)->isValid()) {
                        $options['permalink'] = $this->generateURL(array_get($options, 'permalink'),
                                $module->getName(), $panel->getName(), $action, $row);

                        $html .= Wa::manager('cms.HTML!.table.button', $options + ['type' => $action])->toHtml();
                    }
                }
            }
        }

        return $html;
    }
}