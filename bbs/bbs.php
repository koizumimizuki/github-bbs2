<?php	//PHPの初期化処理
//Written by "Koizumi Mizuki" 2008/06/13

//定数の宣言
//このプログラムのパス
define("THIS_CGI","/bbs");
//このサイトのURL
define("THIS_URL","soreigai.jp");
//アイコンが入っているディレクトリのパス
define("ICON_DIRECTORY","../icon/");
//dataファイルを入れるディレクトリ
//これに関しては相対パスで書く事
//予め、パーミッションは707で作成しておく事
define("DATA_DIRECTORY","data/");
//データファイルのパーミッション
define("DATA_FILE_PERMISSTION",0606);
//アイコン一覧表示用
define("ICONS_PRINT","icons.php");
//アイコン一覧表示用
define("ICONS_PRINT","icons.php");

//強制削除キー
//0文字の場合設定しない
//define("ADMIN_DELETE_KEY","");
define("ADMIN_DELETE_KEY","admin_delete_from_mizuki");

//文字コード
define("TEXT_CODE","UTF-8");

//レスの上限(親スレッド含む)
define("RES_MAX",100);
//スレッドの読み込み規定値
define("READ_THREAD_MASS",5);
//レスの読み込み規制値
define("READ_RES_MASS",4);

//フォーム用
define("FORM_NAME","name");
define("FORM_TITLE","title");
define("FORM_ICON","icon");
define("FORM_COLOR","color");
define("FORM_COMMENT","comment");
define("FORM_HIDEN_ACTION","action");
define("FORM_HIDEN_ACTION_WRITE","write");
define("FORM_HIDEN_ACTION_READ","read");
define("FORM_HIDEN_ACTION_RES","res");
define("FORM_HIDEN_THREAD_ID","thread_id");
define("FORM_HIDEN_RES_ID","res_id");
define("FORM_HIDEN_POSITION","position");
define("FORM_DELETE_KEY","delete_key");
//HTMLフォーム用ではないけれど、、、
define("FORM_SENT_TIME","sent_time");
define("FORM_TRIP","trip");
define("FORM_IP","ip");
define("FORM_DELETE_FLAG","delete_flag");

//入力用
//DataBaseの方の設定も弄る事を忘れずに
define("INPUT_MAX_TITLE",200);
define("INPUT_MAX_COMMENT",2000);
define("INPUT_MAX_NAME",60);
define("INPUT_MAX_TRIP",60);
define("INPUT_MAX_DELETE_KEY",60);

//名無し対処
define("DEFAULT_NAME","名無し");
//クッキー名
define("COOKIE_NAME","bbsCookei");
//デファインマクロが使えないので関数で生成
//何のためのデファインだとorz
function CookieName($String)
{
	return $String = COOKIE_NAME."[".$String."]";
}
//クッキーのTTL 現在時間 + Day * Hour * Minute * Second
$CookieTTL = time() +(31 * 24 * 60 * 60);

//アイコンの名前
define("ICONS_NAME","0");
//アイコンのファイル
define("ICONS_FILE","1");
//アイコンの有無(初期値はFALSE)
define("ICONS_FLAG","2");
//色の名前
define("COLORS_NAME","0");
//色の数値
define("COLORS_RGB","1");
//削除した場合の値
define("DELETE_FLAG_TRUE",1);

//グローバル変数
// アイコン用配列を宣言
$icons = array(
	//追加は最後尾に行う事。
	//array("","",""),
	array("めどい",		"medoi.jpg",	FALSE),
	array("優曇華院",	"udonge.jpg",	FALSE),
	array("中国",		"chu-goku.jpg",	FALSE),
	array("ガーゴイル",	"gargoil.jpg",	FALSE),
	array("ことみ",		"kotomi.jpg",	FALSE),
	array("魔理沙",		"marisa.jpg",	FALSE),
	array("ゆめみ",		"yumemi.jpg",	FALSE),
	array("みさき",		"misaki.jpg",	FALSE),
	array("ライム",		"lime02.jpg",	FALSE),
	array("チルノ",		"chiruno.jpg",	FALSE));

//文字色用配列を宣言
$text_color = array(
	//追加は最後尾に行う事。
	//array("",""),
	array("黒",		"#000000"),
	array("赤",		"#990000"),
	array("緑",		"#009900"),
	array("青",		"#000099"),
	array("黄色",	"#cccc00"),
	array("シアン",	"#009999"),
	array("紫",		"#990099"),
	array("灰",		"#666666"));

//デフォルトセット用
$user_name;
$user_icon;
$user_color;
$user_delkey;


//入力データを調べる
//引数でmysqlのオブジェクトを渡す事。
//セッションの使用の宣言をメインファイルで予め行う事
//session_start();//セッションの使用を宣言

class CheckInput
{
	//mysqlクラスを使うため。
	private $mysql_obj;
	//tripクラスを使うため。
	private $trip_obj;
	//ユーザー名
	private $name;
	//トリップ
	private $trip;
	//本文
	private $comment;
	//コメント
	private $title;
	//スレッドID
	private $id;
	//現在時刻
	private $time_now;
	//削除key
	private $delete_key;
	
