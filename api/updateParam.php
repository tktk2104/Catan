<?php
require_once('../common.php');

$inTileData     = isset($_POST['inTileData'])    ? $_POST['inTileData']   : null;

$inCornerData   = isset($_POST['inCornerData'])  ? $_POST['inCornerData'] : null;
$inSideData     = isset($_POST['inSideData'])    ? $_POST['inSideData']   : null;
$inPlayerData   = isset($_POST['inPlayerData'])  ? $_POST['inPlayerData'] : null;

for ($count = 0; $count < 19; $count++)
{
    $fp = fopen('log.txt', 'a');
    flock($fp, LOCK_EX);
    fwrite($fp, print_r($_POST, true));
    flock($fp, LOCK_UN);
    fclose($fp);

    break;

    setDB1('update tileData set harvest_type=? ,dice_value=? ,stay_thief=? where tile_id=?', [$inTileData[$count][0], $inTileData[$count][1], $inTileData[$count][2], $count]);
}

$param = [
    'end' => "1"
];
echo json_encode($param);