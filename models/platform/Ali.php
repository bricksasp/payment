<?php
namespace bricksasp\payment\models\platform;

use Yii;

class Ali implements PayInterface
{
    public static function app();
    public static function bar();
    public static function qr();
    public static function wap();
	
    public static function web();
}