	//コンストラクタ
	//基本的なデータ読み込み
	public function CheckInput(&$arg_mysql,&$arg_trip)
	{
		//セッションからキー値の入力
		if($_SESSION[FORM_HIDEN_THREAD_ID]>0)
		{
			//セッションが存在する場合
			//値をpostに代入
			$_POST[FORM_HIDEN_THREAD_ID]	=$_SESSION[FORM_HIDEN_THREAD_ID];
			//フラグを変更
			$_POST[FORM_HIDEN_ACTION]		=FORM_HIDEN_ACTION_READ;
			//キーを破棄
			session_unregister(FORM_HIDEN_THREAD_ID);
		}
		
		//オブジェクトのセット
		$this->mysql_obj = &$arg_mysql;
		$this->trip_obj = &$arg_trip;
		
		//現在時刻を取得
		$this->time_now = date("Y/m/d H:i:s");
		
		//actionがセットされている時。
		if(strlen($_POST[FORM_HIDEN_ACTION]) != 0)
		{
			//FORM_HIDEN_ACTION_WRITEの時
			if(strcmp(FORM_HIDEN_ACTION_WRITE,$_POST[FORM_HIDEN_ACTION]) == 0)
			{
				$this->ActionWrite_Base();
			}
			//FORM_HIDEN_ACTION_RESの時
			if(strcmp(FORM_HIDEN_ACTION_RES,$_POST[FORM_HIDEN_ACTION]) == 0)
			{
				$this->ActionWrite_Res();
			}
		}
		//削除キーがセットされている時
		elseif($_POST[FORM_HIDEN_RES_ID]>0)
		{
			//削除関数を呼び出す
			$this->DeleteRes($_POST[FORM_HIDEN_RES_ID]);
		}
	}
	
	//レスの削除を行う
	private function DeleteRes($res_id)
	{
		//削除キー
		$this->delete_key	= htmlspecialchars($_POST[FORM_DELETE_KEY],ENT_QUOTES,TEXT_CODE);
		//スレッドIDをセット
		$this->id = $_POST[FORM_HIDEN_THREAD_ID];
		//スレッドIDをセッションに書き込む
		$_SESSION[FORM_HIDEN_THREAD_ID] = $this->id;
		
		//IDをファイル名に成型
		$file_name = sprintf(DATA_DIRECTORY."%09d.php",$this->id);
		
		//ファイルの有無をチェックして、開けた場合
		if(!file_exists($file_name))
		{
			$this->Error("スレッドファイルが存在しません".$file_name);
		}
		//データの読み込み
		if(!$lines = file($file_name,FILE_TEXT))
		{
			$this->Error("スレッドファイルの読み込みに失敗");
		}
		
		//データの成型
		
		//改行データの削除
		$lines[$res_id] = mb_ereg_replace("\r\n","",$lines[$res_id]);
		$lines[$res_id] = mb_ereg_replace("\n","",$lines[$res_id]);
		$lines[$res_id] = mb_ereg_replace("\r","",$lines[$res_id]);
		
		//データをバッファに入れる
		$split_buf = split('[<]',$lines[$res_id]);
		foreach($split_buf as $buf)
		{
			//バッファをさらに分割
			list($key,$value) = split('[>]',$buf);
			//データを連想配列に割り振る
			$data[$key] = $value;
		}
		
		//スレッドIDをセッションに書き込む
		$_SESSION[FORM_HIDEN_THREAD_ID] = $_POST[FORM_HIDEN_THREAD_ID];
		
		//デリートキーを探してチェックする
		if(	strlen($this->delete_key)	> 0		&&
			(strcmp($data[FORM_DELETE_KEY],$this->delete_key)	== 0	||
			strcmp((string)ADMIN_DELETE_KEY,$this->delete_key)	== 0	))
		{
			//削除フラグを立てる
			$data[FORM_DELETE_FLAG] = (string)DELETE_FLAG_TRUE;
			
			//データの書き換え
			$lines[$res_id] = "";
			foreach($data as $key => $value)
			{
				//Key値が存在する時のみ
				if($key)
				{
					$lines[$res_id] .= "<".$key.">".$value;
				}
			
			}
			//データの終端として改行を入力
			$lines[$res_id] .= "\n";
			
			//データの連結
			$line=join("",$lines);
			
			//ファイルのオープン
			//存在をチェックしてから書き込みモードで開く
			if(!$file_handle = fopen($file_name,"w"))
			{
				$this->Error("スレッドファイルのオープンに失敗");
			}
			//ファイルのロック
			if(!flock($file_handle, LOCK_EX))
			{
				$this->Error("スレッドファイルのロックに失敗");
			}
			
			//データの書き込み
			fwrite($file_handle,$line);
			
			//ロックを解除
			flock($file_handle, LOCK_UN);
			
			//ファイルハンドルをクローズ
			fclose($file_handle);
			
			//Topだった場合SQLでスレッドの削除も行う
			if($res_id == 1)
			{
				if(!$this->mysql_obj->DeleteThread($this->id,DELETE_FLAG_TRUE,$mes))
				{
					$this->Error($mes);
				}
				//スレッドを削除した場合はセッションをクリア
				$_SESSION[FORM_HIDEN_THREAD_ID] = "";
			}
		}
		
		//強制リロード(クッキー用(負荷？知らんがな。
		header("Location:".THIS_CGI);
		
	}
	
	//エラー用
	private function Error($mes)
	{
		print "<b>Error</b><br />\n";
		print "<a href=\"http://".THIS_URL.THIS_CGI."/\">Back</a><br />\n";
		die("$mes");
	}
	
