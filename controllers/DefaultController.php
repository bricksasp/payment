<?php
namespace bricksasp\payment\controllers;

use Yii;
use yii\web\Controller;

/**
 * Default controller for the `payment` module
 */
class DefaultController extends Controller
{
	public function actions() {
		return [
			'error' => [
				'class' => \bricksasp\base\actions\ErrorAction::className(),
			],
			'api-docs' => [
				'class' => 'genxoft\swagger\ViewAction',
				'apiJsonUrl' => \yii\helpers\Url::to(['api-json'], true),
			],
			'api-json' => [
				'class' => 'genxoft\swagger\JsonAction',
				'dirs' => [
                    Yii::getAlias('@bricksasp/base'),
					dirname(__DIR__)
				],
			],
		];
	}

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
