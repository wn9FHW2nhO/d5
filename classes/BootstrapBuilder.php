<?php
class Roose_BootstrapBuilder
{
    public static function alert($m, $type = 'danger')
    {
        return '<div class="alert alert-'.$type.'">'.$m.'</div>';
    }
}