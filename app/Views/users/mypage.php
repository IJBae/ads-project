<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    ZENITH - 마이페이지
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>

<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap myPage-container">
    <div class="title-area">
        <h2 class="page-title">마이페이지</h2>
        <p class="title-disc">혼자서는 작은 한 방울이지만 함께 모이면 바다를 이룬다.</p>
    </div>
    <main class="mt0">
        <!-- <h1><?php echo $user->nickname?></h1> -->
        <div class="row mt0">
            <div>
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h3 class="card-title mb-0">Profile</h3>
                    </div>
                    <div class="card-body">
                    <?php if (session('error') !== null) : ?>
                    <div class="alert alert-danger" role="alert"><?= session('error') ?></div>
                    <?php elseif (session('errors') !== null) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?php if (is_array(session('errors'))) : ?>
                                <?php foreach (session('errors') as $error) : ?>
                                    <?= $error ?>
                                    <br>
                                <?php endforeach ?>
                            <?php else : ?>
                                <?= session('errors') ?>
                            <?php endif ?>
                        </div>
                    <?php endif ?>
                    <?php if (session('message') !== null) : ?>
                        <div class="alert alert-success" role="alert"><?= session('message') ?></div>
                    <?php endif ?>
                        <form action="/mypage/update" method="post">
                        <?php if(!empty($user->nickname)) {?>
                            <dl>
                                <dt><span>이</span><span>름</span></dt>
                                <dd><?php echo $user->nickname?></dd> 
                            </dl>
                        <?php } ?>
                            <dl>
                                <dt><span>아</span><span>이</span><span>디</span></dt>
                                <dd><?php echo $user->username?></dd> 
                            </dl>
                            <dl>
                                <dt><span>이</span><span>메</span><span>일</span></dt>
                                <dd><?php echo $user->getEmail()?></dd> 
                            </dl>
                            <dl>
                                <dt><span>기</span><span>존</span><span>비</span><span>밀</span><span>번</span><span>호</span></dt>
                                <dd><input type="password" class="form-control" name="old_password" placeholder="<?= lang('Auth.old_password') ?>"
                        value="" /></dd>
                            </dl>
                            <dl>
                                <dt><span>신</span><span>규</span><span>비</span><span>밀</span><span>번</span><span>호</span></dt>
                                <dd><input type="password" class="form-control" name="password" placeholder="<?= lang('Auth.password') ?>"
                        value="" /></dd>
                            </dl>
                            <dl>
                                <dt><span>비</span><span>밀</span><span>번</span><span>호</span><span>확</span><span>인</span></dt>
                                <dd><input type="password" class="form-control" name="password_confirm" placeholder="<?= lang('Auth.passwordConfirm') ?>"
                        value="" /></dd>
                            </dl>
                            <button type="submit" class="btn btn-primary">등록</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

</div>
<?=$this->endSection();?>

<?=$this->section('script');?>
<script>
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
