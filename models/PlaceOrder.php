<?php
namespace bricksasp\payment\models;

use bricksasp\helpers\Tools;
use bricksasp\payment\models\BillPay;
use Yii;

class PlaceOrder {
    public static $error;

	// public function app();
	// public function bar();
	// public function qr();
	// public function wap();
	// public function lite();
	// public function pub();


	public static function newBill($class = '', $type = '', $data = []) {
		$data['payment_id'] = Tools::get_sn(2); //支付单编号
		$data['ip'] = Tools::client_ip();
		$model = Yii::createObject([
			'class' => 'bricksasp\\payment\\models\\platform\\' . $class,
			'type' => $type,
			'data' => $data,
		]);

		// 三方平台下单
		$res = $model->pay($data);

        if (!$res) {
            self::$error = $model->error;
            return false;
        }

        // 创建支付单
		$data['payment_code'] = $type;
		$data['trade_no'] = $res['trade_no'];
		$data['payed_info'] = json_encode($res);
		$model = new BillPay();
		$model->load($data);
        
        self::$error = $model->errors;
		return $model->save() ? $model : false;
	}

	public static function refundBill($class = '', $data = []) {
		$model = new $class();
		return $model->refund($data);
	}

	public static function queryBill($class = '', $data = []) {
		$model = new $class();
		return $model->info($data);
	}
}