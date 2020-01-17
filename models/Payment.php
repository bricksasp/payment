<?php
namespace bricksasp\payment\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%payment}}".
 *
 */
class Payment extends \bricksasp\base\BaseActiveRecord
{
    const PAY_WECHAT = 'wechat';
    const PAY_ALI = 'ali';
    const PAY_CMB = 'cmb';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%payment}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => time(),
            ],
            [
                'class' => \bricksasp\helpers\behaviors\UidBehavior::className(),
                'createdAtAttribute' => 'user_id',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['config'], 'string'],
            [['name', 'code'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            'user_id' => 'User ID',
            'sort' => 'Sort',
            'config' => 'Config',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function configs($payCode=null)
    {
        $payment[self::PAY_WECHAT] = [
            'name' => '微信',
            'code' => self::PAY_WECHAT,
            'config' => [
                'app_id'          => '微信支付分配的账号ID',
                'mch_id'          => '微信支付分配的商户号',
                'md5_key'         => '微信的加密密钥，微信商户中心配置',
                'app_cert_pem'    => '证书pem的路径',
                'app_key_pem'     => '证书密钥pem的路径',
                // 'limit_pay'       => '限制的支付方式，no_credit：信用卡',
                'sign_type'       => 'MD5',
                'fee_type'        => 'CNY',
            ],
            'const_config' => [
                'use_sandbox'     => true,
                'notify_url'      => Url::to(['/order/pay/wxnotify'],'https'),
                'redirect_url'    => Url::to(['/order/pay/payed'],'https'),
                'return_raw'      => false,
            ]
        ];
        $payment[self::PAY_ALI] = [
            'name' => '支付宝',
            'code' => self::PAY_ALI,
            'config' => [
                'partner'         => '收款支付宝用户ID(2088开头)',
                'app_id'          => '支付宝分配给开发者的应用ID',
                'ali_public_key'  => '支付宝的公钥内容，也支持路径',
                'rsa_private_key' => '个人生成的私钥内容，也支持路径',
                // 'limit_pay'       => '限制的支付方式，no_credit：信用卡',
                'sign_type'       => 'RSA2',
            ],
            'const_config' => [
                'use_sandbox'     => true,
                'notify_url'      => Url::to(['/order/pay/alinotify'],'https'),
                'return_url'      => Url::to(['/order/pay/payed'],'https'),
                'return_raw'      => false,
            ]
        ];
        $payment[self::PAY_CMB] = [
            'name' => '招行',
            'code' => self::PAY_CMB,
            'config' => [
                'branch_no'       => '商户分行号，4位数字',
                'merchant_no'     => '商户号，6位数字',
                'mer_key'         => '秘钥16位，包含大小写字母 数字',
                'cmb_pub_key'     => '招商的公钥，会定期更新，建议每天主动获取一次',
                'op_pwd'          => '操作员登录密码',
                // 'limit_pay'       => '允许支付的卡类型, A:储蓄卡支付',
                'sign_type'       => 'SHA-256',
            ],
            'const_config' => [
                'use_sandbox'     => true,
                'notify_url'      => Url::to(['/order/pay/cmbnotify'],'https'),
                'sign_notify_url' => '成功签约结果通知地址',
                'return_url'      => Url::to(['/order/pay/payed'],'https'),
                'return_raw'      => false,
            ]
        ];

        if ($payCode) return $payment[$payCode]; else return $payment;
    }
}
