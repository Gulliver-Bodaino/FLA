<?php
session_start();

// CSRFチェック
if (!isset($_POST['token']) || !isset($_SESSION['token']) || $_POST['token'] !== $_SESSION['token']) {
	header('Location: ./');
	exit;
}

require_once 'function.php';

// 「戻る」ボタンが押された場合は送信処理を行わずindex.phpへ戻す
if (isset($_POST['back'])) {
	header('Location: ./');
	exit;
}

// 値取得＋再バリデーション(confirm.phpと同じチェックをここでも実施)
$q1            = q($_POST['q1'] ?? '');
$q2            = q($_POST['q2'] ?? '');
$group_name    = text_max($_POST['group_name'] ?? '', 100);
$group_num     = num_max($_POST['group_num'] ?? '', 10000);

$name_sei      = text_max($_POST['name_sei'] ?? '', 50);
$name_mei      = text_max($_POST['name_mei'] ?? '', 50);
$name_kana_sei = text_max($_POST['name_kana_sei'] ?? '', 50);
$name_kana_mei = text_max($_POST['name_kana_mei'] ?? '', 50);

$zip1          = zip_check($_POST['zip1'] ?? '', 3);
$zip2          = zip_check($_POST['zip2'] ?? '', 4);
$todouhuken    = text_max($_POST['todouhuken'] ?? '', 10);
$city          = text_max($_POST['city'] ?? '', 100);
$city2         = text_max($_POST['city2'] ?? '', 100);
$city3         = text_max($_POST['city3'] ?? '', 100, false);
$city4         = text_max($_POST['city4'] ?? '', 100, false);

$tel           = tel_check($_POST['tel'] ?? '');
$mail          = mail_check($_POST['mail'] ?? '');
$check         = check($_POST['check'] ?? '');

// メール本文用に整形
$name_full = $name_sei . " " . $name_mei;
$kana_full = $name_kana_sei . " " . $name_kana_mei;

$address_block  = "〒{$zip1}-{$zip2}\n";
$address_block .= "{$todouhuken}{$city}{$city2}\n";
$address_block .= "{$city3}\n";
$address_block .= "{$city4}";

$common_body  = "■過去に団体受験案内の申請をしたことがありますか？\n{$q1}\n\n";
$common_body .= "■過去に団体受験受験をしたことがありますか？\n{$q2}\n\n";
$common_body .= "■企業・学校等の団体名\n{$group_name}\n\n";
$common_body .= "■予定受験者数※概数\n{$group_num}\n\n";
$common_body .= "■氏名\n{$name_full}\n\n";
$common_body .= "■フリガナ\n{$kana_full}\n\n";
$common_body .= "■住所\n{$address_block}\n\n";
$common_body .= "■電話番号\n{$tel}\n\n";
$common_body .= "■メールアドレス\n{$mail}\n\n";
$common_body .= "■当協会の個人情報取り扱いにご同意していただけますか？\n{$check}\n";


$divider = str_repeat('-', 63);

// ---------- 登録者用メール ----------
$user_subject = "【食生活アドバイザー 検定事務局】 {$name_full}様 団体受験申請ありがとうございました。";

$user_body  = "{$name_sei} 様\n";
$user_body .= "団体受験申請ありがとうございました。\n";
$user_body .= "団体受験申請いただきました内容は、下記のとおりです。\n";
$user_body .= "{$divider}\n";
$user_body .= $common_body;
$user_body .= "{$divider}\n";
$user_body .= "***************************\n";
$user_body .= "食生活アドバイザー 検定事務局\n";
$user_body .= "一般社団法人　ＦＬＡネットワーク協会\n";
$user_body .= "〒160-0023　東京都新宿区西新宿7-15-10　大山ビル2F\n";
$user_body .= "フリーダイヤル：0120-86-3593\n";
$user_body .= "TEL：03-3371-3593\n";
$user_body .= "月曜日～金曜日 10:00 ～ 16:00 （土日祝日 除く）\n";
$user_body .= "***************************\n";

$user_sent = send_jp_mail($mail, $user_subject, $user_body, 'fla.dantai@flanet.jp', null, '食生活アドバイザー 検定事務局');

// ---------- 管理者用メール ----------
$admin_subject = "【{$name_sei}様】団体受験申請がありました";

$admin_body  = "団体受験申請がありました。\n";
$admin_body .= "対応をお願いします。\n";
$admin_body .= "{$divider}\n";
$admin_body .= $common_body;
$admin_body .= "{$divider}\n";

$admin_to   = "fla.dantai@flanet.jp";
$admin_sent = send_jp_mail($admin_to, $admin_subject, $admin_body, 'fla.dantai@flanet.jp', $mail, '食生活アドバイザー 検定事務局', "{$name_sei} {$name_mei}");

if (!$user_sent || !$admin_sent) {
	error_log("メール送信失敗: user={$user_sent}, admin={$admin_sent}, mail={$mail}");
}

