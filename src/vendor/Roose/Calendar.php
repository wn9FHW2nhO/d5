<?php
/**
* カレンダー生成用クラス。
*
* @package Roose
* @author うちやま
* @since PHP 5.2.17
* @implements Iterator
* @version 1.0.0
*/ 
class Roose_Calender implements Iterator
{
    
    private $basetime = null;
    private $_iterated = 0;
    
    /**
     * @param int|null $year （任意）生成するカレンダーの年
     * @param int|null $month （任意）生成するカレンダーの月
     */ 
    public function __constructor(int $from_year = null, int $from_month = null, $from_day = null)
    {
        $basetime = $this->basetime = new DateTime();
        
        if ($from_year === null) {
            $from_year = $basetime->format('Y')|0;
        }
        
        if ($from_month === null) {
            $from_month = $basetime->format('n')|0;
        }
        
        if ($from_day === null) {
            $from_day = 1;
        }
        
        $basetime->setDate($from_year, $from_month, $from_day);
    }
    
    /**
     * Iteratorインターフェースの実装
     * @return void
     * @ignore
     */ 
    public function rewind() {
        $this->_iterated = 0;
    }
    
    /**
     * @return void
     * @ignore
     */ 
    public function next() {
        $this->_iterated++;
    }
    
    /**
     * @return int
     * @ignore
     */
    public function key() {
        return $this->_iterated;
    }
    
    /**
     * @return int
     * @ignore
     */ 
    public function current() {
        return array(
            'y' => $this->basetime->format('Y')|0,
            'm' => $this->basetime->format('n')|0,
            'd' => $this->basetime->format('j')|0,
            'weekday' => $this->basetime->format('w')|0
        );
    }
    
    /**
     * @return bool
     * @ignore
     */ 
    public function valid() {
        return true;
    }
    
    /**
     * 設定されている月の最初の曜日を取得します。
     * 
     * 0〜6の数値が返され、それぞれ日曜〜土曜に対応しています。
     * @return int 曜日(数値）
     */ 
    public function getFirstWeekday()
    {
        return $this->basetime->format('w')|0;
    }
    
    /**
     * 設定されている月の日数を取得します。
     * @return int 日数
     */
    public function getDays()
    {
        return $this->basetime->format('t')|0;
    }
}


    $today = new DateTime(); // 今日の日付
    $basetime = new DateTime(); // 表示する日付

    // GETパラメータがあれば表示日時を変更する
    if ($_GET) {
        $y = $basetime->format('Y')|0;
        $m = $basetime->format('m')|0;
        
        // パラメータ"y"(年)に数値がセットされていればその値に切り替え
        if (isset($_GET['y']) and is_int($_GET['y']|0)) {
            $y = $_GET['y']|0;
        }
        
        // パラメータ"m"(月)に数値がセットされていて、
        // その数値が１〜１２の間であれば、その値に切り替え
        if (isset($_GET['m']) and is_int($_GET['m']|0) and
            ($_GET['m'] >= 1 and $_GET['m'] <= 12)) {
            $m = $_GET['m'] | 0;
        }
        
        // 表示日時を変更
        $basetime->setDate($y, $m, 1);
    }
    
    // 日付を月初めに変更する
    $basetime->setDate($basetime->format('Y'), $basetime->format('m'), 1);

    // データを分割する
    $calander = array(
        'y' => $basetime->format('Y')|0,
        'm' => $basetime->format('n')|0,
        'd' => $basetime->format('j')|0,
        'wd' => $basetime->format('w')|0,
        'days' => $basetime->format('t')|0
    );

$calander = new Roose_Calender();

foreach ($calander as $day) {
    $day->is_weekday
}