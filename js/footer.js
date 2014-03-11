//menu用、text/HTML 吐くだけ。
//共通処理にしないと、修正が面倒なため。

function PrintFooter()
{
	var FooterText;


	FooterText = '	<br />';
	FooterText +='	<br />';
	FooterText +='	<br />';
	FooterText +='	<div class="subtit2">';
	FooterText +='		このサイトは<b>Mozilla Firefox</b>でレイアウトを確認しております。<br />';
	//FooterText +='		<b>Internet Explorer8.x</b>(互換モード非使用)、';
	FooterText +='		<b>Google Chrome</b>、';
	FooterText +='		<b>Sfari</b>、';
	FooterText +='		<b>Opera</b>、';
	FooterText +='		<b>Netscape</b>、';
	FooterText +='		それぞれの最新版でも<br />';
	FooterText +='		確認は行っておりますが予期せぬ不具合がありえます。<br />';
	FooterText +='		お手数がその場合は御一報ください。<br />';
	FooterText +='	</div>';
	FooterText +='	<br />';
	FooterText +='	<br />';
	FooterText +='	<br />';
	FooterText +='	<center><div class="copyright">';
	FooterText +='		Mizuki created this page.<br />';
	FooterText +='	</div></center><br />';
	FooterText +='</div>';
	FooterText +='<!--Main-->';
	FooterText +='</div>';
	FooterText +='</center>';
	FooterText +='<!--body-->';


	document.write(FooterText);


}

