<?php
namespace App\Controllers\User;

use App\Models\Api\CompanyModel;
use App\Models\Api\IdentityModel;
use CodeIgniter\API\ResponseTrait;
use App\Models\Api\UserModel;
use CodeIgniter\Shield\Authentication\Passwords;
use CodeIgniter\Shield\Entities\User;

class UserController extends \CodeIgniter\Controller 
{
    use ResponseTrait;

    protected $user;
    protected $company;
    protected $data;
    protected $logginedUser;
    protected $validation;

    public function __construct() {
        $this->user = model(UserModel::class);
        $this->company = model(CompanyModel::class);
        $this->logginedUser = auth()->user();
    }
    
    public function index()
    {
        return view('users/user');
    }

    public function myPage()
    {
        return view('users/mypage', ['user'=>$this->logginedUser]);
    }

    public function myPageUpdate()
    {
        $data = $this->request->getPost();

        $credentials = [
            'email'    => $this->logginedUser->getEmail(),
            'password' => $data['old_password'],
        ];
        
        $validCreds = auth()->check($credentials);
        if (! $validCreds->isOK()) {
            return redirect()->back()->with('error', '기존 비밀번호가 일치하지 않습니다.');
        }

        $rules = [
            'password' => [
                'label'  => 'Auth.password',
                'rules'  => 'required|' . Passwords::getMaxLengthRule() . '|strong_password[]',
                'errors' => [
                    'max_byte' => 'Auth.errorPasswordTooLongBytes',
                ],
            ],
            'password_confirm' => [
                'label' => 'Auth.passwordConfirm',
                'rules' => 'required|matches[password]',
            ],
        ];

        if (! $this->validateData($data, $rules, [], config('Auth')->DBGroup)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $cuser = auth()->user();
        $cuser->fill($data);
        $this->user->save($cuser);
        $this->setPasswordChangedAt(true);
        return redirect()->back()->with('message', '비밀번호가 변경되었습니다.');
    }

    public function getUsers(){
        if (/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'get') {
            $param = $this->request->getGet();
            $result = $this->user->getUsers($param);
            foreach ($result['data'] as &$row) {
                $groups = explode(",", $row['groups']);
                $groupsMapping = [
                    'superadmin' => '최고관리자',
                    'admin' => '관리자',
                    'developer' => '개발자',
                    'user' => '직원',
                    'agency' => '광고대행사',
                    'advertiser' => '광고주',
                    'guest' => '게스트',
                ];
                
                foreach ($groups as &$group) {
                    $group = $groupsMapping[$group] ?? '';
                }

                usort($groups, function ($a, $b) {
                    return strcmp($a, $b);
                });

                $row['groups'] = implode(',', $groups);

                if($row['active']){
                    $row['active'] = '활성';
                }else{
                    $row['active'] = '비활성';
                }
            }; 

            $result = [
                'data' => $result['data'],
                'recordsTotal' => $result['allCount'],
                'recordsFiltered' => $result['allCount'],
                'draw' => intval($param['draw']),
            ];

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getUser(){
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get') {
            $param = $this->request->getGet();
            $result = $this->user->getUser($param);
            $result['groups'] = explode(",", $result['groups']);
            $result['permissions'] = explode(",", $result['permissions']);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getSearchUsers(){
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get') {
            $param = $this->request->getGet();
            $result = $this->user->getSearchUsers($param);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getBelongUsers(){
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get') {
            $param = $this->request->getGet();
            $result = $this->user->getBelongUsers($param['company_id']);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
    
    public function setUser()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put') {
            $param = $this->request->getRawInput();
            if (!empty($param)) {
                $company = $this->company->getCompanyByName($param['company_name']);
                if(!empty($param['company_name'])){
                    if(empty($company)) {
                        return $this->failValidationErrors(["company" => "존재하지 않는 광고주/광고대행사입니다."]);
                    }else{
                        $param['company_id'] = $company['id'];
                    }
                }
                if(empty($param['group']) || !$this->checkPermission($this->logginedUser, $param['user_id'], $param['group'])){
                    return $this->failValidationErrors(["groups" => "수정 권한이 없습니다."]);
                }
                $result = $this->user->setUser($param);
            }else{
                return $this->fail("잘못된 요청");
            }

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function setBelongUser()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put') {
            $param = $this->request->getRawInput();
            if (!empty($param)) {
                $belongUser = $this->user->getBelongUser($param);
                if(!empty($belongUser)){
                    return $this->failValidationErrors(["username" => "이미 소속되어 있습니다."]);
                }
                $result = $this->user->setBelongUser($param);
            }else{
                return $this->fail("잘못된 요청");
            }
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function exceptBelongUser()
    {
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'delete') {
            $param = $this->request->getRawInput();
            if (!empty($param)) {
                $result = $this->user->exceptBelongUser($param);
            }else{
                return $this->fail("잘못된 요청");
            }
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function setPasswordChangedAtAjax()
    {
        if (/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'get') {
            $result = $this->setPasswordChangedAt(false);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function setPasswordChangedAt($type = false)
    {
        $user = $this->logginedUser;
        
        if($type === true){
            $date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . "+90 days"));
        }else{
            $date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . "-76 days"));
        }

        $identityModel = model(IdentityModel::class);
        $result = $identityModel->setPasswordChangedAt($user->id, $date);
        
        return $result;
    }

    protected function checkPermission($logginedUser, $updateId, $postGroups)
    {
        $updateUser = $this->user->find($updateId);
        $logginedUserGroups = $logginedUser->getGroups();
        $updateUserGroups = $updateUser->getGroups();

        // 그룹 순서 정의
        $groupOrder = array_keys(config('AuthGroups')->groups);
        foreach ($postGroups as $postGroup) {
            foreach ($logginedUserGroups as $logginedUserGroup) {
                $currentGroupIndex = array_search($logginedUserGroup, $groupOrder);
                $targetGroupIndex = array_search($postGroup, $groupOrder);
                // superadmin, admin, developer, user 그룹만 수정 가능
                $editableGroups = ['superadmin', 'admin', 'developer', 'user'];
                if (in_array($logginedUserGroup, $editableGroups)) {
                    // 자신의 그룹과 상위 그룹은 수정 불가
                    if ($currentGroupIndex >= $targetGroupIndex) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }

        // 수정할 대상 사용자의 그룹이 자신보다 높은지 체크
        foreach ($updateUserGroups as $updateUserGroup) {
            foreach ($logginedUserGroups as $logginedUserGroup) {
                $currentGroupIndex = array_search($logginedUserGroup, $groupOrder);
                $updateGroupIndex = array_search($updateUserGroup, $groupOrder);

                if ($currentGroupIndex >= $updateGroupIndex) {
                    return false;
                }
            }
        }

        return true;
    }

}