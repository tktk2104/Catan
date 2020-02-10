<?php
require_once('../common.php');

$MAX_PLAYER_NUM = 2;

// 現在のゲームステートを取得する
//   0:未初期化
//   1:プレイヤー待ち
//   2:１回目初期配置
//   3:２回目初期配置
//   4:ダイスロール
//   5:ゲームプレイ
$gameState = getDB1('select gameState from gameData where gameData_id=0')['gameState'];

// 入力されたプレイヤー番号を取得
$playerNum = isset($_GET['playerNum']) ? $_GET['playerNum'] : null;

// 現在のターン番号を取得
$curTurnNum = getDB1('select curTrunNum from gameData where gameData_id=0')['curTrunNum'];

// 返されるメッセージ
//   0:待機
//   1:初期家配置
//   2:初期道配置
//   3:ダイスロール
$returnMessage = 0;

// 有効なプレイヤー番号が取得できたら
if ($playerNum != null && $playerNum != 0)
{
    // プレイヤー番号に対応したターン番号を取得する
    $playerId = ($playerNum - 1);
    $playerTurnNum = getDB1('select turn_order from playerData where player_id=?', [$playerId])['turn_order'];

    switch ($gameState){

        // もし１回目初期配置状態なら
        case '2':

            // 現在のターン番号とプレイヤー番号のターン番号が同じだったら「初期家配置」のメッセージを選択する
            if ($curTurnNum == $playerTurnNum) $returnMessage = 1;
            break;

        // もし２回目初期配置状態なら
        case '3':

            // （最大値 - 現在のターン番号 + 1）とプレイヤー番号のターン番号が同じだったら「初期家配置」のメッセージを選択する
            if (($MAX_PLAYER_NUM - $curTurnNum + 1) == $playerTurnNum) $returnMessage = 1;
            break;

        // もしダイスロール状態なら
        case '4':

            // 現在のターン番号とプレイヤー番号のターン番号が同じだったら「ダイスロール」のメッセージを選択する
            if ($curTurnNum == $playerTurnNum) $returnMessage = 3;
            break;

        // もしゲームプレイ状態なら
        case '5':


            break;
    }
}

// 盗賊が居る場所
$thiefPos = 0;

// 盗賊が居る場所を探す
for (; $thiefPos < 19; $thiefPos++)
{
    if (getDB1('select stay_thief from tileData where tile_id=?', [$thiefPos])['stay_thief']) break;
}

// 角情報を取得
for ($i = 0; $i < 54; $i++)
{
    $cornerData[$i][0] = getDB1('select use_player from cornerData where corner_id=?', [$i])['use_player'];
    $cornerData[$i][1] = getDB1('select building_type from cornerData where corner_id=?', [$i])['building_type'];
}

// 辺情報を取得
for ($i = 0; $i < 72; $i++)
{
    $sideData[$i][0] = getDB1('select use_player from sideData where side_id=?', [$i])['use_player'];
}

// プレイヤー情報を取得
for ($i = 0; $i < 4; $i++)
{
    $playerData[$i][0] = getDB1('select turn_order from playerData where player_id=?', [$i])['turn_order'];
    $playerData[$i][1] = getDB1('select score from playerData where player_id=?', [$i])['score'];
    $playerData[$i][2] = getDB1('select woodCount from playerData where player_id=?', [$i])['woodCount'];
    $playerData[$i][3] = getDB1('select stoneCount from playerData where player_id=?', [$i])['stoneCount'];
    $playerData[$i][4] = getDB1('select wheatCount from playerData where player_id=?', [$i])['wheatCount'];
    $playerData[$i][5] = getDB1('select sheepCount from playerData where player_id=?', [$i])['sheepCount'];
    $playerData[$i][6] = getDB1('select ironCount from playerData where player_id=?', [$i])['ironCount'];
}

// 直前に出たサイコロの目を取得
$diceNum[0] = getDB1('select diceNum_1 from gameData where gameData_id=0')['diceNum_1'];
$diceNum[1] = getDB1('select diceNum_2 from gameData where gameData_id=0')['diceNum_2'];

if ($gameState <= '3') $curTurnNum = 0;

// 返すメッセージと取得したカタン島の情報をJSON形式で出力
echo json_encode([
    'returnMessage' => $returnMessage,
    'thiefPos'      => $thiefPos,
    'cornerData'    => $cornerData,
    'sideData'      => $sideData,
    'playerData'    => $playerData,
    'diceNum'       => $diceNum,
    'curTurnNum'    => $curTurnNum
]);

// デバック太郎
//$fp = fopen('log.txt', 'a');
//flock($fp, LOCK_EX);
//fwrite($fp, print_r($_GET, true));
//flock($fp, LOCK_UN);
//fclose($fp);