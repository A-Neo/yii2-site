<?php

use yii\helpers\Url;

/**
 * @var $hide bool
 */
$hide = $hide??false;
?>
<nav role="navigation" class="nav_menu w-nav-menu accordian-class" id="w-nav-menu">
<ul class="show-dropdown main-navbar">
    <div class="selector-active"><div class="top"></div><div class="bottom"></div></div>
    <?php if(Yii::$app->user->can('admin,moderator')): ?>
    <li><a href="<?=Url::to(['/cp/default/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Admin panel')?></a></li>
    <?php endif ?>
    <li><a href="<?=Url::to(['/pm/default/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Dashboard')?></a></li>
    <li><a href="<?=Url::to(['/pm/balance/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Balance')?></a></li>
    <li><a href="<?=Url::to(['/pm/partner/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Personally invited')?></a></li>
    <li><a href="<?=Url::to(['/pm/network/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'My network Sapphire')?></a></li>
	<li><a href="<?=Url::to(['/pm/travel'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'My network Travel')?></a></li>
    <li><a href="<?=Url::to(['/pm/emerald/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Emerald Health')?></a></li>
    <li><a href="<?=Url::to(['/pm/tour/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Sapphire tour')?></a></li>
    <li><a href="<?=Url::to(['/pm/shop/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Shop')?></a></li>
    <li><a href="<?=Url::to(['/pm/profile/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Profile')?></a></li>
    <li><a href="<?=Url::to(['/pm/news/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'News')?></a></li>
    <li><a href="<?=Url::to(['/pm/profile/link'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Referral link')?></a></li>
    <li><a href="<?=Url::to(['/site/logout'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Logout')?></a></li>
</ul>
</nav>
<script>
    window.addEventListener("load", function() {
        var tabsVerticalInner = $('.accordian-class');
        var selectorVerticalInner = $('.accordian-class').find('li').length;
        var activeItemVerticalInner = tabsVerticalInner.find('.active');
        var activeWidthVerticalHeight = activeItemVerticalInner.innerHeight();
        var activeWidthVerticalWidth = activeItemVerticalInner.innerWidth() + 10;
        var itemPosVerticalTop = activeItemVerticalInner.position();
        var itemPosVerticalLeft = activeItemVerticalInner.position();

        $(".selector-active").css({
            "top":itemPosVerticalTop.top + "px",
            "left":itemPosVerticalLeft.left + "px",
            "height": activeWidthVerticalHeight + "px",
            "width": activeWidthVerticalWidth + "px",
            "opacity": 1
        });

        $(".accordian-class").on("click","li",function(e){
            $('.accordian-class ul li').removeClass("active");
            $(this).addClass('active');
            var activeWidthVerticalHeight = $(this).innerHeight();
            var activeWidthVerticalWidth = $(this).innerWidth();
            var itemPosVerticalTop = $(this).position();
            var itemPosVerticalLeft = $(this).position();
            $(".selector-active").css({
                "top":itemPosVerticalTop.top + "px",
                "left":itemPosVerticalLeft.left + "px",
                "height": activeWidthVerticalHeight + "px",
                "width": activeWidthVerticalWidth + "px",
                "opacity": 1
            });
        });
    });
</script>