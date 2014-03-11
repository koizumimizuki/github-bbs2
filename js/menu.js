//menu用、text/HTML 吐くだけ。
//共通処理にしないと、修正が面倒なため。

function PrintMenu()
{
var MenuText;
var SiteURL = "http://soreigai.jp";

MenuText = '<div class="menu">';
MenuText +='	<br />';
MenuText +='	<br />';
MenuText +='	<div class="menutitle">';
MenuText +='		Menu';
MenuText +='	</div>';
MenuText +='	<br />';
MenuText +='	Contents on this page are as follows.<br>';
MenuText +='	<div class="link">';
MenuText +='		<a class="menu" href="' + SiteURL + '/index.html">';
MenuText +='		Top</a><br />';
MenuText +='		<a class="menu" href="' + SiteURL + '/links.html">';
MenuText +='		Links</a><br />';
MenuText +='		<a class="menu" href="' + SiteURL + '/history.html">';
MenuText +='		History</a><br />';
MenuText +='		<a class="menu" href="' + SiteURL + '/bbs/index.php">';
MenuText +='		BBS</a><br />';
MenuText +='		<br />';
MenuText +='		Outer site<br />';
MenuText +='		<a class="menu" target="_brank" href="http://koizumimizuki.blog.fc2.com/">';
MenuText +='		Diary</a><br />';
MenuText +='		<a class="menu" target="_brank" href="http://comics.soreigai.jp/">';
MenuText +='		Comics</a><br />';
MenuText +='		<strike>';
MenuText +='		Uploader</strike><br />';
MenuText +='		<br />';
MenuText +='		<a class="menu" href="mailto:koizumimizuki@soreigai.jp">'
MenuText +='		Mail</a><br />';
MenuText +='		Line ID:koizumimizuki<br />';
MenuText +='		Skype ID:koizumimizuki<br />';
MenuText +='	</div><br />';
MenuText +='	<br />';
MenuText +='	End.<br />';
MenuText +='	<br />';
MenuText +='	<br />';
MenuText +='	<br />';
MenuText +='</div>';

document.write(MenuText);


}

