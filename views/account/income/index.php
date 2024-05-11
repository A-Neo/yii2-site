<?php
use app\models\EmeraldMain;
use app\models\EmeraldDelay;
use app\models\EmeraldUsers;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View
 * @var $levels EmeraldMain[]
 * @var $username string
 * @var $userid integer
 * @var $delayUsers EmeraldDelay[]
 */

$this->title = Yii::t('account', 'Проект «Emerald Health»');
//$this->title = 'Мои агенты [' . $username . ']';

$js = <<<JS

function show_travel_frame(id) {
    set_travel_click();
    $(id).show();
    $('.travel_frame_bg').show();
}

function hide_travel_frame() {
    $('.travel_frame').hide();
    $('.travel_frame_bg').hide();
}

function set_travel_click() {
    $('.js-travel-open').off('click');
    $('.js-travel-open').on('click', function(event) {
        var target = $(event.currentTarget);
        $.get('/pm/emerald/net-list', 
              {uid: target.data('uid'), level: target.data('level')}, 
              function(data) {
                  $('#travel_frame_data').html(data);  
                  show_travel_frame('#matrix-modal');
              }, 'text');
    });

}

$(function() {
    $('.btn_travel_level').click(function(event) {
        var target = $(event.currentTarget);
        $('.btn_travel_level').removeClass('btn_travel_level_selected');
        target.addClass('btn_travel_level_selected');
        $('.travel_level_container').hide();
        $('#travel-level-' + target.data('level')).show();
    });
    
   set_travel_click();
   
    $('.travel_frame_bg').click(function() {
        hide_travel_frame();    
    });
    
    $('.travel_frame_close').click(function() {
        hide_travel_frame();    
    });
    
});

JS;

$this->registerJs($js, $this::POS_END);

$ok = Yii::$app->session->getFlash('okmessage', false);
$err = Yii::$app->session->getFlash('errmessage', false);

?>
<style>
    .section_desctop {
        background-image: none
    }
    .btn_travel_level {
        background-color: #fff !important;
        color: rgba(141,141,141,0.9);
        font-size: 18px;
        font-weight: 500;
        padding: 10px 25px;
        border: 1px solid rgba(141,141,141,0.1);
        border-radius: 8px;
    }
    .btn_travel_level_selected {
        color: #fff;
        background-color: #2365aa !important;
    }
    .travel_login_block {
        min-height: auto;
        font-size: 18px;
        padding: 40px 10px;
        color: #fff;
        background-color: #2365aa !important;
    }
    .travel_login_block_empty {
        background-color: #e0e0e0  !important;
        color: #737373;
    }
    .travel_login_block_number {
        position: absolute;
        width: 75px;
        height: 75px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #e0e0e0  !important;
        color: #737373;
        border-radius: 100%;
        top: calc(-75px / 2);
        left: 50%;
        margin-left: calc(-75px / 2);
        font-size: 20px;
        font-weight: 700;
    }
    .travel_login_block_number.active {
        background: #2365aa;
        color: #fff;
    }
    .travel_login_block_me {
        padding: 20px 10px;
    }
