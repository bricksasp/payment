<?php
namespace bricksasp\payment\controllers;

use Yii;
use bricksasp\payment\models\Payment;
use yii\data\ActiveDataProvider;
use bricksasp\base\BaseController;
use yii\web\HttpException;
use bricksasp\base\actions\FileAction;
use bricksasp\base\models\File;
use bricksasp\helpers\Tools;

/**
 * PaymentController implements the CRUD actions for Payment model.
 */
class PaymentController extends BaseController
{
    public function actions()
    {
        return [
            'fileupload' => [
                'class' => FileAction::className(),
                'file_path' => '/cert/' . date('Y') . '/' . date('m'),
                'validatorConfig' => ['checkExtensionByMimeType' => false],
                'config' => [
                    'allowFiles' => ['key','pem'],
                    'maxSize' => 1024000
                ],
            ]
        ];
    }

    /**
     * 登录可访问 其他需授权
     * @return array
     */
    public function allowAction()
    {
        return array_merge(parent::allowAction(),[
            'fileupload',
        ]);
    }

    /**
     * Lists all Payment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Payment::find($this->dataOwnerUid()),
        ]);

        $data = $this->pageFormat($dataProvider,['config'=>['json_decode',['###',true]]],1);
        $cfs = Payment::configs();
        // print_r($cfs);exit();
        $k = array_column($data['data']['list'],'code');
        foreach ($cfs as $key => $value) {
            if (!in_array($key, $k)) {
                $data['data']['list'][] = $value;
            }
        }

        return $data;
    }

    /**
     * Displays a single Payment model.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionView()
    {
        $model = Payment::find()->where(['code'=>Yii::$app->request->get('code'), 'user_id'=>$this->dataOwnerUid()])->one();
        $cfs = Payment::configs();
        if (!$model) {
            return $this->success($cfs[Yii::$app->request->get('code')]);
        }
        $model->config = json_decode($model->config, true);

        if ($model->code == Payment::PAY_WECHAT) {
            $pub = $model->config['app_cert_pem'];
            $pri = $model->config['app_key_pem'];
        }
        if ($model->code == Payment::PAY_ALI) {
            $pub = $model->config['ali_public_key'];
            $pri = $model->config['rsa_private_key'];
        }
        if ($model->code == Payment::PAY_CMB) {
            $pub = $model->config['cmb_pub_key'];
            $pri = $model->config['mer_key'];
        }

        $data = $model->toArray();

        $data['pubItem'] = FIle::findOne($pub);
        $data['priItem'] = FIle::findOne($pri);
        $data['pubItem']['file_url'] = Tools::file_address($data['pubItem']['file_url']);
        $data['priItem']['file_url'] = Tools::file_address($data['priItem']['file_url']);
        return $this->success($data);
    }

    /**
     * Updates an existing Payment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        $parmas = Yii::$app->request->post();
        $model = Payment::find()->where(['code'=>$parmas['code'], 'user_id'=>$this->uid])->one();
        if (!$model) {
            $model = new Payment();
        }
        // print_r($parmas);exit();
        $parmas['config'] = json_encode($parmas['config'], JSON_UNESCAPED_UNICODE);
        if ($model->load($parmas) && $model->save()) {
            return $this->success();
        }

        return $this->fail($model->errors);
    }
}
