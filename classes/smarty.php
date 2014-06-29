<?php
/**
 * Rooseによる Smartyラッパークラス
 * 
 * HTMLの自動エスケープなどの機能が追加されたSmartyのラッパークラスです。<br>
 * このクラスを使用する前に、
 * Roose_Config::loadメソッドで、smartyの設定を読み込むことを推奨します。<br>
 * そのようにすることで、プロジェクトで一貫した設定を使い回すことができるためです。
 * 
 * 読み込みサンプル
 *      //-- app/bootstrap.php
 *      define('APPPATH', dirname(__FILE__));
 *      
 *      Config::load(APPPATH . '/config/smarty.php', 'smarty');
 *      // もしくは
 *      Config::addLoadPath(APPPATH . '/config/');
 *      
 *      
 *      //-- app/config/smarty.php
 *      <?php
 *          return array(
 *              // string: テンプレートが保存されているディレクトリ
 *              'template_dir' => APPPATH . '/templates/',
 *              
 *              // string: コンパイル済みテンプレートを保存するディレクトリ
 *              'compile_dir' => APPPATH . '/tmp/smarty/compiled/,
 *              
 *              // string: 設定ファイルが保存されているディレクトリ
 *              'config_dir' => null,
 *              
 *              // string: キャッシュファイルを保存するディレクトリ
 *              'cache_dir' => APPPATH . '/tmp/smarty/cache/,
 *              
 *              // boolean: キャッシュの有効 / 無効
 *              'caching' => true,
 *          );
 *      
 *      
 *      //-- somefile.php
 *      $smarty = Roose_Smarty::instance();
 *      $smarty-> *do_something*
 * 
 * @package Roose
 * @author うちやま
 * @since PHP 5.2.17
 * @version 1.0.0
 */ 
class Roose_Smarty extends Smarty
{
    /**
     * @ignore
     * @var array(Roose_Smarty) 生成したインスタンス
     */ 
    private static $instances = array();
    
    
    /**
     * 与えられた文字列を安全なHTML文字列に変換します。
     * 
     * @ignore
     * @param string $str
     */
    private static function toSafeString($str)
    {
        if (is_string($str)) {
            return Roose_Security::safeHtml($str, true);
        } else {
            return $str;
        }
    }
    
    /**
     * Roose_Smartyのインスタンスを取得します。
     * 
     * @param string $name (optional) 取得するインスタンスの名前。
     *   指定されない場合、'null'を使用します。
     * @param array $config Roose_Smartyをインスタンス化するときの設定
     * @return Roose_Smarty
     */
    public static function instance($name = null, $config = array())
    {
        $name = $name === null ? 'default' : $name;
        
        if (isset(self::$instances[$name]) === false) {
            self::$instances[$name] = new self($config);
        }
        
        return self::$instances[$name];
    }
    
    
    /**
     * Smartyのdefaultインスタンスに値を割り当てます。
     * 文字列は自動的にHTMLエスケープされます。
     * 
     * @param string|array $key テンプレート内の変数名
     * @param mixed $value 割り当てる値 / オブジェクト
     * @param boolean (optional) 値を自動的エスケープするか。デフォルト値はtrue
     * @return Roose_Smarty
     */
    /*
    public static function set($key, $value = null, $filtering = true)
    {
        return self::instance()->set($key, $value, $filtering);
    }
    */
    
    /**
     * Smartyのdefaultインスタンスへ割り当てた値を参照します。
     * 
     * @param string キー名
     * @param mixed|null $default 値が設定されていなかった時の初期値
     */
    /*
    public static function get($key, $default = null)
    {
        return self::instance()->set($key, $default);
    }
    */
    
    
    /**
     * @var array smartyに割り当てられた値
     */ 
    private $assign = array();
    
    /**
     * @param array $config Smartyの設定
     */ 
    public function __construct($config = array())
    {
        parent::__construct();
        
        // 設定ファイルを読み込む
        $conf = Roose_Config::get('smarty');
        array_merge($conf, $config);
        
        $this->template_dir = Arr::get($conf, 'template_dir') . DS;
        $this->compile_dir  = Arr::get($conf, 'compile_dir') . DS;
        $this->config_dir   = Arr::get($conf, 'config_dir') . DS;
        $this->cache_dir    = Arr::get($conf, 'cache_dir') . DS;
        $this->caching      = Arr::get($conf, 'caching', true);
    }
    
    
    /**
     * Smartyの実行結果を取得します。
     * 
     * @param string $template 使用するテンプレートへのパス。
     * @param string|null $cache_id (optional) キャッシュID
     * @param string|null $compile_id (optional) コンパイルファイルID
     */
    public function fetch($template, $cache_id = null, $compile_id = null)
    {
        // 変数割り当てを一旦解除
        parent::clear_all_assign();
        
        // 再割り当て
        foreach (array_keys($this->assign) as $k) {
            parent::assign_by_ref($k, $this->assign[$k]);
        }
        
        return parent::fetch($template, $cache_id, $compile_id);
    }
    
    
    /**
     * Smartyの実行結果を出力（表示）します。
     * 
     * @param string $template 使用するテンプレートへのパス。
     * @param string|null $cache_id (optional) キャッシュID
     * @param string|null $compile_id (optional) コンパイルファイルID
     */
    public function display($template, $cache_id = null, $compile_id = null)
    {
        echo $this->fetch($template, $cache_id, $compile_id);
    }
    
    
    /**
     * @ignore
     * @return Roose_Smarty
     */
    public function assign($key, $value = null, $filtering = true)
    {
        return $this->set($key, $value, $filtering);
    }
    
    
    /**
     * @ignore
     * @return Roose_Smarty
     */
    public function assign_by_ref($key, $value)
    {
        return $this->set($key, $value, false);
    }
    
    
    /**
     * @ignore
     * @return Roose_Smarty
     */
    public function clear_all_assign()
    {
        unset($this->assign);
        $this->assign = array();
        
        return $this;
    }
    
    /**
     * Smartyに値を割り当てます。
     * 文字列は自動的にHTMLエスケープされます。
     * 
     * @param string|array $key テンプレート内の変数名
     * @param mixed $value 割り当てる値 / オブジェクト
     * @param boolean $filtering (optional) 値を自動的エスケープするか。デフォルト値はtrue
     * @return Roose_Smarty
     */
    public function set($key, $value = null, $filtering = true)
    {
        if ($filtering !== false) {
            if (is_array($key)) {
                $key = Roose_Arr::map_recursive($key, array('Roose_Smarty', 'toSafeString'));
            } else {
                $value = self::toSafeString($value);
            }
        }
        
        Arr::set($this->assign, $key, $value);
        return $this;
    }
    
    /**
     * Smartyへ割り当てた値を参照します。
     * 
     * @param string|null キー名
     * @param mixed|null $default 値が設定されていなかった時の初期値
     */
    public function get($key = null, $default = null)
    {
        return Arr::get($this->assign, $key, $default);
    }
}