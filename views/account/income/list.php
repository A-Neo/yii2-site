<?php
use app\models\EmeraldMain;

/* @var $this yii\web\View
 * @var $levels EmeraldMain[]
 * @var $username string
 * @var $userid integer
 * @var $level integer
 */

?>

<div class="container">
    <div class="emerald_level_container">
    <?php if (isset($levels[$level])):
    $levelUsers = $levels[$level]->getUsersList();
    ?>
        <div class="row justify-content-center mb-5">
            <div class="col-12">
                <div class="emerald_login_block"><?= $username ?></div>
            </div>
        </div>
        <div class="row justify-content-center">
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
                        <?php endif;  ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        <div class="row justify-content-left">
            <?php while($user = array_shift($levelUsers)): ?>
                <div class="col-md-2 justify-content-center  mb-5">
                    <div class="travel_login_block_avatar">
                        <img src="/pm/emerald/user-avatar?uid=<?= $user->id_user ?>" alt="Avatar" class="rounded-circle">
                    </div>
                    <div class="travel_login_block js-travel-open" data-uid="<?= $user->id_user ?>" data-level="<?= $level ?>"><?= $user->getUsername() ?></div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        Этот уровень пока недоступен
    <?php endif; ?>
    </div>
</div>

