<?php

/* @var $this \yii\web\View */

/* @var $content string */

use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;
use app\widgets\Alert;
use app\models\Balance;

$user = \app\models\User::findOne(['id' => Yii::$app->user->id]);

AppAsset::register($this);
$hasPoints = Balance::find()->where(['to_user_id' => $user->id, 'status' => Balance::STATUS_WAITING, 'comment' => ''])->exists();
$this->registerJs(<<<JS
function checkComment(){
    var comment = $('#tx-comment').val();
    comment=comment.trim();
    if(comment) {
        $('#send-request').removeAttr('disabled').removeClass('disabled');
    } else {
        $('#send-request').attr('disabled','disabled').addClass('disabled');
    }
}
checkComment();
$('#send-request').on('click', function (e){
  e.preventDefault();
  if($('#send-request').hasClass('disabled')) {
      return false;
  }
  $('#request-form')[0].submit();
  return false;
});
$('#tx-comment').on('change keyup paste click', function (){
    checkComment();    
});
JS
);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
<head>
    <meta charset="<?=Yii::$app->charset?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=Html::encode($this->title)?></title>
    <?php $this->registerCsrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<script>
    (function(d, w, c) {
        w.ChatraID = 'dJQc5iTrDpnb9nWmD';
        var s = d.createElement('script');
        w[c] = w[c] || function() {
            (w[c].q = w[c].q || []).push(arguments);
        };
        s.async = true;
        s.src = 'https://call.chatra.io/chatra.js';
        if (d.head) d.head.appendChild(s);
    })(document, window, 'Chatra');
</script>
<body class="body-2">
<?php $this->beginBody() ?>
<div class="section_desctop wf-section <?=Yii::$app->controller->id == 'tour' ? 'istanbul' : ''?>">
    <div class="wrapp_logo-menu">
        <div data-animation="over-left" data-collapse="small" data-duration="400" data-easing="ease" data-easing2="ease" role="banner" class="w-navbar w-nav">
            <div class="container w-container" id="w-nav-container">
                <div class="div-block-8">
                    <div class="div-block-10">
                        <a href="/" class="brand-2 w-nav-brand d-none">
                            <img loading="lazy" src="<?=AppAsset::image('626178820fa83d20781dc9bb_logo-out.png');?>" alt class="image-2">
                        </a>
                    </div>
                    <div class="div-block-9">
                        <div class="wrapp_top-btn _2">
                            <div class="div-block-7">
                                <?php if($hasPoints): ?>
                                    <a href="#" class="new-cart" title="<?=Yii::t('account', 'Sapphire points waiting for request')?>"><p class="card-name"><?=Yii::t('account', 'Request')?></p></a>
                                <?php endif ?>
                                <a href="/pm/balance" class="new-cart"><?=Yii::t('account', 'Balance')?> <span class="span"><?=number_format($user->balance - $user->accumulation, 2)?></span> $</a>
                                <a href="/pm/balance" class="new-cart"><?=Yii::t('account', 'Balance ST')?> <span class="span"><?= number_format($user->balance_travel, 2)?></span> $</a>
                            </div>
                        </div>
                        <div class="menu-button w-nav-button">
                            <div class="icon w-icon-nav-menu"></div>
                        </div>
                    </div>
                </div>
                <?= $this->render('menu'); ?>
            </div>
        </div>
    </div>
    <div class="wrapp_desctop w-100 pl-3">
        <h4 class="h3"><?=$this->title?></h4>
        <?=Alert::widget()?>
        <?=$content?>
    </div>
    <?= $this->render('sapphire-modal');?>
    <?= $this->render('travel-modal');?>
    <?php if(Yii::$app->controller->id == 'profile'): ?>
        <div class="wrapp_img-woomen _2">
            <img src="<?=AppAsset::image('6262759d049c3552ad46dd10_image209-PhotoRoom.png');?>" loading="lazy"
                 srcset="<?=AppAsset::image('6262759d049c3552ad46dd10_image209-PhotoRoom-p-500.png');?> 500w, <?=AppAsset::image('6262759d049c3552ad46dd10_image209-PhotoRoom-p-800.png');?> 800w, <?=AppAsset::image('6262759d049c3552ad46dd10_image209-PhotoRoom.png');?> 914w"
                 sizes="100vw" alt class="image-3"></div>
    <?php elseif(Yii::$app->controller->id == 'balance'): ?>
        <div class="wrapp_img-woomen _3">
            <img src="<?=AppAsset::image('62628a4987788f008198e716_women_balance-PhotoRoom.png');?>" loading="lazy"
                 srcset="<?=AppAsset::image('62628a4987788f008198e716_women_balance-PhotoRoom-p-500.png');?> 500w, <?=AppAsset::image('62628a4987788f008198e716_women_balance-PhotoRoom-p-800.png 800w');?>, <?=AppAsset::image('62628a4987788f008198e716_women_balance-PhotoRoom.png');?> 980w"
                 sizes="(max-width: 991px) 100vw, 980px" alt class="women_balance"></div>
    <?php else: ?>
        <div class="wrapp_img-woomen">
            <img src="<?=AppAsset::image('62624fecef9b10f89e9ce2de_women_desctop20(1).png');?>" loading="lazy"
                 srcset="<?=AppAsset::image('62624fecef9b10f89e9ce2de_women_desctop20(1)-p-500.png');?> 500w, <?=AppAsset::image('62624fecef9b10f89e9ce2de_women_desctop20(1)-p-800.png 800w, images/62624fecef9b10f89e9ce2de_women_desctop20(1)-p-1080.png');?> 1080w, <?=AppAsset::image('62624fecef9b10f89e9ce2de_women_desctop20(1).png');?> 1722w"
                 sizes="(max-width: 991px) 100vw, 700px" alt class="women">
        </div>
    <?php endif ?>
    <div class="wrapp_soc-icon _2">
        <?php $url = Yii::$app->settings->get('social', 'telegram', '#'); ?>
        <?php if(!empty($url) && $url != '#'): ?>
            <a href="<?=$url?>" target="_blank" class="link-in w-inline-block">
                <img src="<?=AppAsset::image('telegram2.png')?>" loading="lazy" width="40" class="image_tl">
            </a>
        <?php endif ?>
        <?php $url = Yii::$app->settings->get('social', 'instagram', '#'); ?>
        <?php if(!empty($url) && $url != '#'): ?>
            <a href="<?=$url?>" target="_blank" class="link-in w-inline-block">
                <img src="<?=AppAsset::image('instagram2.png')?>" loading="lazy" width="40" class="image_tl">
            </a>
        <?php endif ?>
        <?php $url = Yii::$app->settings->get('social', 'youtube', '#'); ?>
        <?php if(!empty($url)): ?>
            <a href="<?=$url?>" target="_blank" class="link-in w-inline-block">
                <img src="<?=AppAsset::image('youtube2.png')?>" loading="lazy" width="40" class="image_tl">
            </a>
        <?php endif ?>
    </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
