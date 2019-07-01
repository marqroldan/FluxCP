<?php
if (!defined('FLUX_ROOT')) exit;

$title     = Flux::message('WoeTitle');
$dayNames  = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
$woeTimes  = array();
$serverTimes = array();
$woe_status = false;

foreach ($session->loginAthenaGroup->athenaServers as $athenaServer) {
	$times = $athenaServer->woeDayTimes;
	if ($times) {
		$woeTimes[$athenaServer->serverName] = array();
			foreach ($times as $time) {
				if(is_array($time['castleID'])) {
					$castle_array = array();
					foreach($time['castleID'] as $id) {
						$castle_array[] = (is_numeric($id)) ? Flux::config('CastleNames.'.$id) : $id;
					}
					$castle = implode(', ',$castle_array);
				}
				else {
					$castle = (is_numeric($time['castleID'])) ? Flux::config('CastleNames.'.$time['castleID']) : $time['castleID'];
				}

				$currentTime = (new DateTime($athenaServer->getServerTime('Y-m-d H:i:s e')));
				$startTime = (new DateTime($dayNames[$time['startingDay']]." ".$time['startingTime']));
				$endTime = (new DateTime($dayNames[$time['endingDay']]." ".$time['endingTime']));
	
				if($startTime <= $currentTime && $endTime <= $currentTime) {
					$startTime->modify("+7 days");
					$endTime->modify("+7 days");
				}

				$tmp = array(
					'start_timestamp' => $startTime->getTimestamp()*1000,
					'end_timestamp' => $endTime->getTimestamp()*1000,
					'start' => $startTime->format('Y-m-d H:i:s'),
					'end' => $endTime->format('Y-m-d H:i:s'),
					'startingDay'  => $dayNames[$time['startingDay']],
					'startingHour' => $time['startingTime'],	
					'endingDay'    => $dayNames[$time['endingDay']],
					'endingHour'   => $time['endingTime'],
					'castle'   => $castle,
					'woeOn' => ($currentTime >= $startTime && $currentTime <= $endTime)  ? 1 : 0,
				);

				$woeTimes[$athenaServer->serverName][] = $tmp;
				$serverTimes[$athenaServer->serverName] = $currentTime->format('Y-m-d H:i:s (l) e');
			}
			sort($woeTimes[$athenaServer->serverName]);
	}
}

if(false) {
	echo "<pre>";
	print_r($woeTimes);
	echo '</pre>';
}

if($params->get('output')=='json') {
	echo json_encode(
		array(
			'serverTimes'=> $serverTimes,
			'woeTimes' => $woeTimes,
			'days' => $dayNames,
			)
	);
	exit();
}
?>