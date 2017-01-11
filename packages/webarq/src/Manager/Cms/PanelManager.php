<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/10/2017
 * Time: 1:39 PM
 */

namespace Webarq\Manager\Cms;


use Wa;
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
     * @var array
     */
    protected $modules = [];

    /**
     * Create PanelManager instance
     *
     * @param AdminManager $admin
     */
    public function __construct(AdminManager $admin = null)
    {
        $this->admin = $admin;

        $modules = Wa::modules();

        if (is_array($modules) && [] !== $modules) {
            foreach ($modules as &$module) {
                $this->modules[$module] = Wa::load('info.module', $module);
            }
        }
    }

    public function isAccessible($module, $panel, $action)
    {

    }

    /**
     * Generate completed panel html element
     *
     * @return string
     */
    public function generateMenu()
    {
        $str = '';

        if ([] !== $this->modules) {
            $str = '<ul class="navigation main">';
            foreach ($this->modules as $module) {
                if ([] !== ($panels = $module->getPanels())) {
                    $str .= '<li><h3 style="text-transform: capitalize;margin-bottom: 0;">' . $module->getName() . '</h3>';
                    $str .= '<ul class="navigation child">';
                    foreach ($panels as $panel) {
                        $action = [] !== $panel->getListing() || false !== $panel->getListing() ? 'listing/' : '';
                        $action .= config('webarq.system.default-action');

                        $str .= '<li>'
                                . \Html::link(
                                        $this->generateURL($panel->getPermalink(), $module->getName(),
                                                $panel->getName(), $action), $panel->getLabel())
                                . '</li>';
                    }
                    $str .= '</ul>';
                    $str .= '</li>';

                }
            }
            $str .= '</ul>';
        }

        return $str;
    }

    public function generateURL($permalink, $module, $panel, $action = '', array $rows = [])
    {
        list($permalink, $params) = $this->getURLParams($permalink, $action, $panel);

        return \URL::panel(\URL::detect($permalink, $module, $panel, $this->suffixed($action)))
                . $this->suffixedParam($params, $rows);
    }

    /**
     * @param string $permalink
     * @param string $action
     * @param string $panel
     * @return array
     */
    protected function getURLParams($permalink, $action, $panel)
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
            switch($action) {
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
}