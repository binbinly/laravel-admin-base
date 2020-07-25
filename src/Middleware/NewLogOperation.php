<?php

namespace AdminBase\Middleware;

use AdminBase\Models\Admin\NewOperationLog;
use Closure;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Middleware\LogOperation;
use Illuminate\Http\Request;
use ReflectionException;
use ReflectionClass;
use Exception;

/**
 * 操作日志
 * Class NewLogOperation
 * @package AdminBase\Middleware
 */
class NewLogOperation extends LogOperation
{
    /**
     * 操作日志收集
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldLogOperation($request)) {
            $routeName = $request->route()->getName();
            if ($routeName == "admin.handle-form") {
                $className = $request->input('_form_');
                try {
                    $class = new ReflectionClass($className);
                    $title = $class->getProperty('title')->getValue(new $className);
                } catch (ReflectionException $e) {
                    return $next($request);
                }
            } elseif ($routeName == "admin.handle-action") {
                $className = str_replace('_', '\\', $request->input('_action'));
                try {
                    $class = new ReflectionClass($className);
                    $title = $class->getProperty('name')->getValue(new $className);
                } catch (ReflectionException $e) {
                    return $next($request);
                }
            } else {
                $title = $routeName;
                $words = ['update', 'store', 'destroy'];
                foreach ($words as $word)//遍历过滤库的词
                {
                    $len = strlen($word);//获取过滤词的长度
                    $pos = strpos($title, $word);//寻找过滤词的位置
                    if (!$pos) {
                        continue;
                    }
                    $newWord = NewOperationLog::$routeLabel[$word];
                    $title = substr_replace($title, $newWord, $pos, $len);
                }
            }

            $log = [
                'user_id' => Admin::user()->id,
                'path' => substr($request->path(), 0, 255),
                'method' => $request->method(),
                'ip' => $request->getClientIp(),
                'input' => json_encode($request->input()),
                'do' => $title,
            ];

            try {
                NewOperationLog::create($log);
            } catch (Exception $exception) {
                // pass
            }
        }

        return $next($request);
    }
}
