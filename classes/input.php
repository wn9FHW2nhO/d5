<?php
/**
 * リクエストに含まれるパラメータを取得します。（POST, GETなど）
 * 
 * @package Roose
 * @author うちやま
 * @since PHP 5.2.17
 * @version 1.0.0
 */ 
class Roose_Input {

    /**
     * リクエストメソッドの種類を取得します。
     * （POST, GET, etcetc...)
     * @return string
     */ 
    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }


    /**
     * POSTパラメータの値を取得します。
     * 
     * @param string|null $key (optional) 取得するパラメータ名
     * @param mixed|null $default (optional) パラメータが存在しない時のデフォルト値
     * @return mixed
     */ 
    public static function post($key = null, $default = null)
    {
        return Roose_Arr::get($_POST, $key, $default);
    }


    /**
     * GETパラメータの値を取得します。
     * 
     * @param string|null $key (optional) 取得するパラメータ名
     * @param mixed|null $default (optional) パラメータが存在しない時のデフォルト値
     * @return mixed
     */ 
    public static function get($key = null, $default = null)
    {
        return Roose_Arr::get($_GET, $key, $default);
    }


    /**
     * $_SERVER変数の値を取得します。
     *
     * @param string|null $key (optional) 取得する属性名
     * @param mixed|null $default (optional)パラメータが存在しない時のデフォルト値
     * @return mixed
     */
    public static function server($key = null, $default = null)
    {
        return Roose_Arr::get($_SERVER, $key, $default);
    }
}
