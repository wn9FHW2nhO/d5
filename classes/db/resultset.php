<?php
/**
 * データベースの結果オブジェクト
 * 
 * @todo コメント書く
 * @todo 実装
 * @todo MySQL関数依存からの脱却
 * 
 * @package Roose\DB
 * @author うちやま
 * @since PHP 5.2.17
 * @version 1.0.0
 */
class Roose_DB_Resultset implements Iterator
{
    private $_result = null;
    private $_index = -1;
    private $_plaindata = array();
    
    public function __construct($result)
    {
        if (is_resource($result) === false) {
            $this->_index = 0;
            $this->_result = array($result);
        } else {
            $this->_result = $result;
            $this->next();
        }
    }
    
    public function current()
    {
        if (isset($this->_plaindata[$this->_index])) {
            return $this->_plaindata[$this->_index];
        }
        
        return null;
    }
    
    public function key()
    {
        return $this->_index;
    }
    
    public function next()
    {
        $i = $this->_index + 1;
        if (isset($this->_plaindata[$i]) === false) {
            // next data isn't fetched? fetch.
            $_plaindata[] = mysql_fetch_array($this->_result);
        }
        
        $this->_index++;
    }
    
    public function rewind()
    {
        $this->_index = 0;
    }
    
    public function valid()
    {
        return
            isset($this->_plaindata[$this->_index])
                and $this->_plaindata[$this->_index] !== false;
    }
    
    public function fetch()
    {
        $ret = $this->current();
        $this->next();
        
        return $ret;
    }
    
    public function free()
    {
        $this->result !== null
            and mysql_free_result($this->_result);
        
        unset($this->_result);
        unset($this->_plaindata);
    }
}