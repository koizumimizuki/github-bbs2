<?php //Trip作成クラス
//Written by "Koizumi Mizuki" 2008/06/24

//全部関数内のローカル変数扱いにする。
//Xbyteのkeyを受け取りX文字のトリップを返却する。
//なお、keyは可変長であるが、返却するトリップの文字数はマスクに依存する

//トリップの長さを取得する
//function GetTripLength()
//トリップを作成する
//function CreateTrip($key)

class Trip
{
	//マスク
	private $mask;
	//変換MAP
	private $map;
	//マスクの長さ
	private $mask_len;
	//変換MAPの長さ
	private $map_len;
	//カウンター
	private $counter;
	//カウンターの最大値
	private $counter_max;
	//キーの長さ
	private $key_len;
	
	//データの初期化
	public function Trip()
	{
		//これの文字数でトリップ全体の文字数が決まる
		$this->mask	="tripmask10";
		//カウンターの最大値
		//2以上かつ、$maskの倍未満が望ましい
		//0はダメ絶対
		$this->counter_max = 9;
		//適当に並べ変える事。
		//文字数を増やす事は推奨するが減らす事は非推奨
		//a-z,A-Z,0-1,を入れている
		$this->map	=array(
		"0","1","2","3","4",
		"h","i","j","k","l","m","n",
		"V","W","X","Y","Z",
		"H","I","J","K","L","M","N",
		"O","P","Q","R","S","T","U",
		"o","p","q","r","s","t","u",
		"A","B","C","D","E","F","G",
		"a","b","c","d","e","f","g",
		"v","w","x","y","z",
		"5","6","7","8","9");
		
		//ここからは基本的に弄らなくてＯＫ♪
		//マスクの長さを取得
		$this->mask_len	= strlen($this->mask);
		//万一4文字未満のため
		if($this->mask_len < 4)
		{
			//強制終了
			die("トリップ設定エラーです");
		}
		//配列の長さを取得
		$this->map_len	= count($this->map);
	}
	
	//トリップの長さを取得する
	public function GetTripLength()
	{
		return $this->mask_len;
	}
	//トリップの作成
	public function CreateTrip($key)
	{
		//keyの長さを取得
		$key_len = strlen(bin2hex($key)) / 2;
		
		//キーの右端と左端を定義
		$trip_left	= 0;
		$trip_right	= 0;
		//変える場所
		$change_point = 0;
		//カウンターを初期化
		$this->counter = 0;
		//戻り値用文字列
		$ret ="";
		
		//トリップの初期化
		//マスクの長さ分ループ
		for($roop=0;$roop<$this->mask_len;$roop++)
		{
			//数字に変換しないと化けるため。
			$trip[$roop] = (int)(bin2hex($this->mask[$roop]));
		}
		//キーの数分ループ
		for($roop=0;$roop<$key_len;$roop++)
		{
			//データ分割
			$byte = bin2hex($key[$roop]);
			//数字は格bit数
			$_12 = ($byte & 0xC0)>>6;
			$this->BitPulsX($_12,$trip_left);
			
			$_34 = ($byte & 0x30)>>4;
			$this->BitPulsX($_34,$trip_right);
			//文字の入れ替え
			$this->change_and_plus($trip,$trip_left,$trip_right);
			//データに加算
			$trip[$change_point] = ($trip[$change_point] + $_12) % $this->map_len;
			$change_point = ($change_point + 1) % $this->mask_len;
			//データに加算
			$trip[$change_point] = ($trip[$change_point] + $_34) % $this->map_len;
			$change_point = ($change_point + 1) % $this->mask_len;
			
			$_56 = ($byte & 0x0C)>>2;
			$this->BitPulsX($_56,$trip_left);
			$_78 = ($byte & 0x03);
			$this->BitPulsX($_78,$trip_left);
			//文字の入れ替え
			$this->change_and_plus($trip,$trip_left,$trip_right);
			//データに加算
			$trip[$change_point] = ($trip[$change_point] + $_56) % $this->map_len;
			$change_point = ($change_point + 1) % $this->mask_len;
			//データに加算
			$trip[$change_point] = ($trip[$change_point] + $_78) % $this->map_len;
			$change_point = ($change_point + 1) % $this->mask_len;
			
		}
		//最終変換
		//マスクの長さ分ループ
		for($roop=0;$roop<$this->mask_len;$roop++)
		{
			$ret .= $this->map[$trip[$roop]];
		}
		
		return $ret;
	}
	
	//文字の入れ替
	private function change_and_plus(&$trip,$left,$right)
	{
		//文字の入れ替
		//とりあえず左の値を逃す
		$esc_char = $trip[$left];
		//左に右の値を入れる
		$trip[$left] = $trip[($this->mask_len - $right)];
		//右に逃した値を入れる
		$trip[($this->mask_len - $right)] = $esc_char;
		
	}
	
	//サブルーチン
	private function BitPulsX(&$value,&$position)
	{
		//FFでマスクをかける
		$value = (($value + $this->counter) & 0xFF);
		$this->counter += 1;
		//counterの長さの脚きり
		$this->counter = $this->counter % $this->counter_max;
		
		
		//並び替える際の位置を定義
		$position = $position + $value;
		//$this->msk_len未満になるように設定
		$position = $position % $this->mask_len;
	}
	
}

?>
