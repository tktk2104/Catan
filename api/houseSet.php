<?php
require_once('../common.php');

// 入力されたプレイヤー番号を取得
$playerNum = isset($_GET['playerNum']) ? $_GET['playerNum'] : null;

// エラーメッセージ
//    0:エラーなし
//    1:資源が足りません
//    2:道を置けない角です
$errorMessage = 0;

// プレイヤーの情報を取得
$playerWoodCount  = getDB1('select woodCount from playerData where player_id=?',  [($playerNum - 1)])['woodCount'];
$playerStoneCount = getDB1('select stoneCount from playerData where player_id=?', [($playerNum - 1)])['stoneCount'];
$playerWheatCount = getDB1('select wheatCount from playerData where player_id=?', [($playerNum - 1)])['wheatCount'];
$playerSheepCount = getDB1('select sheepCount from playerData where player_id=?', [($playerNum - 1)])['sheepCount'];

if ($playerWoodCount < 1 || $playerStoneCount < 1 || $playerWheatCount < 1 || $playerSheepCount < 1)
{
    $errorMessage = 1;
}
else
{
    // 入力された角の番号を取得
    $cornerNum = isset($_GET['cornerNum']) ? $_GET['cornerNum'] : null;

    // 入力された角の情報
    $cornerUsePlayer        = getDB1('select use_player from cornerData where corner_id=?', [$cornerNum])['use_player'];
    $cornerContactCorner[0] = getDB1('select contact_corner_1 from cornerData where corner_id=?', [$cornerNum])['contact_corner_1'];
    $cornerContactCorner[1] = getDB1('select contact_corner_2 from cornerData where corner_id=?', [$cornerNum])['contact_corner_2'];
    $cornerContactCorner[2] = getDB1('select contact_corner_3 from cornerData where corner_id=?', [$cornerNum])['contact_corner_3'];
    $cornerContactSide[0]   = getDB1('select contact_side_1 from cornerData where corner_id=?', [$cornerNum])['contact_side_1'];
    $cornerContactSide[1]   = getDB1('select contact_side_2 from cornerData where corner_id=?', [$cornerNum])['contact_side_2'];
    $cornerContactSide[2]   = getDB1('select contact_side_3 from cornerData where corner_id=?', [$cornerNum])['contact_side_3'];

    // 入力可能フラグ
    $canSet = true;

    // 角に既に建築物が存在したら入力可能フラグを折る
    if ($cornerUsePlayer != 0) $canSet = false;

    // 隣接する辺に自分の建築物があるか？
    $contactPlayerSideBuilding = false;

    for ($i = 0; $i < 3; $i++)
    {
        // つながっている角が存在したら
        if ($cornerContactCorner[$i] != -1)
        {
            // つながっている角の所有者番号
            $cornerContactCornerUsePlayer = getDB1('select use_player from cornerData where corner_id=?', [$cornerContactCorner[$i]])['use_player'];

            // つながっている角に既に建築物が存在したら入力可能フラグを折る
            if ($cornerContactCornerUsePlayer != 0) $canSet = false;
        }

        // つながっている辺が存在したら
        if ($cornerContactSide[$i] != -1)
        {
            // つながっている辺の所有者番号
            $cornerContactSideUsePlayer = getDB1('select use_player from sideData where side_id=?', [$cornerContactSide[$i]])['use_player'];

            // つながっている辺に既に自分の街道が存在したら「$contactPlayerSideBuilding」を立てる
            if ($cornerContactSideUsePlayer == $playerNum) $contactPlayerSideBuilding = true;
        }
    }
    // 隣接する辺に自分の道が無ければ入力可能フラグを折る
    if (!$contactPlayerSideBuilding)  $canSet = false;

    // 入力可能フラグが立っていたら
    if ($canSet)
    {
        // 角の情報を更新する
        setDB1('update cornerData set use_player=? ,building_type=1 where corner_id=?', [$playerNum, $cornerNum]);

        // プレイヤーの情報を更新
        setDB1('update playerData set woodCount=?, stoneCount=?, wheatCount=?, sheepCount=? where player_id=?', [
            --$playerWoodCount,
            --$playerStoneCount,
            --$playerWheatCount,
            --$playerSheepCount,
            ($playerNum - 1)
        ]);
    }
    else $errorMessage = 2;
}

// 家を置けたかの情報をJSON形式で出力
echo json_encode([
    'errorMessage'      => $errorMessage,
    'playerWoodCount'   => $playerWoodCount,
    'playerStoneCount'  => $playerStoneCount,
    'playerWheatCount'  => $playerWheatCount,
    'playerSheepCount'  => $playerSheepCount
]);

//// デバック太郎
//$fp = fopen('log.txt', 'a');
//flock($fp, LOCK_EX);
//fwrite($fp, print_r($_GET, true));
//flock($fp, LOCK_UN);
//fclose($fp);