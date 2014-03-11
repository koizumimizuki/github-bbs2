//menu用、text/HTML 吐くだけ。
//共通処理にしないと、修正が面倒なため。

function PrintHeader()
{
	var HeaderText;

	HeaderText = '<!--top-->';
	HeaderText +='<!--body-->';
	HeaderText +='<center>';
	HeaderText +='<div class="mybody">';
	HeaderText +='<!--Main-->';

	document.write(HeaderText);

}

function PrintMainPage(ThisName)
{
	
	var MainPageText;
	
	MainPageText = '<div class="main">';
	MainPageText +='	<br />';
	MainPageText +='	<center><div class="title">';
	MainPageText +='		それ以外の';
	MainPageText += ThisName;
	MainPageText +='	</div></center><br />';
	MainPageText +='<!--Main_top-->';
	
	document.write(MainPageText);

}
