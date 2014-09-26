<?php
/**
 * D5ライブラリの初期化処理を担うクラス
 */
class D5_Core
{
    private static $instance;

    /**
     * @private
     * @ignore
     */
    private function __construct() {}

    /**
     * ライブラリを初期化します。
     * @private
     */
    public static function init()
    {
        if (self::$instance !== null) {
            return;
        }

        // コアの設定ファイルを読み込む
        D5_Config::addLoadPath(D5_COREPATH . 'config/');

        // 出力バッファリングを始める
        // （コマンドラインで実行中はバッファリングしない。表示結果が得られなくなってしまうため）
        PHP_SAPI !== 'cli' and ob_start();

        self::$instance = new self();
    }
}