	//入力された長さを調べる
	private function CheckLength()
	{
		//エラーメッセージ生成用
		$error_flag =FALSE;
		$error_mes = "書き込みエラー<br />\n";
		
		//ユーザー名
		$this->name			= htmlspecialchars($_POST[FORM_NAME],ENT_QUOTES,TEXT_CODE);
		//本文
		$this->comment		= htmlspecialchars($_POST[FORM_COMMENT],ENT_QUOTES,TEXT_CODE);
		//コメント
		$this->title		= htmlspecialchars($_POST[FORM_TITLE],ENT_QUOTES,TEXT_CODE);
		//削除キー
		$this->delete_key	= htmlspecialchars($_POST[FORM_DELETE_KEY],ENT_QUOTES,TEXT_CODE);
		
		//ユーザー名とトリップを分離
		list($this->name,$this->trip) = split('[#]',$this->name);
		//\記号と改行を置き換える
		//メタ文字の都合ここだけシングルクオートな点に注意
		$this->comment = mb_ereg_replace('\\\\','&#92;',$this->comment);
		$this->comment = mb_ereg_replace("\r\n","\\n",$this->comment);
		$this->comment = mb_ereg_replace("\n","\\n",$this->comment);
		$this->comment = mb_ereg_replace("\r","\\n",$this->comment);
		
		//文字列の長さを確認
		//タイトルはスレッド作成時のみ
		if(	strlen($_POST[FORM_TITLE])									<= 0 &&
			strcmp(FORM_HIDEN_ACTION_WRITE,$_POST[FORM_HIDEN_ACTION])	== 0 )
		{
			$error_flag =TRUE;
			$error_mes .= "タイトルを入力してください！<br />\n";
		}
		elseif(strlen($_POST[FORM_TITLE]) > (int)INPUT_MAX_TITLE)
		{
			$error_flag =TRUE;
			$error_mes .= "タイトルの文字数制限(半角".INPUT_MAX_TITLE."文字)をオーバーしています！<br />\n";
		}
		if(strlen($this->name) <= 0)
		{
			//初期値変更
			$this->name = DEFAULT_NAME;
		}
		elseif(strlen($this->name) > (int)INPUT_MAX_NAME)
		{
			$error_flag =TRUE;
			$error_mes .= "名前の文字数制限(半角".INPUT_MAX_NAME."文字)をオーバーしています！<br />\n";
		}
		if(strlen($this->trip) > (int)INPUT_MAX_TRIP)
		{
			$error_flag =TRUE;
			$error_mes .= "トリップの文字数制限(半角".INPUT_MAX_TRIP."文字)をオーバーしています！<br />\n";
		}
		if(strlen($this->comment) <= 0)
		{
			$error_flag =TRUE;
			$error_mes .= "本文を入力してください！<br />\n";
		}
		elseif(strlen($this->comment) > (int)INPUT_MAX_COMMENT)
		{
			$error_flag =TRUE;
			$error_mes .= "本文の文字数制限(半角".INPUT_MAX_COMMENT."文字)をオーバーしています！<br />\n";
		}
		if(strlen($this->name) > (int)INPUT_MAX_DELETE_KEY)
		{
			$error_flag =TRUE;
			$error_mes .= "削除キーの文字数制限(半角".INPUT_MAX_DELETE_KEY."文字)をオーバーしています！<br />\n";
		}
		
		//エラーメッセージ出力して強制終了
		if($error_flag)
		{
			$this->Error($error_mes);
		}
	}
	//新規スレッドファイルの作成
	private function CreateThreadFile()
	{
		//IDをファイル名に成型
		$file_name = sprintf(DATA_DIRECTORY."%09d.php",$this->id);
		
		//書き込みデータの作成
		if(!touch($file_name))
		{
			$this->Error("スレッドファイルの生成に失敗");
		}
		//パーミッションの変更
		if(!chmod($file_name,DATA_FILE_PERMISSTION))
		{
			$this->Error("スレッドファイルの設定に失敗");
		}
		//ファイルハンドルをオープン
		if(!$file_handle = fopen($file_name,"w"))
		{
			$this->Error("スレッドファイルの書き込みに失敗");
		}
		//データを書き込む
		//フォームの名前を書き込む際にも流用
		fwrite($file_handle,"<?php\n".
			"<".FORM_NAME			.">".$this->name.
			"<".FORM_TITLE			.">".$this->title.
			"<".FORM_COMMENT		.">".$this->comment.
			"<".FORM_TRIP			.">".$this->trip.
			"<".FORM_ICON			.">".$_POST[FORM_ICON].
			"<".FORM_COLOR			.">".$_POST[FORM_COLOR].
			"<".FORM_SENT_TIME		.">".$this->time_now.
			"<".FORM_DELETE_KEY		.">".$_POST[FORM_DELETE_KEY].
			//IPを調べる
			"<".FORM_IP				.">".$_SERVER["REMOTE_ADDR"].
			//フラグはとりあえず0を入れておく
			"<".FORM_DELETE_FLAG	.">"."0".
			"\n?>");
		//ファイルハンドルをクローズ
		fclose($file_handle);
	}
	//スレッドファイルに上書き
	private function WirteThreadFile()
	{
		//スレッドIDをセット
		$this->id = $_POST[FORM_HIDEN_THREAD_ID];
		//IDをファイル名に成型
		$file_name = sprintf(DATA_DIRECTORY."%09d.php",$this->id);
		
		//ファイルの有無をチェックして、開けた場合
		if(!file_exists($file_name))
		{
			$this->Error("スレッドファイルが存在しません".$file_name);
		}
		//データの読み込み
		if(!$lines = file($file_name,FILE_TEXT))
		{
			$this->Error("スレッドファイルの読み込みに失敗");
		}
		//データの成型
		//最終行を調べる
		$line_number = count($lines);
		//書き込めるか調べる
		if($line_number > (RES_MAX + 1))
		{
			$this->Error("書き込みエラー<br />\n".
				"スレッドの上限値を超えています。");
		}
		
		//最終行+1の位置に終端を付加
		$lines[$line_number] = "?>";
		//最終行の位置にデータを代入
		$lines[($line_number - 1)]=
			"<".FORM_NAME			.">".$this->name.
			"<".FORM_COMMENT		.">".$this->comment.
			"<".FORM_TRIP			.">".$this->trip.
			"<".FORM_ICON			.">".$_POST[FORM_ICON].
			"<".FORM_COLOR			.">".$_POST[FORM_COLOR].
			"<".FORM_SENT_TIME		.">".$this->time_now.
			"<".FORM_DELETE_KEY		.">".$_POST[FORM_DELETE_KEY].
			//IPを調べる
			"<".FORM_IP				.">".$_SERVER["REMOTE_ADDR"].
			//フラグはとりあえず0を入れておく
			"<".FORM_DELETE_FLAG	.">"."0".
			"\n";
		
		//データの連結
		$line=join("",$lines);
		
		//ファイルのオープン
		//存在をチェックしてから書き込みモードで開く
		if(!$file_handle = fopen($file_name,"w"))
		{
			$this->Error("スレッドファイルのオープンに失敗");
		}
		//ファイルのロック
		if(!flock($file_handle, LOCK_EX))
		{
			$this->Error("スレッドファイルのロックに失敗");
		}
		
		//データの書き込み
		fwrite($file_handle,$line);
		
		//ロックを解除
		flock($file_handle, LOCK_UN);
		//ファイルハンドルをクローズ
		fclose($file_handle);
		
		//MYSQLのタイムスタンプを更新
		if(!$this->mysql_obj->RefreshTime($this->id,$this->time_now,$mes))
		{
			$this->Error($mes);
		}
	}
	
