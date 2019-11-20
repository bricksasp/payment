<?php
namespace bricksasp\payment\models;

use Yii;

/**
 * This is the model class for table "{{%bill_pay}}".
 *
 */
class BillPay extends \bricksasp\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%bill_pay}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
            [
                'class' => \bricksasp\helpers\behaviors\UidBehavior::className(),
                'createdAtAttribute' => 'user_id',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_id'], 'required'],
            [['user_id', 'type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['money'], 'number'],
            [['payed_info'], 'string'],
            [['payment_id', 'order_id'], 'string', 'max' => 20],
            [['ip', 'trade_no'], 'string', 'max' => 50],
            [['payment_code'], 'string', 'max' => 32],
            [['payment_id'], 'unique'],
            [['type', 'status'], 'default', 'value' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payment_id' => 'Payment ID',
            'order_id' => 'Order ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'status' => 'Status',
            'money' => 'Money',
            'ip' => 'Ip',
            'payment_code' => 'Payment Code',
            'trade_no' => 'Trade No',
            'payed_info' => 'Payed Info',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
