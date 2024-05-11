<?php

use yii\helpers\Url;

/**
 * @var $hide bool
 */
$hide = $hide??false;
?>
<div id="navbar" class="navbar-collapse collapse">
    <ul class="nav navbar-nav">
        <?php if(Yii::$app->user->can('admin,moderator')): ?>
            <li class=""><a href="<?=Url::to(['/cp/default/index'])?>"><?=Yii::t('account', 'Admin panel')?></a></li>
        <?php endif ?>
        <li class=""><a href="<?=Url::to(['/pm/default/index'])?>"><?=Yii::t('account', 'Dashboard')?></a></li>
        <li class=""><a href="<?=Url::to(['/pm/balance/index'])?>"><?=Yii::t('account', 'Balance')?></a></li>
        <li class=""><a href="<?=Url::to(['/pm/partner/index'])?>"><?=Yii::t('account', 'Personally invited')?></a></li>
        <li class=""><a href="<?=Url::to(['/pm/network/index'])?>"><?=Yii::t('account', 'My network Sapphire')?></a></li>
        <li class=""><a href="<?=Url::to(['/pm/travel'])?>"><?=Yii::t('account', 'My network Travel')?></a></li>
        <li class=""><a href="<?=Url::to(['/pm/emerald/index'])?>"><?=Yii::t('account', 'Emerald Health')?></a></li>
        <li class=""><a href="<?=Url::to(['/pm/tour/index'])?>"><?=Yii::t('account', 'Sapphire tour')?></a></li>
        <li class=""><a href="<?=Url::to(['/pm/shop/index'])?>"><?=Yii::t('account', 'Shop')?></a></li>
        <li class=""><a href="<?=Url::to(['/pm/profile/index'])?>"><?=Yii::t('account', 'Profile')?></a></li>
        <li class=""><a href="<?=Url::to(['/pm/news/index'])?>"><?=Yii::t('account', 'News')?></a></li>
        <li class=""><a href="<?=Url::to(['/pm/profile/link'])?>"><?=Yii::t('account', 'Referral link')?></a></li>
        <li class=""><a href="<?=Url::to(['/site/logout'])?>"><?=Yii::t('account', 'Logout')?></a></li>
<!--        <li class="dropdown">-->
<!--            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>-->
<!--            <ul class="dropdown-menu">-->
<!--                <li><a href="#">Action</a></li>-->
<!--                <li><a href="#">Another action</a></li>-->
<!--                <li><a href="#">Something else here</a></li>-->
<!--                <li role="separator" class="divider"></li>-->
<!--                <li class="dropdown-header">Nav header</li>-->
<!--                <li><a href="#">Separated link</a></li>-->
<!--                <li><a href="#">One more separated link</a></li>-->
<!--            </ul>-->
<!--        </li>-->
    </ul>
</div>
