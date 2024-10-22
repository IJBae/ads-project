<?=$this->extend('templates/front.php');?>

<?=$this->section('title');?>
ZENITH - 회계 관리 / 출금요청
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>
<script>
    console.log('header')
</script>
<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap">
    <div class="title-area">
        <h2 class="page-title">출금요청 - 업체등록(출금요청)</h2>
    </div>
    <div>
        <div class="section">
            샘플
        </div>
        <div class="section">
        <form class="search d-flex justify-content-center">
            <select required class=''>
                <option value=''>-선택-</option>
                <option value='BOA은행'> BOA은행</option>
                <option value='SC제일은행'>SC제일은행</option>
                <option value='KDB산업은행'>KDB산업은행</option>
                <option value='경남은행'></option>
                <option value='광주은행'>광주은행</option>
                <option value='국민은행'>국민은행</option>
                <option value='굿모닝신한증권'>굿모닝신한증권</option>
                <option value='기업은행'>기업은행</option>
                <option value='농협'>농협</option>
                <option value='부산은행'>부산은행</option>
                <option value='상호신용금고'>상호신용금고</option>
                <option value='새마을금고'>새마을금고</option>
                <option value='수협중앙회'>수협중앙회</option>
                <option value='신한은행'>신한은행</option>
                <option value='신한금융투자증권'>신한금융투자증권</option>
                <option value='신협중앙회'>신협중앙회</option>
                <option value='외환은행'>외환은행</option>
                <option value='우리은행'>우리은행</option>
                <option value='우체국'>우체국</option>
                <option value='전북은행'>전북은행</option>
                <option value='제주은행'>제주은행</option>
                <option value='카카오뱅크'>카카오뱅크</option>
                <option value='케이뱅크'>케이뱅크</option>
                <option value='토스뱅크'>토스뱅크</option>
                <option value='하나은행'>하나은행</option>
                <option value='한국씨티은행'>한국씨티은행</option>
            </select>
        </form>
    </div>
    </div>
</div>
<?=$this->endSection();?>

<!--스크립트-->
<?=$this->section('script');?>
<script>
    $(document).ready(function() {
    
    });
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
