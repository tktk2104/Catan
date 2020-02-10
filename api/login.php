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

// 現在のプレイヤー番号
$curPlayerNum = 0;

// 下準備ステート
if ($gameState == 0)
{
    // タイルの初期配置
    $initTileData = array(
        array(1,5,false), array(3,2,false), array(1,6,false),
        array(3,10,false), array(2,9,false), array(5,4,false), array(4,3,false),
        array(2,8,false), array(5,11,false), array(0,0,true), array(3,5,false), array(4,8,false),
        array(4,4,false), array(1,3,false), array(5,6,false), array(1,10,false),
        array(2,11,false), array(3,12,false), array(5,9,false)
    );

    // タイル情報の初期化
    for ($i = 0; $i < count($initTileData); $i++)
    {
        setDB1('update tileData set harvest_type=? ,dice_value=? ,stay_thief=? where tile_id=?', [$initTileData[$i][0], $initTileData[$i][1], $initTileData[$i][2], $i]);
    }

    // 角情報の初期化
    setDB1('update cornerData set use_player=0 ,building_type=0', []);

    // 辺情報の初期化
    setDB1('update sideData set use_player=0', []);

    // プレイヤー情報の初期化
    for ($i = 0; $i < 4; $i++)
    {
        setDB1('update playerData set turn_order=?, score=0, woodCount=14, stoneCount=14, wheatCount=12, sheepCount=12, ironCount=10 where player_id=?', [($i + 1), $i]);
    }

    // プレイヤーの接続人数を初期化する
    setDB1('update gameData set joinPlayerNum=0 where gameData_id=0', []);

    // 現在のターンプレイヤー番号を初期化する
    setDB1('update gameData set curTrunNum=1 where gameData_id=0', []);

    // 直前に出たサイコロの目を初期化する
    setDB1('update gameData set diceNum_1=0 where gameData_id=0', []);
    setDB1('update gameData set diceNum_2=0 where gameData_id=0', []);

    // ステートを更新
    setDB1('update gameData set gameState=1 where gameData_id=0', []);
    $gameState++;
}

// プレイヤー登録ステート
if ($gameState == 1)
{
    // 現在のプレイヤーの数を取得
    $curPlayerNum = getDB1('select joinPlayerNum from gameData where gameData_id=0')['joinPlayerNum'];

    // プレイヤー数を増やす
    $curPlayerNum++;

    // プレイヤー数が上限以内だったら
    if ($curPlayerNum <= $MAX_PLAYER_NUM)
    {
        // データベースを更新
        setDB1('update gameData set joinPlayerNum=? where gameData_id=0', [$curPlayerNum]);
    }

    // プレイヤー人数が上限に達したら
    if ($curPlayerNum == $MAX_PLAYER_NUM)
    {
        // ステートを更新
        setDB1('update gameData set gameState=2 where gameData_id=0', []);
    }
}

// タイル情報を取得
for ($i = 0; $i < 19; $i++)
{
    $tileData[$i][0] = getDB1('select harvest_type from tileData where tile_id=?', [$i])['harvest_type'];
    $tileData[$i][1] = getDB1('select dice_value from tileData where tile_id=?', [$i])['dice_value'];
    $tileData[$i][2] = getDB1('select stay_thief from tileData where tile_id=?', [$i])['stay_thief'];
}

// プレイヤー番号とカタン島の固定情報をJSON形式で返す
echo json_encode([
    'playerNum' => $curPlayerNum,
    'tileData' => $tileData
]);

// デバック太郎
//$fp = fopen('log.txt', 'a');
//flock($fp, LOCK_EX);
//fwrite($fp, print_r($_GET, true));
//flock($fp, LOCK_UN);
//fclose($fp);