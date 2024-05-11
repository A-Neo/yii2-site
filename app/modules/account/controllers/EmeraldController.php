<?php

namespace app\modules\account\controllers;

use app\models\Activation;
use app\models\Balance;
use app\models\EmeraldDelay;
use app\models\EmeraldMain;
use app\models\EmeraldUsers;
use app\models\EmeraldOrder;
use app\models\forms\OrderForm;
use app\models\Page;
use app\models\PassiveIncome;
use app\models\Payout;
use app\models\search\PayoutSearch;
use app\models\User;
use Yii;
use yii\helpers\Url;

use app\helpers\FunctionHelper as Help;

class EmeraldController extends Controller
{

    const PRODUCT = 1;

    public $user_id;
    public static $user_init = false;
    public static $check_order = false;
    public function beforeAction($action) {
        if (!parent::beforeAction($action)) {
            return false;
        }
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->user_id = Yii::$app->user->identity->getId();

        return true;
    }


    /**
     * @return int[]
     */
    public function actions()
    {
        $a = 5;
        $b = 5;
        return [$a, $b];
        $this->actionIndex();
    }
    public function actionIndex() {

        $user = User::findOne($this->user_id);
        $emerlad = EmeraldMain::find()->where(['id_user' => $this->user_id])->orderBy(['level' => SORT_DESC])->indexBy('level')->all();
        if ($emerlad) self::$user_init = true;
        if (!self::$check_order && self::$user_init) return $this->actionCheckOrder();

        return $this->render('index',
            [
                'levels'   => $emerlad,
                'username' => Yii::$app->user->identity->username,
                'user' => $this->user,
                'userid' => $this->user_id,
                'delayUsers' => EmeraldDelay::find()->where(['id_ref' => $this->user_id])->all(),
            ]
        );
    }

    public function actionInit() {

        if (Yii::$app->request->isPost) {
            $emeraldMain = Yii::$app->request->post('DynamicModel');
            $ref_user = User::findOne(['username' => $emeraldMain['id_ref']]);
        }

        $user = User::getCurrent();

        $result = EmeraldMain::initUser($this->user_id, $ref_user);

        if ($result === true) {
            Yii::$app->session->setFlash('okmessage', 'Уровень активирован');
            self::$user_init = true;
            return $this->actionCheckOrder();
        } else {
            Yii::$app->session->setFlash('errmessage', 'Ошибка активации: ' . $result);
        }

        return $this->actionIndex();

    }


    public function actionOrder()
    {
        $model = new EmeraldOrder();

        $user = User::findOne($this->user_id);
        $email = $user->email;


        if ($model->saveOrder(Yii::$app->request->post(), $email)) {
            self::$check_order = true;
            // Здесь вы можете добавить дополнительные действия после сохранения, например, редирект или уведомление
            Yii::$app->session->setFlash('success', 'Заказ успешно сохранен.');
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('errmessage', 'Ошибка сохранения заказа.');
            return $this->redirect(['index', 'id' => $model->id]);
        }
    }

    public function actionCheckOrder() {
        $userId = $this->user_id; // ID текущего пользователя

        // Проверяем, существует ли уже заказ для этого пользователя
        $orderExists = EmeraldOrder::find()->where(['id_user' => $userId])->exists();

        if (!$orderExists) {
            Yii::$app->layoutPath = \Yii::$app->basePath . '/views/account/main';
            // Если заказа нет, создаем новую модель и рендерим форму
            $model = new EmeraldOrder();

            return $this->render('order_form', [
                'model' => $model,
                'user_id' => $this->user_id,
                'product' => self::PRODUCT
            ]);
        } else {
            self::$check_order = true;
            // Если заказ уже существует, рендерим сообщение об этом
            return $this->actionIndex();
        }
    }

    public function actionNetList($uid, $level)
    {
        $user = User::findOne(['id' => $uid]);
        if (!$user) {
            return 'User not found!';
        }
        return $this->renderPartial('list',
            [
                'levels'   => EmeraldMain::getLevelsId($user->id),
                'username' => $user->username,
                'userid'   => $user->id,
                'level'    => (int)$level,
            ]
        );
    }

    public function actionUserAvatar($uid)
    {
        $uid = (int)$uid;

        $avatar = Yii::$app->cache->getOrSet('travel_user_avatar_' . $uid, function() use ($uid) {
            $user = User::findOne(['id' => $uid]);
            if ($user && $user->avatar) {
                return $user->avatar;
            }
            return file_get_contents(Yii::getAlias('@webroot') . '/img/noavatar.jpg');
        }, 60);

        return Yii::$app->response->sendContentAsFile($avatar, 'user-avatar-' . $uid . '.jpg', ['inline' => true]);
    }

}
