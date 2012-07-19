<?php
require_once("common.php");
$shikihime = getShikihime();//式姫一覧

//プルダウン生成
foreach ($shikihime as $key => $value) {
	$option .= "<option value=\"$key\">$value</option>\n";
}
?>


<!-- メニュー START -->
<div style="float:left; margin:30px 50px 0px 30px;">

<script type="text/javascript">
function show(){
	if(other.style.display=="block") {other.style.display="none";}
	else {other.style.display="block";}
}
</script>





<a href="index.php">サイトTOP</a>

<br><br>

<form method="post" action="index.php">
陽
<select name="yo" class="selectable selectable_yo">
<?php echo makeOption($_POST["yo"]); ?>
</select>
<br>

陰
<select name="in">
<?php echo makeOption($_POST["in"]); ?>
</select>
<br>

結果
<select name="kekka">
<?php echo makeOption($_POST["kekka"]); ?>
</select>
<br>

<input type="submit" value="絞込み">
</form>

<br>


<a href="touroku.php">データ登録</a><br>
<br>


<a href="javascript:void(0)" onclick="show(); return false;">その他メニュー</a>

<div id="other" style="display:none; background-color:#f8f8ff;">
	<br>

	<a href="ranking.php">陰式姫人気<br>ランキング</a><br>
	<br>

	<a href="sim/chousa.php">異常値調査</a><br>
	<br>

	<a href="all.php">お持ち帰り用</a><br>
	<br>

	<a href="sim/chousa4.php">初期値キャップ？</a><br>
	<br>

	<a href="http://8231.teacup.com/sdb24246/bbs">質問要望とか</a><br>
	<br>

	<a href="snapshot20120304/">過去データ</a><br>
	～2012/02/29<br>

	<br>
</div>

<br><br><br><br>


【姉妹サイト】<br>
<a href="seicho/">成長データバンク</a><br>
<a href="sim/">合体SIM</a><br>
<br>

<font size="-1">
データ転載OK<br>
申告不要。<br>
</font>






<!-- メニュー END -->
</div>


<?php
//オプション
function makeOption($str) {
	global $shikihime;

	//グループ定義
	$group = array("1" => "付喪", "2" => "妖獣", "3" => "鬼", "4" => "霊獣", "5" => "天狗", "6" => "天女", "7" => "舶来", "8" => "神話", "9" => "激レア");

	if ($str === null || $str === "all") {$ted = "selected";} else {$ted = "";}
	$option .= "<optgroup label=\"全て\">\n";
	$option .= "<option value=\"all\" $ted>全て</option>\n";
	$option .= "</optgroup>\n";

	//グループ分け
	foreach ($shikihime as $key => $value) {
		$groupName = $group[substr($key, 1, 1)];
		$data[$groupName][$key] = $value;
	}

	foreach (array_keys($data) as $x1) {

		//グループ開始
		$option .= "<optgroup label=\"$x1\">\n";

		foreach ($data[$x1] as $key => $value) {
			if ($str === $key) {$ted = "selected";} else {$ted = "";}
			$option .= "<option value=\"$key\" $ted>$value</option>\n";
		}

		//グループ閉じ
		$option .= "</optgroup>\n";
	}

	return $option;
}
?>