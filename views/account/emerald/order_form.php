<?php

use app\assets\AppAsset;
use app\components\View;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

$this->title = Yii::t('account', 'Оформление заказа');
$user = Yii::$app->user->identity;
$countries = include_once ROOT_DIR . '/config/' . substr(Yii::$app->language, 0, 2) . '/countries.php';
$data = array_combine(array_column($countries, 'name'), array_column($countries, 'name'));
$countries = array_combine(array_column($countries, 'name'), array_values($countries));
$url = AppAsset::image('../flags/', false);
$format = <<< SCRIPT
function format(state) {
    if (!state.id) return state.text; // optgroup
    src = '$url' +  $(state.element).attr('alpha2') + '.png'
    return '<img class="flag" src="' + src + '" height="24"/> ' + state.text;
}
SCRIPT;
$escape = new JsExpression("function(m) { return m; }");
$this->registerJs($format, View::POS_HEAD);

/* @var $this yii\web\View */
/* @var $model app\models\EmeraldOrder */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container">
    <div class="form-container">
        <div class="left-container">
            <div class="left-inner-container">
                <h2>Оформление заказа</h2>
                <p>Бесплатная доставка за активацию первого уровня.</p>
                <p>Заполняй форму и приглашайте друзей!</p>
                <div class="container-mail">
                    <div class="mail">
                        <div class="mail__back"></div>
                        <div class="mail__top"></div>
                        <div class="mail__letter">
                            <div class="mail__letter-square">
                            </div>
                            <div class="mail__letter-lines">
                            </div>
                        </div>
                        <div class="mail__left"></div>
                        <div class="mail__right"></div>
                        <div class="mail__bottom"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="right-container">
            <div class="right-inner-container">
                <div action="#">
                    <h2 class="lg-view">Форма</h2>
                    <h2 class="sm-view">Оставьте свои контактные данные для отправки посылки</h2>
                    <?php $form = ActiveForm::begin([
                        'action' => ['emerald/order'], // Укажите здесь нужный контроллер и действие
                        'method' => 'post', // Метод отправки формы
                    ]); ?>
                    <?= $form->field($model, 'id_user')->hiddenInput(['value'=> $user_id])->label(false) ?>
                    <?= $form->field($model, 'product_id')->hiddenInput(['value'=> $product])->label(false) ?>
                    <?= $form->field($model, 'fullname')->textInput(['readonly' => !empty($model->fullname), 'disabled' => !empty($model->fullname), 'placeholder' => Yii::t('account', 'Please enter your full name')])?>
                    <?= $form->field($model, 'phone')->textInput(['readonly' => !empty($model->phone), 'disabled' => !empty($model->phone), 'placeholder' => Yii::t('account', 'Please enter your phone')])?>
                    <?= $form->field($model, 'whatsapp')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'country')->widget(Select2::class, [
                        'language'      => Yii::$app->language,
                        'options'       => [
                            'placeholder' => Yii::t('site', 'Select a country ...'),
                            'class'       => 'reg_id w-select form-control',
                            'options'     => $countries,
                            'readonly'    => !empty($model->country),
                            'disabled'    => !empty($model->country),
                        ],
                        'pluginOptions' => [
                            'templateResult'    => new JsExpression('format'),
                            'templateSelection' => new JsExpression('format'),
                            'escapeMarkup'      => $escape,
                        ],
                        'data'          => $data,
                    ])?>
                    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'zip_code')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'status')->hiddenInput(['value'=> 1])->label(false) ?>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Отправить')) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>