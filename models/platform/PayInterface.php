<?php
namespace bricksasp\payment\models\platform;

use Yii;

interface PayInterface
{
    public function pay();
    public function refund();
    public function query();
}
