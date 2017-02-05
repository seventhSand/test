<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 12:18 PM
 */

if (Wa::config('system.configs.queryLog')) {
    DB::enableQueryLog();
}

/**
 * @param string $class
 * @param string $string
 * @return string
 */
function webarqMakeControllerMethod($class, $string)
{
    $method = config('webarq.system.action-prefix') . studly_case(strtolower(Request::method() . ' ' . $string));

    if (method_exists($class, $method)) {
        return $method;
    }
}

/**
 * @param $namespace
 * @param $directory
 * @param $file
 * @return string
 */
function webarqMakeControllerClass($namespace, $directory, $file)
{
    if (is_file($directory . $file . 'Controller.php')) {
        return $namespace . $file . 'Controller';
    }
}

function webarqMakePattern($paramLength)
{
    $pattern = '{module?}/{panel?}/{action?}';

    if ($paramLength > 0) {
        for ($i = 1; $i <= $paramLength; $i++) {
            $pattern .= '/{param' . $i . '?}';
        }
    }

    return $pattern;
}

function webarqMakeRoutePattern($param)
{
    $pattern = '{module?}/{controller?}/{action?}';

    for ($i = 1; $i <= $param; $i++) {
        $pattern .= '/{param' . $i . '?}';
    }

    return $pattern;
}

/**
 * @param string $group Group directory
 * @param string $a Default module
 * @param string $b Default panel
 * @param string $c Default action
 * @param int $len Param length
 */
function webarqAutoRoute($group, $a, $b = null, $c = null, $len = 4)
{
    $pattern = webarqMakeRoutePattern($len);
    Route::match(['get', 'post'], $pattern, function ($mod = null, $pnl = null, $act = null)
    use ($group, $a, $b, $c, $len) {
// Default controller
        $class = $con = $method = null;
// Set module, controller, and action
        if (null === $mod) {
            $mod = $a;
        }
        $mod = strtolower($mod);
        $scMod = studly_case($mod);

        if (null === $pnl) {
            $pnl = $b;
        }
        $pnl = strtolower($pnl);
        $scPnl = studly_case($pnl);

        if (null === $act) {
            $act = $c;
        }

        if (null !== $act) {
            $act = strtolower($act);
        }
        $scAct = studly_case($act);

// Params
        $params = [];
        $i = 4;
        $t = 3 + $len;

        if ('Panel' === $group) {
            $i += 1;
            $t += 1;
        }

        for ($i; $i <= $t; $i++) {
            if (Request::segment($i)) {
                $params[] = Request::segment($i);
            }
        }

// Server directory separator
        $sep = DIRECTORY_SEPARATOR;
        $ns = 'App' . $sep . 'Http' . $sep . 'Controllers' . $sep . $group . $sep;
        $rt = '..' . $sep . 'app' . $sep . 'Http' . $sep . 'Controllers' . $sep . $group . $sep;
        $md = null;

// Looking out for controller by given url path
// Panel is a directory and action is a file controller
        if (is_dir($rt . $scMod . $sep . $scPnl)
                && null !== ($class = webarqMakeControllerClass($ns, $rt, $scMod . $sep . $scPnl . $sep . $scAct))
        ) {
            $con = $act;
            $act = array_get($params, 0);
            if (null !== ($method = webarqMakeControllerMethod($class, array_get($params, 0)))) {
                array_pull($params, 0);
            }
// Module is a directory, and panel is a controller
        } elseif (is_dir($rt . $scMod)
                && null !== ($class = webarqMakeControllerClass($ns, $rt, $scMod . $sep . $scPnl))
        ) {
            $con = $pnl;
            if (null !== $act && null === ($method = webarqMakeControllerMethod($class, $act))) {
                array_unshift($params, $act);
                $act = null;
            }
// Instead of a directory, this time module is a controller file
        } elseif (null !== ($class = webarqMakeControllerClass($ns, $rt, $scMod))) {
            $con = $mod;
            if (null !== ($method = webarqMakeControllerMethod($class, $pnl))) {
                if (null !== $act) {
                    array_unshift($params, $act);
                }
                $act = $pnl;
            }
        } // Last attempt, get default controller
        elseif (null !== ($con = config('webarq.system.default-controller'))
                && null !== ($class = webarqMakeControllerClass($ns, $rt, studly_case($con)))
        ) {
            if (null !== ($method = webarqMakeControllerMethod($class, $mod))) {
                if (null !== $act) {
                    array_unshift($params, $pnl, $act);
                } else {
                    array_unshift($params, $pnl);
                }
                $act = $mod;
            }
        }

// Yay, found a class
        if (isset($class)) {

            if ('Panel' === $group && 'helper' === $mod) {
                $mod = array_pull($params, 0);
                $pnl = array_pull($params, 1);
// Reindex array keys
                if ([] !== $params) {
                    $params = array_combine(range(1, count($params)), array_values($params));
                }
            }

            $params['module'] = $mod;
            $params['panel'] = $pnl;
            $params['controller'] = $con;
            $params['action'] = $act;

            if (null === $method) {
//                $act = 'Panel' === $group ? 'forbidden' : config('webarq.system.default-action');
                $act = config('webarq.system.default-action');
                $method = config('webarq.system.action-prefix') . ucfirst(strtolower(Request::method()))
                        . studly_case($act);
            }

// Resolving class
            $class = resolve($class, ['params' => $params]);
// Execute class "escape" method if any
            if (method_exists($class, 'escape')) {
                if (null !== ($escape = $class->escape())) {
                    return $escape;
                }
            }
// Execute class "before" method if any
            if (method_exists($class, 'before')) {
                $class->before($params);
            }

// Call method (do not forget about method injection)
            $call = App::call([$class, $method], $params);

            if (!is_null($call)) {
                return $call;
            } elseif (method_exists($class, 'after')) {
                return $class->after();
            } else {
                return view('webarq.errors.204');
            }
        } else {
            abort(404, 'Route not matched');
        }
    });
}

