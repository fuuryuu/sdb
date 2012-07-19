<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>合体データバンク</title>
<style type="text/css">
<!--
body {
	font-family:'メイリオ',Meiryo,'ＭＳ Ｐゴシック',sans-serif;
}
-->
</style>
</head>
<body>

<?php


require_once("common.php");

$def_server = getServer();//サーバ一覧

//データ読み込み
$data = explode("\n", file_get_contents("cache.tsv"));

//ヘッダ削除
array_shift($data);

//画面表示用ヘッダ
$str = "陽,Lv,HP,力,技,速,守,賢,運,陰,Lv,HP,力,技,速,守,賢,運,結果,Lv,HP,力,技,速,守,賢,運,合体日,投稿者ID,サーバー<br>\n";


foreach ($data as $x1) {

	//nullならスキップ
	if (! $x1) {continue;}

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

	//削除済みならスキップ
	if ($v[16]) {continue;}

	$str .= $v[1] . ",";//式姫
	$str .= $v[2] . ",";//Lv
	$str .= $v[3] . ",";//HP
	$str .= str_replace("_", ",", $v[4]) . ",";//ステ
	$str .= $v[5] . ",";//式姫
	$str .= $v[6] . ",";//Lv
	$str .= $v[7] . ",";//HP
	$str .= str_replace("_", ",", $v[8]) . ",";//ステ
	$str .= $v[9] . ",";//式姫
	$str .= "1,";//Lv
	$str .= $v[10] . ",";//HP
	$str .= str_replace("_", ",", $v[11]) . ",";//ステ
	$str .= $v[12] . ",";//合体日
	$str .= $v[14] . ",";//投稿者ハッシュ

	//サーバー
	if ($v[17] === "999") {$server = "不明";}
	else {$server = $def_server[$v[17]];}

	$str .= $server . "<br>\n";
}



//画面表示
echo $str;


?>