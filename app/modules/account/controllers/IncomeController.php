<?php

namespace app\modules\account\controllers;

use app\models\Activation;
use app\models\PassiveIncome;
use app\models\User;
use Yii;
use yii\helpers\Url;

use app\helpers\FunctionHelper as Help;

class IncomeController extends Controller
{

    public $user_id;

    public function beforeAction($action) {
        parent::beforeAction($action);
        if (!isset(Yii::$app->user->identity) || Yii::$app->user->identity == null) {
            return false;
        }
        $this->user_id = Yii::$app->user->identity->getId();
        return true;
    }

    public function actionIndex()
    {
        return $this->render('index',
            [
                'levels'   => TravelMain::getLevels($this->user_id),
                'username' => Yii::$app->user->identity->username,
                'userid' => $this->user_id,
                'delayUsers' => TravelDelay::find()->where(['id_ref' => $this->user_id])->all(),
            ]
        );
    }

    public function actionPayment($uid = 10, $iid = 3)
    {
        $income = PassiveIncome::findOne(['id' => $iid]);
        if (!$income) {
            return 'Income not found!';
        }

        return $income;
        Help::dd($income);

        return $this->renderPartial('list',
            [
                'levels'   => EmeraldMain::getLevelsId($user->id),
                'username' => $user->username,
                'userid'   => $user->id,
                'level'    => (int)$level,
            ]
        );
    }
}
