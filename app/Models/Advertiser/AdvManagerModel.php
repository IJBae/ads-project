<?php

namespace App\Models\Advertiser;

use CodeIgniter\Model;

class AdvManagerModel extends Model
{
    protected $zenith, $facebook, $google, $kakao;
    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
		$this->facebook = model(AdvFacebookManagerModel::class);
        $this->google = model(AdvGoogleManagerModel::class);
        $this->kakao = model(AdvKakaoManagerModel::class);
    }

    public function getAccounts($data)
    {
        return $this->getQueryResults($data, 'getAccounts');
    }

    public function getMediaAccounts($data)
    {
        return $this->getQueryResults($data, 'getMediaAccounts');
    }

    public function getCampaigns($data)
    {
        return $this->getQueryResults($data, 'getCampaigns');
    }

    public function getAdSets($data)
    {
        return $this->getQueryResults($data, 'getAdsets');
    }

    public function getAds($data)
    {
        return $this->getQueryResults($data, 'getAds');
    }

    public function getOnlyAdAccount($data)
    {   
        $data['searchData']['stx'] = $data['stx'] ?? '';
        $data['searchData']['media'] = 'facebook|google';
        return $this->getQueryResults($data, 'getOnlyAdAccount');
    }

    public function getReport($data)
    {
        if(!empty($data['check'])){
            $data = $this->setArgs($data);
        }
        
        return $this->getQueryResults($data, 'getReport');
    }

    private function setArgs($data)
    {
        if (!isset($data['searchData']['data'])) {
            // data 키가 없을 경우 기본값 설정 또는 예외 처리
            return $data;
        }
        $kakaoNumbers = array();
        $googleNumbers = array();
        $facebookNumbers = array();
        foreach($data['searchData']['data'][$data['searchData']['type']] as $value) {
            $parts = explode('_', $value);
            $media = array_shift($parts);
            $id = end($parts);
            
            switch ($media) {
                case 'kakao': $kakaoNumbers[] = $id; break;
                case 'google': $googleNumbers[] = $id; break;
                case 'facebook': $facebookNumbers[] = $id; break;
                default: break;
            }
        }

        $data['searchData']['facebookCheck'] = $facebookNumbers;
        $data['searchData']['googleCheck'] = $googleNumbers;
        $data['searchData']['kakaoCheck'] = $kakaoNumbers;
        unset($facebookNumbers, $googleNumbers, $kakaoNumbers, $data['searchData']['media']);
        $data['searchData']['media'] = [];

        if(!empty($data['searchData']['facebookCheck'])){
            array_push($data['searchData']['media'], 'facebook');
        }
        
        if(!empty($data['searchData']['googleCheck'])){
            array_push($data['searchData']['media'], 'google');
        }
        
        if(!empty($data['searchData']['kakaoCheck'])){
            array_push($data['searchData']['media'], 'kakao');
        }

        $data['searchData']['media'] = implode("|", $data['searchData']['media']);
        return $data;
    }

    private function getQueryResults($data, $type)
    {
        // $cache = \Config\Services::cache();
        // $cacheName = $type."_".md5(json_encode($data['searchData']));
        // $cacheData = $cache->get($cacheName);
        // if($cacheName && $cacheData) return $cacheData;

        $builders = [];
        if(!isset($data['searchData'])) {
            $data['searchData'] = [
                'media' => 'facebook|google|kakao',
                'sdate' => date('Y-m-d'),
                'edate' => date('Y-m-d'),
            ];
        }
        if(!isset($data['searchData']['media']) || empty($data['searchData']['media'])){
            $media = ['facebook', 'google', 'kakao'];
        }else{
            $media = explode("|", $data['searchData']['media']);
        }

        if (in_array('facebook', $media)) {
            $facebookBuilder = $this->facebook->$type($data['searchData']);
            $builders[] = $facebookBuilder;
        }

        if (in_array('google', $media)) {
            $googleBuilder = $this->google->$type($data['searchData']);
            $builders[] = $googleBuilder;
        }

        if (in_array('kakao', $media)) {
            $kakaoBuilder = $this->kakao->$type($data['searchData']);
            $builders[] = $kakaoBuilder;
        }

        $unionBuilder = null;
        foreach ($builders as $builder) {
            if ($unionBuilder) {
                $unionBuilder->union($builder);          
            } else {
                $unionBuilder = $builder;
            }
        }

        if ($unionBuilder) {
            $resultQuery = $this->zenith->newQuery()->fromSubquery($unionBuilder, 'adv');
            if($type == 'getAccounts'){   
                $resultQuery->groupBy('adv.company_id');
                $resultQuery->orderBy('adv.company_name', 'asc');
            }

            if($type == 'getMediaAccounts'){  
                $resultQuery->groupBy('adv.media_account_id');
                $resultQuery->orderBy('adv.media_account_name', 'asc');
            }
            
            if($type == 'getAdsets' || $type == 'getAds') {
                $ids = [];
                if($type == 'getAdsets' && (isset($data['searchData']['data']['campaigns']) && count($data['searchData']['data']['campaigns']))) {
                    $ids = array_map(function($v) { $v=explode('_', $v); return (integer)array_pop($v); }, $data['searchData']['data']['campaigns']);                 
                    $resultQuery->whereIn('campaign_id', $ids);
                } else if($type == 'getAds') {
                    if(isset($data['searchData']['data']['campaigns']) && count($data['searchData']['data']['campaigns'])) {
                        $ids = array_map(function($v) { $v=explode('_', $v); return (integer)array_pop($v); }, $data['searchData']['data']['campaigns']);
                        $resultQuery->whereIn('campaign_id', $ids);
                    }
                    if(isset($data['searchData']['data']['adsets']) && count($data['searchData']['data']['adsets'])) {
                        $ids = array_map(function($v) { $v=explode('_', $v); return (integer)array_pop($v); }, $data['searchData']['data']['adsets']);
                        $resultQuery->whereIn('adset_id', $ids);
                    }
                }
            }
            if($type == 'getCampaigns' || $type == 'getAdsets' || $type == 'getAds'){   
                $resultQuery->groupBy('id');
                $resultQuery->orderBy('create_date', 'desc');
            }
            // echo $resultQuery->getCompiledSelect(); exit;
            $result = $resultQuery->get()->getResultArray();
        } else {
            $result = null;
        }
        // if(!$cacheData)
            // $cache->save($cacheName, $result, 300);
        return $result;
    }

    public function getAccountStat($data)
    {
        if (empty($data['media'])) {
            $data['media'] = 'facebook|google|kakao';
        }
        if (empty($data['sdate'])) {
            $data['sdate'] = date('Y-m-d');
        }
        if (empty($data['edate'])) {
            $data['edate'] = date('Y-m-d');
        }
        $media = explode("|", $data['media']);
        
        $sdate = $data['sdate'];
        $edate = $data['edate'];
        
        $queries = [];
        
        if (in_array('google', $media)) {
            $queries[] = "
                SELECT 
                    e.customerId, e.name, 
                    ROUND(SUM(a.cost) / SUM(a.db_count)) AS cpa, 
                    SUM(a.db_count) AS db_count, 
                    SUM(a.cost) AS cost, 
                    SUM(a.margin) AS margin, 
                    ROUND(SUM(a.margin) / SUM(a.sales) * 100, 2) AS margin_ratio, 
                    SUM(a.sales) AS sales,
                    'google' AS source,
                    ROUND(SUM(a.margin) / SUM(SUM(a.margin)) OVER () * 100, 2) AS total_margin_ratio
                FROM z_adwords.aw_adgroup_report_history AS a
                LEFT JOIN z_adwords.aw_adgroup AS c ON a.adgroup_id = c.id
                LEFT JOIN z_adwords.aw_campaign AS d ON c.campaignId = d.id
                LEFT JOIN z_adwords.aw_ad_account AS e ON d.customerId = e.customerId
                WHERE a.date BETWEEN '{$sdate}' AND '{$edate}' AND e.is_hidden = 0 AND e.is_exposed = 1
                GROUP BY e.customerId, e.name
            ";
        }
        
        if (in_array('facebook', $media)) {
            $queries[] = "
                SELECT 
                    E.ad_account_id AS customerId, 
                    E.name, 
                    ROUND(SUM(A.spend) / SUM(A.db_count)) AS cpa, 
                    SUM(A.db_count) AS db_count, 
                    SUM(A.spend) AS cost, 
                    SUM(A.margin) AS margin, 
                    ROUND(SUM(A.margin) / SUM(A.sales) * 100, 2) AS margin_ratio, 
                    SUM(A.sales) AS sales,
                    'facebook' AS source,
                    ROUND(SUM(A.margin) / SUM(SUM(A.margin)) OVER () * 100, 2) AS total_margin_ratio
                FROM z_facebook.fb_ad_insight_history AS A
                LEFT JOIN z_facebook.fb_ad AS B ON A.ad_id = B.ad_id
                LEFT JOIN z_facebook.fb_adset AS C ON B.adset_id = C.adset_id
                LEFT JOIN z_facebook.fb_campaign AS D ON C.campaign_id = D.campaign_id
                LEFT JOIN z_facebook.fb_ad_account AS E ON D.account_id = E.ad_account_id
                WHERE A.date BETWEEN '{$sdate}' AND '{$edate}' AND E.perm = 1 AND E.status = 1
                GROUP BY E.ad_account_id, E.name
            ";
        }
        
        if (in_array('kakao', $media)) {
            $queries[] = "
                SELECT 
                    E.id AS customerId, 
                    E.name, 
                    ROUND(SUM(A.cost) / SUM(A.db_count)) AS cpa, 
                    SUM(A.db_count) AS db_count, 
                    SUM(A.cost) AS cost, 
                    SUM(A.margin) AS margin, 
                    ROUND(SUM(A.margin) / SUM(A.sales) * 100, 2) AS margin_ratio, 
                    SUM(A.sales) AS sales,
                    'kakao' AS source,
                    ROUND(SUM(A.margin) / SUM(SUM(A.margin)) OVER () * 100, 2) AS total_margin_ratio
                FROM z_moment.mm_creative_report_basic AS A
                LEFT JOIN z_moment.mm_creative AS B ON A.id = B.id
                LEFT JOIN z_moment.mm_adgroup AS C ON B.adgroup_id = C.id
                LEFT JOIN z_moment.mm_campaign AS D ON C.campaign_id = D.id
                LEFT JOIN z_moment.mm_ad_account AS E ON D.ad_account_id = E.id
                WHERE A.date BETWEEN '{$sdate}' AND '{$edate}' AND E.config = 'ON' AND E.is_update = 1
                GROUP BY E.id, E.name
            ";
        }
        
        $combinedSql = implode(" UNION ALL ", $queries);
        $finalQuery = "
            WITH combined_data AS (
                {$combinedSql}
            )
            SELECT 
                source, customerId, name, cpa, db_count, cost, margin, margin_ratio, sales, total_margin_ratio, 
                ROUND(margin / SUM(margin) OVER () * 100, 2) AS source_margin_ratio
            FROM combined_data
            ORDER BY name ASC
        ";
        
        $query = $this->zenith->query($finalQuery);
        return $query->getResultArray();
    }

    public function getMemo($data = null)
    {
        $fbBuilder = $this->zenith->table('z_facebook.fb_ad_account AS faa');
        $fbBuilder->select('am.*, faa.name AS account_name, fc.campaign_name AS campaign_name, fas.adset_name AS adset_name, fa.ad_name AS ad_name');   
        $fbBuilder->join('z_facebook.fb_campaign AS fc', 'faa.ad_account_id = fc.account_id');
        $fbBuilder->join('z_facebook.fb_adset AS fas', 'fc.campaign_id = fas.campaign_id');
        $fbBuilder->join('z_facebook.fb_ad AS fa', 'fas.adset_id = fa.adset_id');
        $fbBuilder->join('zenith.advertisement_memo AS am', "am.media = 'facebook' AND ((fa.ad_id = am.id AND am.type = 'ads') OR (fas.adset_id = am.id AND am.type = 'adsets') OR (fc.campaign_id = am.id AND am.type = 'campaigns'))");

        $ggBuilder = $this->zenith->table('z_adwords.aw_ad_account AS aaa');
        $ggBuilder->select('am.*, aaa.name AS account_name, ac.name AS campaign_name, aag.name AS adset_name, aa.name AS ad_name');
        $ggBuilder->join('z_adwords.aw_campaign ac', 'aaa.customerId = ac.customerId');
        $ggBuilder->join('z_adwords.aw_adgroup aag', 'ac.id = aag.campaignId');
        $ggBuilder->join('z_adwords.aw_ad aa', 'aag.id = aa.adgroupId');
        $ggBuilder->join('zenith.advertisement_memo AS am', "am.media = 'google' AND ((aa.id = am.id AND am.type = 'ads') OR (aag.id = am.id AND am.type = 'adsets') OR (ac.id = am.id AND am.type = 'campaigns'))");

        $kkBuilder = $this->zenith->table('z_moment.mm_ad_account AS maa');
        $kkBuilder->select('am.*, maa.name AS account_name, mc.name AS campaign_name, mag.name AS adset_name, mct.name AS ad_name');
        $kkBuilder->join('z_moment.mm_campaign mc', 'maa.id = mc.ad_account_id');
        $kkBuilder->join('z_moment.mm_adgroup mag', 'mc.id = mag.campaign_id');
        $kkBuilder->join('z_moment.mm_creative mct', 'mag.id = mct.adgroup_id');
        $kkBuilder->join('zenith.advertisement_memo AS am', "am.media = 'kakao' AND ((mct.id = am.id AND am.type = 'ads') OR (mag.id = am.id AND am.type = 'adsets') OR (mc.id = am.id AND am.type = 'campaigns'))");

        $fbBuilder->union($ggBuilder)->union($kkBuilder);
        $resultQuery = $this->zenith->newQuery()->fromSubquery($fbBuilder, 'memo');
        if(!empty($data['id'])){
            $resultQuery->where('memo.id', $data['id']);
            $resultQuery->where('memo.type', $data['type']);
        } else {
            $resultQuery->where("(memo.datetime >= DATE_SUB(NOW(), INTERVAL 3 DAY) OR (memo.datetime <= DATE_SUB(NOW(), INTERVAL 3 DAY) AND memo.is_done = 0))");
        }
        $resultQuery->groupBy('memo.seq');
        $resultQuery->orderBy('memo.datetime', 'desc');
        $result = $resultQuery->get()->getResultArray();
        return $result;
    }

    public function addMemo($data)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('advertisement_memo');
        $builder->insert($data);
        $result = $this->zenith->transComplete();
        return $result;
    }

    public function checkMemo($data)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('advertisement_memo');
        $builder->set('is_done', $data['is_done']);
        $builder->set('done_nickname', $data['done_nickname']);
        $builder->where('seq', $data['seq']);
        $builder->update();
        $result = $this->zenith->transComplete();
        return $result;
    }

    public function getAdvs($data)
    {
        $kakaoNumbers = array();
        $googleNumbers = array();
        $facebookNumbers = array();

        foreach ($data['check'] as $value) {
            $parts = explode('_', $value);
            $id = $parts[1];
            
            switch ($parts[0]) {
                case 'kakao':
                    $kakaoNumbers[] = $id;
                    break;
                case 'google':
                    $googleNumbers[] = $id;
                    break;
                case 'facebook':
                    $facebookNumbers[] = $id;
                    break;
                default:
                    break;
            }
        }

        $data['facebook'] = $facebookNumbers;
        $data['google'] = $googleNumbers;
        $data['kakao'] = $kakaoNumbers; 

        switch ($data['type']) {
            case 'campaigns':
                if(!empty($data['facebook'])){
                    $facebookBuilder = $this->facebook->getCampaignById($data['facebook']);
                    $builders[] = $facebookBuilder;
                }

                if(!empty($data['google'])){
                    $googleBuilder = $this->google->getCampaignById($data['google']);
                    $builders[] = $googleBuilder;
                }

                if(!empty($data['kakao'])){
                    $kakaoBuilder = $this->kakao->getCampaignById($data['kakao']);
                    $builders[] = $kakaoBuilder;
                }
                break;
            case 'adsets':
                if(!empty($data['facebook'])){
                    $facebookBuilder = $this->facebook->getAdgroupById($data['facebook']);
                    $builders[] = $facebookBuilder;
                }

                if(!empty($data['google'])){
                    $googleBuilder = $this->google->getAdgroupById($data['google']);
                    $builders[] = $googleBuilder;
                }

                if(!empty($data['kakao'])){
                    $kakaoBuilder = $this->kakao->getAdgroupById($data['kakao']);
                    $builders[] = $kakaoBuilder;
                }
                break;
            
            case 'ads':
                if(!empty($data['facebook'])){
                    $facebookBuilder = $this->facebook->getAdById($data['facebook']);
                    $builders[] = $facebookBuilder;
                }

                if(!empty($data['google'])){
                    $googleBuilder = $this->google->getAdById($data['google']);
                    $builders[] = $googleBuilder;
                }

                if(!empty($data['kakao'])){
                    $kakaoBuilder = $this->kakao->getAdById($data['kakao']);
                    $builders[] = $kakaoBuilder;
                }
                break;
            
            default:
                break;
        }

        $unionBuilder = null;
        foreach ($builders as $builder) {
            if ($unionBuilder) {
                $unionBuilder->union($builder);
                
            } else {
                $unionBuilder = $builder;
            }
        }
        
        $result = $unionBuilder->get()->getResultArray();
        return $result;
    }

    public function getChangeLogs($id = null, $dates = null)
    {
        $builder = $this->zenith->table('adv_change_logs');
        $builder->select('adv_change_logs.*, IFNULL(users.nickname, adv_change_logs.nickname) as nickname');
        $builder->join('users', 'adv_change_logs.nickname = users.username', 'left');
        
        if (!empty($id)) {
            $builder->where('adv_change_logs.id', $id);
        } 

        if (!empty($dates)) {
            $builder->where('adv_change_logs.datetime >=', date('Y-m-d', strtotime($dates['sdate'])));
            $builder->where('adv_change_logs.datetime <',  date('Y-m-d', strtotime($dates['edate']. '+1 day')));
        }
        $builder->orderBy('adv_change_logs.datetime', 'DESC');

        $changeLogs = $builder->get()->getResultArray();
        
        foreach ($changeLogs as &$log) {
            $media = $log['media'];
            $type = $log['type'];
            $id = $log['id'];
            $name = '';
            $accountName = '';

            switch ($media) {
                case 'facebook':
                    switch ($type) {
                        case 'campaigns':
                        case 'campaign':
                            $name = $this->zenith->table('z_facebook.fb_campaign')->select('campaign_name')->where('campaign_id', $id)->get()->getRow()->campaign_name;
                            $accountName = $this->zenith->table('z_facebook.fb_campaign')
                                              ->select('z_facebook.fb_ad_account.name')
                                              ->join('z_facebook.fb_ad_account', 'z_facebook.fb_campaign.account_id = z_facebook.fb_ad_account.ad_account_id')
                                              ->where('z_facebook.fb_campaign.campaign_id', $id)
                                              ->get()->getRow()->name;
                            break;
                        case 'adsets':
                        case 'adset':
                            $name = $this->zenith->table('z_facebook.fb_adset')->select('adset_name')->where('adset_id', $id)->get()->getRow()->adset_name;
                            $accountName = $this->zenith->table('z_facebook.fb_adset')
                                              ->select('z_facebook.fb_ad_account.name')
                                              ->join('z_facebook.fb_campaign', 'z_facebook.fb_adset.campaign_id = z_facebook.fb_campaign.campaign_id')
                                              ->join('z_facebook.fb_ad_account', 'z_facebook.fb_campaign.account_id = z_facebook.fb_ad_account.ad_account_id')
                                              ->where('z_facebook.fb_adset.adset_id', $id)
                                              ->get()->getRow()->name;
                            break;
                        case 'ads':
                        case 'ad':
                            $name = $this->zenith->table('z_facebook.fb_ad')->select('ad_name')->where('ad_id', $id)->get()->getRow()->ad_name;
                            $accountName = $this->zenith->table('z_facebook.fb_ad')
                                              ->select('z_facebook.fb_ad_account.name')
                                              ->join('z_facebook.fb_adset', 'z_facebook.fb_ad.adset_id = z_facebook.fb_adset.adset_id')
                                              ->join('z_facebook.fb_campaign', 'z_facebook.fb_adset.campaign_id = z_facebook.fb_campaign.campaign_id')
                                              ->join('z_facebook.fb_ad_account', 'z_facebook.fb_campaign.account_id = z_facebook.fb_ad_account.ad_account_id')
                                              ->where('z_facebook.fb_ad.ad_id', $id)
                                              ->get()->getRow()->name;
                            break;
                    }
                    break;
                case 'google':
                    switch ($type) {
                        case 'campaigns':
                        case 'campaign':
                            $name = $this->zenith->table('z_adwords.aw_campaign')->select('name')->where('id', $id)->get()->getRow()->name;
                            $accountName = $this->zenith->table('z_adwords.aw_campaign')
                                              ->select('z_adwords.aw_ad_account.name')
                                              ->join('z_adwords.aw_ad_account', 'z_adwords.aw_campaign.customerId = z_adwords.aw_ad_account.customerId')
                                              ->where('z_adwords.aw_campaign.id', $id)
                                              ->get()->getRow()->name;
                            break;
                        case 'adsets':
                        case 'adset':
                            $name = $this->zenith->table('z_adwords.aw_adgroup')->select('name')->where('id', $id)->get()->getRow()->name;
                            $accountName = $this->zenith->table('z_adwords.aw_adgroup')
                                              ->select('z_adwords.aw_ad_account.name')
                                              ->join('z_adwords.aw_campaign', 'z_adwords.aw_adgroup.campaignId = z_adwords.aw_campaign.id')
                                              ->join('z_adwords.aw_ad_account', 'z_adwords.aw_campaign.customerId = z_adwords.aw_ad_account.customerId')
                                              ->where('z_adwords.aw_adgroup.id', $id)
                                              ->get()->getRow()->name;
                            break;
                        case 'ads':
                        case 'ad':
                            $name = $this->zenith->table('z_adwords.aw_ad')->select('name')->where('id', $id)->get()->getRow()->name;
                            $accountName = $this->zenith->table('z_adwords.aw_ad')
                                              ->select('z_adwords.aw_ad_account.name')
                                              ->join('z_adwords.aw_adgroup', 'z_adwords.aw_ad.adgroupId = z_adwords.aw_adgroup.id')
                                              ->join('z_adwords.aw_campaign', 'z_adwords.aw_adgroup.campaignId = z_adwords.aw_campaign.id')
                                              ->join('z_adwords.aw_ad_account', 'z_adwords.aw_campaign.customerId = z_adwords.aw_ad_account.customerId')
                                              ->where('z_adwords.aw_ad.id', $id)
                                              ->get()->getRow()->name;
                            break;
                    }
                    break;
                case 'kakao':
                    switch ($type) {
                        case 'campaigns':
                        case 'campaign':
                            $name = $this->zenith->table('z_moment.mm_campaign')->select('name')->where('id', $id)->get()->getRow()->name;
                            $accountName = $this->zenith->table('z_moment.mm_campaign')
                                              ->select('z_moment.mm_ad_account.name')
                                              ->join('z_moment.mm_ad_account', 'z_moment.mm_campaign.ad_account_id = z_moment.mm_ad_account.id')
                                              ->where('z_moment.mm_campaign.id', $id)
                                              ->get()->getRow()->name;
                            break;
                        case 'adsets':
                        case 'adset':
                            $name = $this->zenith->table('z_moment.mm_adgroup')->select('name')->where('id', $id)->get()->getRow()->name;
                            $accountName = $this->zenith->table('z_moment.mm_adgroup')
                                              ->select('z_moment.mm_ad_account.name')
                                              ->join('z_moment.mm_campaign', 'z_moment.mm_adgroup.campaign_id = z_moment.mm_campaign.id')
                                              ->join('z_moment.mm_ad_account', 'z_moment.mm_campaign.ad_account_id = z_moment.mm_ad_account.id')
                                              ->where('z_moment.mm_adgroup.id', $id)
                                              ->get()->getRow()->name;
                            break;
                        case 'ads':
                        case 'ad':
                            $name = $this->zenith->table('z_moment.mm_creative')->select('name')->where('id', $id)->get()->getRow()->name;
                            $accountName = $this->zenith->table('z_moment.mm_creative')
                                              ->select('z_moment.mm_ad_account.name')
                                              ->join('z_moment.mm_adgroup', 'z_moment.mm_creative.adgroup_id = z_moment.mm_adgroup.id')
                                              ->join('z_moment.mm_campaign', 'z_moment.mm_adgroup.campaign_id = z_moment.mm_campaign.id')
                                              ->join('z_moment.mm_ad_account', 'z_moment.mm_campaign.ad_account_id = z_moment.mm_ad_account.id')
                                              ->where('z_moment.mm_creative.id', $id)
                                              ->get()->getRow()->name;
                            break;
                    }
                    break;
            }
            $log['item_name'] = $name;
            $log['account_name'] = $accountName;
        }
        return $changeLogs;
    }
}
