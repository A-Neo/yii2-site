<?php

use yii\helpers\Url;
use yii\bootstrap4\Html;
use app\components\Api;
use rmrevin\yii\fontawesome\FA;
use rmrevin\yii\fontawesome\FAS;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Balance;


/* @var $this yii\web\View */
/* @var $searchModel app\models\search\BalanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('account', 'Balance');
$user = Yii::$app->user->identity;

/**
 * @var $user \app\models\User
 */
if(!empty($incomes->next_payment_date)) {
$js = <<<JS
initCountdown('$incomes->next_payment_date');
JS;

$this->registerJs($js, $this::POS_END);
}
?>
<?php /*if(!$user->isActive()): ?>
    <div class="card">
        <div class="card-body">
            <a href="<?=Url::to(['/pm/activation/index'])?>"><?=Yii::t('account', 'Activation required')?></a>
        </div>
    </div>
    <?php return ?>
<?php else:*/ ?>
<div class="balance_block">
    <?=$this->render('_tabs')?>
</div>
<?php /*endif */?>
<style>
    .inform_tour {
        background: #fff;
        width: 100%;
        padding: 22px 20px;
        font-size: 16px;
        border: 1px solid rgba(141,141,141,0.1);
        border-radius: 12px;
    }
    .balance {
        width: 100%;
        padding-bottom: 20px;
        border-bottom: 1px solid rgba(141,141,141,0.1);
        font-size: 21px;
        font-weight: 400;
        color: rgba(141,141,141,1);
    }
    .balance_amount {
        margin-top: 15px;
        font-weight: 700;
        color: #262626;
        font-size: 21px;
    }
    #countdown_btn {
        display: none;
        font-size: 16px;
        padding: 9px 15px;
        background-color: #3898EC;
        color: white;
        border: 0;
        line-height: inherit;
        text-decoration: none;
        cursor: pointer;
        margin-right: 1vw;
        padding-top: 6px;
        padding-bottom: 6px;
        border-radius: 8px;
        -webkit-transition: all 200ms ease;
        transition: all 200ms ease;
        font-weight: 500;
        text-align: center;
    }
    #countdown_btn.active {
        display: block;
    }
    .none {
        display: none;
    }
</style>

<div class="row w-100">
    <div class="col-xs-12 col-sm-12 col-xl-4 p-3">
        <div class="inform_tour w-inline-block w-100">
            <div class="balance">Emerald Health - Countdown</div>
            <div class="balance_amount"><div id="countdown">Time</div> <?php if(!empty($incomes->next_payment_date)) : ?> <a id="countdown_btn" data-uid="<?= $user->id ?>" data-iid="<?= $incomes->id ?>">Получить выплату</a> <?php endif; ?></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-xl-4 p-3">
        <div class="inform_tour w-inline-block w-100">
            <div class="balance">Emerald Health - Many left</div>
            <div class="balance_amount">
                <span class="span text-nowrap"><?php if(!empty($incomes->next_payment_date)) : ?> <?=number_format($incomes->getRemainingAmount(), 2, '.', '')?> <?=FAS::icon('dollar-sign')?> <?php endif; ?></span>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-xl-4 p-3">
        <div class="inform_tour w-inline-block w-100">
            <div class="balance">Emerald Health - Total Passive Balance</div>
            <div class="balance_amount">
                <span class="span text-nowrap"><?php if(!empty($incomes->next_payment_date)) : ?> <?=number_format($incomes->getTotalPaymentsAmount(), 2, '.', '')?> <?=FAS::icon('dollar-sign')?> <?php endif; ?></span>
            </div>
        </div>
    </div>
</div>
<div class="w-100 wrapper_form-balance">
    <?php Pjax::begin(); ?>
    <?=GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel'  => $searchModel,
        'rowOptions'   => function ($model, $key, $index, $grid) {
            $s = $model->to_user_id == Yii::$app->user->id;
            $ss = $model->type == Balance::TYPE_ACCUMULATION;
            return [
                'class' => 'text-' . ($ss ? 'warning' : ($s ? 'success' : 'danger')),
            ];
        },
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'type',
                'format'    => 'html',
                'value'     => function ($model) {
                    return $model->getTypeName();
                },
            ],
            [
                'attribute' => 'from_user_id',
                'value'     => 'fromUserName',
            ],
            [
                'attribute' => 'to_user_id',
                'value'     => 'toUserName',
            ],
            //'history_id',
            [
                'attribute' => 'from_amount',
                'format'    => 'html',
                'value'     => function ($model) {
                    $s = $model->to_user_id == Yii::$app->user->id;
                    if($model->from_sapphire > 0 || $model->to_sapphire > 0){
                        return ($s ? '+ ' : '- ') . ($s ? $model->to_sapphire : $model->from_sapphire) . ' ' . Yii::t('account', 'Points');
                    }
                    return ($s ? '+ ' : '- ') . Api::asNumber($s ? $model->to_amount : $model->from_amount);
                },
            ],
            [
                'class'     => 'app\components\columns\ToggleColumn',
                'attribute' => 'status',
                'readonly'  => 'true',
            ],
            'created_at:dateTime',
            'comment',
            //'updated_at:dateTime',
        ],
    ]);?>
    <?php Pjax::end(); ?>
</div>
<script>
    // Функция для расчета разницы между двумя датами и вывода результата
    function calculateCountdown(endDate) {
        const now = new Date();
        const end = new Date(endDate);
        const timeLeft = end - now;

       // if (end <= now) return { 0, 0, 0, 0 };

        let days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        let hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

        return { days, hours, minutes, seconds };
    }

    // Функция для обновления текста на странице с обратным отсчетом
    function updateCountdownDisplay(countdown) {
        const countdownElement = document.getElementById('countdown');
        if (countdown.minutes <= 0) {
            countdownElement.innerHTML = '';
            document.getElementById('countdown_btn').classList.add('active');
        } else {
            countdownElement.innerHTML = `${countdown.days} дн. - ${countdown.hours} : ${countdown.minutes} : ${countdown.seconds}`;
        }
    }

    // Функция инициализации обратного отсчета
    function initCountdown(endDate) {
        setInterval(function() {
            const countdown = calculateCountdown(endDate);
            updateCountdownDisplay(countdown);
        }, 1000);
    }

    function setTravelClick() {
        var countdownBtn = document.getElementById('countdown_btn');
        countdownBtn.removeEventListener('click', onCountdownClick);
        countdownBtn.addEventListener('click', onCountdownClick);
    }

    function onCountdownClick(event) {
        var target = event.currentTarget;
        var uid = target.getAttribute('data-uid');
        var iid = target.getAttribute('data-iid');

        fetch('/pm/balance/payment')
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.text();
            })
            .then(function(data) {
                console.log(data);
            })
            .catch(function(error) {
                console.error('Fetch error:', error);
            });

    }

    setTravelClick();


    // Запуск обратного отсчета
</script>