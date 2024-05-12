<?php

namespace app\components\emerald;

use app\models\PassiveIncome;
use app\models\TravelDelay;
use app\models\TravelUsers;
use Yii;
use yii\db\ActiveRecord;

use app\components\RecordCopier;

use app\models\User;
use app\models\Balance;
use app\models\EmeraldMain;
use app\models\EmeraldUsers;
use app\models\EmeraldDelay;

trait BaseTrait
{
    /**
     * Получаем рефералов юзера
     *
     * @return EmeraldUsers[]|array|ActiveRecord[]
     */
    public function getRefUsers($id_ref)
    {
        return EmeraldUsers::find()->where(['id_table' => $this->id])->all();
    }

    public static function getLevelsId($id)
    {
        return EmeraldMain::find()->where(['id_user' =>$id])->orderBy(['level' => SORT_ASC])->indexBy('level')->all();
    }

    public static function checkIsBalance()
    {
        return self::$user->balance > self::CONTRIBUTION;
    }

    public static function makeBalanceRecord($type, $level, $from, $to, $fromAmount, $toAmount, $comment = '')
    {
        $balance = new Balance();
        $balance->type = $type;

        if($level > 0) $balance->table = $level;
        if($from > 0) $balance->from_user_id = $from;
        if($to > 0) $balance->to_user_id = $to;
        if($fromAmount > 0) $balance->from_amount = $fromAmount;
        if($toAmount > 0) $balance->to_amount = $toAmount;

        $balance->comment = $comment;
        $balance->status = 1;

        return $balance->save();
    }

    public static function setLevel($referer, $level = 1)
    {
        $up_level = $level + 1;
        if ($up_level <= self::MAX_LEVEL) {
            /*
             * Проверяем есть ли у рефера стол, который подходит под мой уровень (формальная проверка) его быть и не должно!
             * Если ты не user с id = 1
             */
            $up_table = EmeraldMain::findOne(['id_user' => $referer->id, 'level' => $up_level]);
            if (!$up_table) {
                // Создаем новый стол для реферала
                $table = new EmeraldMain();
                $table->id_user = $referer->id;
                $table->level = $up_level;
                if (!$table->save()) return;

                /*
                 * Проверяем, можно ли стать на стол к своему рефералу
                 * Если можно - добавляем к столу
                 * В ином случае - откладываем на 24 часа
                 */
                $table_ref = self::findOne(['id_user' => $referer->id_ref_emerald, 'level' => $up_level]);

                if ($table_ref) {
                    /*
                     * Стол доступен - добавляем
                     * Рекурсивно вызываем этот метод для его реферала
                     */
                    $model_user = new EmeraldUsers();
                    $model_user->id_table = $table_ref->id;
                    $model_user->id_ref = $referer->id_ref_emerald;
                    $model_user->id_user = $referer->id;
                    if ($model_user->save()) {
                        self::$user = User::findOne(['id' => $referer->id]);
                        self::$reffer = User::findOne(['id' => self::$user->id_ref_emerald]);
                        self::$_this = EmeraldMain::findOne(['id_user' => self::$reffer->id, 'level' => $up_level]);
                        self::updateReferer($up_level);
                    }
                } else {
                    /*
                     * Стол заполнен - у реферала нет места
                     * Откладываем на 24 часа
                     * После ищем подходящего реферала
                     */
                    $delay = new EmeraldDelay();
                    $delay->id_user = $referer->id;
                    $delay->id_ref = $referer->id_ref_emerald;
                    $delay->level = $up_level;
                    $delay->date_end = time() + 172800;
                    $delay->save();
                }
            }
        }
    }

