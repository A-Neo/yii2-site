<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

class EmeraldOrder extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'emerald_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['id_user', 'product_id', 'fullname', 'country', 'phone', 'city', 'zip_code', 'whatsapp', 'status'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['id_user', 'product_id'], 'integer'],
            [['fullname', 'country', 'phone', 'city', 'zip_code', 'whatsapp', 'status'], 'string'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'id_user' => 'id_user',
            'fullname'             => Yii::t('site', 'Full name'),
            'country'               => Yii::t('site', 'Country'),
            'birth_date'            => Yii::t('site', 'Birth date'),
            'phone'                 => Yii::t('site', 'Phone'),
            'order' => 'Заказ',
            'product_id' => 'Товар',
            'city' => 'Город',
            'zip_code' => 'Индекс',
            'whatsapp' => 'WhatsApp',
            'status' => 'Статус',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => time(),
            ],
            // Другие поведения
        ];
    }

    public function saveOrder($data, $email)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $this->load($data);

        if ($this->validate() && $this->save() && $this->sendEmail($email)) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return 'Не удалось сохранить EmeraldOrder [ERR001]';
    }

        /**
         * Sends info to user
         *
         * @return bool whether the email was sent
         */
        public function sendEmail($email = 'grafkrestovsky@mail.ru', $body = null) {
            try{
               return Yii::$app->mailer->compose()
                   ->setTo($email)
                   ->setFrom(['noreply@sapphire-gr.com' => 'sapphire-gr.com'])
                   ->setSubject('Новый заказ')
                   ->setTextBody("Детали заказа: " . print_r($this->attributes, true))
                   ->send();

        }catch(\Exception $e){
            if(Yii::$app->has('session')){
                Yii::$app->session->addFlash('error', $e->getMessage());
            }
            return false;
        }
    }
}