function webarqAutoRouteR($directory)
{
// Create url pattern
    $pattern = '{module?}/{panel?}/{action?}';
    $paramLength = 4;
    if ($paramLength > 0) for ($i = 1; $i <= $paramLength; $i++) $pattern .= '/{param' . $i . '?}';

    Route::match(['get', 'post'], $pattern, function () use ($paramLength, $pattern, $directory) {
// Original params
        $params = [];
        if ('/' !== Request::path()) {
            $params = array_filter(explode('/', strtolower(Request::path())), function ($value) {
                return trim($value) !== '';
            });
        }
// Check if on admin page
        $onAdminPage = false;
        if ('Panel' === $directory) {
            $onAdminPage = true;
            array_pull($params, 0);
        }
// Pull non param item from params array
        $module = array_pull($params, 1, 'System');
        $panel = array_pull($params, 2, config('webarq.system.default-controller', 'base'));
        $action = array_pull($params, 3);

// Server directory separator
        $sep = DIRECTORY_SEPARATOR;
// Set convention name
        $directory1 = studly_case($directory);
        $module1 = studly_case($module);
        $panel1 = studly_case($panel);
        $action1 = studly_case($action);
// Controller action prefix
// File controller should be under App/Http/Controllers/{$directory}
        $namespace = 'App' . $sep . 'Http' . $sep . 'Controllers' . $sep . $directory1 . $sep;
// Relative path in to directory
        $root = '..' . $sep . 'app' . $sep . 'Http' . $sep . 'Controllers' . $sep . $directory . $sep;
        $method = null;
// Starting by checking if segment panel is a directory
        if (is_dir($root . $module1 . $sep . $panel1)
                && null !==
                ($class = webarqMakeControllerClass($namespace, $root, $module1 . $sep . $panel1 . $sep . $action1))
        ) {
            $controller = $action;
// Pull out first parameter item when it is a valid method of class controller
            if (null !== ($method = webarqMakeControllerMethod($class, array_get($params, 4))) || $onAdminPage) {
                $action = array_pull($params, 4);
            }
        } // Down to segment module (as directory)
        elseif (is_dir($root . $module1)
                && null !== ($class = webarqMakeControllerClass($namespace, $root, $module1 . $sep . $panel1))
        ) {
            $controller = $panel;
// Un-shift $action into modified parameters when it is not a valid method of class controller and not in admin page
            if (null === ($method = webarqMakeControllerMethod($class, $action1)) && isset($action) && !$onAdminPage) {
                $params1 = [$action];
                $action = null;
            }
        } // Down to segment directory
        elseif (null !== ($class = webarqMakeControllerClass($namespace, $root, $module1))) {
            $controller = $module;
            $params1 = [];
// Un-shift $action into modified parameters
            if (isset($action)) {
                $params1[] = $action;
            }
            if (null !== ($method = webarqMakeControllerMethod($class, $panel1)) && isset($panel) || $onAdminPage) {
                $action = $panel;
            } elseif (isset($panel)) {
// Un-shift $panel into modified parameters
                array_unshift($params1, $panel);
            }
        } // Down to default controller
        elseif (null !== ($controller = config('webarq.system.default-controller'))
                && null !== ($class = webarqMakeControllerClass($namespace, $root, studly_case($controller)))
        ) {
            $params1 = [];
// Un-shift $panel into modified parameters
            if (isset($panel)) {
                $params1[] = $panel;
// Un-shift $action into modified parameters
                if (isset($action)) {
                    $params1[] = $action;
                }
            }

            if ((null === ($method = webarqMakeControllerMethod($class, $module1)) && isset($module)) || $onAdminPage) {
                $action = $module;
// Un-shift $module into parameters
            } elseif (isset($module)) {
                array_unshift($params1, $module);
            }
        }
// Merge unexpected arguments
        if (isset($params1) && [] !== $params1) {
            $params = array_merge($params1, $params);
        } elseif ([] !== $params) { // Re-indexing parameters while not empty
            $params = array_combine(range(1, count($params)), array_values($params));
        }

// Yay, found a class
        if (isset($class)) {
            if (is_null($method)) {
                $action = isset($action) && $onAdminPage && null !== Request::segment(2)
                        ? 'forbidden' : config('webarq.system.default-action');
                $method = config('webarq.system.action-prefix') . ucfirst(strtolower(Request::method()))
                        . studly_case($action);
            }

// When on admin page and using helper
            if ($onAdminPage && 'helper' === $module) {
                $module = array_pull($params, 1);
                $panel = array_pull($params, 2);
                array_unshift($params, 'blank');
                unset($params[0]);
            }

            $params += [
                    'module' => $module,
                    'panel' => $panel,
                    'controller' => $controller,
                    'action' => $action,
            ];

// Resolving class
            $class = resolve($class, ['params' => $params]);
// Execute class object "before" method if any
            if (method_exists($class, 'before')) {
                if (null !== ($before = $class->before($params))) {
                    return $before;
                }
            }

// Call method (do not forget about method injection)
            $call = App::call([$class, $method], $params);
            if (!is_null($call)) {
                return $call;
            } elseif (method_exists($class, 'after')) {
                return $class->after();
            } else {
                return view('webarq.errors.204');
            }
        } else {
            abort(404, 'Route not matched');
        }
    });
}