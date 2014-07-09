<?php
/**
 * nullセーフな配列操作クラス
 * 
 * ドット記法をメインに、配列操作系の便利なメソッドが揃っています。<br>
 * 
 * Example:
 * ```php
 * <?php
 *  $ar  = array();
 * 
 *  Roose_Arr::set($ar, 'element.0', 'value'); // クッソ長い
 *  
 *  Arr::set($ar, 'element.1', 'value'); // It's fuckin cool code.
 * ```
 * 
 * 
 * ```
 * このクラスは Arrクラスとして呼び出すことが可能です。`
 * ```
 * 
 * @package Roose
 * @author うちやま
 * @since PHP 5.2.17
 * @version 1.0.0
 */
class Roose_Arr
{
    /**
     * 指定された配列から要素を取得します。
     * 
     * キーにドット区切りの文字列を与えると、多次元配列の任意の階層を指定できます。
     * 
     * 例えば、第一引数に$array、第二引数に"deep.deep.index"を指定した時は、  
     * $array["deep"]["deep"]["index"]を参照します。  
     * 
     * 指定された要素を取得できない場合は$defaultに指定された値を返します。
     * 
     *        $value = Arr::get($array, "deep.deep.index"); // これは
     *        $value = $array["deep"]["deep"]["index"]; // これと同じことです。
     *        
     *        // しかし $array["deep"]["deep"]["index"]が存在しなければ後者はエラーを発しますが
     *        // Arr::get()ではnullが返され、エラーは起きません。
     *
     * @param array $array 操作する配列
     * @param mixed|null $key 取得するインデックス
     * @param mixed|null $default 初期値。デフォルト値はnull
     * @return mixed
     */
    public static function get(Array &$array, $key = null, $default = null)
    {
        if ($key === null) {
            // keyがnullなら配列をそのまま返す
            return $array;
        }
        
        if (is_array($key)) {
            $ar = array();
            
            foreach ($key as $v) {
                Arr::set($ar, $v, Roose_Arr::get($array, $v, $default));
            }
            
            return $ar;
        }
        
        if (is_int($key)) {
            // key数値ならindexが存在するかチェックして適切な値を返す
            return isset($array[$key]) ? $array[$key] : $default;
        }
        
        $path = explode(".", $key);
        $index = array_pop($path);
        $pt = &$array;

        // 多次元配列を掘る
        foreach ($path as $k) {
            if (isset($pt[$k]) and is_array($pt[$k])) {
                $pt = &$pt[$k];
            } else {
                return $default;
            }
        }

        return isset($pt[$index]) ? $pt[$index] : $default;
    }


    /**
     * 指定された配列に要素を設定します。
     * 
     * キーにドット区切りの文字列を与えると、多次元配列の任意の階層を指定できます。
     * （インデックス指定方法の詳細は、getメソッドのコメントを参照）
     * 
     *  途中の階層が存在しない場合、自動的に配列を生成して続行します。
     * （インデックスが存在し、要素が配列でない場合に
     *  要素が配列に置き換えられることに注意してください）
     * 
     *         $user = array(
     *             "name" => "John",
     *             "detail" => array(
     *                 "age" => 24
     *             )
     *         );
     * 
     *         // $user["detail"]["age"]を12に変更します。
     *         Arr::set($user, "detail.age", 12);
     * 
     *         // 新しい階層を自動で生成します。
     *         // すでにインデックスが存在している場合、その値が上書きされることに注意してください。
     *         // このサンプルでは $user["name"]を配列にしています。
     *         Arr::set($user, array("name.first" => "John", "name.last" => "Discors"));
     *
     * @param Array &$array
     * @param string|array $key
     * @param mixed|null $value
     */
    public static function set(Array &$array, $key, $value = null)
    {
        if (is_array($key)) {
            // keyが配列の時、$arrayに$keyの内容を統合
            foreach ($key as $k => $v) {
                self::set($array, $k, $v);
            }
            return;
        }

        $path = explode(".", $key);
        $index = array_pop($path);
        $pt = &$array;

        // 多次元配列を掘る
        foreach ($path as $k) {
            if (!isset($pt[$k]) || !is_array($pt[$k])) {
                $pt[$k] = array();
            }

            $pt = &$pt[$k];
        }

        $pt[$index] = $value;
    }


    /**
     * 配列の指定したインデックスを削除します。
     * 
     * キーにドット区切りの文字列を与えると、多次元配列の任意の階層を指定できます。
     * 
     *        $user = array(
     *             "name" => "John",
     *             "age" => 24
     *         );
     *         
     *         // $user["age"]を削除
     *         Arr::delete($user, "age");
     * 
     * @param Array $array 操作対象の配列
     * @param string $key 削除するインデックス
     */
    public static function delete(Array &$array, $key = null)
    {
        if (empty($key)) {
            $keys = array_keys($array);
            foreach ($keys as $k) {
                Arr::delete($array, $k);
            }
            return;
        }
        
        if (is_array($key)) {
            foreach ($key as $k) {
                Arr::delete($array, $k);
            }
            return;
        }

        $path = explode(".", $key);
        $index = array_pop($path);
        $pt = &$array;

        // 多次元配列を掘る
        foreach ($path as $k) {
            if (isset($pt[$k]) and is_array($pt[$k])) {
                $pt = &$pt[$k];
            } else {
                return;
            }
        }

        unset($pt[$index]);
    }


    /**
     * array_map関数の入れ子対応版
     * 
     * @param array $array 操作するの配列
     * @param callable $fn 配列を処理する関数
     */
    public static function mapRecursive(Array $array, $fn)
    {
        foreach ($array as $k => $v) {
            if (is_array($array)) {
                $array[$k] = self::array_map_recursive($array[$k], $fn);
            } else {
                $array[$k] = call_user_func($fn, $v);
            }
        }
        
        return $array;
    }
}
