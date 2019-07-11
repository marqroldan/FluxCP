<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Viewing Monster';
$mobID = $params->get('id');

require_once 'Flux/TemporaryTable.php';

// Monsters table.
$mobDB      = "{$server->charMapDatabase}.monsters";
$fromTables = array("{$server->charMapDatabase}.mob_db", "{$server->charMapDatabase}.mob_db2");
$tempMobs   = new Flux_TemporaryTable($server->connection, $mobDB, $fromTables);

// Monster Skills table.
$skillDB    = "{$server->charMapDatabase}.mobskills";
$fromTables = array("{$server->charMapDatabase}.mob_skill_db", "{$server->charMapDatabase}.mob_skill_db2");
$tempSkills = new Flux_TemporaryTable($server->connection, $skillDB, $fromTables);

// Items table.
$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
$itemDB    = "{$server->charMapDatabase}.items";
$tempItems = new Flux_TemporaryTable($server->connection, $itemDB, $fromTables);

$col  = 'origin_table, ID as monster_id, Sprite AS sprite, kName AS kro_name, iName AS iro_name, LV AS level, HP AS hp, ';
$col .= 'EXP AS base_exp, JEXP as job_exp, Range1 AS range1, Range2 AS range2, Range3 AS range3, ';
$col .= 'DEF AS defense, MDEF AS magic_defense, DEF AS defense, MDEF AS magic_defense, ';
if($server->isRenewal) {
	$col .= '(LV+STR+FLOOR(ATK1*8/10)) AS attack1, (LV+STR+FLOOR(ATK1*12/10)) AS attack2, ';
}else{
	$col .= 'ATK1 AS attack1, ATK2 AS attack2, ';
}
$col .= 'STR AS strength, AGI AS agility, VIT AS vitality, `INT` AS intelligence, DEX AS dexterity, LUK AS luck, ';
$col .= 'Scale AS size, Race AS race, (Element%10) AS element_type, (Element/20) AS element_level, Mode AS mode, ';
$col .= 'Speed AS speed, aDelay AS attack_delay, aMotion AS attack_motion, dMotion AS delay_motion, ';
$col .= 'MEXP AS mvp_exp, ';

// Item drops.
$col .= 'Drop1id AS drop1_id, Drop1per AS drop1_chance, ';
$col .= 'Drop2id AS drop2_id, Drop2per AS drop2_chance, ';
$col .= 'Drop3id AS drop3_id, Drop3per AS drop3_chance, ';
$col .= 'Drop4id AS drop4_id, Drop4per AS drop4_chance, ';
$col .= 'Drop5id AS drop5_id, Drop5per AS drop5_chance, ';
$col .= 'Drop6id AS drop6_id, Drop6per AS drop6_chance, ';
$col .= 'Drop7id AS drop7_id, Drop7per AS drop7_chance, ';
$col .= 'Drop8id AS drop8_id, Drop8per AS drop8_chance, ';
$col .= 'Drop9id AS drop9_id, Drop9per AS drop9_chance, ';
$col .= 'DropCardid AS dropcard_id, DropCardper AS dropcard_chance, ';

// MVP drops.
$col .= 'MVP1id AS mvpdrop1_id, MVP1per AS mvpdrop1_chance, ';
$col .= 'MVP2id AS mvpdrop2_id, MVP2per AS mvpdrop2_chance, ';
$col .= 'MVP3id AS mvpdrop3_id, MVP3per AS mvpdrop3_chance ';

$sql  = "SELECT $col FROM $mobDB WHERE ID = ? LIMIT 1";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array($mobID));
$monster = $sth->fetch();

