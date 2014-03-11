//足回り、共通処理用

function CheckJS()
{
	if(!navigator.javaEnabled())
	{
		window.alert("現在JAVAが使えない状態です!! ネットワーク設定のJAVAの項目をチェックして下さい!!");
	}
}

function init()
{
	//CheckJS();
}

//新しいウィンドウを開く
function create_display_icons(url){
	window.open(url, "show_icons", "width=220,height=500");
}