	//新規スレッド時の処理
	private function ActionWrite_Base()
	{
		//文字列の長さを調べる
		$this->CheckLength();
		//トリップが１文字以上の時、トリップを変換する
		if(strlen($this->trip) > 0)
		{
			$this->trip = $this->trip_obj->CreateTrip($this->trip);
		}
		
		//MySQLにデータを送信
		if(!$this->mysql_obj->WriteData(
			$this->id,
			$this->title,
			$this->name,
			$this->trip,
			$this->time_now,
			$mes))
		{
			$this->Error($mes);
		}
		
		//新規スレッドファイルの作成
		$this->CreateThreadFile();
		
		//クッキーの生成（上書き
		$this->setcookieDefault(CookieName(FORM_NAME),$_POST[FORM_NAME]);
		$this->setcookieDefault(CookieName(FORM_ICON),$_POST[FORM_ICON]);
		$this->setcookieDefault(CookieName(FORM_COLOR),$_POST[FORM_COLOR]);
		$this->setcookieDefault(CookieName(FORM_DELETE_KEY),$_POST[FORM_DELETE_KEY]);
		
		//強制リロード(クッキー用(負荷？知らんがな。
		header("Location:".THIS_CGI);
	}
	
	//レス時の処理
	private function ActionWrite_Res()
	{
		//文字列の長さを調べる
		$this->CheckLength();
		//トリップが１文字以上の時、トリップを変換する
		if(strlen($this->trip) > 0)
		{
			$this->trip = $this->trip_obj->CreateTrip($this->trip);
		}
		
		//データの追記
		$this->WirteThreadFile();
		
		//クッキーの生成（上書き
		$this->setcookieDefault(CookieName(FORM_NAME),$_POST[FORM_NAME]);
		$this->setcookieDefault(CookieName(FORM_ICON),$_POST[FORM_ICON]);
		$this->setcookieDefault(CookieName(FORM_COLOR),$_POST[FORM_COLOR]);
		$this->setcookieDefault(CookieName(FORM_DELETE_KEY),$_POST[FORM_DELETE_KEY]);
		
		//スレッドIDをセッションに書き込む
		$_SESSION[FORM_HIDEN_THREAD_ID] = $_POST[FORM_HIDEN_THREAD_ID];
		//強制リロード(クッキー用(負荷？知らんがな。
		header("Location:".THIS_CGI);
	}
	
	//セットクッキー用サブルーチン
	private function setcookieDefault($CookieNameBuf,$value)
	{
		//クッキーのTTL(グローバルから参照
		global $CookieTTL;
		//クッキーの生成（上書き
		setcookie($CookieNameBuf,$value,$CookieTTL,THIS_CGI,THIS_URL);
	}
}
//クッキーを調べる
class CheckCookie
{
	//コンストラクタでクッキーを読み込む
	public function CheckCookie()
	{
		$GLOBALS['user_name']	= $_COOKIE[COOKIE_NAME][FORM_NAME];
		$GLOBALS['user_icon']	= $_COOKIE[COOKIE_NAME][FORM_ICON];
		$GLOBALS['user_color']	= $_COOKIE[COOKIE_NAME][FORM_COLOR];
		$GLOBALS['user_delkey']	= $_COOKIE[COOKIE_NAME][FORM_DELETE_KEY];
	}
}
//入力フォームの作成
class InputForm
{

