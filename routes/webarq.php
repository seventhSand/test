<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 12:18 PM
 */

/*
 * By default, uri formatted in "{directory segment}/{module segment}/{panel segment}/{action segment}/{parameters ...}
 */


/**
 * @param string $class
 * @param string $string
 * @return string
 */
function webarqMakeControllerMethod($class, $string)
{
    $method = config('webarq.system.action-prefix') . ucfirst(strtolower(Request::method())) . $string;
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
    if (is_file($directory . DIRECTORY_SEPARATOR . $file . 'Controller.php')) {
        return $namespace . $file . 'Controller';
    }
}

if (Wa::config('system.configs.queryLog')) {
    DB::enableQueryLog();
}
// Starts with accepted url format
$urlFormat = '{directory?}/{module?}/{panel?}/{action?}';
// Following by accepted parameter
$paramLength = 4;
if ($paramLength > 0) {
    for ($i = 1; $i <= $paramLength; $i++) {
        $urlFormat .= '/{param' . $i . '?}';
    }
}
Route::match(['get', 'post'], $urlFormat, function () use ($paramLength, $urlFormat) {
// Original params
    $params = '/' !== Request::path()
            ? array_filter(explode('/', strtolower(Request::path())), function ($value) {
                return trim($value) !== '';
            })
            : [];

    $directory = array_pull($params, 0, 'Site');
    $module = array_pull($params, 1, 'System');
    $panel = array_pull($params, 2, config('webarq.system.default-controller', 'base'));
    $action = array_pull($params, 3);
// Since we do not need to write down directory name in url while accessing site page,
// we need to re-assign $directory, $module, $panel, $action and $params value
    if (config('webarq.system.panel-url-prefix') !== $directory) {
        $action = $panel;
        $panel = $module;
        $module = $directory;
        $directory = 'Site';
        $inAdminPage = false;
    } else {
        $inAdminPage = true;
        $directory = 'Panel';
    }
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
    if (is_dir($root . $module1 . $sep . $panel1) &&
            null !==
            ($class = webarqMakeControllerClass($namespace, $root, $module1 . $sep . $panel1 . $sep . $action1))
    ) {
        $controller = $action;
// Pull out first parameter item when it is a valid method of class controller
        if (null !== ($method = webarqMakeControllerMethod($class, array_get($params, 4))) || $inAdminPage) {
            $action = array_pull($params, 4);
        }
    } // Down to segment module (as directory)
    elseif (is_dir($root . $module1) &&
            null !== ($class = webarqMakeControllerClass($namespace, $root, $module1 . $sep . $panel1))
    ) {
        $controller = $panel;
// Un-shift $action into modified parameters when it is not a valid method of class controller and not in admin page
        if (null === ($method = webarqMakeControllerMethod($class, $action1)) && isset($action) && !$inAdminPage) {
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
        if (null !== ($method = webarqMakeControllerMethod($class, $panel1)) && isset($panel) || $inAdminPage) {
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

        if ((null === ($method = webarqMakeControllerMethod($class, $module1)) && isset($module)) || $inAdminPage) {
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
            $action = isset($action) && isset($inAdminPage) && null !== Request::segment(2)
                    ? 'forbidden' : config('webarq.system.default-action');
            $method = config('webarq.system.action-prefix') . ucfirst(strtolower(Request::method()))
                    . studly_case($action);
        }
// Resolving class
        $class = resolve($class, [
                'controller' => $controller,
                'module' => $module, 'panel' => $panel,
                'action' => $action, 'params' => $params]);

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