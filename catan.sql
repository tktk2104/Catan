/**
 * カタンDB
 */

/*----------------------------*/
/* DB作成                     */
/*----------------------------*/
CREATE DATABASE IF NOT EXISTS catan_db;
USE catan_db;

CREATE TABLE IF NOT EXISTS gameData(
	`gameData_id`		integer,	/* ゲームデータID */
	`gameState`    		integer, 	/* 現在のゲームの状態 */
	`joinPlayerNum`    	integer,	/* ゲームの参加人数 */
	`curTrunNum`    	integer,	/* 現在のターンプレイヤー */
	`diceNum_1`    		integer,	/* 直前に出たサイコロの目（1個目） */
	`diceNum_2`    		integer		/* 直前に出たサイコロの目（2個目） */
)
ENGINE=InnoDB   		/* MySQLのエンジンを指定 */
CHARSET=utf8;   		/* 文字コード */
TRUNCATE TABLE gameData;	/* テーブルを初期化 */

/*----------------------------*/
/* テーブルを作成             */
/*----------------------------*/
CREATE TABLE IF NOT EXISTS tileData(
	`tile_id`    		integer,  	/* タイルID */
	`harvest_type`	integer,	/* 収穫される資源の種類（砂漠=0,木=1,レンガ=2,小麦=3,羊=4,鉄鉱石=5） */
	`dice_value`		integer,	/* 対応する出目の種類 */
	`stay_thief`		bit			/* 盗賊が存在するか？ */
)
ENGINE=InnoDB   		/* MySQLのエンジンを指定 */
CHARSET=utf8;   		/* 文字コード */
TRUNCATE TABLE tileData;	/* テーブルを初期化 */


CREATE TABLE IF NOT EXISTS cornerData(
	`corner_id`    		integer,  	/* 角のID */
	`use_player`			integer,	/* 所持しているプレイヤー */
	`building_type`		integer,	/* 建物の種類 */
	`contact_tile_1`		integer,	/* 接しているタイル番号１つめ */
	`contact_tile_2`		integer,	/* 接しているタイル番号２つめ */
	`contact_tile_3`		integer,	/* 接しているタイル番号３つめ */
	`contact_corner_1`	integer,	/* 接している角の番号１つめ */
	`contact_corner_2`	integer,	/* 接している角の番号２つめ */
	`contact_corner_3`	integer,	/* 接している角の番号３つめ */
	`contact_side_1`		integer,	/* 接している辺の番号１つめ */
	`contact_side_2`		integer,	/* 接している辺の番号２つめ */
	`contact_side_3`		integer,	/* 接している辺の番号３つめ */
	`contact_port`		integer	/* 接している港の番号 */
)
ENGINE=InnoDB   		/* MySQLのエンジンを指定 */
CHARSET=utf8;   		/* 文字コード */
TRUNCATE TABLE cornerData;	/* テーブルを初期化 */

CREATE TABLE IF NOT EXISTS sideData(
	`side_id`    			integer,  	/* 辺のID */
	`use_player`			integer,	/* 所持しているプレイヤー */
	`contact_corner_1`	integer,	/* 接している角の番号１つめ */
	`contact_corner_2`	integer	/* 接している角の番号２つめ */
)
ENGINE=InnoDB   		/* MySQLのエンジンを指定 */
CHARSET=utf8;   		/* 文字コード */
TRUNCATE TABLE sideData;	/* テーブルを初期化 */


CREATE TABLE IF NOT EXISTS playerData(
	`player_id`    			integer,  	/* プレイヤーのID */
	`turn_order`				integer,	/* ターンの順番 */
	`score`					integer,	/* スコア */
	`woodCount`				integer,	/* 木材の数 */
	`stoneCount`				integer,	/* レンガの数 */
	`wheatCount`				integer,	/* 小麦の数 */
	`sheepCount`				integer,	/* 羊の数 */
	`ironCount`				integer,	/* 鉄鉱石の数 */
	`use_event_cards`			integer,	/* 使用したイベントカードの種類 */
	`not_use_event_cards`	integer	/* 使用していないイベントカードの種類 */
)
ENGINE=InnoDB   		/* MySQLのエンジンを指定 */
CHARSET=utf8;   		/* 文字コード */
TRUNCATE TABLE playerData;	/* テーブルを初期化 */