if ($monster) {
	$monster->boss = $monster->mvp_exp;
	
	$monster->base_exp = $monster->base_exp * $server->expRates['Base'] / 100;
	$monster->job_exp  = $monster->job_exp * $server->expRates['Job'] / 100;
	$monster->mvp_exp  = $monster->mvp_exp * $server->expRates['Mvp'] / 100;
	
	$dropIDs = array(
		'drop1'    => $monster->drop1_id,
		'drop2'    => $monster->drop2_id,
		'drop3'    => $monster->drop3_id,
		'drop4'    => $monster->drop4_id,
		'drop5'    => $monster->drop5_id,
		'drop6'    => $monster->drop6_id,
		'drop7'    => $monster->drop7_id,
		'drop8'    => $monster->drop8_id,
		'drop9'    => $monster->drop9_id,
		'dropcard' => $monster->dropcard_id,
		'mvpdrop1' => $monster->mvpdrop1_id,
		'mvpdrop2' => $monster->mvpdrop2_id,
		'mvpdrop3' => $monster->mvpdrop3_id
	);
	
	$sql = "SELECT id, name_japanese, type FROM $itemDB WHERE id IN (".implode(', ', array_fill(0, count($dropIDs), '?')).")";
	$sth = $server->connection->getStatement($sql);
	$sth->execute(array_values($dropIDs));
	$items = $sth->fetchAll();
	
	$needToSet = array();
	if ($items) {
		foreach ($dropIDs AS $dropField => $dropID) {
			$needToSet[$dropField] = true;
		}
		
		foreach ($items as $item) {
			foreach ($dropIDs AS $dropField => $dropID) {
				if ($needToSet[$dropField] && $dropID == $item->id) {
					$needToSet[$dropField] = false;
					$monster->{$dropField.'_name'} = $item->name_japanese;
					$monster->{$dropField.'_type'} = $item->type;
				}
			}
		}
	}
	
	$itemDrops = array();
	foreach ($needToSet as $dropField => $isset) {
		if ($isset === false) {
			$itemDrops[$dropField] = array(
				'id'     => $monster->{$dropField.'_id'},
				'name'   => $monster->{$dropField.'_name'},
				'chance' => $monster->{$dropField.'_chance'}
			);
			
			if (preg_match('/^dropcard/', $dropField)) {
				$adjust = ($monster->boss) ? $server->dropRates['CardBoss'] : $server->dropRates['Card'];
				$itemDrops[$dropField]['type'] = 'card';
			}
			elseif (preg_match('/^mvpdrop/', $dropField)) {
				$adjust = $server->dropRates['MvpItem'];
				$itemDrops[$dropField]['type'] = 'mvp';
			}
			elseif (preg_match('/^drop/', $dropField)) {
				switch($monster->{$dropField.'_type'}) {
					case 0: // Healing
						$adjust = ($monster->boss) ? $server->dropRates['HealBoss'] : $server->dropRates['Heal'];
						break;
					
					case 2: // Useable
					case 18: // Cash Useable
						$adjust = ($monster->boss) ? $server->dropRates['UseableBoss'] : $server->dropRates['Useable'];
						break;
					
					case 4: // Weapon
					case 5: // Armor
					case 8: // Pet Armor
						$adjust = ($monster->boss) ? $server->dropRates['EquipBoss'] : $server->dropRates['Equip'];
						break;
					
					default: // Common
						$adjust = ($monster->boss) ? $server->dropRates['CommonBoss'] : $server->dropRates['Common'];
						break;
				}
				
				$itemDrops[$dropField]['type'] = 'normal';
			}
			
			$itemDrops[$dropField]['chance'] = $itemDrops[$dropField]['chance'] * $adjust / 10000;

			if ($itemDrops[$dropField]['chance'] > 100) {
				$itemDrops[$dropField]['chance'] = 100;
			}
		}
	}
	
	$sql = "SELECT * FROM $skillDB WHERE mob_id = ?";
	$sth = $server->connection->getStatement($sql);
	$sth->execute(array($mobID));
	$mobSkills = $sth->fetchAll();
}

