<?php
require_once('../common.php');

$MAX_PLAYER_NUM = 2;

// 入力されたプレイヤー番号を取得
$playerNum = isset($_GET['playerNum']) ? $_GET['playerNum'] : null;

// ステートを更新
setDB1('update gameData set gameState=4 where gameData_id=0');

// 現在のターン番号を取得
$curTurnNum = getDB1('select curTrunNum from gameData where gameData_id=0')['curTrunNum'];

// 現在のターンプレイヤー番号を更新して処理を終了する
if (++$curTurnNum > $MAX_PLAYER_NUM) $curTurnNum = 1;
setDB1('update gameData set curTrunNum=? where gameData_id=0', [$curTurnNum]);

echo json_encode(['changeTurn' => true]);