// CSV保存用データ(氏名・フリガナ・住所は結合、指定の列順に合わせる)
$csv_header = [
	'登録日',
	'過去に団体受験案内の願書請求をしたことがありますか？',
	'過去に団体受験受験をしたことがありますか？',
	'企業・学校等の団体名',
	'予定受験者数',
	'氏名',
	'フリガナ',
	'郵便番号',
	'住所',
	'電話番号',
	'メールアドレス',
];

$row = [
	date("Y-m-d H:i:s"),
	$q1,
	$q2,
	$group_name,
	$group_num,
	$name_sei . " " . $name_mei,
	$name_kana_sei . " " . $name_kana_mei,
	$zip1 . "-" . $zip2,
	$todouhuken . $city . $city2 . " " . $city3 . " " . $city4,
	$tel,
	$mail,
];

// UTF-8 → Shift_JIS（Excel向け）
$header_sjis = array_map(fn($v) => mb_convert_encoding((string)$v, "SJIS-win", "UTF-8"), $csv_header);
$row_sjis = array_map(fn($v) => mb_convert_encoding((string)$v, "SJIS-win", "UTF-8"), $row);

$csv_path = '../../csvdata/dantai_form.csv';
$is_new_file = !file_exists($csv_path);

$csv = fopen($csv_path, "a");
if ($csv && flock($csv, LOCK_EX)) {
	if ($is_new_file) {
		fputcsv($csv, $header_sjis);
	}
	fputcsv($csv, $row_sjis);
	flock($csv, LOCK_UN);
}
if ($csv) {
	fclose($csv);
}
unset($_SESSION['form']);
unset($_SESSION['token']);
// 完了画面
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
	<title>送信完了</title>
</head>

<body>
	<div id="wrapper" class="form_main"> <a name="page_top" id="page_top"></a>

		<div id="header" class="cont_flex" style="align-items: center;">
			<div class="header_main">
				<h1 id="keyword"><span>健康増進　健康経営　栄養管理　ダイエット　女性</span> 食と生活に関する資格なら食生活アドバイザー<sup>®</sup></h1>
				<p class="logo"><a href="http://www.flanet.jp/"><img src="../img/logo.gif" alt="一般社団法人 FLAネットワーク協会 食生活アドバイザー検定"></a></p>
			</div>
			<div class="header_ssl">
				<script src="//ssif1.globalsign.com/SiteSeal/siteSeal/siteSeal/siteSeal.do?p1=gulliver.xsrv.jp&amp;p2=SZ115-57&amp;p3=image&amp;p4=ja&amp;p5=V0001&amp;p6=S001&amp;p7=https"></script><span><img name="ss_imgTag" border="0" src="//ssif1.globalsign.com/SiteSeal/siteSeal/siteSeal/siteSealImage.do?p1=gulliver.xsrv.jp&amp;p2=SZ115-57&amp;p3=image&amp;p4=ja&amp;p5=V0001&amp;p6=S001&amp;p7=https&amp;deterDn=" alt="" oncontextmenu="return false;" galleryimg="no" style="width:115px"></span><span id="ss_siteSeal_fin_SZ115-57_image_ja_V0001_S001"></span>
				<script type="text/javascript" src="//seal.globalsign.com/SiteSeal/gs_flash_115-57_ja.js"></script>
			</div>
		</div>
		<div id="contents_form">
			<div id="main">
				<div id="dantai_form" class="form_wrp">
					<h2 class="form_tit m_b15">団体受験申請フォーム</h2>
					<div id="thanks_box">
						<p class="m_b25">団体受験の申請ありがとうございました。<br>
							団体受験に必要なデータにつきましてはご担当者様のメールアドレスに送信いたします。<br>
							万が一、１週間経ってもデータが届いていない場合は、検定事務局までご連絡ください。<br>
							(ただし、連休をはさむ場合は遅れることがありますのでご了承ください。)</p>
						<p class="txt24 txt_b"><span class=" js_tel-btn" href="0120-86-3593">フリーダイヤル：0120-86-3593</span></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="footer">
		<p id="footer_copy">Copyright(C) 2019 FLAネットワーク協会 All Rights Reserved</p>
	</div>

	<img src="http://www.flanet.jp/daycount11/daycount.cgi?gif" width="0" height="0" border="0" alt="all"> <img src="http://www.flanet.jp/daycount11/daycount.cgi?today" width="0" height="0" border="0" alt="today"> <img src="http://www.flanet.jp/daycount11/daycount.cgi?yes" width="0" height="0" border="0" alt="yes">
	<script type="text/javascript" src="//xa.shinobi.jp/ufo/19079641r"></script><noscript><a href="//xa.shinobi.jp/bin/gg?19079641r" target="_blank"><img src="//xa.shinobi.jp/bin/ll?19079641r" border="0"></a><br><span style="font-size:9px"><img style="margin:0;vertical-align:text-bottom;" src="//img.shinobi.jp/tadaima/fj.gif" width="19" height="11"> </span></noscript><!--shinobi2-->

</body>

</html>