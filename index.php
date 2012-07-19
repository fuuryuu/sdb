<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>合体データバンク</title>
<style type="text/css">
<!--
.table1px {
	border-collapse: collapse;
	border: 1px #333333 solid;
}
.table1px td {
	border: 1px #333333 solid;
	padding:6px;
}
.table1px th {
	border: 1px #333333 solid;
	padding:2px;
	background-color: #99CCFF;
	font-weight: normal;
}
.table1px_2 {
	border-collapse: collapse;
	border: 1px #333333 solid;
}
.table1px_2 td {
	border: 1px #333333 solid;
	padding:6px;
}
.table1px_2 th {
	border: 1px #333333 solid;
	padding:2px;
	background-color: orange;
	font-weight: normal;
}
body {
	font-family:'メイリオ',Meiryo,'ＭＳ Ｐゴシック',sans-serif;
}
-->
</style>
</head>
<body>


<?php include("menu.php"); ?>
<div style="float:left;">

<h1>合体データバンク</h1>

<?php
require_once("common.php");
$shikihime = getShikihime();//式姫一覧
$server = getServer();//サーバ一覧
$campaign = getCampaign();//キャンペーン

//並び順
$sort = $_POST["sort"];

//初期値は【合体日_ID】
if ($sort === null) {$sort = "0";}

//入力チェック
if (! in_array($sort, array("0", "1", "2"))) {echo "えらー"; exit;}



//ハッシュ
$hash = substr(md5($_SERVER["REMOTE_ADDR"]), 0, 10);

if ($_GET["message"] === "1") {echo "<font color=blue><B>データ登録完了。</B></font><br><br><br><br><br>\n";}
else if ($_GET["message"] === "2") {echo "<font color=blue><B>データ削除完了。</B></font><br><br><br><br><br>\n";}

//陽
$search_yo = $shikihime[$_REQUEST["yo"]];
$search_in = $shikihime[$_REQUEST["in"]];
$search_kekka = $shikihime[$_REQUEST["kekka"]];



//データ読み込み
$data = explode("\n", file_get_contents("data.tsv"));

//ヘッダ削除
array_shift($data);



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

	//------- 絞込み ------
	if ($search_yo && $v[1] <> $search_yo) {continue;}
	if ($search_in && $v[5] <> $search_in) {continue;}
	if ($search_kekka && $v[9] <> $search_kekka) {continue;}

	//キャンペーンチェック
	$can_flg = 0;
	foreach ($campaign as $c) {
		if ($v[1] === $c["種族名"] || $v[5] === $c["種族名"]) {
			if ($v[12] >= $c["開始日"] && $v[12] <= $c["終了日"]) {$can_flg = 1;}
		}
	}






	//通常合体なら青
	if ($v[1] === $v[9]) {$cl = "table1px";} else {$cl = "table1px_2";}


	//陽ステ（力技速守賢運）
	$yo = explode("_", $v[4]);
	$yo_total = array_sum($yo);

	//陰ステ（力技速守賢運）
	$in = explode("_", $v[8]);
	$in_total = array_sum($in);

	//結果ステ（力技速守賢運）
	$kekka = explode("_", $v[11]);
	$kekka_total = array_sum($kekka);

	$str = "";



	$str .= "合体日：". date("Y/m/d", strtotime($v[12]));
	if ($can_flg) {$str .= "<font color=red><B>（キャンペーン中）</B></font>\n";}
	$str .= "<br>投稿者ID：". $v[14]."　　".$server[$v[17]];

	//削除ボタン
	if ($hash === $v[14]) {
		$str .= "<form method=\"post\" action=\"delete.php\" style=\"display: inline;\"><input type=\"submit\" value=\"削除\"><input type=\"hidden\" name=\"id\" value=\"$v[0]\"></form>\n";
	}
	$str .= "<br>\n";
	$str .= "<table class=\"$cl\"><tr><th>種族</th><th>Lv</th><th>HP</th><th>力</th><th>技</th><th>速</th><th>守</th><th>賢</th><th>運</th><th>総合</th></tr>\n";
	$str .= "<tr><td>". $v[1]. "</td><td>". $v[2]. "</td><td>". $v[3]. "</td><td>". str_replace("_", "</td><td>", $v[4]). "</td><td>".$yo_total."</td></tr>\n";
	$str .= "<tr><td>". $v[5]. "</td><td>". $v[6]. "</td><td>". $v[7]. "</td><td>". str_replace("_", "</td><td>", $v[8]). "</td><td>".$in_total."</td></tr>\n";
	$str .= "<tr><td>". $v[9]. "</td><td>1</td><td>". $v[10]. "</td><td>". str_replace("_", "</td><td>", $v[11]). "</td><td>".$kekka_total."</td></tr>\n";
	$str .= "</table><br><br>\n";

	//並び替えのキー
	if ($sort === "0") {$key = $v[12] . "_" . $v[0];}//合体日
	else if ($sort === "1") {$key = str_pad($kekka_total, 3, "0", STR_PAD_LEFT) . "_" . $v[0];}//総合ステ
	else if ($sort === "2") {$key = str_pad(($v[2]+$v[6]), 2, "0", STR_PAD_LEFT) . "_" . $v[0];}//Lv合計




	$body[$key] = $str;
}



echo "<form method=\"post\" action=\"index.php\">\n";
echo "<select name=\"sort\">\n";
if($sort === "0") {$selected = " selected";} else {$selected = "";}
echo "<option value=\"0\"$selected>合体日</option>\n";
if($sort === "1") {$selected = " selected";} else {$selected = "";}
echo "<option value=\"1\"$selected>総合ステ</option>\n";
if($sort === "2") {$selected = " selected";} else {$selected = "";}
echo "<option value=\"2\"$selected>素材の合計レベル</option>\n";
echo "</select>\n";

echo "<input type=\"hidden\" name=\"yo\" value=\"".$_REQUEST['yo']."\">";
echo "<input type=\"hidden\" name=\"in\" value=\"".$_REQUEST['in']."\">";
echo "<input type=\"hidden\" name=\"kekka\" value=\"".$_REQUEST['kekka']."\">";

echo "<input type=\"submit\" value=\"並び替え\">\n";
echo "</form>\n";

echo "通常合体は<font color=blue>青</font>、レシピ合体は<font color=orange>オレンジ</font><br>\n";



if ($body === null) {echo "データがありません。"; exit;}

//キーでソート
krsort($body);

echo count($body) . "件のデータがあります。<br><br><br>\n";


//画面表示
$cnt = 0;
foreach ($body as $x1) {
	echo $x1;
	$cnt++;

	if ($cnt > 50 ) {echo "<font color=blue>該当結果を５０件まで表示します。</font><br>"; break;}
}


?>
</div>
</body>
</html>