	//フォーム用(printで使う都合で変数)
	private $FormName				= FORM_NAME;
	private $FormTitle				= FORM_TITLE;
	private $FormColor				= FORM_COLOR;
	private $FormIcon				= FORM_ICON;
	private $FormComment			= FORM_COMMENT;
	private $FormHidenAction		= FORM_HIDEN_ACTION;
	private $FormHidenActionWrite	= FORM_HIDEN_ACTION_WRITE;
	private $FormHidenActionRead	= FORM_HIDEN_ACTION_READ;
	private $FormHidenActionRes		= FORM_HIDEN_ACTION_RES;
	private $FormHidenThreadID		= FORM_HIDEN_THREAD_ID;
	private $FormDeleteKey			= FORM_DELETE_KEY;
	//アイコン表示ファイル
	private $icons_print_uri		= ICONS_PRINT;
	
	//アイコンの部分の生成
	private function SetIcons()
	{
		//iconデータの参照
		global $icons;
		//クッキーのデフォルトセット用
		global $user_icon;
		
		//アイコンの数分ループ
		for($roop_1=0; $roop_1<count($icons);$roop_1++)
		{
			
			//アイコンの有無をチェックする
			if(file_exists(ICON_DIRECTORY . $icons[$roop_1][ICONS_FILE]))
			{
				//とりあえずフラグを立てる
				$icons[$roop_1][ICONS_FLAG] = TRUE;
				//アイコンの選択部分を生成
				//ダブルクオート内部は変数エスケープシーケンスは受け取るがdefineは不可
				//過去に選択したものをセット
				if($roop_1 == $user_icon)
				{
					print "\t\t\t<option selected=\"selected\" value=\"$roop_1\">".
						$icons[$roop_1][ICONS_NAME]."</option>\n";
				}
				else
				{
					print "\t\t\t<option value=\"$roop_1\">".
						$icons[$roop_1][ICONS_NAME]."</option>\n";
				}
			}
		}
	}
	
	//色部分の生成
	private function SetColors()
	{
		//色データの参照
		global $text_color;
		//クッキーのデフォルトセット用
		global $user_color;
		
		//色数分ループする
		for($roop_1=0;$roop_1<count($text_color);$roop_1++)
		{
			//色の選択部分を生成
			//過去に選択したものをセット
			if($roop_1 == $user_color)
			{
				print "\t\t\t<option selected=\"selected\" value=\"$roop_1\" style=\"color:".
					$text_color[$roop_1][COLORS_RGB]."\">".$text_color[$roop_1][COLORS_NAME]."</option>\n";
			}
			else
			{
				print "\t\t\t<option value=\"$roop_1\" style=\"color:".$text_color[$roop_1][COLORS_RGB]."\">".
					$text_color[$roop_1][COLORS_NAME]."</option>\n";
			}
		}
	}
	
	//文字、名前、アイコン
	//基本入力フォームの作成
	public function CreateForm_Base()
	{
		//クッキーのデフォルトセット用
		global $user_name;
		global $user_delkey;
		
print <<<EndOfHTML
<hr class="bbs">
新規スレッドを立てる
<hr class="bbs">
<form name="main"  method="post" action="">
<input type="hidden" name="$this->FormHidenAction" value="$this->FormHidenActionWrite">
	<table class="bbs">
		<tr>
			<td class="bbs1">お名前</td>
			<td class="bbs2"><INPUT type="text" class="name" name="$this->FormName" value="$user_name"/></td>
		</tr><tr>
			<td class="bbs1">タイトル</td>
			<td class="bbs2"><INPUT type="text" class="title" name="$this->FormTitle" /></td>
		</tr><tr>
			<td class="bbs1">色</td>
			<td class="bbs2"><select name="$this->FormColor" size="1">

EndOfHTML;
		//色部分の生成
		$this->SetColors();
print <<<EndOfHTML
			</select></td>
		</tr><tr>
			<td class="bbs1">アイコン</td>
			<td class="bbs2"><select name="$this->FormIcon" size="1">

EndOfHTML;
		//アイコン部分の生成
		$this->SetIcons();
print <<<EndOfHTML
			</select>
			<a href="$this->icons_print_uri" target="show_icons" onClick="create_display_icons('$this->icons_print_uri')">
			アイコン一覧</a>
			</td>
		</tr><tr>
			<td class="bbs1">削除PASS</td>
			<td class="bbs2">
				<INPUT type="password" class="delete_key" 
				name="$this->FormDeleteKey" value="$user_delkey"/></td>
		</tr><tr>
			<td colspan=2>
				<textarea class="bbs" name="$this->FormComment"></textarea><br />
				<input class="submit" type="submit" value="書き込む" />
			</td>
		</tr>
	</table>
</form>

EndOfHTML;
		
	}
	//文字、名前、アイコン
	//レス用入力フォームの作成
	public function CreateForm_Res()
	{
		//クッキーのデフォルトセット用
		global $user_name;
		global $user_delkey;
		
		//スレッドIDのセット
		$thread_id = $_POST[FORM_HIDEN_THREAD_ID];
		
print <<<EndOfHTML
	<form name="main"  method="post" action="">
		<input type="hidden" name="$this->FormHidenAction" value="$this->FormHidenActionRes">
		<input type="hidden" name="$this->FormHidenThreadID" value="$thread_id">
		<table class="bbs">
			<tr>
				<td class="bbs1">お名前</td>
				<!--バランス取るために苦肉の策としてtitleクラスを使用-->
				<td class="bbs2"><INPUT type="text" class="title" name="$this->FormName" value="$user_name"/></td>
			</tr><tr>
				<td class="bbs1">色</td>
				<td class="bbs2"><select name="$this->FormColor" size="1">

EndOfHTML;
		//色部分の生成
		$this->SetColors();
print <<<EndOfHTML
				</select></td>
			</tr><tr>
				<td class="bbs1">アイコン</td>
				<td class="bbs2"><select name="$this->FormIcon" size="1">

EndOfHTML;
		//アイコン部分の生成
		$this->SetIcons();
print <<<EndOfHTML
				</select>
				<a href="$this->icons_print_uri" target="show_icons" onClick="create_display_icons('$this->icons_print_uri')">
				アイコン一覧</a>
				</td>
			</tr><tr>
				<td class="bbs1">削除PASS</td>
				<td class="bbs2">
					<INPUT type="password" class="delete_key" 
					name="$this->FormDeleteKey" value="$user_delkey"/></td>
			</tr><tr>
				<td colspan=2>
					<textarea class="bbs" name="$this->FormComment"></textarea><br />
					<input class="submit" type="submit" value="書き込む" />
				</td>
			</tr>
		</table>
	</form>

EndOfHTML;
	}

}

