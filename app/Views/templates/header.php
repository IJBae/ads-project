<!doctype html>
<html lang="ko">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="format-detection" content="telephone=no"><!-- 전화번호 인식 하지 않도록 제어 -->
    <link href="/static/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/node_modules/daterangepicker/daterangepicker.css" rel="stylesheet"> 
    <link href="/static/css/ui.css?<?=date('Ymd')?>" rel="stylesheet"> 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/static/node_modules/jquery-ui/dist/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
    <title><?= $this->renderSection('title') ?></title>
    <script src="/static/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="/static/node_modules/jquery-ui/dist/jquery-ui.min.js"></script>
    <script src="/static/node_modules/moment/moment.js"></script>
    <script src="/static/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/static/node_modules/daterangepicker/daterangepicker.js"></script>
    <script src="/static/js/ui.js?<?=date('Ymd')?>"></script>
    <script src="/static/js/index.js?<?=date('Ymd')?>"></script>
    <script src="/static/js/clipboard.min.js?<?=date('Ymd')?>"></script>
    <?=$this->renderSection('header')?>
</head>
<body>
<?=$this->renderSection('guestContent')?>