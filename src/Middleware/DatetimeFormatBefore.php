<?php

namespace AdminBase\Middleware;

use AdminBase\Common\Format;
use Closure;
use Illuminate\Http\Request;

/**
 * 兼容mongo，格式化请求参数
 * Class DatetimeFormatBefore
 * @package AdminBase\Middleware
 */
class DatetimeFormatBefore
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $params = Format::formatRequest($request->all());

        $request->merge($params);

        return $next($request);
    }
}
