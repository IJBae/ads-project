<?php

namespace App\Models\Advertiser;

use App\Libraries\Calc;
use CodeIgniter\Model;

class AdvGoogleManagerModel extends Model
{
	public function getManageAccounts() {
        $builder = $this->google->table('aw_ad_account');  
		$builder->select('*');
        $builder->where('canManageClients', 1);
        $builder->where('status', 'ENABLED');
        //$builder->where('is_exposed', 1);
        $builder->orderBy('name', 'asc');
        $builder->orderBy('create_time', 'asc');

        $result = $builder->get()->getResultArray();

		return $result;
	}

    public function getAccounts($data)
	{
		$builder = $this->db->table('z_adwords.aw_ad_report A');
        $builder->select('
		G.id AS company_id,
		G.name AS company_name
		');
		$builder->join('z_adwords.aw_ad B', 'A.ad_id = B.id');
        $builder->join('z_adwords.aw_adgroup C', 'B.adgroupId = C.id');
        $builder->join('z_adwords.aw_campaign D', 'C.campaignId = D.id');
        $builder->join('z_adwords.aw_ad_account E', 'D.customerId = E.customerId');
		$builder->join('zenith.company_adaccounts F', 'E.customerId = F.ad_account_id AND F.media = "google"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');
        $builder->where('D.status !=', 'NODATA');

        if(!empty($data['sdate']) && !empty($data['edate'])){
            $builder->where('A.date >=', $data['sdate']);
            $builder->where('A.date <', date('Y-m-d', strtotime($data['edate'].' + 1day')));
        }
		
		if(!empty($data['resta'])){
			$builder->whereIn('G.id', explode("|",$data['resta']));
        }
		
		if(!empty($data['googleCheck'])){
			switch ($data['type']) {
				case 'campaigns':
					$builder->whereIn('D.id', $data['googleCheck']);
					break;
				case 'adsets':
					$builder->whereIn('C.id', $data['googleCheck']);
					break;
				case 'ads':
					$builder->whereIn('B.id', $data['googleCheck']);
					break;
				default:
					break;
			}
        }

		return $builder;
	}

	public function getMediaAccounts($data)
	{
		$builder = $this->db->table('z_adwords.aw_ad_report A');
        $builder->select('
			"google" AS media,
			E.name AS media_account_name,
			E.customerId AS media_account_id,
			E.status AS status,
			"" AS isAdminStop,
			E.is_exposed AS is_exposed,
			E.db_count AS db_count,
			SUM(A.db_count) AS db_sum,
			COUNT(DISTINCT A.date) AS date_count
		');
		$builder->join('z_adwords.aw_ad B', 'A.ad_id = B.id');
        $builder->join('z_adwords.aw_adgroup C', 'B.adgroupId = C.id');
        $builder->join('z_adwords.aw_campaign D', 'C.campaignId = D.id');
        $builder->join('z_adwords.aw_ad_account E', 'D.customerId = E.customerId');
		$builder->join('zenith.company_adaccounts F', 'E.customerId = F.ad_account_id AND F.media = "google"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');
        $builder->where('D.status !=', 'NODATA');

        if(!empty($data['sdate']) && !empty($data['edate'])){
            $builder->where('A.date >=', $data['sdate']);
            $builder->where('A.date <', date('Y-m-d', strtotime($data['edate'].' + 1day')));
        }

		if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }
		
		if(!empty($data['resta'])){
			$builder->whereIn('G.id', explode("|",$data['resta']));
        }
		
		if(!empty($data['googleCheck'])){
			switch ($data['type']) {
				case 'campaigns':
					$builder->whereIn('D.id', $data['googleCheck']);
					break;
				case 'adsets':
					$builder->whereIn('C.id', $data['googleCheck']);
					break;
				case 'ads':
					$builder->whereIn('B.id', $data['googleCheck']);
					break;
				default:
					break;
			}
        }
		$builder->groupBy('E.customerId');

		return $builder;
	}

	public function getOnlyAdAccount($data)
	{
		$builder = $this->db->table('z_adwords.aw_ad_account');
        $builder->select('
			"google" AS media,
			name AS media_account_name,
			customerId AS media_account_id,
			is_exposed AS is_exposed,
			canManageClients AS canManageClients,
			db_count AS db_count
		');
		$builder->where('is_hidden', 0);
		if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('name', $data['stx']);
            $builder->groupEnd();
        }
		
        return $builder;
	}

	public function getCampaigns($data)
	{
		$subQuery = $this->db->table('z_adwords.aw_ad_report A');
		$subQuery->select('
			D.customerId as customerId,
			D.id AS id, 
			D.name AS name, 
			D.status AS status, 
			D.amount AS budget, 
			D.cpaBidAmount AS bidamount, 
			D.create_time AS create_date,
			C.biddingStrategyType AS biddingStrategyType,
			SUM(A.impressions) AS impressions, 
			SUM(A.clicks) AS click, 
			SUM(A.cost) AS spend, 
			SUM(A.sales) AS sales, 
			SUM(A.db_count) AS unique_total, 
			SUM(A.margin) AS margin
		');
		$subQuery->join('z_adwords.aw_ad B', 'A.ad_id = B.id');
		$subQuery->join('z_adwords.aw_adgroup C', 'B.adgroupId = C.id');
		$subQuery->join('z_adwords.aw_campaign D', 'C.campaignId = D.id');
		if(!empty($data['sdate']) && !empty($data['edate'])){
			$subQuery->where('A.date >=', $data['sdate']);
			$subQuery->where('A.date <', date('Y-m-d', strtotime($data['edate'].' + 1day')));
		}
		$subQuery->groupBy('D.id');
	
		$metricsSubQuery = $this->db->table('z_adwords.aw_adgroup_report_history A');
		$metricsSubQuery->select('
			D.id as campaignId,
			A.date,
			A.hour,
			SUM(A.impressions) AS impressions,
			SUM(A.clicks) AS clicks,
			SUM(A.cost) AS cost,
			SUM(A.sales) AS sales,
			SUM(A.db_count) AS db_count,
			SUM(A.margin) AS margin
		');
		$metricsSubQuery->join('z_adwords.aw_adgroup C', 'A.adgroup_id = C.id');
		$metricsSubQuery->join('z_adwords.aw_campaign D', 'C.campaignId = D.id');
		if(!empty($data['sdate']) && !empty($data['edate'])){
			$metricsSubQuery->where('A.date >=', $data['sdate']);
			$metricsSubQuery->where('A.date <', date('Y-m-d', strtotime($data['edate'].' + 1day')));
		}
		$metricsSubQuery->groupBy(['D.id', 'A.date', 'A.hour']);
	
		$metricsSubQuerySql = $metricsSubQuery->getCompiledSelect();
	
		$finalMetricsSubQuery = $this->db->table("($metricsSubQuerySql) AS metrics");
		$finalMetricsSubQuery->select('
			metrics.campaignId,
			JSON_OBJECTAGG(
				CONCAT(metrics.date, "_", metrics.hour),
				JSON_OBJECT(
					"impressions", metrics.impressions,
					"click", metrics.clicks,
					"spend", metrics.cost,
					"sales", metrics.sales,
					"unique_total", metrics.db_count,
					"cpa", IFNULL(ROUND(metrics.cost/metrics.db_count, 1), 0),
					"margin", metrics.margin, 
					"margin_ratio", IFNULL(ROUND((metrics.margin/metrics.sales)*100, 1), 0)
				)
			) AS metrics
		');
		$finalMetricsSubQuery->groupBy('metrics.campaignId');
	
		$finalMetricsSubQuerySql = $finalMetricsSubQuery->getCompiledSelect();
	
		$builder = $this->db->newQuery()->fromSubquery($subQuery, 'sub');
		$builder->select('
			G.id AS company_id, 
			G.name AS company_name, 
			E.customerId as customerId,
			"google" AS media, 
			sub.id AS id, 
			sub.name AS name, 
			COUNT(DISTINCT memo.seq) AS memo_cnt,
			sub.status,
			sub.budget,
			sub.bidamount,
			"" AS bidamount_type,
			sub.biddingStrategyType AS biddingStrategyType,
			sub.impressions, 
			sub.click, 
			sub.spend, 
			sub.sales, 
			sub.unique_total, 
			sub.margin,
			metrics.metrics AS metrics,
			sub.create_date
		');
		$builder->join('z_adwords.aw_ad_account E', 'sub.customerId = E.customerId');
		$builder->join('zenith.company_adaccounts F', 'E.customerId = F.ad_account_id AND F.media = "google"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');
		$builder->join('zenith.advertisement_memo memo', 'sub.id = memo.id AND memo.media = "google" AND memo.type = "campaigns" AND is_done <> 1', 'left');
		$builder->join("($finalMetricsSubQuerySql) metrics", 'metrics.campaignId = sub.id', 'left');
		$builder->where('sub.status !=', 'NODATA');
		
		if(!empty($data['business'])){
			$builder->whereIn('E.manageCustomer', explode("|",$data['business']));
		}
	
		if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
		}
	
		if(!empty($data['account'])){
			$builder->whereIn('E.customerId', explode("|",$data['account']));
		}
	
		if(!empty($data['resta'])){
			$builder->whereIn('G.id', explode("|",$data['resta']));
		}
	
		if(!empty($data['stx'])){
			$builder->groupStart();
			$builder->like('sub.name', $data['stx']);
			$builder->groupEnd();
		}
	
		$builder->groupBy('F.company_id');
		$builder->groupBy('sub.id');
		$builder->orderBy('sub.name', 'asc');
	
		return $builder;
	}

    public function getAdsets($data)
	{
		$subQuery = $this->db->table('z_adwords.aw_ad_report A');
        $subQuery->select('
		C.campaignId as campaign_id,
		C.id AS id, 
		C.name AS name, 
		C.status AS status, 
		C.biddingStrategyType AS biddingStrategyType,
		C.create_time AS create_date,
		CASE 
			WHEN C.biddingStrategyType = "TARGET_CPA" THEN C.cpaBidAmount
			WHEN C.biddingStrategyType = "MANUAL_CPC" THEN C.cpcBidAmount
			ELSE 0
		END AS bidamount,
		CASE 
			WHEN C.biddingStrategyType = "TARGET_CPA" THEN "cpa"
			WHEN C.biddingStrategyType = "MANUAL_CPC" THEN "cpc"
			ELSE ""
		END AS bidamount_type,
		SUM(A.impressions) AS impressions, 
		SUM(A.clicks) AS click, 
		SUM(A.cost) AS spend, 
		SUM(A.sales) AS sales, 
		SUM(A.db_count) AS unique_total, 
		SUM(A.margin) AS margin
		', false);
		$subQuery->join('z_adwords.aw_ad B', 'A.ad_id = B.id');
        $subQuery->join('z_adwords.aw_adgroup C', 'B.adgroupId = C.id');
		if(!empty($data['sdate']) && !empty($data['edate'])){
            $subQuery->where('A.date >=', $data['sdate']);
			$subQuery->where('A.date <', date('Y-m-d', strtotime($data['edate'].' + 1day')));
        }
		$subQuery->groupBy('C.id');

		$metricsSubQuery = $this->db->table('z_adwords.aw_adgroup_report_history A');
		$metricsSubQuery->select('
			C.id as id,
			A.date,
			A.hour,
			SUM(A.impressions) AS impressions,
			SUM(A.clicks) AS clicks,
			SUM(A.cost) AS cost,
			SUM(A.sales) AS sales,
			SUM(A.db_count) AS db_count,
			SUM(A.margin) AS margin
		');
		$metricsSubQuery->join('z_adwords.aw_adgroup C', 'A.adgroup_id = C.id');
		if(!empty($data['sdate']) && !empty($data['edate'])){
			$metricsSubQuery->where('A.date >=', $data['sdate']);
			$metricsSubQuery->where('A.date <', date('Y-m-d', strtotime($data['edate'].' + 1day')));
		}
		$metricsSubQuery->groupBy(['C.id', 'A.date', 'A.hour']);
		$metricsSubQuerySql = $metricsSubQuery->getCompiledSelect();
	
		$finalMetricsSubQuery = $this->db->table("($metricsSubQuerySql) AS metrics");
		$finalMetricsSubQuery->select('
			metrics.id,
			JSON_OBJECTAGG(
				CONCAT(metrics.date, "_", metrics.hour),
				JSON_OBJECT(
					"impressions", metrics.impressions,
					"click", metrics.clicks,
					"spend", metrics.cost,
					"sales", metrics.sales,
					"unique_total", metrics.db_count,
					"cpa", IFNULL(ROUND(metrics.cost/metrics.db_count, 1), 0),
					"margin", metrics.margin, 
					"margin_ratio", IFNULL(ROUND((metrics.margin/metrics.sales)*100, 1), 0)
				)
			) AS metrics
		');
		$finalMetricsSubQuery->groupBy('metrics.id');
		$finalMetricsSubQuerySql = $finalMetricsSubQuery->getCompiledSelect();

		$builder = $this->db->newQuery()->fromSubquery($subQuery, 'sub');
        $builder->select('
		G.id AS company_id,
		G.name AS company_name,
		E.customerId as customerId,
		D.id AS campaign_id,
		"google" AS media,
		sub.id AS id, 
		sub.name AS name, 
		COUNT(DISTINCT memo.seq) AS memo_cnt,
		sub.status,
		0 AS budget,
		sub.biddingStrategyType AS biddingStrategyType,
		sub.bidamount AS bidamount,
		D.cpaBidAmount AS campaign_bidamount,
		sub.bidamount_type,
		sub.impressions, 
		sub.click, 
		sub.spend, 
		sub.sales, 
		sub.unique_total, 
		sub.margin,
		metrics.metrics AS metrics,
		sub.create_date');
        $builder->join('z_adwords.aw_campaign D', 'sub.campaign_id = D.id');
        $builder->join('z_adwords.aw_ad_account E', 'D.customerId = E.customerId');
		$builder->join('zenith.company_adaccounts F', 'E.customerId = F.ad_account_id AND F.media = "google"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');
		$builder->join('zenith.advertisement_memo memo', 'sub.id = memo.id AND memo.media = "google" AND memo.type = "adsets" AND is_done <> 1', 'left');
		$builder->join("($finalMetricsSubQuerySql) metrics", 'metrics.id = sub.id', 'left');
        $builder->where('D.status !=', 'NODATA');
        
        if(!empty($data['business'])){
			$builder->whereIn('E.manageCustomer', explode("|",$data['business']));
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

		if(!empty($data['account'])){
			$builder->whereIn('E.customerId', explode("|",$data['account']));
        }

		if(!empty($data['resta'])){
			$builder->whereIn('G.id', explode("|",$data['resta']));
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('sub.name', $data['stx']);
            $builder->groupEnd();
        }

		$builder->groupBy('F.company_id');
        $builder->groupBy('sub.id');
        $builder->orderBy('sub.name', 'asc');
		return $builder;
	}

    public function getAds($data)
	{
		$subQuery = $this->db->table('z_adwords.aw_ad_report A');
        $subQuery->select('
		B.adgroupId as adgroupId,
		B.id AS id, 
		B.name AS name, 
		B.code AS code,
		B.status AS status, 
		B.approvalStatus AS approval_status,
		B.policyTopic AS policyTopic,
		B.imageUrl AS thumbnail,
		B.assets AS assets,
		B.finalUrl AS landingUrl,
		B.create_time AS create_date,
		SUM(A.impressions) AS impressions, 
		SUM(A.clicks) AS click, 
		SUM(A.cost) AS spend, 
		SUM(A.sales) AS sales, 
		SUM(A.db_count) AS unique_total, 
		SUM(A.margin) AS margin, 
		');
		$subQuery->join('z_adwords.aw_ad B', 'A.ad_id = B.id');
		if(!empty($data['sdate']) && !empty($data['edate'])){
            $subQuery->where('A.date >=', $data['sdate']);
			$subQuery->where('A.date <', date('Y-m-d', strtotime($data['edate'].' + 1day')));
        }
		$subQuery->groupBy('B.id');

		$metricsSubQuery = $this->db->table('z_adwords.aw_ad_report_history A');
		$metricsSubQuery->select('
			B.id as id,
			A.date,
			A.hour,
			SUM(A.impressions) AS impressions,
			SUM(A.clicks) AS clicks,
			SUM(A.cost) AS cost,
			SUM(A.sales) AS sales,
			SUM(A.db_count) AS db_count,
			SUM(A.margin) AS margin
		');
		$metricsSubQuery->join('z_adwords.aw_ad B', 'A.ad_id = B.id');
		if(!empty($data['sdate']) && !empty($data['edate'])){
			$metricsSubQuery->where('A.date >=', $data['sdate']);
			$metricsSubQuery->where('A.date <', date('Y-m-d', strtotime($data['edate'].' + 1day')));
		}
		$metricsSubQuery->groupBy(['B.id', 'A.date', 'A.hour']);
		$metricsSubQuerySql = $metricsSubQuery->getCompiledSelect();
	
		$finalMetricsSubQuery = $this->db->table("($metricsSubQuerySql) AS metrics");
		$finalMetricsSubQuery->select('
			metrics.id,
			JSON_OBJECTAGG(
				CONCAT(metrics.date, "_", metrics.hour),
				JSON_OBJECT(
					"impressions", metrics.impressions,
					"click", metrics.clicks,
					"spend", metrics.cost,
					"sales", metrics.sales,
					"unique_total", metrics.db_count,
					"cpa", IFNULL(ROUND(metrics.cost/metrics.db_count, 1), 0),
					"margin", metrics.margin, 
					"margin_ratio", IFNULL(ROUND((metrics.margin/metrics.sales)*100, 1), 0)
				)
			) AS metrics
		');
		$finalMetricsSubQuery->groupBy('metrics.id');
		$finalMetricsSubQuerySql = $finalMetricsSubQuery->getCompiledSelect();

		$builder = $this->db->newQuery()->fromSubquery($subQuery, 'sub');
        $builder->select('
		G.id AS company_id,
		G.name AS company_name,
		E.customerId as customerId,
		D.id AS campaign_id,
		C.id AS adset_id,
		"google" AS media,
		sub.id AS id, 
		sub.name AS name, 
		COUNT(DISTINCT memo.seq) AS memo_cnt,
		sub.code AS code,
		sub.status AS status,
		sub.approval_status AS approval_status,
		sub.policyTopic AS policyTopic,
		sub.thumbnail AS thumbnail,
		sub.assets AS assets,
		sub.landingUrl AS landingUrl,
		0 AS budget, 
		0 AS bidamount,
		"" AS bidamount_type,
		sub.impressions, 
		sub.click, 
		sub.spend, 
		sub.sales, 
		sub.unique_total, 
		sub.margin,
		metrics.metrics AS metrics,
		sub.create_date');
        $builder->join('z_adwords.aw_adgroup C', 'sub.adgroupId = C.id');
        $builder->join('z_adwords.aw_campaign D', 'C.campaignId = D.id');
        $builder->join('z_adwords.aw_ad_account E', 'D.customerId = E.customerId');
		$builder->join('zenith.company_adaccounts F', 'E.customerId = F.ad_account_id AND F.media = "google"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');
		$builder->join('zenith.advertisement_memo memo', 'sub.id = memo.id AND memo.media = "google" AND memo.type = "ads" AND is_done <> 1', 'left');
		$builder->join("($finalMetricsSubQuerySql) metrics", 'metrics.id = sub.id', 'left');
        $builder->where('D.status !=', 'NODATA');
        
        if(!empty($data['business'])){
			$builder->whereIn('E.manageCustomer', explode("|",$data['business']));
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

		if(!empty($data['account'])){
			$builder->whereIn('E.customerId', explode("|",$data['account']));
        }

		if(!empty($data['resta'])){
			$builder->whereIn('G.id', explode("|",$data['resta']));
        }

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('sub.name', $data['stx']);
            $builder->groupEnd();
        }

		$builder->groupBy('F.company_id');
        $builder->groupBy('sub.id');
        $builder->orderBy('sub.name', 'asc');
		return $builder;
	}

    public function getStatuses($param, $result, $dates)
    {
        foreach ($result as &$row) {
            /* $stat = $this->getStat($params, $row['id'], $dates[0], $dates[1]);
			$optimization_stat = $this->getOptimization($params, $row['id']);
			$optimization_budget = $this->getOptimization_budget($params, $row['id']);

		 	$row['unique_total'] = $stat['unique_total'];	//유효db
			$row['sales'] = $row['sales'];	//매출액

//			echo $stat['shortterm']."!<br/>";

//			$row['margin'] = Calc::margin($row['sales'], $row['cost'], $stat['margin']);	// 수익
			$row['margin'] = $stat['margin'];	// 수익
			
			if($params=="campaigns"){ // 캠페인단 ai
				$optimization_campaign = "OFF";
				$song_optimization_campaign = "OFF";
				$choi_optimization_campaign = "OFF";
				for($i=0;$i<sizeof($optimization_stat);$i++){
					if($optimization_stat[$i]['type'] =="901"){
						$optimization_campaign = "ON";
					}
					if($optimization_stat[$i]['type'] =="801" || $optimization_stat[$i]['type'] =="802" || $optimization_stat[$i]['type'] =="803"){
						$song_optimization_campaign = "Lv".substr($optimization_stat[$i]['type'],-1);
					}
					if($optimization_stat[$i]['type'] =="701"){
						$choi_optimization_campaign = "ON";
					}
				}
				$row['optimization_campaign'] = $optimization_campaign;
				$row['song_optimization_campaign'] = $song_optimization_campaign;
				$row['choi_optimization_campaign'] = $choi_optimization_campaign;
			}
			else if($params=="ads"){ // 광고 ai
				if($optimization_stat =="701"){
					$row['optimization_ad'] = "ON";
				}
				else {
					$row['optimization_ad'] = "OFF";
				}
			}
			else {

				if($optimization_stat =="1"){
					$row['optimization'] = "ON";	//어른정파고
					$row['optimization_ch'] = "OFF";	// 어린이정파고
				}
				else if($optimization_stat =="2"){
					$row['optimization'] = "OFF";	//어른정파고
					$row['optimization_ch'] = "ON";	// 어린이정파고
				}
				else {
					$row['optimization'] = "OFF";
					$row['optimization_ch'] = "OFF";
				}
			}
			if($choi_optimization_campaign=="ON") $row['optimization_campaign_budget'] = $optimization_budget;	// ai 예산 */

			if($row['status'] == 'ENABLED'){
				$row['status'] = "ON";
			}else{
				$row['status'] = "OFF";
			}

            $row['margin_ratio'] = Calc::margin_ratio($row['margin'], $row['sales']);	// 수익률


			$row['cpc'] = Calc::cpc($row['spend'], $row['click']);	// 클릭당단가 (1회 클릭당 비용)
			$row['ctr'] = Calc::ctr($row['click'], $row['impressions']);	// 클릭율 (노출 대비 클릭한 비율)
			$row['cpa'] = Calc::cpa($row['unique_total'], $row['spend']);	//DB단가(전환당 비용)
			$row['cvr'] = Calc::cvr($row['unique_total'], $row['click']);	//전환율

			switch (!empty($row['biddingStrategyType'])) {
				case 'TARGET_CPA' :
					$row['biddingStrategyType'] = '타겟 CPA';
					break;
				case 'TARGET_ROAS' :
					$row['biddingStrategyType'] = '타겟 광고 투자수익(ROAS)';
					break;
				case 'TARGET_SPEND' :
					$row['biddingStrategyType'] = '클릭수 최대화';
					break;
				case 'MAXIMIZE_CONVERSIONS' :
					$row['biddingStrategyType'] = '전환수 최대화';
					break;
                /* //값이 뭔지 모름ㅠㅠ
				case '' :
					$row['biddingStrategyType'] = '검색 결과 위치 타겟';
					break;
				case '' :
					$row['biddingStrategyType'] = '경쟁 광고보다 내 광고가 높은 순위에 게재되는 비율 타겟';
					break;
				case '' :
					$row['biddingStrategyType'] = '타겟 노출 점유율';
					break;
				*/
                case 'PAGE_ONE_PROMOTED' :
                    $row['biddingStrategyType'] = '향상된 CPC 입찰기능';
                    break;
                case 'MANUAL_CPM' :
                    $row['biddingStrategyType'] = '수동 입찰 전략';
                    break;
                case 'MANUAL_CPC' :
                    $row['biddingStrategyType'] = '수동 CPC';
                    break;
                case 'UNKNOWN' :
                    $row['biddingStrategyType'] = '알수없음';
                    break;
                default :
                    break;
			}
        }
        return $result;
    }
	public function getReport($data)
	{
		$builder = $this->db->table('z_adwords.aw_ad_report A');
        $builder->select('
		A.date, 
		SUM(A.impressions) AS impressions,
		SUM(A.clicks) AS click,
		(SUM(A.clicks) / SUM(A.impressions)) * 100 AS click_ratio,
		(SUM(A.db_count) / SUM(A.clicks)) * 100 AS conversion_ratio,
		SUM(A.cost) AS spend,
		SUM(A.db_count) AS unique_total,
		IFNULL(SUM(A.cost) / SUM(A.db_count), 0) AS unique_one_price,
		SUM(A.db_price) AS unit_price,
		SUM(A.sales) AS price,
		SUM(A.margin) AS profit,
		(SUM(A.db_price * A.db_count) - SUM(A.cost)) / SUM(A.db_price * A.db_count) * 100 AS per
		');
		$builder->join('z_adwords.aw_ad B', 'A.ad_id = B.id');
        $builder->join('z_adwords.aw_adgroup C', 'B.adgroupId = C.id');
        $builder->join('z_adwords.aw_campaign D', 'C.campaignId = D.id');
        $builder->join('z_adwords.aw_ad_account E', 'D.customerId = E.customerId');
		$builder->join('zenith.company_adaccounts F', 'E.customerId = F.ad_account_id AND F.media = "google"');
		$builder->join('zenith.companies G', 'F.company_id = G.id');
        $builder->where('D.status !=', 'NODATA');

        if(!empty($data['sdate']) && !empty($data['edate'])){
            $builder->where('A.date >=', $data['sdate']);
            $builder->where('A.date <', date('Y-m-d', strtotime($data['edate'].' + 1day')));
        }
        
		if(!empty($data['googleCheck'])){
			switch ($data['type']) {
				case 'campaigns':
					$builder->whereIn('D.id', $data['googleCheck']);
					break;
				case 'adsets':
					$builder->whereIn('C.id', $data['googleCheck']);
					break;
				case 'ads':
					$builder->whereIn('B.id', $data['googleCheck']);
					break;
				default:
					break;
			}
        }
		
        if(!empty($data['business'])){
			$builder->whereIn('E.manageCustomer', explode("|",$data['business']));
        }

        if(!empty($data['company'])){
			$builder->whereIn('G.id', explode("|",$data['company']));
        }

		if(!empty($data['account'])){
			$builder->whereIn('E.customerId', explode("|",$data['account']));
        }
		
		if(!empty($data['resta'])){
			$builder->whereIn('G.id', explode("|",$data['resta']));
        }

        $builder->groupBy('A.date');
		
		return $builder;
	}

    public function getDisapproval()
	{
        $builder = $this->db->table('z_adwords.aw_ad_account acc');
		$builder->select('
		acc.customerId, 
		acc.name AS customer_name,
        ac.id AS campaign_id, 
		ac.name AS campaign_name,
        ag.id AS adgroup_id, 
		ag.name AS adgroup_name,
        ad.id, 
		ad.name, 
		ad.code, 
		ass.url, 
		ad.status, 
		ad.policyTopic,
		ad.reviewStatus, 
		ad.approvalStatus, 
		ad.adType, 
		ad.finalUrl, 
		ad.create_time, 
		ad.update_time');
		$builder->join('z_adwords.aw_campaign ac', 'ac.customerId = acc.customerId', 'left');
		$builder->join('z_adwords.aw_adgroup ag', 'ag.campaignId = ac.id', 'left');
		$builder->join('z_adwords.aw_ad ad', 'ad.adgroupId = ag.id', 'left');
		$builder->join('z_adwords.aw_asset ass', "SUBSTRING_INDEX(ad.assets, ',', 1) = ass.id", 'left');
        $builder->where("(ad.approvalStatus = 'DISAPPROVED' OR ad.approvalStatus = 'AREA_OF_INTEREST_ONLY' OR ad.approvalStatus = 'APPROVED_LIMITED' OR (ad.approvalStatus = 'APPROVED' AND ad.policyTopic LIKE '%HEALTH_IN_PERSONALIZED_ADS%')) AND acc.is_exposed = 1 AND acc.status = 'ENABLED' AND ac.status = 'ENABLED' AND ag.status = 'ENABLED' AND ad.status = 'ENABLED'");
		// $builder->orderBy('ad.create_time', 'DESC');
		$result = $builder->get()->getResultArray();

        return $result;
	}

	public function getCampaignById($ids)
    {
		$builder = $this->db->table('z_adwords.aw_campaign');
		$builder->select('"구글" as media, "캠페인" as type, id, name, status');
		$builder->whereIn('id', $ids);
        
        return $builder;
    }

	public function getAdgroupById($ids)
    {
		$builder = $this->db->table('z_adwords.aw_adgroup');
		$builder->select('"구글" as media, "광고그룹" as type, id, name, status');
		$builder->whereIn('id', $ids);
		
        return $builder;
    }

	public function getAdById($ids)
    {
		$builder = $this->db->table('z_adwords.aw_ad');
		$builder->select('"구글" as media, "광고" as type, id, name, status');
		$builder->whereIn('id', $ids);
		
        return $builder;
    }
	
	public function updateCode($data) {
		$result = [];
		$this->db->transStart();
        $builder = $this->db->table('z_adwords.aw_ad');  
		$builder->set('code', $data['code']);
		$builder->where('id', $data['id']);
		$builder->update();
		$queryResult = $this->db->transComplete();
		if($queryResult){
			$result['code'] = $data['code'] ?? '';
		}

		return $result;
	}

	public function getCustomerByCampaignId($campaignIds) {
        $builder = $this->db->table('z_adwords.aw_campaign A');  
		$builder->select('A.id, B.customerId, B.manageCustomer');
		$builder->join('z_adwords.aw_ad_account B', 'A.customerId = B.customerId');
		$builder->whereIn('A.id', $campaignIds);
		$builder->groupBy('A.id');
		$result = $builder->get()->getResultArray();

		return $result;
	}

	public function setUpdatingByAds($campaignIds){
		$this->db->transStart();
		$builder = $this->db->table('z_adwords.aw_campaign');
		$builder->whereIn('id', $campaignIds);
		$builder->set('is_updating', 1);
		$builder->update();
		$result = $this->db->transComplete();

		return $result;
	}

	public function updateDbCount($data) {
		$this->db->transStart();
        $builder = $this->db->table('z_adwords.aw_ad_account');  
		$builder->set('db_count', (integer)$data['db_count']);
		$builder->where('customerId', $data['id']);
		$builder->update();
		$result = $this->db->transComplete();

		return $result;
	}

	public function updateExposed($data) {
		$this->db->transStart();
        $builder = $this->db->table('z_adwords.aw_ad_account');  
		$builder->set('is_exposed', (integer)$data['is_exposed']);
		$builder->where('customerId', $data['id']);
		$builder->update();
		$result = $this->db->transComplete();

		return $result;
	}

	public function getCustomerById($id, $type) {
		if(empty($type)){return false;}
        $builder = $this->db->table('z_adwords.aw_ad_account A');  
		$builder->select('A.customerId');
		$builder->join('z_adwords.aw_campaign B', 'A.customerId = B.customerId');
		$builder->join('z_adwords.aw_adgroup C', 'B.id = C.campaignId');
		$builder->join('z_adwords.aw_ad D', 'C.id = D.adgroupId');
		
		if($type == 'campaign'){
			$builder->where('B.id', $id);
		}else if($type == 'adgroup'){
			$builder->where('C.id', $id);
		}else if($type == 'ad'){
			$builder->where('D.id', $id);
		}
		
		$builder->groupBy('A.customerId');
		$result = $builder->get()->getRowArray();

		return $result;
	}

	public function getAsset($id) {
        $builder = $this->db->table('z_adwords.aw_asset');  
		$builder->select('url');
		$builder->where('id', $id);
		$result = $builder->get()->getRowArray();

		return $result;
	}
}