if($params->get('output')=='json') {
	$json_arr = array();
	if($monster) {
		$json_arr['labels']['monster'] = array(
			'monster_id' => "Monster ID",
			'kro_name' => "kRO Name",
			'iro_name' => "iRO Name",
			'sprite' => "Sprite",
			'custom' => "Custom",
			'size' => "Size",
			'race' => "Race",
			'level' => "Level",
			'element' => "Element",
			'speed' => "Speed",
			'mvp_exp' => "MVP EXP",
			'base_job' => "Base/Job EXP",
			'attack' => "Attack",
			'def_mdef' => "DEF/MDEF",
			'delay' => "Attack Delay",
			'attack_motion' => "Attack Motion",
			'defense_motion' => "Defense Motion",
			'attack_range' => "Attack Range",
			'spell_range' => "Spell Range",
			'vision_range' => "Vision Range",
			'hp_sp' => "HP/SP",
			'monster_mode' => "Monster Mode",
			'monster_stats' => 'Monster Stats',
		);
		$json_arr['labels']['itemDrops'] = array(
			'item_id' => "ID",
			'name' => "Name",
			'chance' => "Drop Chance",
		);
		$json_arr['labels']['mobSkills'] = array(
			'info' => "Name",
			'skill_lvl' => "Level",
			'state' => "State",
			'rate' => "Rate",
			'casttime' => "Cast Time",
			'delay' =>  "Delay",
			'cancellable' => "Cancellable",
			'target' => "Target",
			'condition' => "Condition",
			'value' => "Value",
		);
		$json_arr['monster']['monster_id'] = $monster->monster_id;
		$json_arr['monster']['kro_name'] = $monster->kro_name;
		$json_arr['monster']['iro_name'] = $monster->iro_name;
		$json_arr['monster']['sprite'] = $monster->sprite;
		$json_arr['monster']['custom'] = (preg_match('/mob_db2$/', $monster->origin_table)) ? "Yes" : "No";
		$json_arr['monster']['size'] = ($size=Flux::monsterSizeName($monster->size)) ? $size : "Unknown";
		$json_arr['monster']['race'] = ($race=Flux::monsterSizeName($monster->race)) ? $race : "Unknown";
		$json_arr['monster']['level'] = number_format($monster->level);
		$json_arr['monster']['element'] = Flux::elementName($monster->level). " (Lv ".floor($monster->element_level).")";
		$json_arr['monster']['speed'] = number_format($monster->speed);
		$json_arr['monster']['mvp_exp'] = $monster->mvp_exp ? $monster->mvp_exp : 0;
		$json_arr['monster']['base_job'] = number_format($monster->base_exp)."/".number_format($monster->job_exp);
		$json_arr['monster']['attack'] = number_format($monster->attack1).'~'.number_format($monster->attack2);
		$json_arr['monster']['def_mdef'] = number_format($monster->defense)."/".number_format($monster->magic_defense);
		$json_arr['monster']['delay'] = number_format($monster->attack_delay);
		$json_arr['monster']['attack_motion'] = number_format($monster->attack_motion);
		$json_arr['monster']['defense_motion'] = number_format($monster->defense_motion);
		$json_arr['monster']['attack_range'] = number_format($monster->range1);
		$json_arr['monster']['spell_range'] = number_format($monster->range2);
		$json_arr['monster']['vision_range'] = number_format($monster->range3);
		$json_arr['monster']['hp_sp'] = number_format($monster->hp)."/".number_format($monster->sp);
		$json_arr['monster']['monster_mode'] = $this->monsterMode($monster->mode);
		$json_arr['monster']['monster_stats'] = array (
			'str' => number_format((int)$monster->strength),
			'agi' => number_format((int)$monster->agility),
			'vit' => number_format((int)$monster->vitality),
			'int' => number_format((int)$monster->intelligence),
			'dex' => number_format((int)$monster->dexterity),
			'luk' => number_format((int)$monster->luck),
		);	

		if ($image=$this->monsterImage($monster->monster_id)) {
			$json_arr['monster']['monster_image'] = $image;
		}

		$json_arr['itemDrops'] = $itemDrops;

		foreach ($mobSkills as $skill) {
			$tmp = array(
				'info' => $skill->INFO,
				'skill_lvl' => $skill->SKILL_LV,
				'state' => ucfirst($skill->STATE),
				'rate' => $skill->RATE,
				'casttime' => $skill->CASTTIME,
				'delay' =>  $skill->DELAY,
				'cancellable' => ucfirst($skill->CANCELABLE),
				'target' => ucfirst($skill->TARGET),
				'condition' => $skill->CONDITION.((!is_null($skill->CONDITION_VALUE) && trim($skill->CONDITION_VALUE) !== '') ? "(".$skill->CONDITION_VALUE.")" : ""),
			);
			$json_arr['mobSkills'][] = $tmp;
		}
	}
	echo json_encode($json_arr);
	exit();
}

?>
