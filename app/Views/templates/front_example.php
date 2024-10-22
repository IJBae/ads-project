<?=$this->include('templates/header.php')?>
<!-- exam scss 파일 연결 -->
<link href="/static/css/exam/_account.css" rel="stylesheet">
<link href="/static/css/exam/_agree.css" rel="stylesheet">
<link href="/static/css/exam/_etc.css" rel="stylesheet">
<!-- //exam scss 파일 연결 -->
<?=$this->renderSection('body')?>
<div class="app">  
    <div class="wrap d-flex">
        <?=$this->include('templates/inc/navbar_example.php')?>
        <?=$this->renderSection('content')?>
    </div>
    <?=$this->renderSection('modal')?>
</div>
<?=$this->renderSection('script')?>

<?=$this->include('templates/footer.php')?>
