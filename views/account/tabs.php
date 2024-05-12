<?php

use app\models\Balance;
use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Url;

/**
 * @var $hasPoints bool
 */
$hasPoints = $hide??false;
?>

<div class="row w-100 mt-3 mb-5">
    <?php if(!empty($url) && $url != '#'): ?>
    <div class="col-xs-12 col-sm-6 col-xl-3">
        <div class="new-cart container">
            <p class="card-name"><?=Yii::t('account', 'Balance')?></p>
            <h1 class="card-balance"><?=number_format($user->balance - $user->accumulation, 2, '.', '')?> <?=FAS::icon('dollar-sign')?></h1>
            <img class="logo" src="https://i.imgur.com/NWUeYG4.png" alt="">
        </div>
    </div>
    <?php endif ?>
    <div class="col-xs-12 col-sm-6 col-xl-3">
        <div class="new-cart container">
            <p class="card-name"><?=Yii::t('account', 'Status')?></p>
            <h1 class="card-balance"><?=$user->getActiveActivations()->orderBy(['table' => SORT_DESC])->select('table')->scalar()?> <?=Yii::t('account', 'Level')?></h1>
            <img class="logo" src="https://i.imgur.com/NWUeYG4.png" alt="">
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-xl-3">
        <div class="new-cart container">
            <p class="card-name"><?=Yii::t('account', 'Sapphire tour')?></p>
            <h1 class="card-balance"><?=$user->sapphire?> <?=Yii::t('account', 'Points')?> <?=Yii::t('account', 'of')?> 36</h1>
            <img class="logo" src="https://i.imgur.com/NWUeYG4.png" alt="">
        </div>
    </div>
    <?php if(!empty($url) && $url != '#'): ?>
    <div class="col-xs-12 col-sm-6 col-xl-3">
        <div class="new-cart container">
            <p class="card-name"><?=Yii::t('account', 'Earned')?></p>
            <h1 class="card-balance"><?=sprintf('%0.2f', Balance::find()->where(['to_user_id' => $user->id, 'type' => Balance::TYPE_CHARGING])->select('SUM(`to_amount`)')->scalar())?> <?=FAS::icon('dollar-sign')?><</h1>
            <img class="logo" src="https://i.imgur.com/NWUeYG4.png" alt="">
        </div>
    </div>
    <?php endif ?>
</div>