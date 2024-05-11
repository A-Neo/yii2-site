<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAssetForever;
use app\widgets\Alert;
use app\models\Balance;

$user = Yii::$app->user->identity;
AppAssetForever::register($this);
$hasPoints = Balance::find()->
where(['to_user_id' => $user->id, 'status' => Balance::STATUS_WAITING, 'comment' => ''])
    ->orWhere(['from_user_id' => $user->id])
    ->exists();

?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
<head>
    <meta charset="<?=Yii::$app->charset?>">
    <title><?=Html::encode($this->title)?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<!--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rampart+One&family=Roboto&display=swap" rel="stylesheet">
    <?php $this->registerCsrfMetaTags() ?>
    <?php $this->head() ?>
    <style>
        * {
            font-family: 'Rampart', 'Roboto', sans-serif;
            box-sizing:border-box;
            margin:0;
        }

        html {
            padding: 50px;
        }
        body {
            background: linear-gradient(90deg, #BE8CEF 0%, rgba(61, 46, 232, 0.83) 100%);
            display: flex;
            align-items: center;
            flex-direction: column;
            font-size:14px;
        }

        .container {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.25),
            0 10px 10px rgba(0,0,0,0.22);
            position: relative;
            overflow: hidden;
            width: 1400px;
            max-width: 100%;
            /*min-height: calc(95vh - 100px);*/
            max-height: 1200px;
            min-width: 360px;
            margin: 0;
            padding: 0;
            /*overflow-y: scroll;*/
        }
        h2 {
            font-weight: 800;
            font-size:3.6rem;
            margin-bottom:1rem;
        }
        .form-container {
            display:flex;

        }
        .right-container {
            display:flex;
            width: 100%;
            height:100%;
            background-color: #fff;
            justify-content:center;
            align-items:center;
            padding: 35px 0;
        }

        .left-container {
            position: relative;
            display:flex;
            width: 100%;
            justify-content:center;
            align-items:center;
            color:#fff;
            background-color:#00b4cf;
        }

        .left-container p {
            font-size:1rem;
        }

        .right-inner-container {
            width:90%;
            height:80%;
            text-align: left;
        }

        .left-inner-container {
            height:50%;
            width:80%;
            text-align:center;
            line-height:22px;
        }

        form .form-group input, form .form-group select, form .form-group .select2-container--krajee-bs4 .select2-selection {
            background-color: #eee;
            border: none;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
            font-size: 1rem;
            height: 55px;
        }

        form .form-group input:focus, form .form-group select:focus{
            outline:none;
        }

        form .form-group button {
            margin-top: 20px;
            border-radius: 20px;
            border: 1px solid #00b4cf;
            background-color: #00b4cf;
            color: #FFFFFF;
            font-size: 1rem;
            font-weight: bold;
            padding: 12px 50px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
            cursor:pointer;
        }

        form .form-group button:hover {
            opacity:0.7;
        }

        form .form-group label {
            text-align: left;
            font-weight: 900;
            font-size: 18px;
            width: 100%;
            margin-bottom: 0.3rem;
        }
        @media only screen and (max-width: 800px) {
            .left-container{
                display: none;
            }
            .lg-view {
                display:none;
            }
        }

        @media only screen and (min-width: 800px) {
            .sm-view {
                display:none;
            }
        }

        form p {
            text-align:left;
        }

        /* MAIL ICON */
        .container-mail {
            grid-column: 2 / 3;
            grid-row: 2 / 6;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 31rem;
            height: 28rem;
            margin-bottom: 4rem;
        }
        @media only screen and (max-width: 1200px) {
            .container-mail {
                grid-column: 1 / 2;
                grid-row: 6 / 7;
                width: 100%;
                height: 100%;
                margin-top: 4rem;
                justify-self: center;
            }
        }
        @media only screen and (max-width: 790px) {
            h2 {
                font-size: 2.8rem;
            }
            .container-mail {
                grid-column: 1 / 2;
                grid-row: 6 / 7;
                height: 12rem;
                margin-top: 4rem;
                justify-self: center;
            }
        }

        .mail {
            position: relative;
            top: -4rem;
            left: -6rem;
        }
        @media only screen and (max-width: 790px) {
            .mail {
                left: -27%;
                top: -5rem;
            }
        }

        @media only screen and (max-width: 620px) {
            h2 {
                font-size: 1.8rem;
            }
            form {
                margin-top: 40px;
            }
        }

        .mail__top {
            position: absolute;
            top: -5.70rem;
            width: 0;
            height: 0;
            border-right: 8rem solid transparent;
            border-left: 8rem solid transparent;
            border-bottom: 5.80rem solid #ffab17;
            z-index: 0;
        }
        .mail__top.closed {
            transition: transform .6s .8s, z-index .2s .4s;
            z-index: 2;
            transition-delay: .5s;
            transform-origin: bottom left;
            transform: rotate3d(1, 0, 0, 180deg);
        }

        .mail__back {
            position: absolute;
            background: #ffab17;
            width: 16rem;
            height: 10rem;
            box-shadow: 0 .1rem 1rem rgba(0, 0, 0, .3);
        }

        .mail__left {
            position: absolute;
            width: 0;
            height: 0;
            border-left: 8rem solid #ffc867;
            border-top: 5rem solid transparent;
            border-bottom: 5rem solid transparent;
        }

        .mail__right {
            position: absolute;
            left: 8rem;
            width: 0;
            height: 0;
            border-right: 8rem solid #ffc867;
            border-top: 5rem solid transparent;
            border-bottom: 5rem solid transparent;
        }

        .mail__bottom {
            position: absolute;
            top: 4.92rem;
            width: 0;
            height: 0;
            border-right: 8rem solid transparent;
            border-left: 8rem solid transparent;
            border-bottom: 5.08rem solid #ffbb43;
        }

        .mail__letter {
            position: absolute;
            top: -4rem;
            left: 2rem;
            width: 12rem;
            height: 9rem;
            background: #fff;
            box-shadow: 0 0 .8rem rgba(0, 0, 0, .15);
            overflow: hidden;
            transition: all .8s ease;
        }
        .mail__letter.move {
            transform: translateY(45px);
        }

        .mail__letter-square {
            position: absolute;
            top: 3rem;
            left: 1rem;
            width: 3.8rem;
            height: 4rem;
        }
        .mail__letter-square::before {
            content: "";
            position: absolute;
            top: -2rem;
            width: 10rem;
            height: 1.5rem;
            background: inherit;
        }

        .mail__letter-lines {
            position: absolute;
            top: 4.9rem;
            left: 5.8rem;
            width: 5rem;
            height: .3rem;
            background: #e0e0e0;
        }
        .mail__letter-lines::before {
            content: "";
            position: absolute;
            top: -1rem;
            width: 5rem;
            height: .3rem;
            background: #e0e0e0;
        }
        .mail__letter-lines::after {
            content: "";
            position: absolute;
            top: 1rem;
            width: 5rem;
            height: .3rem;
            background: #e0e0e0;
        }

    </style>
</head>
<body>
<?php $this->beginBody() ?>
<header></header>
<?=$content?>
<?php $this->endBody() ?>
<script>
    $('.form__btn').click(function() {
        $('.mail__letter').toggleClass('move');
        $('.mail__top').toggleClass('closed');
        $('.form__btn--invisible').toggleClass('visible');
        $('.form__btn--visible').toggleClass('invisible');
    });

    $('input').focus(function() {
        $(this).parent().addClass('active');
        $('input').focusout(function() {
            if($(this).val() == '') { $(this).parent().removeClass('active');
            } else { $(this).parent().addClass('active');
            }
        })
    });
</script>
</body>
</html>
<?php $this->endPage() ?>