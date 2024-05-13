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

    /**
     * Получить все записи пользователя
     * Сортировка по уровням ASC
     * @param $id
     * @return array|ActiveRecord[]
     */
    public static function getLevelsId($id)
    {
        return EmeraldMain::find()->where(['id_user' =>$id])->orderBy(['level' => SORT_ASC])->indexBy('level')->all();
    }

    /**
     * Проверить достаточно ли средства на балансе
     * @CONST self::CONTRIBUTION
     * @return bool
     */
    public static function checkIsBalance()
    {
        return self::$user->balance > self::CONTRIBUTION;
    }

    /**
     * Сделать запись тразакции баланца
     * @param $type
     * @param $level
     * @param $from
     * @param $to
     * @param $fromAmount
     * @param $toAmount
     * @param $comment
     * @return bool
     */
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

    /**
     * Отправить сообщение администрации
     * @param $message
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
     * Пассивный доход
     * @param $user
     * @param $referer
     * @param $level
     * @param $count
     * @return PassiveIncome|string
     */
    public static function setPassiveIncome($user, $referer, $level, $count)
    {
        $plan = self::PLAN_LIST[$level];
        $months = $plan['slot'][$count]['month'];

        $passive = new PassiveIncome();
        $passive->user_id = $referer->id;
        $passive->amount = $plan['passive'];
        $passive->months = $months;
        $passive->level = $level;
        $passive->slot_active = $count;
        $passive->payments_done = 0;
        $passive->activation_date = date('Y-m-d'); // Текущая дата
        $passive->end_date = date('Y-m-d', strtotime("+$months months")); // Дата окончания
        $passive->next_payment_date = date('Y-m-d', strtotime('+1 month')); // Следующий платеж

        return !$passive->save() ? 'Не удалось сохранить пассивный доход пользователя [ERR0011]' : $passive;
    }

    /**
     * Разовая выплата
     * @param $user
     * @param $referer
     * @param int $level
     * @return void
     */
    public static function addRefererBalance($user, $referer, int $level = 1)
    {
        $plan = self::PLAN_LIST[$level];
        $referer->balance += $plan['payment'];
        $referer->updateAttributes(['balance']);
        self::makeBalanceRecord(Balance::TYPE_EMERALD, $level, $user->id, $referer->id, 0, (double) $plan['payment'], 'Пополнение от реферала');
    }

    /**
     * Повысить уровень реферала
     * @param User $referer
     * @param Integer $level
     * @return void
     */
    public static function setLevel($referer, $level = 1)
    {
        /**
         * Проверяем есть ли у реферала стол, который подходит под уровень (#)
         * Его быть и не должно!
         * Искключение $user->id = 1
         */
        $level += 1;
        if ($level < self::MAX_LEVEL) {
            $up_table = EmeraldMain::findOne(['id_user' => $referer->id, 'level' => $level]);
            if (!$up_table) {
                // Создаем новый стол для реферала
                $table = new EmeraldMain();
                $table->id_user = $referer->id;
                $table->level = $level;
                if (!$table->save()) return;

                /**
                 * Проверяем, можно ли стать на стол к своему рефералу
                 * Если можно - добавляем к столу
                 * В ином случае - откладываем на 24 часа
                 */
                $table_ref = self::findOne(['id_user' => $referer->id_ref_emerald, 'level' => $level]);

                if ($table_ref) {
                    /**
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
                        self::$_this = EmeraldMain::findOne(['id_user' => self::$reffer->id, 'level' => $level]);
                        self::updateReferer($level);
                    }
                } else {
                    /**
                     * Стол недоступен
                     * Откладываем на 24 часа
                     * Есле реферал не передаст своему партнеру
                     * То ищем подходящего партнера у реферала по дате регистрации
                     */
                    $delay = new EmeraldDelay();
                    $delay->id_user = $referer->id;
                    $delay->id_ref = $referer->id_ref_emerald;
                    $delay->level = $level;
                    $delay->date_end = time() + 172800;
                    $delay->save();
                }
            }
        }
    }
    /**
     * Обноваить реферала
     * @param int $level
     * @return void
     */
    public static function updateReferer($level = 1)
    {
        /**
         * Получаем стол рефера
         */
        self::$_this = self::findOne(['id_user' => self::$reffer->id, 'level' => $level]);
        if (!self::$_this) return;
        /**
         * Получаем количество юзеров на столе рефера
         * @params $table->id
         */
        $count_user_table = EmeraldUsers::find()->where(['id_table' => self::$_this->id])->count();
        /**
         * Если на столе все свободные места (count < 1)
         * @return bool
         */
        if ($count_user_table < 1) return;
        $count = $count_user_table % self::MAX_USERS;
        if ($level == 5) {
            $message = 'Заполнен стол | Президент | Пользователь: ' . self::$reffer->username;
            self::addRefererBalance(self::$user, self::$reffer, $level);
            if ($count == 0) {
                self::setPassiveIncome(self::$user, self::$reffer, $level, 4);
                self::sender($message);
                return;
            }
            self::setPassiveIncome(self::$user, self::$reffer, $level, $count);
            return;
        }
        if ($level == 4) {
            $message = 'Заполнен стол | DIAMOND | Пользователь: ' . self::$reffer->username;
            self::addRefererBalance(self::$user, self::$reffer, $level);
            if ($count_user_table == 4) {
                self::setPassiveIncome(self::$user, self::$reffer, $level, $count_user_table);
                self::sender($message);
                self::setLevel(self::$reffer, $level);
            }
            self::setPassiveIncome(self::$user, self::$reffer, $level, $count_user_table);
            return;
        }
        if ($level == 3) {
            self::addRefererBalance(self::$user, self::$reffer, $level);
            self::setPassiveIncome(self::$user, self::$reffer, $level, $count_user_table);
            /**
             * За 4 реферала - 3-го уровня
             * Бонус: 10 месяцев пассивного дохода по 20$
             */
            if ($count_user_table == 4) {
                self::setPassiveIncome(self::$user, self::$reffer, $level, 5);
                self::setLevel(self::$reffer, $level);
            }
            return;
        }
        if ($level < 3) {
            self::addRefererBalance(self::$user, self::$reffer, $level);
            if ($count_user_table == 4) self::setLevel(self::$reffer, $level);
        }
        return;
    }
}