<?php	//PHPの初期化処理

//MySQL用クラスの読み込み
require "mysql-class-child.php";
//Trip用クラスの読み込み
require "trip.php";
//BBS用クラスの読み込み
require "bbs.php";

?>
<!--HTML-->
<!--ヘッダー-->
<!-- saved from url=(0014)about:internet -->
<HTML lang="ja-JP">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<TITLE>それ以外のナニカ</TITLE>
<link rel="stylesheet" href="../css/base.css" type="text/css" />
</HEAD>
<!--ヘッダー-->
<body class="icons">
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
<!--Main-->
<!--PHPで生成-->
<?php
//アイコン一覧の表示
print "<br />\n";
print "<div width=\"200px\">\n";
print "<center>\n";
print "<b>アイコン一覧</b><br />\n";
Print_All_Icons();
print '<a href="#" onClick="window.close(); return false;">'."\n";
print "Close</a>\n";
print "</center>\n";
print "</dif>\n";
?>
<!--Main-->
<!--フッダー-->
<!--アクセス解析-->
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-1769549-4");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</HTML>