    /*
     *
     * @var EmeraldMain self::$_this
     * User self::$reffer, Int $level
     *
     */
    public static function updateReferer($level = 1)
    {
        //Получаем количество юзеров на столе рефера с уровенем $level
        $count_user_table = (int)EmeraldUsers::find()->where(['id_ref' => self::$reffer->id ,'id_table' => self::$_this->id])->count();
        $count = $count_user_table % 4;

        /*
         * Если на столе нет мест - ошибка
         */
        if ($count_user_table < 1) return true;
        if ($level < 3) {
            self::addRefererBalance(self::$user, self::$reffer, $level);
            if ($count_user_table == 4) {
                return self::setLevel(self::$reffer, $level);
            }
            return true;
        }

        if ($level == 3) {
            self::addRefererBalance(self::$user, self::$reffer, $level);
            if ($count_user_table >= 4) {
                self::setPassiveIncome(self::$user, self::$reffer, $level, $count_user_table);
            }
            if ($count_user_table == 4) {
                return self::setLevel(self::$reffer, $level);
            }
            if ($count == 0) {
                /*
                 * На уровне 3 за заполнение стола + 10 месяцев пассивного дохода
                 */
                self::setPassiveIncome(self::$user, self::$reffer, $level, 5);

            }

            return true;
        }
        if ($level == 5) {
            $message = 'Заполнен стол | Президент | Пользователь: ' . self::$reffer->username;
            self::addRefererBalance(self::$user, self::$reffer, $level);
            if ($count == 0) {
                self::setPassiveIncome(self::$user, self::$reffer, $level, 4);
                self::sender($message);
                return true;
            }
            self::setPassiveIncome(self::$user, self::$reffer, $level, $count);
            return true;
        }

        if ($level == 4) {
            $message = 'Заполнен стол | DIAMOND | Пользователь: ' . self::$reffer->username;
            self::addRefererBalance(self::$user, self::$reffer, $level);
            if ($count == 0) {
                self::setPassiveIncome(self::$user, self::$reffer, $level, 4);
                self::sender($message);
                return self::setLevel(self::$reffer, $level);
            }
            self::setPassiveIncome(self::$user, self::$reffer, $level, $count);
            return true;
        }

        return true;
    }


    /**
     * @param $user
     * @return bool
     */
    public static function sender($message)
    {
        return Yii::$app->mailer->compose()
            ->setTo('noreply@sapphire-gr.com')
            ->setFrom(['noreply@sapphire-gr.com' => 'sapphire-gr.com'])
            ->setSubject('Уровень: Президент')
            ->setTextBody($message)
            ->send();
    }

    /**
     *
     * Пассивный доход
     *
     * @param $user
     * @param $referer
     * @param $level
     * @param $count
     * @return PassiveIncome
     */
    public static function setPassiveIncome($user, $referer, $level, $count)
    {
        $plan = self::PLAN_LIST[$level];
        $months = $plan['slot'][$count]['month'];

        $passive = new PassiveIncome();
        $passive->user_id = $referer->id;
        $passive->amount = $plan['passive'];
        $passive->months = $plan['slot'][$level]['month'];
        $passive->level = $level;
        $passive->slot_active = $count;
        $passive->payments_done = 0;
        $passive->activation_date = date('Y-m-d'); // Текущая дата
        $passive->end_date = date('Y-m-d', strtotime("+$months months")); // Дата окончания
        $passive->next_payment_date = date('Y-m-d', strtotime('+1 month')); // Следующий платеж

        if (!$passive->save()) {
            return 'Не удалось сохранить пассивный доход пользователя [ERR0011]';
        }

        return $passive;

    }

    /**
     * Разовая выплата
     *
     * @param $user
     * @param $reffer
     * @param int $level
     * @return void
     */
    public static function addRefererBalance($user, $referer, $level = 1)
    {
        $plan = self::PLAN_LIST[$level];
        $referer->balance += $plan['payment'];
        $referer->updateAttributes(['balance']);
        self::makeBalanceRecord(Balance::TYPE_EMERALD, $level, $user->id, $referer->id, 0, (double) $plan['payment'], 'Пополнение от реферала');
    }

}