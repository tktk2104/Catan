<?php
require_once('../common.php');

// 入力されたプレイヤー番号を取得
$playerNum = isset($_GET['playerNum']) ? $_GET['playerNum'] : null;

// 入力された角の番号を取得
$cornerNum = isset($_GET['cornerNum']) ? $_GET['cornerNum'] : null;

// 入力された角の情報
$cornerUsePlayer        = getDB1('select use_player from cornerData where corner_id=?', [$cornerNum])['use_player'];
$cornerContactCorner[0] = getDB1('select contact_corner_1 from cornerData where corner_id=?', [$cornerNum])['contact_corner_1'];
$cornerContactCorner[1] = getDB1('select contact_corner_2 from cornerData where corner_id=?', [$cornerNum])['contact_corner_2'];
$cornerContactCorner[2] = getDB1('select contact_corner_3 from cornerData where corner_id=?', [$cornerNum])['contact_corner_3'];

// 入力可能フラグ
$canSet = true;

// 角に既に建築物が存在したら入力可能フラグを折る
if ($cornerUsePlayer != 0) $canSet = false;

for ($i = 0; $i < 3; $i++)
{
    // つながっている角が存在したら
    if ($cornerContactCorner[$i] != -1)
    {
        // つながっている角の所有者番号
        $cornerContactCornerUsePlayer = getDB1('select use_player from cornerData where corner_id=?', [$cornerContactCorner[$i]])['use_player'];

        // つながっている角に既に建築物が存在したら入力可能フラグを折る
        if ($cornerContactCornerUsePlayer != 0)
        {
            $canSet = false;
            break;
        }
    }
}

// プレイヤーの情報を取得
$playerWoodCount  = getDB1('select woodCount from playerData where player_id=?',  [($playerNum - 1)])['woodCount'];
$playerStoneCount = getDB1('select stoneCount from playerData where player_id=?', [($playerNum - 1)])['stoneCount'];
$playerWheatCount = getDB1('select wheatCount from playerData where player_id=?', [($playerNum - 1)])['wheatCount'];
$playerSheepCount = getDB1('select sheepCount from playerData where player_id=?', [($playerNum - 1)])['sheepCount'];
$playerIronCount  = getDB1('select ironCount from playerData where player_id=?', [($playerNum - 1)])['ironCount'];

// 入力可能フラグが立っていたら
if ($canSet)
{
    // 現在のゲームステートを取得する
    //   0:未初期化
    //   1:プレイヤー待ち
    //   2:１回目初期配置
    //   3:２回目初期配置
    //   4:ダイスロール
    //   5:ゲームプレイ
    $gameState = getDB1('select gameState from gameData where gameData_id=0')['gameState'];

    // もし２回目初期配置だったら
    if ($gameState == 3)
    {
        // 入力した角に隣接するタイル番号
        $cornerContactTileId[0] = getDB1('select contact_tile_1 from cornerData where corner_id=?', [$cornerNum])['contact_tile_1'];
        $cornerContactTileId[1] = getDB1('select contact_tile_2 from cornerData where corner_id=?', [$cornerNum])['contact_tile_2'];
        $cornerContactTileId[2] = getDB1('select contact_tile_3 from cornerData where corner_id=?', [$cornerNum])['contact_tile_3'];

        for ($i = 0; $i < 3; $i++)
        {
            // もし隣接したタイルが海だったら何もしない
            if ($cornerContactTileId[$i] == -1) continue;

            // 隣接するタイルから収穫される種類
            $cornerContactTileHarvestType = getDB1('select harvest_type from tileData where tile_id=?', [$cornerContactTileId[$i]])['harvest_type'];

            // その種類に応じて初期資源を与える
            switch ($cornerContactTileHarvestType)
            {
                case '1': $playerWoodCount++;   break;
                case '2': $playerStoneCount++;  break;
                case '3': $playerWheatCount++;  break;
                case '4': $playerSheepCount++;  break;
                case '5': $playerIronCount++;   break;
            }
        }
    }

    // 角の情報を更新する
    setDB1('update cornerData set use_player=? ,building_type=1 where corner_id=?', [$playerNum, $cornerNum]);

    // プレイヤーの情報を更新
    setDB1('update playerData set woodCount=?, stoneCount=?, wheatCount=?, sheepCount=?, ironCount=? where player_id=?', [
        --$playerWoodCount,
        --$playerStoneCount,
        --$playerWheatCount,
        --$playerSheepCount,
        $playerIronCount,
        ($playerNum - 1)
    ]);
}

// 家を置けたかの情報をJSON形式で出力
echo json_encode([
    'setComplete'       => $canSet,
    'playerWoodCount'   => $playerWoodCount,
    'playerStoneCount'  => $playerStoneCount,
    'playerWheatCount'  => $playerWheatCount,
    'playerSheepCount'  => $playerSheepCount,
    'playerIronCount'   => $playerIronCount
]);

//// デバック太郎
//$fp = fopen('log.txt', 'a');
//flock($fp, LOCK_EX);
//fwrite($fp, print_r($_GET, true));
//flock($fp, LOCK_UN);
//fclose($fp);