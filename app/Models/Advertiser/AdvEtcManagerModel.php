<?php

namespace App\Models\Advertiser;

use CodeIgniter\Model;

class AdvEtcManagerModel extends Model
{
    public function getList($searchParams)
    {
        $whereConditions = [];

        if (!empty($searchParams['sdate'])) {
            $searchSDate = (new \DateTime($searchParams['sdate']))->format('Y-m-d');
            $whereConditions[] = "el.reg_date >= '{$searchSDate} 00:00:00'";
        }
        if (!empty($searchParams['edate'])) {
            $searchEDate = (new \DateTime($searchParams['edate']))->format('Y-m-d');
            $whereConditions[] = "el.reg_date <= '{$searchEDate} 23:59:59'";
        }

        $whereSQL = '';
        if (!empty($whereConditions)) {
            $whereSQL = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        $includeMedia = ['카카오광고','카카오비즈폼','카카오플러스친구','토스','틱톡','당근마켓','타불라','유튜브']; //표시 할 광고주
        $excludeMedia = ['유튜브', '틱톡', '토스', '타불라']; //부가세 제외 광고주
        if($searchSDate != $searchEDate) {
            $groupBy = 'GROUP BY el.event_seq, el.site';
        } else {
            $groupBy = 'GROUP BY date, el.event_seq, el.site';
        }
        $sql = "
        SELECT
            evt.*,
            IFNULL(ROUND(db.goal, 2), 0) as goal,
            IFNULL(db.unitprice, 0) as unitprice,
            IFNULL(
                CASE WHEN evt.media_name IN ('" . implode("', '", $excludeMedia) . "')
                    THEN db.total_price
                    ELSE db.total_price / 1.1
                END, 0
            ) as total_price,
            IFNULL(ROUND(
                CASE WHEN evt.media_name IN ('" . implode("', '", $excludeMedia) . "')
                    THEN db.total_price / evt.db_unique
                    ELSE db.total_price / 1.1 / evt.db_unique
                END, 0
            ), 0) as now_unitprice,
            IFNULL(ROUND(
                (db.unitprice - (
                    CASE WHEN evt.media_name IN ('" . implode("', '", $excludeMedia) . "')
                        THEN db.total_price / evt.db_unique
                        ELSE db.total_price / 1.1 / evt.db_unique
                    END
                )) * evt.db_unique, 0
            ), 0) as profit, 
            IFNULL(ROUND(
                ((db.unitprice - (
                    CASE WHEN evt.media_name IN ('" . implode("', '", $excludeMedia) . "')
                        THEN db.total_price / evt.db_unique
                        ELSE db.total_price / 1.1 / evt.db_unique
                    END
                )) * evt.db_unique) / (db.unitprice * evt.db_unique) * 100, 1
            ), 0) as profit_rate,
            db.confirm,
            db.reg_date
        FROM
            (
                SELECT
                    ea.name as advertiser_name,
                    em.media as media_name,
                    ei.seq as event_seq,
                    el.site,
                    ei.description,
                    DATE(el.reg_date) as date,
                    COUNT(el.seq) as db_total,
                    SUM(CASE WHEN el.status=1 THEN 1 ELSE 0 END) as db_unique,
                    SUM(CASE WHEN el.status<>1 THEN 1 ELSE 0 END) as db_invalid
                FROM event_leads as el
                LEFT JOIN event_information as ei ON el.event_seq = ei.seq
                LEFT JOIN event_advertiser as ea ON ei.advertiser = ea.seq
                LEFT JOIN event_media as em ON ei.media = em.seq
                $whereSQL
                $groupBy
            ) as evt
        LEFT JOIN db_price as db ON evt.event_seq = db.event_seq AND evt.site = db.site AND evt.date = db.date
        WHERE evt.db_unique > 0 AND evt.media_name IN ('" . implode("', '", $includeMedia) . "')
        ORDER BY evt.date DESC, evt.advertiser_name, evt.event_seq, evt.site
        ";
        // echo $sql; exit;

        $query = $this->db->query($sql);
        return $query->getResult();
    }

    public function updateOrInsertEventData($data)
    {
        $builder = $this->db->table('db_price');
        $builder->where('date', $data['date']);
        $builder->where('event_seq', $data['event_seq']);
        $builder->where('site', $data['site']);
        $exists = $builder->countAllResults(false);
        if ($exists) {
            // 데이터가 존재하면 업데이트
            return $builder->update($data);
        } else {
            // 데이터가 존재하지 않으면 삽입
            return $builder->insert($data);
        }
    }
}

