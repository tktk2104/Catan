<?php
require_once('../common.php');

// 入力されたプレイヤー番号を取得
$playerNum = isset($_GET['playerNum']) ? $_GET['playerNum'] : null;

// エラーメッセージ
//    0:エラーなし
//    1:資源が足りません
//    2:都市を置けない角です
$errorMessage = 0;

// プレイヤーの情報を取得
$playerWheatCount = getDB1('select wheatCount from playerData where player_id=?', [($playerNum - 1)])['wheatCount'];
$playerIronCount  = getDB1('select ironCount from playerData where player_id=?', [($playerNum - 1)])['ironCount'];

if ($playerWheatCount < 2 || $playerIronCount < 3)
{
    $errorMessage = 1;
}
else
{
    // 入力された角の番号を取得
    $cornerNum = isset($_GET['cornerNum']) ? $_GET['cornerNum'] : null;

    // 入力された角の情報
    $cornerUsePlayer        = getDB1('select use_player from cornerData where corner_id=?', [$cornerNum])['use_player'];
    $cornerBuildingType     = getDB1('select building_type from cornerData where corner_id=?', [$cornerNum])['building_type'];

    // 入力可能フラグ
    $canSet = true;

    // 入力された角が既に自分の角で、そこに開拓地が立っていたら
    if (($cornerUsePlayer == $playerNum) && ($cornerBuildingType == 1))
    {
        // 角の情報を更新する
        setDB1('update cornerData set use_player=? ,building_type=2 where corner_id=?', [$playerNum, $cornerNum]);

        $playerWheatCount -= 2;
        $playerIronCount -= 3;

        // プレイヤーの情報を更新
        setDB1('update playerData set wheatCount=?, ironCount=? where player_id=?', [
            $playerWheatCount,
            $ironCount,
            ($playerNum - 1)
        ]);
    }
    else $errorMessage = 2;
}

// 家を置けたかの情報をJSON形式で出力
echo json_encode([
    'errorMessage'      => $errorMessage,
    'playerWheatCount'  => $playerWheatCount,
    'playerIronCount'   => $playerIronCount
]);

//// デバック太郎
//$fp = fopen('log.txt', 'a');
//flock($fp, LOCK_EX);
//fwrite($fp, print_r($_GET, true));
//flock($fp, LOCK_UN);
//fclose($fp);