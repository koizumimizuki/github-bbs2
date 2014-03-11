<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<!--IEでCSSを正しく表示させるために先頭行に必須-->
<?php	//PHPの初期化処理
//Written by "Koizumi Mizuki" 2008/06/13

//MySQL用クラスの読み込み
require "mysql-class-child.php";
//Trip用クラスの読み込み
require "trip.php";
//BBS用クラスの読み込み
require "bbs.php";

//セッションの使用を宣言
session_start();

?>
<?php
//SQLに接続
$obj_SQL = new MySQL_Connection_Child();
//接続に失敗した場合、デバックメッセージを呼び出し終了させる。
if(!$obj_SQL->Connection)
{
	print "<br />\n$obj_SQL->DebugMessage <br />\n";
	die("SQLエラー！");
}
?>
<?php
//トリップクラスの呼び出し
$obj_Trip = new Trip();
//データ入力のチェック
$obj_CI = new CheckInput($obj_SQL,$obj_Trip);
?>
<?php	//クッキーの読み出し
$obj_CC = new CheckCookie();
?>
<!--ヘッダー-->
<!-- saved from url=(0014)about:internet -->
<html lang="ja-JP">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<title>それ以外のナニカ</title>
<link rel="stylesheet" href="../css/base.css" type="text/css" />
</head>
<!--ヘッダー-->
<body>
<!--JavaScritp初期化処理-->
<script type="text/javascript" src="../js/base.js"></script>
<script type="text/javascript">
	<!--
	init();
	//-->
</script>
<!--JavaScritp非対応時処理-->
<noscript>
	JavaScript対応ブラウザで表示してください。
</noscript>
<!--IE時の処理-->
<!--描画部ヘッダー-->
<script type="text/javascript" src="../js/header.js"></script>
<script type="text/javascript">
	<!--
		PrintHeader();
	//-->
</script>
<!--描画部ヘッダー-->
<!--描画部左メニュー-->
<script type="text/javascript" src="../js/menu.js"></script>
<script type="text/javascript">
	<!--
		PrintMenu();
	//-->
</script>
<!--描画部左メニュー-->
<!--描画部右メニュー-->
<script type="text/javascript" src="../js/advertisement.js"></script>
<script type="text/javascript">
	<!--
		PrintAdvertisement();
	//-->
</script>
<!--描画部右メニュー-->
<!--メインページ-->
<script type="text/javascript">
	<!--
		PrintMainPage("BBS version-β");
	//-->
</script>

<!--PHPで生成-->
<?php 
//スレッドの読み込み
$obj_RT = new ReadThreads($obj_SQL);

?>
<!--描画部フッダー-->
<script type="text/javascript" src="../js/footer.js"></script>
<script type="text/javascript">
	<!--
		PrintFooter();
	//-->
</script>
<!--描画部フッダー-->
<!--メインページ-->
</body>
</html>
