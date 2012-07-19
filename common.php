<?php

//キャンペーンも含めた「種族CD」と「種族名」を取得
//引数：画面非表示　←これが1で、なおかつファイル内の画面非表示フラグが1なら、結果として出力しない
function getShikihime2($noDispFlg = 0) {
	//このファイルのディレクトリ
	$dir = dirname(__FILE__);

	//*** 非キャンペーン種族 ***
	$shikihime1 = getShikihime($noDispFlg);

	//*** キャンペーン種族 ***
	$campaign = getCampaign();
	//種族CD	キャンペーン識別番号	種族名	キャンペーン名	開始日	終了日	画面非表示フラグ
	foreach ($campaign as $x1) {
		if ($noDispFlg && $x1['画面非表示フラグ']) {continue;}//画面非表示フラグがONならスキップ
		$shikihime2[$x1['種族CD'].$x1['キャンペーン識別番号']] = $x1['種族名'].$x1['キャンペーン名'];
	}

	$body = $shikihime1 + $shikihime2;

	//種族CDでソート
	ksort($body);

	return $body;
}








//キャンペーンを除く「種族CD」と「種族名」を取得
//引数：画面非表示　←これが1で、なおかつファイル内の画面非表示フラグが1なら、結果として出力しない
function getShikihime($noDispFlg = 0) {
	//このファイルのディレクトリ
	$dir = dirname(__FILE__);

	//非キャンペーン種族
	$path = "$dir/def_shikihime.tsv";//ファイル名
	if (! file_exists($path)) {die("error.68716871687");}//ファイルが存在しない場合
	$data = explode("\n", file_get_contents($path));
	array_shift($data);//ヘッダ削除
	foreach ($data as $x1) {
		if (! $x1) {continue;}//nullならスキップ

		//種族CD	種族名	画面非表示フラグ
		$TMP = explode("\t", $x1);
		if ($noDispFlg && $TMP[2]) {continue;}//画面非表示フラグがONならスキップ
		$body[$TMP[0]] = $TMP[1];
	}

	//種族CDでソート
	ksort($body);

	return $body;
}







//*** サーバ一覧を取得 ***
function getServer() {
	//このファイルのディレクトリ
	$dir = dirname(__FILE__);

	$path = "$dir/def_server.tsv";//ファイル名
	if (! file_exists($path)) {die("error.554268480");}//ファイルが存在しない場合
	$data = explode("\n", file_get_contents($path));
	array_shift($data);//ヘッダ削除
	foreach ($data as $x1) {
		if (! $x1) {continue;}//nullならスキップ

		//サーバCD	サーバ名	削除フラグ
		$TMP = explode("\t", $x1);
		if ($TMP[2]) {continue;}//削除フラグONならスキップ
		$body[$TMP[0]] = $TMP[1];
	}

	return $body;
}


//キャンペーン取得
function getCampaign() {
	//common.phpからの相対パスを指定
	return getTsv("def_campaign.tsv");
}




//*** 汎用型TSV取得関数 ***
//【重要】プライベートなので注意！
//引数１：このファイルから見た、開くファイルの相対パス
//戻り値：２次元配列　$body[行番号][列名]　※行番号は0から始まる
function getTsv($path) {
	//このファイルのディレクトリ
	$dir = dirname(__FILE__);
	//フルパス
	$path = "$dir/$path";

	if (! file_exists($path)) {die("error.83513456874");}//ファイルが存在しない場合
	$data = explode("\n", file_get_contents($path));

	//ヘッダ
	$header = explode("\t", array_shift($data));

	foreach ($data as $x1) {
		if ($x1 === "") {continue;}//スキップ

		$TMP = explode("\t", $x1);

		$data = array();//初期化
		foreach (array_keys($TMP) as $x2) {
			$data[$header[$x2]] = $TMP[$x2];
		}
		//$bodyは２次元配列
		$body[] = $data;
	}

	return $body;
}




//*** キャッシュデータ再作成 ***
function makeCache() {

	$campaign = getCampaign();//キャンペーン

	//このファイルのディレクトリ
	$dir = dirname(__FILE__);
	//フルパス
	$path = "$dir/data.tsv";

	if (! file_exists($path)) {die("error.8571861678553");}//ファイルが存在しない場合

	$data = explode("\n", file_get_contents($path));
	$body = array_shift($data);//ヘッダ削除
	$body .= "\n";

	foreach ($data as $x1) {
		if (! $x1) {continue;}//nullならスキップ
		/*
		0	ID
		1	式姫（陽）
		2	Lv
		3	HP
		4	陽ステ（力技速守賢運）
		5	式姫（陰）
		6	Lv
		7	HP
		8	陰ステ（力技速守賢運）
		9	式姫（結果）
		10	HP
		11	結果ステ（力技速守賢運）
		12	合体日
		13	コメント
		14	投稿者ハッシュ
		15	作成日時
		16	削除フラグ
		17	サーバー
		*/
		$v = explode("\t", $x1);

		//キャンペーンチェック
		foreach ($campaign as $c) {
			if ($v[1] === $c["種族名"] && $v[12] >= $c["開始日"] && $v[12] <= $c["終了日"]) {$v[1] .= $c["キャンペーン名"];}
			if ($v[5] === $c["種族名"] && $v[12] >= $c["開始日"] && $v[12] <= $c["終了日"]) {$v[5] .= $c["キャンペーン名"];}
			if ($v[9] === $c["種族名"] && $v[12] >= $c["開始日"] && $v[12] <= $c["終了日"]) {$v[9] .= $c["キャンペーン名"];}
		}

		$body .= implode("\t", $v) . "\n";
	}

	//キャッシュ書き込み
	file_put_contents('cache.tsv', $body, LOCK_EX);
}

?>