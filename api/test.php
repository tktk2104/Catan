<?php
require_once('../common.php');

for ($count = 0; $count < 19; $count++)
{
    $tileData[$count][0] = getDB1('select harvest_type from tileData where tile_id=?', [$count])['harvest_type'];
    $tileData[$count][1] = getDB1('select dice_value from tileData where tile_id=?', [$count])['dice_value'];
    $tileData[$count][2] = getDB1('select stay_thief from tileData where tile_id=?', [$count])['stay_thief'];
}

for ($count = 0; $count < 54; $count++)
{
    $cornerData[$count][0] = getDB1('select use_player from cornerData where corner_id=?', [$count])['use_player'];
    $cornerData[$count][1] = getDB1('select building_type from cornerData where corner_id=?', [$count])['building_type'];
    $cornerData[$count][2] = [
        getDB1('select contact_tile_1 from cornerData where corner_id=?', [$count])['contact_tile_1'],
        getDB1('select contact_tile_2 from cornerData where corner_id=?', [$count])['contact_tile_2'],
        getDB1('select contact_tile_3 from cornerData where corner_id=?', [$count])['contact_tile_3']
    ];
    $cornerData[$count][3] = [
        getDB1('select contact_corner_1 from cornerData where corner_id=?', [$count])['contact_corner_1'],
        getDB1('select contact_corner_2 from cornerData where corner_id=?', [$count])['contact_corner_2'],
        getDB1('select contact_corner_3 from cornerData where corner_id=?', [$count])['contact_corner_3']
    ];
    $cornerData[$count][4] = [
        getDB1('select contact_side_1 from cornerData where corner_id=?', [$count])['contact_side_1'],
        getDB1('select contact_side_2 from cornerData where corner_id=?', [$count])['contact_side_2'],
        getDB1('select contact_side_3 from cornerData where corner_id=?', [$count])['contact_side_3']
    ];
    $cornerData[$count][5] = getDB1('select contact_port from cornerData where corner_id=?', [$count])['contact_port'];
}

for ($count = 0; $count < 72; $count++)
{
    $sideData[$count][0] = getDB1('select use_player from sideData where side_id=?', [$count])['use_player'];
    $sideData[$count][1] = [
        getDB1('select contact_corner_1 from sideData where side_id=?', [$count])['contact_corner_1'],
        getDB1('select contact_corner_2 from sideData where side_id=?', [$count])['contact_corner_2']
    ];
}

for ($count = 0; $count < 4; $count++)
{
    $playerData[$count][0] = getDB1('select turn_order from playerData where player_id=?', [$count])['turn_order'];
    $playerData[$count][1] = getDB1('select score from playerData where player_id=?', [$count])['score'];
    $playerData[$count][2] = getDB1('select woodCount from playerData where player_id=?', [$count])['woodCount'];
    $playerData[$count][3] = getDB1('select stoneCount from playerData where player_id=?', [$count])['stoneCount'];
    $playerData[$count][4] = getDB1('select wheatCount from playerData where player_id=?', [$count])['wheatCount'];
    $playerData[$count][5] = getDB1('select sheepCount from playerData where player_id=?', [$count])['sheepCount'];
    $playerData[$count][6] = getDB1('select ironCount from playerData where player_id=?', [$count])['ironCount'];
}

$param = [
    'tileData' => $tileData,
    'cornerData' => $cornerData,
    'sideData' => $sideData,
    'playerData' => $playerData
];

echo json_encode($param);