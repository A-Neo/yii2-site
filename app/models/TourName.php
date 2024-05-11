<?php

namespace app\models;

use app\models\behaviors\TranslateBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%tour_name}}".
 *
 * @property int    $id
 * @property int    $price
 * @property string $name
 * @property int    $status
 * @property int    $created_at
 * @property int    $updated_at
 *
 * @property Tour[] $tours
 */
class TourName extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE   = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%tour_name}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['price', 'status', 'created_at', 'updated_at'], 'integer'],
            [['price'], 'integer', 'min' => 3],
            [['name'], 'required'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['name'], 'string', 'max' => 2048],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'         => Yii::t('site', 'ID'),
            'price'      => Yii::t('site', 'Price'),
            'name'       => Yii::t('site', 'Name'),
            'status'     => Yii::t('site', 'Status'),
            'created_at' => Yii::t('site', 'Created'),
            'updated_at' => Yii::t('site', 'Updated'),
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
            [
                'class'      => TranslateBehavior::class,
                'attributes' => ['name'],
            ],
        ];
    }

    public function beforeDelete() {
        $result = parent::beforeDelete(); // TODO: Change the autogenerated stub
        if(!empty($this->tours)){
            return false;
        }
        return $result;
    }

    /**
     * Gets query for [[Tours]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTours() {
        return $this->hasMany(Tour::class, ['tour_name_id' => 'id']);
    }


    public function getStatusName() {
        return $this->getStatusesList()[(int)$this->status];
    }

    public function getStatusesList() {
        return [
            self::STATUS_INACTIVE => Yii::t('site', 'Inactive'),
            self::STATUS_ACTIVE   => Yii::t('site', 'Active'),
        ];
    }
}