</style>
<style>
    .emerald-wrapper {
        background: #fff;
        width: 100%;
        padding: 22px 20px;
        font-size: 16px;
        border: 1px solid rgba(141,141,141,0.1);
        border-radius: 12px;
    }
    .emerald-head {
        width: 100%;
        padding-bottom: 20px;
        border-bottom: 1px solid rgba(141,141,141,0.1);
    }
    .emerald-name {
        font-size: 21px;
        font-weight: 700;
        color: #262626;
    }
    .emerald-refferal {
        margin-top: 15px;
        font-weight: 400;
        color: rgba(141,141,141,1)
    }
    .emerald-refferal b {
        margin: 0 5px;
    }
    .emerald-body {
        margin-top: 20px;
        width: 100%;
    }
    .emerald-body__list {
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .emerald-body__list li {
        margin-top: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .emerald-body__list li span {
        font-weight: 400;
        color: rgba(141,141,141,1);
    }
    .emerald-body__list li b {
        font-weight: 700;
        color: #262626;
    }
    .emerald-link {
        display: block;
        margin-top: 20px;
        text-decoration: none;
        font-weight: 600;
        color: #2F5256;
    }
    .emerald_login_block {
        width: 100%;
        height: 50px;
        display: flex;
        justify-content: center;
        align-items: center;
        border: 1px solid rgba(141,141,141,0.1);
        border-radius: 12px;
    }
</style>
<?php if($ok): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Поздравляем!</strong> <?= $ok ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>
<?php if($err): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Ошибка: </strong> <?= $err ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
<?php if(empty($delayUsers) && count($delayUsers) > 0): ?>
<div class="travel_delay_users_block">
    <?php while ($delayUser = array_shift($delayUsers)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Внимание! </strong> Пользователь <strong><?= $delayUser->getUserLogin() ?></strong> перешёл на стол
        №<?= $delayUser->level ?> раньше Вас! Если вы не поторопитесь и не откроете стол №<?= $delayUser->level ?>,
        то через <strong><?= date('H:i:s', $delayUser->date_end - time()) ?></strong> этот пользователь перейдет
        к Вашему рефереру!
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endwhile; ?>
</div>
<?php endif; ?>
<div class="container travel_main_block">
    <div class="mb-5">
        <button class="mb-3 btn_travel_level <?= (isset($levels[1]) ? 'btn_travel_level_active' : '') ?> btn_travel_level_selected" data-level="1">Silver</button>
        <button class="mb-3 btn_travel_level <?= (isset($levels[2]) ? 'btn_travel_level_active' : '') ?>" data-level="2">Gold</button>
        <button class="mb-3 btn_travel_level <?= (isset($levels[3]) ? 'btn_travel_level_active' : '') ?>" data-level="3">Platinum</button>
        <button class="mb-3 btn_travel_level <?= (isset($levels[4]) ? 'btn_travel_level_active' : '') ?>" data-level="4">Diamond</button>
        <button class="mb-3 btn_travel_level <?= (isset($levels[5]) ? 'btn_travel_level_active' : '') ?>" data-level="5">President</button>
        <a href="/pm/emerald/passive" class="mb-3 btn_travel_level">Пассивный доход</a>
    </div>
    <div class="container">
        <?php for($levelNum = 1; $levelNum <= 5; $levelNum++): ?>
            <div id="travel-level-<?= $levelNum ?>" class="travel_level_container" <?= ($levelNum > 1 ? 'style="display: none;"' : '') ?>>
                <?php if (isset($levels[$levelNum])): $levelUsers = $levels[$levelNum]->getRefUsers($userid); ?>
                    <div class="row justify-content-center mb-5">
                        <div class="col-12">
                            <h1 style="font-size: 36px !important; text-align: center;">Моя команда</h1>
                        </div>
                    </div>
                    <div class="row justify-content-center mb-5">
                        <?php for($j = 1; $j <= 4; $j++) : $user = array_shift($levelUsers); ?>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6" style="margin-bottom: 1.5rem;">
                            <div class="emerald-wrapper">
                                <?php if ($user):  ?>
                                <div class="emerald-head">
                                    <div class="emerald-name"><?= $user->getFullname() ?></div>
                                    <div class="emerald-refferal"><span>Наставник</span><b>•</b><span><?= $user->getUsername() ?></span></div>
                                </div>
                                <div class="emerald-body">
                                    <ul class="emerald-body__list">
                                        <li><span>Ваш ранг</span><b><?= $user->getRang() ?></b></li>
                                        <li><span>Партнеров</span><b><?= $user->getPartnersCount() ?></b></li>
                                        <li><span>Участников</span><b><?= $user->getSubscribersCount() ?></b></li>
                                    </ul>
                                </div>
                                <a href="#" class="emerald-link js-travel-open" data-uid="<?= $user->id_user ?>" data-level="<?= $levelNum ?>">Подробно</a>
                                <?php else:  ?>
                                    <div class="emerald-head">
                                        <div class="emerald-name">Не занято</div>
                                        <div class="emerald-refferal"><span>Наставник</span><b>•</b><span> - </span></div>
                                    </div>
                                    <div class="emerald-body">
                                        <ul class="emerald-body__list">
                                            <li><span>Ваш ранг</span><b>-</b></li>
                                            <li><span>Партнеров</span><b>-</b></li>
                                            <li><span>Участников</span><b>-</b></li>
                                        </ul>
                                    </div>
                                    <a href="#" class="emerald-link">Подробно</a>
                                <?php endif;  ?>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                <?php else: ?>
                    <?php if ($levelNum == 1): ?>
                    <div class="col-md-3 justify-content-center" style="width: 300px">
                        <?php $form = ActiveForm::begin([
                            'action' => Url::to(['/pm/emerald/init']),
                            'method' => 'post',
                        ]); ?>
                        <?= $form->field(new \yii\base\DynamicModel(['id_ref']), 'id_ref')->label('Referal login') ?>
                        <div class="form-group">
                            <?=Html::submitButton(Yii::t('account', 'Activate'), ['class' => 'btn btn-primary', 'name' => 'signup-button'])?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                    <?php else: ?>
                        Этот уровень пока недоступен
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endfor; ?>

    </div>
</div>

<script>
    // js

</script>
