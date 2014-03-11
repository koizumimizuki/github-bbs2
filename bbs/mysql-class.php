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

//設定ファイルの読み出し
require	"mysql-class-setting.php";

class MySQL_Connection
{
	//接続状態
	public		$Connection;
	//デバック用メッセージ
	public		$DebugMessage;
	//コネクションハンドル
	private		$my_connection;
	
	
	//コンストラクタで接続
	public	function MySQL_Connection()
	{
		//接続
		$this->my_connection = mysql_connect(CONNECTION_HOST,CONNECTION_USER,CONNECTION_PASS);
		//フラグの初期化
		$this->Connection = FALSE;
		//とりあえず、成功と入れておく。
		$this->DebugMessage = "接続成功！";
		
		if(!$this->my_connection)
		{
			//エラーメッセージの生成
			$this->DebugMessage = "接続に失敗:".mysql_error();
		}else{
			//文字コードの設定
			mysql_query("set names ".MYSQL_CHARSET,$this->my_connection);
			//データベースの選択
			if(mysql_select_db(DB_NAME,$this->my_connection))
			{
				//テーブルの状態を確認。
				//&&使いたいけど、確認タイミングの詳細不明のため
				//２段階である事を明記する。
				if($this->Tabale_Check())
				{
					//要素を確認
					if($this->Element_Check())
					{
						//成功をした場合TRUEを入力
						$this->Connection = TRUE;
					}
				}
			}
			else
			{
				//エラーメッセージの生成
				$this->DebugMessage = "DBの選択に失敗:".mysql_error();
			}
		}
	}

	//テーブルが保持している要素数をチェックする
	//無い要素がある場合は、要素を追加する。
	private function Element_Check()
	{
		//戻り値用Falgの初期化
		$ReturnFalg=TRUE;
		//グローバルからデータを呼び出す
		global $table_elements;
		
		//要素名の一覧を取得する。
		//一覧調べるときに使った関数
		//$this->ShowMysqlResult("desc ".TABLE_NAME);
		
		$result = mysql_query("desc ".TABLE_NAME);
		while($row = mysql_fetch_array($result))
		{
			//要素検索
			foreach($table_elements as $key => $value)
			{
				//デバック用
				//print $row["Field"].":".$table_elements[$key]["NAME"]."<br />\n";
				if(strcmp($row["Field"],$table_elements[$key]["NAME"]) == 0)
				{
					//Flagを立てる
					$table_elements[$key]["EXISTENCE"] = TRUE;
					//ループを抜ける
					break;
				}
			}
		}
		//全ての要素が存在するかチェックする
		foreach($table_elements as $key => $value)
		{
			//存在しない要素がある場合エラーメッセージを生成
			if($table_elements[$key]["EXISTENCE"] == FALSE)
			{
				//カラムを追加する
				//追加に失敗した場合
				if(!mysql_query("alter table ".TABLE_NAME." add ".
					$table_elements[$key]["NAME"]." ".
					$table_elements[$key]["TYPE"]." ".
					$table_elements[$key]["OPTION"]))
				{
					//エラーを設定
					$ReturnFalg=FALSE;
					//エラーメッセージの生成
					$this->DebugMessage = "要素の追加に失敗:".mysql_error();
				}
			}
		}
		
		
		//戻り値を返す
		return $ReturnFalg;
	}
	//テーブルデータの確認
	private	function Tabale_Check()
	{
		//戻り値用Falgの初期化
		$ReturnFalg=FALSE;
		//データの参照
		global $table_elements;
		
		//テーブルの一覧のリソースを取得する。
		$table_list = mysql_list_tables( DB_NAME, $this->my_connection);
		//テーブルの一覧を取得する。
		while($table_name = mysql_fetch_array( $table_list, MYSQL_ASSOC ))
		//一覧を調べて目的のテーブルを探す。
		foreach($table_name as $key => $value)
		{
			//目的のテーブルか否か。
			//PHPのstrcmpはなぜか0を返す点に注意。
			if(strcmp($value,TABLE_NAME)==0)
			{
				//あった場合はフラグを立てる。
				$ReturnFalg=TRUE;
			}
		}
		//無い場合はテーブルを作成
		if(!$ReturnFalg)
		{
			//ループ用フラグ
			$loop_flag = FALSE;
			//文字列の成型
			$transmission_buffer = "create table ".TABLE_NAME."(";
			//要素を全部突っ込んで行く
			foreach($table_elements as $key => $value)
			{
				//ループ用フラグが立っていた場合コロンを挿入
				if($loop_flag == TRUE)
				{
						$transmission_buffer .= ",";
				}
				//名前
				//スペースの挿入忘れずに
				$transmission_buffer .= $table_elements[$key]["NAME"]." ";
				//型
				$transmission_buffer .= $table_elements[$key]["TYPE"]." ";
				//オプション
				$transmission_buffer .= $table_elements[$key]["OPTION"]." ";
				
				//フラグを立てる
				$loop_flag = TRUE;
			}
			$transmission_buffer .=")";
			
			if(mysql_query($transmission_buffer,$this->my_connection))
			{
				//テーブルが作成できた場合はフラグを立てる
				$ReturnFalg=TRUE;
			}
			else
			{
				//エラーメッセージの生成
				$this->DebugMessage = "テーブルの作成に失敗<br />\n"
					."($transmission_buffer)<br />\n"
					.mysql_error();
			}
		}
		
		//戻り値を返す
		return $ReturnFalg;
	}
	
	//切断する、何に使うかは不明。
	public	function Connection_Close()
	{
		//切断
		mysql_close($my_connection);
	}
	
	//コネクションハンドル取得用
	//サブクラスからの参照は可能にしておく
	protected	function Get_Connection()
	{
		return $this->my_connection;
	}
	
	//デバック用、中身調べたいときにどうぞ。
	protected function ShowMysqlResult($command)
	{
		$result = mysql_query($command,$this->my_connection);
		while($row = mysql_fetch_array($result,MYSQL_BOTH))
		{
			print "--------------------<br />\n";
			foreach($row as $key => $value)
			{
				print $key.":".$value."<br />\n";
			}
			print "--------------------<br />\n";
		}
	}
}
?>
