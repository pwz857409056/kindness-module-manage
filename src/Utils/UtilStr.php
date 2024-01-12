<?php

namespace Kindness\ModuleManage\Utils;

use Illuminate\Support\Str;

class UtilStr
{
    /**
     * @desc:方法将带有 _的字符串转换成驼峰命名的字符串
     *
     * @return string
     */
    public static function getStudlyName($name): string
    {
        return Str::studly($name);
    }

    /**
     * @desc:获取小写名字
     *
     * @return string
     */
    public static function getLowerName($name): string
    {
        return strtolower($name);
    }

    /**
     * @desc:获取全大写名字
     *
     * @return string
     */
    public static function getUpperName($name): string
    {
        return strtoupper($name);
    }
}
