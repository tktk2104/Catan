<?php
require_once('../common.php');

// 入力されたプレイヤー番号を取得
$playerNum = isset($_GET['playerNum']) ? $_GET['playerNum'] : null;

// エラーメッセージ
//    0:エラーなし
//    1:資源が足りません
//    2:道を置けない辺です
$errorMessage = 0;

// プレイヤーの情報を取得
$playerWoodCount  = getDB1('select woodCount from playerData where player_id=?',  [($playerNum - 1)])['woodCount'];
$playerStoneCount = getDB1('select stoneCount from playerData where player_id=?', [($playerNum - 1)])['stoneCount'];

if ($playerWoodCount < 1 || $playerStoneCount < 1)
{
    $errorMessage = 1;
}
else
{
    // 入力された辺の番号を取得
    $sideNum = isset($_GET['sideNum']) ? $_GET['sideNum'] : null;

    // 入力された辺の情報
    $sideUsePlayer        = getDB1('select use_player from sideData where side_id=?', [$sideNum])['use_player'];
    $sideContactCornerId[0] = getDB1('select contact_corner_1 from sideData where side_id=?', [$sideNum])['contact_corner_1'];
    $sideContactCornerId[1] = getDB1('select contact_corner_2 from sideData where side_id=?', [$sideNum])['contact_corner_2'];

    // 入力可能フラグ
    $canSet = true;

    // 辺に既に建築物が存在したら入力可能フラグを折る
    if ($sideUsePlayer != 0) $canSet = false;

    // 隣接する角または辺に自分の建築物があるか？
    $contactPlayerBuilding = false;

    for ($i = 0; $i < 2; $i++)
    {
        // つながっている角が存在したら
        if ($sideContactCornerId[$i] != -1)
        {
            // つながっている角の所有者番号
            $sideContactCornerUsePlayer = getDB1('select use_player from cornerData where corner_id=?', [$sideContactCornerId[$i]])['use_player'];

            // つながっている角に自分の家があれば「$contactPlayerBuilding」を立てる
            if ($sideContactCornerUsePlayer == $playerNum) $contactPlayerBuilding = true;

            // つながってる角につながっている辺の番号
            $sideContactCornerContactSideId[0] = getDB1('select contact_side_1 from cornerData where corner_id=?', [$sideContactCornerId[$i]])['contact_side_1'];
            $sideContactCornerContactSideId[1] = getDB1('select contact_side_2 from cornerData where corner_id=?', [$sideContactCornerId[$i]])['contact_side_2'];
            $sideContactCornerContactSideId[2] = getDB1('select contact_side_3 from cornerData where corner_id=?', [$sideContactCornerId[$i]])['contact_side_3'];

            for ($j = 0; $j < 3; $j++)
            {
                // つながっている辺が存在したら
                if ($sideContactCornerContactSideId[$j] != -1)
                {
                    // つながっている辺の所有者番号
                    $sideContactCornerContactSideUsePlayer = getDB1('select use_player from sideData where side_id=?', [$sideContactCornerContactSideId[$j]])['use_player'];

                    // つながっている辺に自分の道があれば「$contactPlayerBuilding」を立てる
                    if ($sideContactCornerContactSideUsePlayer == $playerNum) $contactPlayerBuilding = true;
                }
            }
        }
    }
    // 隣接する角に自分の家が無ければ入力可能フラグを折る
    if (!$contactPlayerBuilding) $canSet = false;

    // 入力可能フラグが立っていたら
    if ($canSet)
    {
        // 辺の情報を更新する
        setDB1('update sideData set use_player=? where side_id=?', [$playerNum, $sideNum]);

        // プレイヤーの情報を更新
        setDB1('update playerData set woodCount=?, stoneCount=? where player_id=?', [
            --$playerWoodCount,
            --$playerStoneCount,
            ($playerNum - 1)
        ]);
    }
    else $errorMessage = 2;
}

// 道を置けたかの情報をJSON形式で出力
echo json_encode([
    'errorMessage'      => $errorMessage,
    'playerWoodCount'   => $playerWoodCount,
    'playerStoneCount'  => $playerStoneCount
]);

// デバック太郎
//$fp = fopen('log.txt', 'a');
//flock($fp, LOCK_EX);
//fwrite($fp, print_r($_GET, true));
//flock($fp, LOCK_UN);
//fclose($fp);