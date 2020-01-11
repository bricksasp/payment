<?php
namespace bricksasp\payment\models\platform;

use Yii;
use yii\base\BaseObject;
use bricksasp\member\models\UserWx;
use bricksasp\payment\models\Payment;
use bricksasp\helpers\Tools;
use bricksasp\base\models\File;
use bricksasp\base\Config;
use bricksasp\base\models\Setting;
use WeChat\Pay;

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
        $setting = Setting::getSetting($this->data['owner_id'], 'wx_');
        $cfg = json_decode($c->config,true);
        $app_key_pem = File::findOne($cfg['app_key_pem']);
        $app_cert_pem = File::findOne($cfg['app_cert_pem']);
        $cfg['app_key_pem'] = (Config::instance()->file_base_path ? Config::instance()->file_base_path : Yii::$app->basePath) . '/web' . $app_key_pem->file_url;
        $cfg['app_cert_pem'] = (Config::instance()->file_base_path ? Config::instance()->file_base_path : Yii::$app->basePath) . '/web' . $app_cert_pem->file_url;
        $cfg = array_merge($cfg, $setting);
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
        if (empty($wx)) {
            Tools::exceptionBreak(950003);
        }

        $config = $this->config();
        $pconf = Payment::configs(Payment::PAY_WECHAT);
        $notify_url = $pconf['const_config']['notify_url'];
        $config = [
            'appid'          => $config['wx_applet_appid'],
            'appsecret'      => $config['wx_applet_secret'],
            'encodingaeskey' => '',
            'mch_id'         => $config['mch_id'],
            'mch_key'        => $config['md5_key'],
            // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
            'ssl_key'        => $config['app_key_pem'],
            'ssl_cer'        => $config['app_cert_pem'],
            // 缓存目录配置（可选，需拥有读写权限）
            'cache_path'     => '',
        ];
        $wechat = new Pay($config);
  
        // 组装参数，可以参考官方商户文档
        $options = [
          'body'             => '支付单' . $this->data['payment_id'],
          'out_trade_no'     => $this->data['payment_id'],
          'total_fee'        => $this->data['money'] * 100,
          'openid'           => $wx->openid,
          'trade_type'       => 'JSAPI',
          'notify_url'       => $notify_url,
          'spbill_create_ip' => $this->data['ip'],
        ];

        try {
            // 生成预支付码
            $result = $wechat->createOrder($options);
            
            // 创建JSAPI参数签名
            return  $wechat->createParamsForJsApi($result['prepay_id']);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
        return false;
    }


    public function pay(){
        return call_user_func_array([$this,$this->type],[]);
    }

    public function refund(){}

    public function query(){}




}