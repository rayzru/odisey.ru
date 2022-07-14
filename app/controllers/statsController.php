<?php

namespace odissey;

class StatsController extends Controller
{

	const GROUPBY_HOUR = 'hour';
	const GROUPBY_DAY = 'day';
	const GROUPBY_WEEK = 'week';
	const GROUPBY_MONTH = 'month';

	const DATE_FORMAT = "Y-m-d H:i:s";
	const DATE_FORMAT_0 = "Y-m-d 00:00:00";

	private $statsData;

	public function __construct() {
		parent::__construct();
		$this->statsData = new Stats();
	}

	public function store(\stdClass $trackingData) {
		$data = [
			'user_id'   => $trackingData->user_id ?? null,
			'url'       => $trackingData->url ?? null,
			'referrer'  => $trackingData->ref ?? null,
			'ua'        => $trackingData->ua ?? null,
			'ipv4'      => $trackingData->ipv4 ?? null,
			'wh'        => $trackingData->wh ?? null,
			'ww'        => $trackingData->ww ?? null,
			'sh'        => $trackingData->sh ?? null,
			'sw'        => $trackingData->sw ?? null,
			'sessionid' => $trackingData->session ?? null,
			'lng'       => $trackingData->lng ?? null,
			'lat'       => $trackingData->lat ?? null,
			'secret'    => $this->statsData->getSecret()
		];
		return $this->db->insert('stats', (array)$data);
	}

	public function getLive($minutes = 30) {
		$fields = [
			'COUNT(id) AS hits',
			'COUNT(DISTINCT sessionid)  AS sessions',
			"trackdate AS dt"
		];
		$data = $this->db
			->where("trackdate > NOW() - INTERVAL {$minutes} MINUTE")
			->groupBy("HOUR(trackdate), MINUTE(trackdate)")
			->orderBy("trackdate")
			->get('stats', null, $fields);
		return $data;
	}

	public function getActiveSessions() {
		return $this->db
			->where("trackdate > (NOW() - INTERVAL 15 MINUTE)")
			->getValue('stats', "COUNT(DISTINCT sessionid)");
	}

	public function getStatsNumbers($from, $to = null) {
		$to = $to ?? date("Y-m-d H:i:s");
		if ($this->validateDate($from) && $this->validateDate($to)) {
			$data = $this->db->rawQueryValue("
			SELECT count(*) AS hits, count(DISTINCT sessionid) AS sessions FROM stats
			WHERE trackdate >= DATE(?)
			AND trackdate <= DATE(?)
		", [$from, $to]);
			return $data;
		}
		return false;
	}

	public function getStats($start, $end = null, $groupBy = self::GROUPBY_DAY) {
		$end = $end ?? date("Y-m-d H:i:s");

		if ($this->validateDate($start) && $this->validateDate($end)) {
			switch ($groupBy) {
				case self::GROUPBY_HOUR:
					$format = '%Y-%m-%d %H:00:00';
					$groupBy = "YEAR(trackdate), MONTH(trackdate), DAY(trackdate), HOUR(trackdate)";
					break;
				case self::GROUPBY_MONTH:
					$format = '%M %Y';
					$groupBy = "YEAR(trackdate), MONTH(trackdate)";
					break;
				default:
				case self::GROUPBY_WEEK:
				case self::GROUPBY_DAY:
					$format = '%Y-%m-%d';
					$groupBy = "YEAR(trackdate), MONTH(trackdate), DAY(trackdate)";
					break;
			}

			$data = $this->db->rawQuery("
			SELECT count(*) AS hits, count(DISTINCT sessionid) AS sessions,
			DATE_FORMAT(trackdate, '{$format}') AS dt FROM stats
			WHERE trackdate >= ? AND trackdate <= ?
			GROUP BY {$groupBy}
		", [$start, $end]);
			return $data;
		}

		return false;
	}

	public function validateDate($dateString) {
		return (\DateTime::createFromFormat('Y-m-d G:i:s', $dateString) !== false);
	}
}
