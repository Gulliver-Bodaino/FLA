<?php
session_start();

// CSRFチェック
if (!isset($_POST['token']) || !isset($_SESSION['token']) || $_POST['token'] !== $_SESSION['token']) {
	header('Location: ./');
	exit;
}

require_once 'function.php';

// POSTをセッションへ保存
$_SESSION['form'] = $_POST;

$q1 = q($_POST['q1'] ?? '');
$q2 = q($_POST['q2'] ?? '');
$group_name = text_max($_POST['group_name'] ?? '', 100);
$group_num = num_max($_POST['group_num'] ?? '', 10000);

$name_sei = text_max($_POST['name_sei'] ?? '', 50);
$name_mei = text_max($_POST['name_mei'] ?? '', 50);
$name_kana_sei = text_max($_POST['name_kana_sei'] ?? '', 50);
$name_kana_mei = text_max($_POST['name_kana_mei'] ?? '', 50);

$zip1 = zip_check($_POST['zip1'] ?? '', 3);
$zip2 = zip_check($_POST['zip2'] ?? '', 4);
$todouhuken = text_max($_POST['todouhuken'] ?? '', 10);
$city = text_max($_POST['city'] ?? '', 100);
$city2 = text_max($_POST['city2'] ?? '', 100);
$city3 = text_max($_POST['city3'] ?? '', 100, false);
$city4 = text_max($_POST['city4'] ?? '', 100, false);

$tel = tel_check($_POST['tel'] ?? '');
$mail = mail_check($_POST['mail'] ?? '', 100);
$check = check($_POST['check'] ?? '');

?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="../common/css/import.css">
	<link rel="stylesheet" href="./form.css?v=20260730">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Kosugi+Maru&family=Noto+Sans+JP:wght@100..900&family=Share+Tech+Mono&family=VT323&display=swap" rel="stylesheet">
	<script src="../common/js/common.js"></script>
	<script src="./form.js"></script>
	<title>確認画面 ｜ 食生活アドバイザー®｜FLAネットワーク協会</title>
</head>

<body>

	<div class="header_main">
		<h1 id="keyword"><span>健康増進　健康経営　栄養管理　ダイエット　女性</span> 食と生活に関する資格なら食生活アドバイザー<sup>®</sup></h1>
		<p class="logo"><a href="http://www.flanet.jp/"><img src="../img/logo.gif" alt="一般社団法人 FLAネットワーク協会 食生活アドバイザー検定"></a></p>
	</div>
	<div id="contents">
		<!--main start -->
		<div id="main">
			<!-- juken start -->
			<div id="dantai_form" class="form_wrp">
				<h2 class="form_tit m_b15">団体受験申請フォーム確認画面</h2>
				<form action="thanks.php" method="POST">
					<input type="hidden" name="token" value="<?= h($_SESSION['token']) ?>">
					<input type="hidden" name="q1" value="<?= h($_POST['q1'] ?? '') ?>">
					<input type="hidden" name="q2" value="<?= h($_POST['q2'] ?? '') ?>">
					<input type="hidden" name="group_name" value="<?= h($_POST['group_name'] ?? '') ?>">
					<input type="hidden" name="group_num" value="<?= h($_POST['group_num'] ?? '') ?>">
					<input type="hidden" name="name_sei" value="<?= h($_POST['name_sei'] ?? '') ?>">
					<input type="hidden" name="name_mei" value="<?= h($_POST['name_mei'] ?? '') ?>">
					<input type="hidden" name="name_kana_sei" value="<?= h($_POST['name_kana_sei'] ?? '') ?>">
					<input type="hidden" name="name_kana_mei" value="<?= h($_POST['name_kana_mei'] ?? '') ?>">
					<input type="hidden" name="zip1" value="<?= h($_POST['zip1'] ?? '') ?>">
					<input type="hidden" name="zip2" value="<?= h($_POST['zip2'] ?? '') ?>">
					<input type="hidden" name="todouhuken" value="<?= h($_POST['todouhuken'] ?? '') ?>">
					<input type="hidden" name="city" value="<?= h($_POST['city'] ?? '') ?>">
					<input type="hidden" name="city2" value="<?= h($_POST['city2'] ?? '') ?>">
					<input type="hidden" name="city3" value="<?= h($_POST['city3'] ?? '') ?>">
					<input type="hidden" name="city4" value="<?= h($_POST['city4'] ?? '') ?>">
					<input type="hidden" name="tel" value="<?= h($_POST['tel'] ?? '') ?>">
					<input type="hidden" name="mail" value="<?= h($_POST['mail'] ?? '') ?>">
					<input type="hidden" name="check" value="<?= h($_POST['check'] ?? '') ?>">

					<!--form_box start-->
					<div class="form_box">
						<table class="main_table">
							<tbody>
								<tr>
									<th>過去に団体受験案内の申請をしたことがありますか？</th>
									<td><?= h($q1) ?></td>
								</tr>
								<tr>
									<th>過去に団体受験受験をしたことがありますか？</th>
									<td><?= h($q2) ?></td>
								</tr>
								<tr>
									<th>企業・学校等の団体名</th>
									<td><?= h($group_name) ?></td>
								</tr>
								<tr>
									<th>予定受験者数<br>※概数</th>
									<td><?= h($group_num) ?></td>
								</tr>
								<tr>
									<th>氏名</th>
									<td><?= h($name_sei . " " . $name_mei) ?></td>
								</tr>
								<tr>
									<th>フリガナ</th>
									<td><?= h($name_kana_sei . " " . $name_kana_mei) ?></td>
								</tr>
								<tr>
									<th>住所</th>
									<td>
										〒<?= h($zip1 . "-" . $zip2) ?><br>
										<?= h($todouhuken . $city . $city2) ?><br>
										<?= h($city3) ?><br>
										<?= h($city4) ?></td>
								</tr>
								<tr>
									<th>電話番号</th>
									<td><?= h($tel) ?></td>
								</tr>
								<tr>
									<th>メールアドレス</th>
									<td><?= h($mail) ?></td>
								</tr>
								<tr>
									<th>当協会の個人情報取り扱いにご同意していただけますか？</th>
									<td><?= h($check) ?></td>
								</tr>
							</tbody>
						</table>
					</div>

					<div id="contact_btn">
						<button type="submit" name="back" class="next_btn back">戻る</button>
						<button type="submit" name="send" class="next_btn">送信</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div id="footer">
		<p id="footer_copy">Copyright(C) 2019 FLAネットワーク協会 All Rights Reserved</p>
	</div>
</body>

</html>