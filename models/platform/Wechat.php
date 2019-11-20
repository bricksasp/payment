<?php
namespace bricksasp\payment\models\platform;

use Yii;
use yii\base\BaseObject;
use bricksasp\member\models\UserWx;
use bricksasp\payment\models\Payment;
use Payment\Common\PayException;
use Payment\Client\Charge;
use Payment\Config;
use bricksasp\helpers\Tools;
use bricksasp\base\models\File;
use bricksasp\base\Config as BaseConfig;

class Wechat extends BaseObject implements PayInterface
{
    public $type;
    public $data;
    public $error;

    public function config()
    {
        $c = Payment::find()->where(['code'=> Payment::PAY_WECHAT, 'user_id' => $this->data['owner_id']])->one();
        if (!$c) {
            Tools::exceptionBreak(950002);
        }
        $cfg = json_decode($c->config,true);
        $app_key_pem = File::findOne($cfg['app_key_pem']);
        $app_cert_pem = File::findOne($cfg['app_cert_pem']);
        $cfg['app_key_pem'] = BaseConfig::instance()->file_base_path ? BaseConfig::instance()->file_base_path : Yii::$app->basePath . '/web' . $app_key_pem->file_url;
        $cfg['app_cert_pem'] = BaseConfig::instance()->file_base_path ? BaseConfig::instance()->file_base_path : Yii::$app->basePath . '/web' . $app_cert_pem->file_url;
        return $cfg;
    }

    public function app(){

        return 'appaaaafdfds';
    }
    // public function bar();
    // public function qr();
    // public function wap();
    // public function pub(){};

    public function lite(){
        $wx = UserWx::find()->select(['user_id', 'openid'])->where([
                'user_id'=> $this->data['user_id'], 
                'owner_id'=> $this->data['owner_id']
            ])->one();
        $payData = [
            'body'    => '支付单' . $this->data['payment_id'],
            'order_no'    => $this->data['payment_id'],
            'subject'    => $this->data['payment_id'],
            'timeout_express' => time() + 600,// 表示必须 600s 内付款
            'amount'    => $this->data['money'],
            'client_ip' => $this->data['ip'],// 客户地址
            'openid' => $wx->openid,
            'return_param' => base64_encode(json_encode(['user_id'=> $wx->user_id]))
        ];

        $w = Payment::configs(Payment::PAY_WECHAT);
        $config = array_merge($w['const_config'], $this->config());
        $config['app_id'] = $config['lite_app_id']; //小程序
        // print_r($payData);exit;
        try {
            return Charge::run(Config::WX_CHANNEL_LITE, $config, $payData);
        } catch (PayException $e) {
            $this->error = $e->errorMessage();
        }
        return false;
    }


    public function pay(){
        return call_user_func_array([$this,$this->type],[]);
    }

    public function refund(){}

    public function query(){}

}