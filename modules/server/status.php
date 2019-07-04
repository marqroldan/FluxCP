<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('ServerStatusTitle');
$cache = FLUX_DATA_DIR.'/tmp/ServerStatus.cache';

if (file_exists($cache) && (time() - filemtime($cache)) < (Flux::config('ServerStatusCache') * 60)) {
	$serverStatus = unserialize(file_get_contents($cache));
}
else {
	$serverStatus = array();
	$smallest = 5;
	foreach (Flux::$loginAthenaGroupRegistry as $groupName => $loginAthenaGroup) {
		if (!array_key_exists($groupName, $serverStatus)) {
			$serverStatus[$groupName] = array();
		}

		$loginServerUp = $loginAthenaGroup->loginServer->isUp() ? 1 : 0;

		foreach ($loginAthenaGroup->athenaServers as $athenaServer) {
			$serverName = $athenaServer->serverName;
			
			$sql = "SELECT COUNT(char_id) AS population FROM {$athenaServer->charMapDatabase}.char WHERE online > 0";
			$sth = $loginAthenaGroup->connection->getStatement($sql);
			$sth->execute();
			$res = $sth->fetch();
			$population = intval($res ? $res->population : 0);
			
			$sql = "SELECT COUNT(char_id) AS autotrade_merchants FROM {$athenaServer->charMapDatabase}.autotrade_merchants";
			$sth = $loginAthenaGroup->connection->getStatement($sql);
			$sth->execute();
			$res = $sth->fetch();
			$autotrade_merchants = intval($res ? $res->autotrade_merchants : 0);
			
			$serverStatus[$groupName][$serverName] = array(
				'loginServerUp' => $loginServerUp,
				 'charServerUp' => $athenaServer->charServer->isUp() ? 1 : 0,
				  'mapServerUp' => $athenaServer->mapServer->isUp() ? 1 : 0,
				'playersOnline' => $population - $autotrade_merchants,
				'autotradeMerchants' => $autotrade_merchants,
				'population' => $population,
				'serverTime' => $athenaServer->getServerTime('Y-m-d H:i:s e')
			);

			$sum = $serverStatus[$groupName][$serverName]['loginServerUp'] + $serverStatus[$groupName][$serverName]['charServerUp'] + $serverStatus[$groupName][$serverName]['mapServerUp'];

			if($sum <= $smallest) $smallest = $sum;
		}
	}

	$serverStatus = array(
		'minimum' => $smallest,
		'serverStatus' => $serverStatus,
	);
	
	$fp = fopen($cache, 'w');
	if (is_resource($fp)) {
		fwrite($fp, serialize($serverStatus));
		fclose($fp);
	}
}

if($params->get('output')=='json') {
	echo json_encode($serverStatus);
	exit();
}
?>