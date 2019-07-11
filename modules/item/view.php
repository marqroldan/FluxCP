<?php
if (!defined('FLUX_ROOT')) exit;

//$this->loginRequired();

$title = 'Viewing Item';

require_once 'Flux/TemporaryTable.php';

$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);
$shopTable = Flux::config('FluxTables.ItemShopTable');

$itemID = $params->get('id');

$col  = 'items.id AS item_id, name_english AS identifier, ';
$col .= 'name_japanese AS name, type, ';
$col .= 'price_buy, price_sell, weight/10 AS weight, defence, `range`, slots, ';
$col .= 'equip_jobs, equip_upper, equip_genders, equip_locations, equip_level_min, equip_level_max, ';
$col .= 'weapon_level, refineable, view_sprite as view, script, equip_script, unequip_script, origin_table, ';
$col .= "$shopTable.cost, $shopTable.id AS shop_item_id, atk, matk";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.items ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.$shopTable ON $shopTable.nameid = items.id ";
$sql .= "WHERE items.id = ? LIMIT 1";

$sth  = $server->connection->getStatement($sql);
$sth->execute(array($itemID));

$item = $sth->fetch();
$isCustom = null;

if ($item) {
	$isCustom = (bool)preg_match('/item_db2$/', $item->origin_table);
	
	$mobDB      = "{$server->charMapDatabase}.monsters";
	$fromTables = array("{$server->charMapDatabase}.mob_db", "{$server->charMapDatabase}.mob_db2");
	$mobTable   = new Flux_TemporaryTable($server->connection, $mobDB, $fromTables);
	
	$col  = 'ID AS monster_id, iName AS monster_name, LV AS monster_level, ';
	$col .= 'Race AS monster_race, (Element%10) AS monster_element, (Element/20) AS monster_ele_lv, MEXP AS mvp_exp, ';
	
	// Normal drops.
	$col .= 'Drop1id AS drop1_id, Drop1per AS drop1_chance, ';
	$col .= 'Drop2id AS drop2_id, Drop2per AS drop2_chance, ';
	$col .= 'Drop3id AS drop3_id, Drop3per AS drop3_chance, ';
	$col .= 'Drop4id AS drop4_id, Drop4per AS drop4_chance, ';
	$col .= 'Drop5id AS drop5_id, Drop5per AS drop5_chance, ';
	$col .= 'Drop6id AS drop6_id, Drop6per AS drop6_chance, ';
	$col .= 'Drop7id AS drop7_id, Drop7per AS drop7_chance, ';
	$col .= 'Drop8id AS drop8_id, Drop8per AS drop8_chance, ';
	$col .= 'Drop9id AS drop9_id, Drop9per AS drop9_chance, ';
	
	// Card drops.
	$col .= 'DropCardid AS dropcard_id, DropCardper AS dropcard_chance, ';
	
	// MVP rewards.
	$col .= 'MVP1id AS mvpdrop1_id, MVP1per AS mvpdrop1_chance, ';
	$col .= 'MVP2id AS mvpdrop2_id, MVP2per AS mvpdrop2_chance, ';
	$col .= 'MVP3id AS mvpdrop3_id, MVP3per AS mvpdrop3_chance';
	
	$sql  = "SELECT $col FROM $mobDB WHERE ";
	
	// Normal drops.
	$sql .= 'Drop1id = ? OR ';
	$sql .= 'Drop2id = ? OR ';
	$sql .= 'Drop3id = ? OR ';
	$sql .= 'Drop4id = ? OR ';
	$sql .= 'Drop5id = ? OR ';
	$sql .= 'Drop6id = ? OR ';
	$sql .= 'Drop7id = ? OR ';
	$sql .= 'Drop8id = ? OR ';
	$sql .= 'Drop9id = ? OR ';
	
	// Card drops.
	$sql .= 'DropCardid = ? OR ';
	
	// MVP rewards.
	$sql .= 'MVP1id = ? OR ';
	$sql .= 'MVP2id = ? OR ';
	$sql .= 'MVP3id = ? ';
	
	//$sql .= 'GROUP BY ID, iName';
	
	$sth  = $server->connection->getStatement($sql);
	$res = $sth->execute(array_fill(0, 13, $itemID));
	
	$dropResults = $sth->fetchAll();
	$itemDrops   = array();
	$dropNames   = array(
		'drop1', 'drop2', 'drop3', 'drop4', 'drop5', 'drop6', 'drop7', 'drop8', 'drop9',
		'dropcard', 'mvpdrop1', 'mvpdrop2', 'mvpdrop3'
	);
	
	// Sort callback.
	function __tmpSortDrops($arr1, $arr2)
	{
		if ($arr1['drop_chance'] == $arr2['drop_chance']) {
			return strcmp($arr1['monster_name'], $arr2['monster_name']);
		}
		
		return $arr1['drop_chance'] < $arr2['drop_chance'] ? 1 : -1;
	}
	
	foreach ($dropResults as $drop) {
		foreach ($dropNames as $dropName) {
			$dropID     = $drop->{$dropName.'_id'};
			$dropChance = $drop->{$dropName.'_chance'};
			
			if ($dropID == $itemID) {
				$dropArray = array(
					'monster_id'      => $drop->monster_id,
					'monster_name'    => $drop->monster_name,
					'monster_level'   => $drop->monster_level,
					'monster_race'    => $drop->monster_race,
					'monster_element' => $drop->monster_element,
					'monster_ele_lv'  => $drop->monster_ele_lv,
					'drop_id'         => $itemID,
					'drop_chance'     => $dropChance
				);
				
				if (preg_match('/^dropcard/', $dropName)) {
					$adjust = ($drop->mvp_exp) ? $server->dropRates['CardBoss'] : $server->dropRates['Card'];
					$dropArray['type'] = 'card';
				}
				elseif (preg_match('/^mvp/', $dropName)) {
					$adjust = $server->dropRates['MvpItem'];
					$dropArray['type'] = 'mvp';
				}
				elseif (preg_match('/^drop/', $dropName)) {
					switch($item->type) {
						case 0: // Healing
							$adjust = ($drop->mvp_exp) ? $server->dropRates['HealBoss'] : $server->dropRates['Heal'];
							break;
						
						case 2: // Useable
						case 18: // Cash Useable
							$adjust = ($drop->mvp_exp) ? $server->dropRates['UseableBoss'] : $server->dropRates['Useable'];
							break;
						
						case 4: // Weapon
						case 5: // Armor
						case 8: // Pet Armor
							$adjust = ($drop->mvp_exp) ? $server->dropRates['EquipBoss'] : $server->dropRates['Equip'];
							break;
						
						default: // Common
							$adjust = ($drop->mvp_exp) ? $server->dropRates['CommonBoss'] : $server->dropRates['Common'];
							break;
					}
					
					$dropArray['type'] = 'normal';
				}
				
				$dropArray['drop_chance'] = $dropArray['drop_chance'] * $adjust / 10000;

				if ($dropArray['drop_chance'] > 100) {
					$dropArray['drop_chance'] = 100;
				}
				
				$itemDrops[] = $dropArray;
			}
		}
	}
	
	// Sort so that monsters are ordered by drop chance and name.
	usort($itemDrops, '__tmpSortDrops');

}

