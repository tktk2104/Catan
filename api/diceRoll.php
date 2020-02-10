<?php
require_once('../common.php');

// 出目を出す
$diceNum[0] = rand(1, 6);
$diceNum[1] = rand(1, 6);

// タイル情報を取得
for ($i = 0; $i < 19; $i++)
{
    $tileData[$i][0] = getDB1('select harvest_type from tileData where tile_id=?', [$i])['harvest_type'];
    $tileData[$i][1] = getDB1('select dice_value from tileData where tile_id=?', [$i])['dice_value'];
    $tileData[$i][2] = getDB1('select stay_thief from tileData where tile_id=?', [$i])['stay_thief'];
}

// プレイヤー情報の一部を取得
for ($i = 0; $i < 4; $i++)
{
    $playerData[$i][0] = getDB1('select woodCount from playerData where player_id=?', [$i])['woodCount'];
    $playerData[$i][1] = getDB1('select stoneCount from playerData where player_id=?', [$i])['stoneCount'];
    $playerData[$i][2] = getDB1('select wheatCount from playerData where player_id=?', [$i])['wheatCount'];
    $playerData[$i][3] = getDB1('select sheepCount from playerData where player_id=?', [$i])['sheepCount'];
    $playerData[$i][4] = getDB1('select ironCount from playerData where player_id=?', [$i])['ironCount'];
}

// 角情報の一部を取得
for ($i = 0; $i < 54; $i++)
{
    // 角の所有者番号を取得
    $usePlayer = getDB1('select use_player from cornerData where corner_id=?', [$i])['use_player'];

    // 角の所持者がいなければ何もしない
    if ($usePlayer == 0) continue;

    // 角に面するタイルのデータ
    $cornerTileId = [
        getDB1('select contact_tile_1 from cornerData where corner_id=?', [$i])['contact_tile_1'],
        getDB1('select contact_tile_2 from cornerData where corner_id=?', [$i])['contact_tile_2'],
        getDB1('select contact_tile_3 from cornerData where corner_id=?', [$i])['contact_tile_3']
    ];

    // 角に存在する建物のデータ
    $buildingType= getDB1('select building_type from cornerData where corner_id=?', [$i])['building_type'];

    // 角に面するタイル毎にループ
    for ($j = 0; $j < 3; $j++)
    {
        // タイルが海なら何もしない
        if ($cornerTileId[$j] == -1) continue;

        $diceSum = $diceNum[0] + $diceNum[1];
        // タイルに盗賊が居なく、タイルの数字がダイスの目と一致したら
        if (($tileData[$cornerTileId[$j]][2] == 0) && ($tileData[$cornerTileId[$j]][1] == ($diceNum[0] + $diceNum[1])))
        {
            // 角の所有者にタイルに対応する資源を建物に対応した数だけ与える
            $playerData[($usePlayer - 1)][($tileData[$cornerTileId[$j]][0] - 1)] += $buildingType;
        }
    }
}

// もし出目の合計が７だったら
if (($diceNum[0] + $diceNum[1]) == 7)
{
    for ($i = 0; $i < 4; $i++)
    {
        // プレイヤーの資源の合計
        $playerResourceSum = ($playerData[$i][0] + $playerData[$i][1] + $playerData[$i][2] + $playerData[$i][3] + $playerData[$i][4]);

        // 資源の合計が７より多かったら
        if ($playerResourceSum > 7)
        {
            // 半分になるようにランダムにバーストする
            for ($j = 0; $j < ($playerResourceSum * 0.5); )
            {
                $lostType = rand(0, 4);

                if ($playerData[$i][$lostType] > 0)
                {
                    $playerData[$i][$lostType]--;
                    $j++;
                }
            }
        }
    }
}

// プレイヤー情報を更新
for ($i = 0; $i < 4; $i++)
{
    // プレイヤーの情報を更新
    setDB1('update playerData set woodCount=?, stoneCount=?, wheatCount=?, sheepCount=?, ironCount=? where player_id=?', [
        $playerData[$i][0], 
        $playerData[$i][1], 
        $playerData[$i][2], 
        $playerData[$i][3], 
        $playerData[$i][4], 
        $i
    ]);
}

// 直前に出たサイコロの目を更新
setDB1('update gameData set diceNum_1=? where gameData_id=0', [$diceNum[0]]);
setDB1('update gameData set diceNum_2=? where gameData_id=0', [$diceNum[1]]);

// ステートを更新
setDB1('update gameData set gameState=5 where gameData_id=0', []);

// ダイスの出目とプレイヤー情報をJSON形式で出力
echo json_encode([
    'diceNum'       => $diceNum,
    'playerData'    => $playerData
]);

// デバック太郎
//$fp = fopen('log.txt', 'a');
//flock($fp, LOCK_EX);
//fwrite($fp, print_r($_GET, true));
//flock($fp, LOCK_UN);
//fclose($fp);