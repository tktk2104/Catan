<?php
require_once('../common.php');

$MAX_PLAYER_NUM = 2;

// 入力されたプレイヤー番号を取得
$playerNum = isset($_GET['playerNum']) ? $_GET['playerNum'] : null;

// 入力された辺の番号を取得
$sideNum = isset($_GET['sideNum']) ? $_GET['sideNum'] : null;

// 入力された辺の情報
$sideUsePlayer        = getDB1('select use_player from sideData where side_id=?', [$sideNum])['use_player'];
$sideContactCorner[0] = getDB1('select contact_corner_1 from sideData where side_id=?', [$sideNum])['contact_corner_1'];
$sideContactCorner[1] = getDB1('select contact_corner_2 from sideData where side_id=?', [$sideNum])['contact_corner_2'];

// 入力可能フラグ
$canSet = true;

// 辺に既に建築物が存在したら入力可能フラグを折る
if ($sideUsePlayer != 0) $canSet = false;

// 隣接する角に自分の家があるか？
$contactPlayerCornerBuilding = false;

for ($i = 0; $i < 2; $i++)
{
    // つながっている角が存在したら
    if ($sideContactCorner[$i] != -1)
    {
        // つながっている角の所有者番号
        $sideContactCornerUsePlayer = getDB1('select use_player from cornerData where corner_id=?', [$sideContactCorner[$i]])['use_player'];

        // つながっている角に自分の家があれば「$contactPlayerCornerBuilding」を立てる
        if ($sideContactCornerUsePlayer == $playerNum)
        {
            $contactPlayerCornerBuilding = true;
        }
    }
}

// 隣接する角に自分の家が無ければ入力可能フラグを折る
if (!$contactPlayerCornerBuilding) $canSet = false;

// 入力可能フラグが立っていたら
if ($canSet)
{
    // 辺の情報を更新する
    setDB1('update sideData set use_player=? where side_id=?', [$playerNum, $sideNum]);

    // プレイヤーの情報を取得
    $playerData[0] = getDB1('select woodCount from playerData where player_id=?',  [($playerNum - 1)])['woodCount'];
    $playerData[1] = getDB1('select stoneCount from playerData where player_id=?', [($playerNum - 1)])['stoneCount'];

    // プレイヤーの情報を更新
    setDB1('update playerData set woodCount=?, stoneCount=? where player_id=?', [
        $playerData[0] - 1,
        $playerData[1] - 1,
        ($playerNum - 1)
    ]);

    // 現在のゲームステートを取得する
    $gameState = getDB1('select gameState from gameData where gameData_id=0')['gameState'];

    // 現在のターン番号を取得
    $curTurnNum = getDB1('select curTrunNum from gameData where gameData_id=0')['curTrunNum'];

    // もし現在のターン番号が最大値になっていたらステートを更新する
    if ($curTurnNum == $MAX_PLAYER_NUM) setDB1('update gameData set gameState=? where gameData_id=0', [($gameState + 1)]);

    // 現在のターンプレイヤー番号を更新する
    $curTurnNum++;
    if ($curTurnNum > $MAX_PLAYER_NUM) $curTurnNum = 1;
    setDB1('update gameData set curTrunNum=? where gameData_id=0', [$curTurnNum]);
}

// 道を置けたかの情報をJSON形式で出力
echo json_encode([
    'setComplete' => $canSet
]);

// デバック太郎
//$fp = fopen('log.txt', 'a');
//flock($fp, LOCK_EX);
//fwrite($fp, print_r($_GET, true));
//flock($fp, LOCK_UN);
//fclose($fp);