if($params->get('output')=='json') {
	$json_arr = array();
	if($item) {

		$json_arr['labels']['item'] = array(
			'item_id' => "Item ID",
			'identifier' => "Identifier",
			'name' => "Name",
			'type' => "Type",
			/*
			'for_sale' => "For Sale",
			'cost' => "Cost",
			*/
			'buy_sell' => "Buy/Sell",
			'weight' => "Weight",
			'defense' => "Defense",
			'atk_matk' => "ATK/MATK",
			'weapon_level' => "Weapon Level",
			'range' => "Range",
			'slots' => "Slots",
			'refineable' => "Refineable",
			'equip_level' => "Min/Max Equip Level",
			'equip_locations' => "Equip Locations",
			'equip_upper' => "Equip Upper",
			'equip_jobs' => "Equip Jobs",
			'equip_gender' => "Equip Gender",
			'item_use_script' => "Item Use Script",
			'equip_script' => "Equip Script",
			'unequip_script' => "Unequip Script",
		);
		$json_arr['labels']['itemDrops'] = array(
			'monster_id' => "ID",
			'monster_name' => "Name",
			'drop_chance' => "Drop Chance",
			'monster_level' => "Level",
			'monster_race' => "Race",
			'monster_element' => "Element",
		);
		

		$json_arr['item']['item_id'] = $item->item_id;
		if($g=$this->iconImage($item->item_id)) {
			$json_arr['item']['icon'] = $g;
		}
		if($g=$this->itemImage($item->item_id)) {
			$json_arr['item']['image'] = $g;
		}
		$json_arr['item']['identifier'] = htmlspecialchars($item->identifier);
		if ($item->cost)  {
			$json_arr['item']['shop_item_id'] = $item->shop_item_id;
			$json_arr['item']['cost'] = number_format((int)$item->cost);
		}
		$json_arr['item']['name'] = htmlspecialchars($item->name);
		$json_arr['item']['type'] = $this->itemTypeText($item->type, $item->view);
		$json_arr['item']['buy_sell'] = number_format((int)$item->price_buy)."/".((is_null($item->price_sell) && $item->price_buy) ? number_format(floor($item->price_buy / 2)) : number_format((int)$item->price_sell));
		$json_arr['item']['weight'] = round($item->weight, 1);
		$json_arr['item']['atk_matk'] = number_format((int)$item->atk)."/".number_format((int)$item->matk);
		$json_arr['item']['weapon_level'] = number_format((int)$item->weapon_level);
		$json_arr['item']['range'] = number_format((int)$item->range);
		$json_arr['item']['defense'] = number_format((int)$item->defence);
		$json_arr['item']['slots'] = number_format((int)$item->slots);
		$json_arr['item']['refineable'] = ($item->refineable) ? "Yes" : "No";
		$json_arr['item']['equip_level'] = number_format((int)$item->equip_level_min)."/".(($item->equip_level_max == 0) ? "None" : number_format((int)$item->equip_level_max));
		$json_arr['item']['equip_locations'] = ($locs=$this->equipLocations($item->equip_locations)) ? htmlspecialchars(implode(' + ', $locs)) : "None";
		$json_arr['item']['equip_upper'] = ($upper=$this->equipUpper($item->equip_upper)) ? htmlspecialchars(implode(' / ', $upper)) : "None";
		$json_arr['item']['equip_jobs'] = ($jobs=$this->equippableJobs($item->equip_jobs)) ? htmlspecialchars(implode(' / ', $jobs)) : "None";
		$json_arr['item']['equip_gender'] = ($item->equip_genders === '0') ? "Female" : (($item->equip_genders === '1') ? "Male" : (($item->equip_genders === '2') ? "Both (Male and Female)" : "Unknown"));

		if (($isCustom && $auth->allowedToSeeItemDb2Scripts) || (!$isCustom && $auth->allowedToSeeItemDbScripts)) {
		$json_arr['item']['item_use_script'] = ($script=$this->displayScript($item->script)) ? $script : "None";
		$json_arr['item']['equip_script'] = (($script=$this->displayScript($item->equip_script))) ? $script : "None";
		$json_arr['item']['unequip_script'] = (($script=$this->displayScript($item->unequip_script))) ? $script : "None";
		}

 		if ($itemDrops) {
			foreach ($itemDrops as $itemDrop) {
				$tmp = array();
				$tmp['monster_id'] = $itemDrop['monster_id'];
				if($auth->actionAllowed('monster', 'view')) {
					$tmp['monster_link'] = 1;
				}
				$tmp['monster_name'] = (($itemDrop['type'] == 'mvp') ? '<span class="mvp">MVP!</span>' : '').$itemDrop['monster_name'];
				$tmp['drop_chance'] = $itemDrop['drop_chance']."%";
				$tmp['monster_level'] = number_format($itemDrop['monster_level']);
				$tmp['monster_race'] =  Flux::monsterRaceName($itemDrop['monster_race']);
				$tmp['monster_element'] = "Level " . floor($itemDrop['monster_ele_lv']) ." ". Flux::elementName($itemDrop['monster_element']);
				$json_arr['itemDrops'][] = $tmp;
			}
		}
	}
	echo json_encode($json_arr);
	exit();
}
?>
