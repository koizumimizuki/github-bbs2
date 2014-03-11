<?php //SQL接続用クラス
//Written by "Koizumi Mizuki" 2008/06/15

//設定はmysql-class-setting.phpで行っている。

//隠蔽について
//基本的にこのクラスをセットするディレクトリを変更する事
//FTP時の暗号化などで対処する事。

//MySQL_Connection class説明

//public
//$Connection
//	接続状態
//$DebugMessage
//	デバック用メッセージ
//コンストラクタ
//	接続を行う
//	テーブル、カラムの有無をチェックする
//	テーブル及びカラムが無い場合は作成する
//	TRUE or FALSEで接続の成功か失敗かを返す
//	直後$DebugMessageには、詳細メッセージが格納されている。
//	接続に失敗しても中断しないので、
//	$Connectionを利用して中断処理を入れてください。
//Connection_Close
//	接続を中止する。
//	明示的に切断する必要がある場合に使用してください。
//	使わなくてもスクリプト終了時にクローズされます。

//protected
//Get_Connection
//	コネクションハンドルを返す(サブクラス用)
//ShowMysqlResult($command)
//	デバック用、MYSQLから返される中身調べたいときにどうぞ。

//親クラスの読み出し
require	"mysql-class.php";

class MySQL_Connection_Child extends MySQL_Connection
{
	public function MySQL_Connection_Child()
	{
		//親クラスのコンストラクタを呼び出す
		parent::__construct();
	}
	
	//データの書き込み
	//PHPはマルチバイト文字を含む引数がある場合はオーバーライドできないそうです。
	public function WriteData(&$id,&$title,&$name,&$trip,&$time_now,&$mes)
	{
		//戻り値の初期化
		$ret = FALSE;
		//書き込み処理
		if(mysql_query("insert into ".TABLE_NAME."\n values(0,\n'".
			//タイトル
			$title."',\n'".
			//名前
			$name."',\n'".
			//トリップ
			$trip."',\n'".
			//現在時刻
			$time_now."',\n'".
			//最終更新時刻は現在時間
			$time_now."',\n'".
			//削除フラグはとりあえず0を代入
			"0"."')",parent::Get_Connection()))
		{
			//IDを入力、ラグは気にする必要無し
			if($id = mysql_insert_id())
			{
				//書き込み成功
				$ret = TRUE;
			}
			else
			{
				$mes ="データの取得に失敗:".
					mysql_error();
			}
		}
		else
		{
			//書き込み失敗
			$mes ="データの追加に失敗:".
				mysql_error();
		}
		
		return $ret;
	}
	
	//データの読み込み
	//引数は初期位置
	//削除無視
	public function ReadData_notDelete($StartPosition,&$files_name,$max)
	{
		//全体データの呼び出し
		global $table_elements;
		//戻り値の初期化
		$ret = FALSE;
		//全データ数が$max以上ならば、
		//$StartPosition * $max 件分、マイナスして、なおかつ、その数が
		//$max未満ならば、差分を加算する。
		if($result = mysql_query(
			"select ".$table_elements["THREAD_ID"]["NAME"].
			" from ".TABLE_NAME.
			" order by ".$table_elements["LAST_TIME"]["NAME"].
			" desc limit ".$StartPosition.",".$max,parent::Get_Connection()))
		{
			//成功flgを立てる
			$ret = TRUE;
			//ループ用変数を初期化
			$roop=0;
			while($row = mysql_fetch_array($result,MYSQL_BOTH))
			{
				//ファイルIDを代入
				$files_name[$roop] = $row[$table_elements["THREAD_ID"]["NAME"]];
				//カウンターに+1(インクリメントは使わないほうが吉
				$roop = $roop + 1;
			}
		}
		
		return $ret;
	}
	//データの読み込み
	//引数は初期位置
	public function ReadData($StartPosition,&$files_name,$max)
	{
		//全体データの呼び出し
		global $table_elements;
		//戻り値の初期化
		$ret = FALSE;
		//全データ数が$max以上ならば、
		//$StartPosition * $max 件分、マイナスして、なおかつ、その数が
		//$max未満ならば、差分を加算する。
		if($result = mysql_query(
			"select ".$table_elements["THREAD_ID"]["NAME"].
			" from ".TABLE_NAME.
			" where ".$table_elements["DELETE_FLAG"]["NAME"]." = 0".
			" order by ".$table_elements["LAST_TIME"]["NAME"].
			" desc limit ".$StartPosition.",".$max,parent::Get_Connection()))
		{
			//成功flgを立てる
			$ret = TRUE;
			//ループ用変数を初期化
			$roop=0;
			while($row = mysql_fetch_array($result,MYSQL_BOTH))
			{
				//ファイルIDを代入
				$files_name[$roop] = $row[$table_elements["THREAD_ID"]["NAME"]];
				//カウンターに+1(インクリメントは使わないほうが吉
				$roop = $roop + 1;
			}
		}
		
		return $ret;
	}
	
	//タイムスタンプの更新
	public function RefreshTime($Target,$Time,&$mes)
	{
		//全体データの呼び出し
		global $table_elements;
		//戻り値の初期化
		$ret = TRUE;
		//データの更新
		if(!mysql_query(
			"update ".TABLE_NAME.
			" set ".$table_elements["LAST_TIME"]["NAME"].'=\''.$Time.'\''.
			" where ".$table_elements["THREAD_ID"]["NAME"]."=".$Target
			,parent::Get_Connection()))
		{
			//書き込み失敗
			$ret = FALSE;
			$mes ="データの更新に失敗:".
				mysql_error();
		}
		
		return $ret;
	}
	
	//タイムスタンプの更新
	public function DeleteThread($Target,$value,&$mes)
	{
		//全体データの呼び出し
		global $table_elements;
		//戻り値の初期化
		$ret = TRUE;
		//データの更新
		if(!mysql_query(
			"update ".TABLE_NAME.
			" set ".$table_elements["DELETE_FLAG"]["NAME"].'= '.$value.
			" where ".$table_elements["THREAD_ID"]["NAME"]."=".$Target
			,parent::Get_Connection()))
		{
			//書き込み失敗
			$ret = FALSE;
			$mes ="データの更新に失敗:".
				mysql_error();
		}
		
		return $ret;
	}
}

?>
