<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

use app\helpers\FunctionHelper as Help;

class PassiveIncome extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'passive_income';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'level', 'slot_active'], 'integer'],
            [['amount', 'user_id', 'level', 'months', 'activation_date', 'end_date', 'next_payment_date', 'payments_done', 'complete', 'tour_id', 'phone'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'amount' => 'amount',
            'user_id' => 'user_id',
            'level' => 'level',
            'slot_active' => 'Slot Active',
            'months' => 'months',
            'activation_date' => 'activation_date',
            'end_date' => 'end_date',
            'next_payment_date' => 'next_payment_date',
            'payments_done' => 'payments_done',
            'complete' => 'complete',
            'tour_id' => 'tour_id',
            'phone' => 'phone',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'), // Используйте SQL-выражение для получения текущего времени
            ],
        ];
    }

    public function create($user_id = 1, $amount, $months)
    {
        $this->user_id = $user_id;
        $this->amount = $amount;
        $this->months = $months;
        $this->level = $level;
        $this->activation_date = date('Y-m-d'); // Текущая дата
        $this->end_date = date('Y-m-d', strtotime("+$months months")); // Дата окончания
        $this->next_payment_date = date('Y-m-d', strtotime('+1 month')); // Следующий платеж
        $this->slot_active = 1;
        $this->payments_done = 0;
    }

    public function addMonths($months)
    {
        $this->months += $months;
        $this->end_date = date('Y-m-d', strtotime($this->end_date . " +$months months"));
    }

    public function getRemainingPayments()
    {
        return $this->months - $this->payments_done;
    }

    public function getNextPaymentAmount()
    {
        return $this->amount;
    }

    public function getTotalPaymentsAmount()
    {
        return $this->amount * $this->months;
    }

    public function getSlotActive()
    {
        return $this->slot_active ?? 0;
    }

    public function getRemainingAmount()
    {
        return $this->getTotalPaymentsAmount() - ($this->amount * $this->payments_done);
    }
}
