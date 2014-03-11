<?php //SQL接続用クラス
//Written by "Koizumi Mizuki" 2008/06/16

//基本設定
//文字コード
define("MYSQL_CHARSET","utf8");
//ユーザーID
define("CONNECTION_USER","UserID");
//ユーザーパスワード
define("CONNECTION_PASS","PassWord");
//ホスト(基本的にこれでＯＫ
define("CONNECTION_HOST","mysql.example.com");
//DBの名前(DBの作成は予め行っておくこと。)
define("DB_NAME","koizumimizuki_bbs");
//テーブル名
define("TABLE_NAME","threads");

//テーブルに配置するデータ
//追加は常に後ろ側に行う事により整合性を確保する事。
$table_elements = array(
//実際使うときはとりあえず0を入れる
	"THREAD_ID"		=> array("NAME" => "thread_id","TYPE" => "bigint",
		"value" => NULL,"OPTION" => "auto_increment primary key","EXISTENCE" => FALSE),
	"THREAD_TITLE"	=> array("NAME" => "thread_title","TYPE" => "varchar(255)",
		"value" => NULL,"OPTION" => "","EXISTENCE" => FALSE),
	"USER_NAME"		=> array("NAME" => "user_name","TYPE" => "varchar(70)",
		"value" => NULL,"OPTION" => "","EXISTENCE" => FALSE),
	"USER_TRIP"		=> array("NAME" => "user_trip","TYPE" => "varchar(20)",
		"value" => NULL,"OPTION" => "","EXISTENCE" => FALSE),
	"FIRST_TIME"	=> array("NAME" => "first_time","TYPE" => "DATETIME",
		"value" => NULL,"OPTION" => "","EXISTENCE" => FALSE),
	"LAST_TIME"		=> array("NAME" => "last_time","TYPE" => "DATETIME",
		"value" => NULL,"OPTION" => "","EXISTENCE" => FALSE),
	"DELETE_FLAG"	=> array("NAME" => "delete_flag","TYPE" => "INT",
		"value" => NULL,"OPTION" => "","EXISTENCE" => FALSE)
	);
	
//トリップ、投稿時間、最終更新時間、DeleteFlg
?>