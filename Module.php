<?php
namespace bricksasp\payment;

/**
 * payment module definition class
 */
class Module extends \bricksasp\base\BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'bricksasp\payment\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
