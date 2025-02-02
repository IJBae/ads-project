<?php

namespace App\Models\Api;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $zenith, $facebook, $google, $kakao;
    protected $allowedFields = [
        'type',
        'name',
        'nickname',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $validationRules      = [
        'name' => 'required',
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => '이름은 필수 입력사항입니다.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
        $this->facebook = \Config\Database::connect('facebook');
        $this->google = \Config\Database::connect('google');
        $this->kakao = \Config\Database::connect('kakao');
    }

    public function getCompanies($data)
    {
        $srch = $data['searchData'];
        $builder = $this->zenith->table('companies AS c');
        $builder->select('c.*, ci.company_parent_id as p_id, parent_c.name as p_name');
        $builder->join('companies_idx AS ci', 'c.id = ci.company_id', 'left');
        $builder->join('companies AS parent_c', 'parent_c.id = ci.company_parent_id', 'left');
        $builder->where('c.status !=', 0);
        if(isset($srch['sdate']) && isset($srch['edate'])){
            $builder->where('c.created_at >=', $srch['sdate']);
            $builder->where('c.created_at <=', date('Y-m-d', strtotime($srch['edate'].' + 1day')));
        }

        if(!empty($srch['stx'])){
            $builder->groupStart();
            $builder->like('c.type', $srch['stx']);
            $builder->orLike('c.name', $srch['stx']);
            $builder->orLike('c.nickname', $srch['stx']);
            $builder->orLike('parent_c.name', $srch['stx']);
            $builder->groupEnd();
        }
        $builder->groupBy('c.id');
        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $builder;

        // limit 적용한 쿼리
        
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $orderBy[] = "id DESC";
        $builder->orderBy(implode(",", $orderBy),'',true);

        if($data['length'] > 0){
            $builder->limit($data['length'], $data['start']);
        }
    
        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();

        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getCompany($id)
    {
        $builder = $this->zenith->table('companies AS c');
        $builder->select('c.*, GROUP_CONCAT(DISTINCT u.username) as username, ci.company_parent_id as p_id, parent_c.name as p_name');
        $builder->join('companies_users as cu', 'c.id = cu.company_id', 'left');
        $builder->join('users as u', 'cu.user_id = u.id', 'left');
        $builder->join('companies_idx as ci', 'c.id = ci.company_id', 'left');
        $builder->join('companies as parent_c', 'ci.company_parent_id = parent_c.id', 'left');
        $builder->where('c.id', $id);
        $builder->where('c.status !=', 0);
        $result = $builder->get()->getRowArray();

        return $result;
    }

    public function getSearchCompanies($stx = NULL)
    {
        $builder = $this->zenith->table('companies');
        $builder->select('id, name');
        $builder->where('status !=', 0);
        if(!empty($stx)){
            $builder->like('name', $stx);
        }

        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getSearchAgencies($stx = NULL)
    {
        $builder = $this->zenith->table('companies');
        $builder->select('id, name');
        $builder->where('type', '광고대행사');
        $builder->where('status !=', 0);
        if(!empty($stx)){
            $builder->like('name', $stx);
        }

        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getAgencyByName($p_name)
    {
        if(empty($p_name)){
            return false;
        }
        $builder = $this->zenith->table('companies');
        $builder->select('id, name');
        $builder->where('type', '광고대행사');
        $builder->where('status !=', 0);
        $builder->where('name', $p_name);
    
        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function getCompanyByName($p_name)
    {
        $builder = $this->zenith->table('companies');
        $builder->select('id, name');
        $builder->where('status !=', 0);
        if(!empty($p_name)){
            $builder->where('name', $p_name);
        }

        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function getCompaniesByName($names)
    {
        $builder = $this->zenith->table('companies');
        $builder->select('id');
        $builder->where('status !=', 0);
        $builder->whereIn('name', $names);

        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function createCompany($data, $agency)
    {
        $this->zenith->transStart();
        $builder_1 = $this->zenith->table('companies');
        $builder_1->set('type', $data['type']);
        $builder_1->set('name', $data['name']);
        $builder_1->set('nickname', auth()->user()->nickname ?? '');
        $builder_1->set('status', 1);
        $result_1 = $builder_1->insert();
        if(!empty($agency)){
            $insertId = $this->zenith->insertID();
            $builder_2 = $this->zenith->table('companies_idx');
            $newRecord = [
                'company_id' => $insertId,
                'company_parent_id' => $agency['id']
            ];
            $builder_2->insert($newRecord);
        }
        
        $result = $this->zenith->transComplete();

        // sql error 구문이 찍히지 않아서 select
        if($result===false){
            $builder = $this->zenith->table('companies');
            $builder->select('name');
            $builder->set('name', $data['name']);
            $cnt = $builder->get()->getNumRows();
            if($cnt>0) $result = "duplicate";
        }

        return $result;
    }

    public function setCompany($data, $agency)
    {
        $this->zenith->transStart();
        $builder_1 = $this->zenith->table('companies');
        $builder_1->set('name', $data['name']);
        $builder_1->where('id', $data['id']);
        $builder_1->update();

        $builder_2 = $this->zenith->table('companies_idx');
        $builder_2->where('company_id', $data['id']);
        $result = $builder_2->get()->getResult();

        if(!empty($agency)){
            if (empty($result)) {
                $newRecord = [
                    'company_id' => $data['id'],
                    'company_parent_id' => $agency['id']
                ];
                $result_2 = $builder_2->insert($newRecord);
            } else {
                $builder_2->set('company_parent_id', $agency['id']);
                $builder_2->where('company_id', $data['id']);
                $result_2 = $builder_2->update();
            }
        }
        $result = $this->zenith->transComplete();

        return $result;
    }

    public function deleteCompany($data)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('companies');
        $builder->where('id', $data['id']);
        $builder->delete();

        $builder = $this->zenith->table('company_adaccounts');
        $builder->where('company_id', $data['id']);
        $builder->delete();
        $result = $this->zenith->transComplete();

        return $result;
    }

    public function getSearchAdAccounts($stx = NULL)
    {
		if(!$this->facebook->tableExists('fb_ad_account') || !$this->google->tableExists('aw_ad_account') || !$this->kakao->tableExists('mm_ad_account')){
			return false;
		}

        $facebookBuilder = $this->facebook->table('fb_ad_account');
        $kakaoBuilder = $this->kakao->table('mm_ad_account');
        $googleBuilder = $this->google->table('aw_ad_account');

        $facebookBuilder->select('"facebook" AS media, name, ad_account_id AS account_id, status AS status');
        $facebookBuilder->like('name', $stx);
        $facebookResult = $facebookBuilder->get()->getResultArray();

        $kakaoBuilder->select('"kakao" AS media, name, id AS account_id, config AS status');
        $kakaoBuilder->like('name', $stx);
        $kakaoResult = $kakaoBuilder->get()->getResultArray();

        $googleBuilder->select('"google" AS media, name, , customerId AS account_id, status AS status');
        $googleBuilder->like('name', $stx);
        $googleResult = $googleBuilder->get()->getResultArray();

        $mergedResults = array_merge($facebookResult, $kakaoResult, $googleResult);

        return $mergedResults;
    }

    public function getCompanyAdAccounts($data)
    {
		if(!$this->facebook->tableExists('fb_ad_account') || !$this->google->tableExists('aw_ad_account') || !$this->kakao->tableExists('mm_ad_account')){
			return false;
		}

        $zenithBuilder = $this->zenith->table('company_adaccounts AS ca');

        $facebookBuilder = clone $zenithBuilder;
        $facebookBuilder->join('z_facebook.fb_ad_account AS fa', 'ca.ad_account_id = fa.ad_account_id');
        $facebookBuilder->select('ca.ad_account_id AS accountId, "facebook" AS media, fa.name AS name, fa.status AS status');
        $facebookBuilder->where('ca.company_id', $data['company_id']);
        $facebookResult = $facebookBuilder->get()->getResultArray();

        $kakaoBuilder = clone $zenithBuilder;
        $kakaoBuilder->join('z_moment.mm_ad_account AS ma', 'ca.ad_account_id = ma.id');
        $kakaoBuilder->select('ca.ad_account_id AS accountId, "kakao" AS media, ma.name AS name, ma.config AS status');
        $kakaoBuilder->where('ca.company_id', $data['company_id']);
        $kakaoResult = $kakaoBuilder->get()->getResultArray();

        $googleBuilder = clone $zenithBuilder;
        $googleBuilder->join('z_adwords.aw_ad_account AS awa', 'ca.ad_account_id = awa.customerId');
        $googleBuilder->select('ca.ad_account_id AS accountId, "google" AS media, awa.name AS name, awa.status AS status');
        $googleBuilder->where('ca.company_id', $data['company_id']);
        $googleResult = $googleBuilder->get()->getResultArray();

        $mergedResults = array_merge($facebookResult, $kakaoResult, $googleResult);
        return $mergedResults;
    }

    public function getCompanyAdAccount($data)
    {
        $builder = $this->zenith->table('company_adaccounts');
        $builder->where('company_id', $data['company_id']);
        $builder->where('ad_account_id', $data['ad_account_id']);
        $builder->where('media', $data['media']);
        $result = $builder->get()->getResult();

        return $result;
    }

    public function setCompanyAdAccount($data)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('company_adaccounts');
        $newRecord = [
            'company_id' => $data['company_id'],
            'ad_account_id' => $data['ad_account_id'],
            'media' => $data['media']
        ];
        $builder->insert($newRecord);
        $result = $this->zenith->transComplete();

        return $result;
    }

    public function exceptCompanyAdAccount($data)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('company_adaccounts');
        $builder->where('company_id', $data['company_id']);
        $builder->where('ad_account_id', $data['ad_account_id']);
        $builder->where('media', $data['media']);
        $builder->delete();
        $result = $this->zenith->transComplete();

        return $result;
    }
}