//ログの読み出し

class ReadThreads
{
	//mysqlクラスを使うため。
	private $mysql_obj;
	//フォーム用(printで使う都合で変数)
	private $FormName				= FORM_NAME;
	private $FormTitle				= FORM_TITLE;
	private $FormColor				= FORM_COLOR;
	private $FormIcon				= FORM_ICON;
	private $FormComment			= FORM_COMMENT;
	private $FormHidenAction		= FORM_HIDEN_ACTION;
	private $FormHidenActionWrite	= FORM_HIDEN_ACTION_WRITE;
	private $FormHidenActionRead	= FORM_HIDEN_ACTION_READ;
	private $FormHidenActionRes		= FORM_HIDEN_ACTION_RES;
	private $FormHidenThreadID		= FORM_HIDEN_THREAD_ID;
	private $FormHidenResID			= FORM_HIDEN_RES_ID;
	private $FormTrip				= FORM_TRIP;
	private $FormSentTime			= FORM_SENT_TIME;
	private $FormDeleteKey			= FORM_DELETE_KEY;
	//入力フォーム用
	private $obj_InputForm;
	
	//コンストラクタ
	//基本的なデータ読み込み
	public function ReadThreads(&$arg_mysql)
	{
		$this->mysql_obj	= $arg_mysql;
		//フレーム用クラスの呼び出し
		$this->obj_InputForm = new InputForm();
		
		//もっと読むを押した場合
		//GETをPOSTに変換するマジックナンバー（マジックスペル？）を使用
		//define定義の意味が無さ過ぎるorz
		if(strlen($_GET['action'])>0)
		{
			$_POST[FORM_HIDEN_ACTION]		= $_GET['action'];
			$_POST[FORM_HIDEN_THREAD_ID]	= $_GET["thread_id"];
		}
		//もしくは、レスした場合
		if(	strcmp(FORM_HIDEN_ACTION_READ,$_POST[FORM_HIDEN_ACTION])	== 0 ||
			strcmp(FORM_HIDEN_ACTION_RES,$_POST[FORM_HIDEN_ACTION])		== 0 )
		{
			//スレッドの表示
			if(!$this->PrintThread_res())
			{
				//エラーの場合通常と同様に処理を行う
				$this->GetThreads();
			}
		}
		else
		{
			$this->GetThreads();
		}
	}
	
