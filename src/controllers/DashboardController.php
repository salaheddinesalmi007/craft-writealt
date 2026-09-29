<?php

namespace writealt\controllers;

use Craft;
use craft\web\Controller;
use Throwable;
use writealt\Plugin;

class DashboardController extends Controller
{
    protected array|bool|int $allowAnonymous = false;

    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        $this->requireCpRequest();
        $this->requirePermission('accessPlugin-writealt');
        return true;
    }

    public function actionAssets(): \yii\web\Response
    {
        try {
            return $this->asJson(['ok' => true, 'assets' => Plugin::getInstance()->get('assetService')->listAssets()]);
        } catch (Throwable $e) {
            return $this->fail($e);
        }
    }

    public function actionCredits(): \yii\web\Response
    {
        try {
            return $this->asJson(['ok' => true, 'credits' => Plugin::getInstance()->get('client')->credits()]);
        } catch (Throwable $e) {
            return $this->fail($e);
        }
    }

    public function actionGenerate(): \yii\web\Response
    {
        $this->requirePostRequest();
        try {
            $id = (int)Craft::$app->getRequest()->getBodyParam('id');
            return $this->asJson(['ok' => true, 'asset' => Plugin::getInstance()->get('assetService')->generate($id)]);
        } catch (Throwable $e) {
            return $this->fail($e);
        }
    }

    public function actionOptimize(): \yii\web\Response
    {
        $this->requirePostRequest();
        try {
            $id = (int)Craft::$app->getRequest()->getBodyParam('id');
            return $this->asJson(['ok' => true, ...Plugin::getInstance()->get('assetService')->optimize($id)]);
        } catch (Throwable $e) {
            return $this->fail($e);
        }
    }

    private function fail(Throwable $e): \yii\web\Response
    {
        Craft::$app->getResponse()->setStatusCode(400);
        return $this->asJson(['ok' => false, 'error' => $e->getMessage()]);
    }
}
