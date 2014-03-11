//menu用、text/HTML 吐くだけ。
//共通処理にしないと、修正が面倒なため。

function PrintAdvertisement()
{
	var AdvertisementText;

	AdvertisementText = '<div class="advertisement">';
	AdvertisementText +='	<br />';
	AdvertisementText +='	<br />';
	AdvertisementText +='	<div class="advertisementtitle">';
	AdvertisementText +='		Advertisement';
	AdvertisementText +='	</div>';
	AdvertisementText +='	<br />';
	AdvertisementText +='	Please click if it is interested.<br>';
	AdvertisementText +='	<div class="link">';
	AdvertisementText +='		<a class="menu" href="/">';
	AdvertisementText +='		Nothing</a><br />';
	AdvertisementText +='	</div><br />';
	AdvertisementText +='	<br />';
	AdvertisementText +='	End.<br />';
	AdvertisementText +='	<br />';
	AdvertisementText +='	<br />';
	AdvertisementText +='	<br />';
	AdvertisementText +='</div>';

	document.write(AdvertisementText);


}