	private function PrintThread_res()
	{
		//戻り値を初期化
		$ret = FALSE;
		//グローバルのアイコンデータを使用
		global $icons;
		//グローバルからカラーデータを使用
		global $text_color;
		
		//スレッドIDを確定
		$thread_id = $_POST[FORM_HIDEN_THREAD_ID];
		
		//idからファイル名を成型
		$file_name = sprintf(DATA_DIRECTORY."%09d.php",$thread_id);
		
		//ファイルの有無をチェックして、開けた場合
		if(file_exists($file_name))
		{
			if($lines = file($file_name,FILE_TEXT))
			{
				//先頭行と最終行を除いて表示させる
				for($roop = 1;$roop <(count($lines) -1);$roop++)
				{
					//データをバッファに入れる
					$split_buf = split('[<]',$lines[$roop]);
					foreach($split_buf as $buf)
					{
						//バッファをさらに分割
						list($key,$value) = split('[>]',$buf);
						//データを連想配列に割り振る
						$data[$key] = $value;
					}
					
					//改行を改行及びタグに置き換える
					$comment = mb_ereg_replace('\\\\n',"<br />\n",$data[$this->FormComment]);
					//アンカを実装する
					$comment = mb_ereg_replace('(&gt;&gt;([1-9][0-9]*))','<a href="#\2">\1</a>'."\n",$comment);
					//$comment = preg_mach('(&gt;&gt;([1-9][0-9]*))','<a href="#\1">\1</a>'."\n",$comment);
					
					//アイコンデータをパスに変換
					$icon_path = ICON_DIRECTORY;
					$icon_path .= $icons[$data[$this->FormIcon]][ICONS_FILE];
					//名前
					$name = $data[$this->FormName];
					//トリップ
					$trip = $data[$this->FormTrip];
					//投稿時間
					$sent_time = $data[$this->FormSentTime];
					//タイトル
					$title = $data[$this->FormTitle];
					//文字色
					$my_color = $text_color[$data[$this->FormColor]][COLORS_RGB];
					
					//先頭行のみヘッダなどを書き込む
					if($roop == 1)
					{
						//削除されていた場合書き換え
						if($data[FORM_DELETE_FLAG] != 0)
						{
							$title = "このスレッドは削除されました";
							$name = "";
							$comment ="";
							$trip ="";
						}
						print <<<EndOfHTML
<br />
<div class="thread" id="$roop">
	<font class="thread_title">$title</font>
	<hr class="bbs">
	<img src="$icon_path">
	$roop.<b>$name</b>さん $trip 
	投稿時間:$sent_time<br />
	<font color="$my_color">
$comment
	</font>
	<hr class="bbs">
	<div class="delete_key">
	<form name="main"  method="post" action="">
		<input type="hidden" name="$this->FormHidenThreadID" value="$thread_id" />
		<input type="hidden" name="$this->FormHidenResID" value="$roop" />
		<INPUT type="password" class="delete_key" name="$this->FormDeleteKey"/>
		<input type="submit" class="delete_key" value="削除" />
	</form>
	</div>
	<hr class="bbs">

EndOfHTML;
					}
					//削除されていた場合無視
					elseif($data[FORM_DELETE_FLAG] == 0)
					{
						print <<<EndOfHTML
	<div class="res" id="$roop">
		<img src="$icon_path">
		$roop.<b>$name</b>さん $trip 
		投稿時間:$sent_time<br />
		<font color="$my_color">
$comment
		</font>
		<hr class="bbs">
		<div class="delete_key">
		<form name="main"  method="post" action="">
			<input type="hidden" name="$this->FormHidenThreadID" value="$thread_id" />
			<input type="hidden" name="$this->FormHidenResID" value="$roop" />
			<INPUT type="password" class="delete_key" name="$this->FormDeleteKey"/>
			<input type="submit" class="delete_key" value="削除" />
		</form>
		</div>
		<hr class="bbs">
	</div>

EndOfHTML;
					}
				}
				
				//レス上限を超えた場合スレッドを表示させない
				if(count($lines) > (RES_MAX + 1))
				{
					print "<div class=\"res\">\n";
					print "このスレッドは".RES_MAX."を超えたため<br />\n";
					print "書き込むことが出来ません。<br />\n";
					print "</div>\n";
				}
				else
				{
					//入力フォーム
					$this->obj_InputForm->CreateForm_Res();
				}
				//フッダー
				print <<<EndOfHTML
</div><br />
<br />
<br />
<form name="main"  method="get" action="" />
	<input type="hidden" name="$this->FormHidenAction" value="" />
	<input class="back" type="submit" value="戻る" />
</form>
<br />

EndOfHTML;
				
				//戻り値に成功を入れる
				$ret = TRUE;
			}
			//戻り値の返却
			return $ret;
		}
	}
	
	private function GetThreads()
	{
		//スレッドの読み込み初期位置を初期化
		$start_position = 0;
		
		//初期位置指定がある場合
		if($_POST[FORM_HIDEN_POSITION] > 0)
		{
			//初期位置を代入
			$start_position = $_POST[FORM_HIDEN_POSITION];
		}
		
		//データの取得
		//表示スレ数×位置分値をずらす。
		//取得数を+1する。
		$this->mysql_obj->ReadData(
			($start_position * READ_THREAD_MASS),
			$threads_id,(READ_THREAD_MASS+1));
		
		//ファイル名が存在する限りループ
		for($roop = 0;$roop <count($threads_id);$roop++)
		{
			//表示最大数を超えていた場合スルー
			if($roop < READ_THREAD_MASS)
			{
				//ファイル名の成型してスレッドの表示
				$this->PrintThread_top($threads_id[$roop]);
			}
		}
		
		//<<前、次>> の表示
		print '<table class="fr_table">'."\n";
		print '<tr><td class="fr_td">'."\n";
		//初期位置が0では無い場合
		//<<　を表示
		if($start_position > 0)
		{
			print '<form name="main"  method="post" action="">'."\n";
			print '<input type="hidden" name="'.FORM_HIDEN_POSITION.'" value="'.(string)($start_position - 1).'" />'."\n";
			print '<input class="fr" type="submit" value="&lt;&lt;" />'."\n";
			print '</form>'."\n";
		}
		print '&nbsp;'."\n";
		print '</td><td class="fr_base">'."\n";
		//次スレットが存在する場合
		//>>を表示する
		if(count($threads_id) > READ_THREAD_MASS)
		{
			print '<form name="main"  method="post" action="">'."\n";
			print '<input type="hidden" name="'.FORM_HIDEN_POSITION.'" value="'.(string)($start_position + 1).'" />'."\n";
			print '<input class="fr" type="submit" value="&gt;&gt;" />'."\n";
			print '</form>'."\n";
		}
		print '&nbsp;'."\n";
		print '</td></tr>'."\n";
		print '</table>'."\n";
		
		//入力フォームの作成
		$this->obj_InputForm->CreateForm_Base();
	}
	
	//基本スレッド表示
	private function PrintThread_top($thread_id)
	{
		//idからファイル名を成型
		$file_name = sprintf(DATA_DIRECTORY."%09d.php",$thread_id);
		//グローバルのアイコンデータを使用
		global $icons;
		//グローバルからカラーデータを使用
		global $text_color;
		
		//ファイルオープンに失敗した場合基本的に何もしない。
		//成功した場合
		if(file_exists($file_name))
		{
			if($lines = file($file_name,FILE_TEXT))
			{
				//省略数を数えるカウンターの初期化
				$counter = 0;
				//表示量
				$printMax = 0;
				
				//荒業につき修正希望
				//先頭行と最終行を除いて表示させる
				for($roop = 1;$roop <(count($lines) -1);$roop++)
				{
					//print $lines[$roop]."<br />\n";
					//データをバッファに入れる
					$split_buf = split('[<]',$lines[$roop]);
					foreach($split_buf as $buf)
					{
						//バッファをさらに分割
						list($key,$value) = split('[>]',$buf);
						//データを連想配列に割り振る
						$data[$key] = $value;
						
					}
					//削除されていた場合表示量の最大値を変更
					if($data[FORM_DELETE_FLAG] != 0)
					{
						$printMax = $printMax + 1;
					}
				}
				
				//先頭行と最終行を除いて表示させる
				for($roop = 1;$roop <(count($lines) -1);$roop++)
				{
					//データをバッファに入れる
					$split_buf = split('[<]',$lines[$roop]);
					foreach($split_buf as $buf)
					{
						//バッファをさらに分割
						list($key,$value) = split('[>]',$buf);
						//データを連想配列に割り振る
						$data[$key] = $value;
						
					}
					
					//改行を改行及びタグに置き換える
					$comment = mb_ereg_replace('\\\\n',"<br />\n",$data[$this->FormComment]);
					//アイコンデータをパスに変換
					$icon_path = ICON_DIRECTORY;
					$icon_path .= $icons[$data[$this->FormIcon]][ICONS_FILE];
					//名前
					$name = $data[$this->FormName];
					//トリップ
					$trip = $data[$this->FormTrip];
					//投稿時間
					$sent_time = $data[$this->FormSentTime];
					//タイトル
					$title = $data[$this->FormTitle];
					//文字色
					$my_color = $text_color[$data[$this->FormColor]][COLORS_RGB];
					
					//先頭行のみヘッダなどを書き込む
					if($roop == 1)
					{
						//削除されていた場合
						if($data[FORM_DELETE_FLAG] != 0)
						{
							$title = "このスレッドは削除されました";
							$name = "";
							$comment ="";
							$trip ="";
						}
						print <<<EndOfHTML
<br />
<div class="thread">
	<font class="thread_title">$title</font>
	<hr class="bbs">
	<img src="$icon_path">
	$roop.<b>$name</b>さん $trip 
	投稿時間:$sent_time<br />
	<font color="$my_color">
$comment
	</font>
	<hr class="bbs">

EndOfHTML;
					}
					//レス(新着(READ_RES_MASS)レス目までを表示する
					elseif($roop >= (count($lines) -(1 + READ_RES_MASS + $printMax)) &&
						$data[FORM_DELETE_FLAG] == 0)
					{
						//カウンターの数字が0ではないとき
						//テキストを表示してカウンターをリセットする
						if($counter > 0)
						{
							print "\t<hr class=\"bbs\">\n";
							print "\t<b>".$counter."件</b>の記事が省略されております。<br />\n";
							print "\tもっと読むをクリックしてご覧ください。\n";
							print "\t<hr class=\"bbs\">\n";
							//カウンターをリセット
							$counter = 0;
						}
						
						print <<<EndOfHTML
	<div class="res">
		<img src="$icon_path">
		$roop.<b>$name</b>さん $trip 
		投稿時間:$sent_time<br />
		<font color="$my_color">
$comment
		</font>
		<hr class="bbs">
	</div>

EndOfHTML;
						}
						//削除フラグが立ってない場合のみ
						//カウンターに加算
						elseif($data[FORM_DELETE_FLAG] == 0)
						{
							$counter = $counter + 1;
						}
					}
					
				}
				print <<<EndOfHTML
	<form name="main"  method="get" action="">
		<input type="hidden" name="$this->FormHidenAction" value="$this->FormHidenActionRead" />
		<input type="hidden" name="$this->FormHidenThreadID" value="$thread_id" />
		<input class="res" type="submit" value="もっと読むor返信する" />
	</form>
</div><br />

EndOfHTML;
		}
	}
	
}

//アイコンを表示させる。
function Print_All_Icons()
{
	//グローバルのアイコンデータを使用
	global $icons;
	
	print "<table>";
	//アイコンの数分ループ
	for($roop_1=0; $roop_1<count($icons);$roop_1++)
	{
		//アイコンの有無をチェックする
		if(file_exists(ICON_DIRECTORY . $icons[$roop_1][ICONS_FILE]))
		{
			//アイコンデータをパスに変換
			$icon_path = ICON_DIRECTORY;
			$icon_path .= $icons[$roop_1][ICONS_FILE];
			
			print "\t<tr><td>\n";
			print "\t\t".$icons[$roop_1][ICONS_NAME]."\n";
			print "\t</td><td>\n";
			print "\t\t<img src=\"".$icon_path."\">\n";
			print "\t</td></tr>\n";
	
		}
	}
	print "</table>";
}
?>