<?php
session_start();

// CSRFトークン生成
$token = bin2hex(random_bytes(32));
$_SESSION['token'] = $token;

require_once 'function.php';

$data = $_SESSION['form'] ?? [];

$q1 = $data['q1'] ?? '';
$q2 = $data['q2'] ?? '';
$group_name = $data['group_name'] ?? '';
$group_num = $data['group_num'] ?? '';
$name_sei = $data['name_sei'] ?? '';
$name_mei = $data['name_mei'] ?? '';
$name_kana_sei = $data['name_kana_sei'] ?? '';
$name_kana_mei = $data['name_kana_mei'] ?? '';
$zip1 = $data['zip1'] ?? '';
$zip2 = $data['zip2'] ?? '';
$todouhuken = $data['todouhuken'] ?? '';
$city = $data['city'] ?? '';
$city2 = $data['city2'] ?? '';
$city3 = $data['city3'] ?? '';
$city4 = $data['city4'] ?? '';
$tel = $data['tel'] ?? '';
$mail = $data['mail'] ?? '';
$check = $data['check'] ?? '';

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
	<script src="https://ajaxzip3.github.io/ajaxzip3.js"></script>
	<title>団体受験申請フォーム｜食生活アドバイザー®｜FLAネットワーク協会</title>
</head>
<body>

<div class="header_main">
	<h1 id="keyword"><span>健康増進　健康経営　栄養管理　ダイエット　女性</span> 食と生活に関する資格なら食生活アドバイザー<sup>®</sup></h1>
	<p class="logo"><a href="http://www.flanet.jp/"><img src="../img/logo.gif" alt="一般社団法人 FLAネットワーク協会 食生活アドバイザー検定"></a></p>
</div>

<h2>団体受験申請フォーム</h2>
<p><a href="javascript:history.length>1?history.back():location.href='/index.html'">[ 前ページへ戻る]</a></p>

<h3>入力フォーム</h3>

<form action="confirm.php" method="post">
  <input type="hidden" name="token" value="<?= h($token) ?>">

	<table class="main_table">
		<tr>
			<th>過去に団体受験案内の申請をしたことがありますか？<span class="red">(必須)</span></th>
			<td>
				<label><input type="radio" name="q1" value="1" <?= ($data['q1'] ?? '') === '1' ? 'checked' : '' ?> required>はい</label>
				<label><input type="radio" name="q1" value="0" <?= ($data['q1'] ?? '') === '0' ? 'checked' : '' ?>>いいえ</label>
			</td>
		</tr>
		<tr>
			<th>過去に団体受験受験をしたことがありますか？<span class="red">(必須)</span></th>
			<td>
					<label><input type="radio" name="q2" value="1" <?= ($data['q2'] ?? '') === '1' ? 'checked' : '' ?> required>はい</label>
					<label><input type="radio" name="q2" value="0" <?= ($data['q2'] ?? '') === '0' ? 'checked' : '' ?>>いいえ</label>
			</td>
		</tr>
		<tr>
			<th>企業・学校等の団体名<span class="red">(必須)</span></th>
			<td><input class="w95p" type="text" name="group_name" value="<?= h($group_name) ?>" maxlength="100" required></td>
			
		</tr>
		<tr>
			<th>予定受験者数<span class="red">(必須)</span><br>※概数</th>
			<td><input name="group_num" type="number" class="w20p" value="<?= h($group_num) ?>" min="1" required>人</td>
		</tr>
	</table>

	<h3 class="form_sub_tit">氏名等</h3>
	<table class="main_table">
		<tr>
			<th>氏名<span class="red">(必須)</span><br>(全角)</th>
			<td>
				<input type="text" class="w40p" name="name_sei" value="<?= h($name_sei) ?>" maxlength="50" placeholder="姓" required data-zenkaku>
				<input type="text" class="w40p" name="name_mei" value="<?= h($name_mei) ?>" maxlength="50" placeholder="名" required data-zenkaku>
			</td>
		</tr>
		<tr>
			<th>フリガナ<span class="red">(必須)</span><br>(全角)</th>
			<td>
				<input type="text" class="w40p"name="name_kana_sei" value="<?= h($name_kana_sei) ?>" maxlength="50" placeholder="セイ" required data-kana>
				<input type="text" class="w40p"name="name_kana_mei" value="<?= h($name_kana_mei) ?>" maxlength="50" placeholder="メイ" required data-kana>
			</td>
		</tr>
	</table>

	<h3 class="form_sub_tit">住所等</h3>
	<table class="main_table">
		<tr>
			<th width="120">郵便番号<span class="red">(必須)</span></th>
			<td>
				〒<input name="zip1" value="<?= h($zip1) ?>" type="text" size="8" id="zip1" maxlength="3" data-number required>-
				<input name="zip2" value="<?= h($zip2) ?>" type="text" id="zip2" maxlength="4" class="w100" data-number onkeyup="AjaxZip3.zip2addr('zip1','zip2','todouhuken','city','city2');" required>
			</td>
		</tr>
		<tr class="zipSuggest">
			<th>都道府県<span class="red">(必須)</span></th>
			<td><input name="todouhuken" type="text" class="w95p" value="<?= h($todouhuken) ?>" maxlength="10" required></td>
		</tr>
		<tr class="zipSuggest">
			<th>市区町村<span class="red">(必須)</span><br>(全角)</th>
			<td><input name="city" type="text" class="w95p" value="<?= h($city) ?>" placeholder="例：新宿区" maxlength="100" required></td>
		</tr>
		<tr><th>町域番地<span class="red">(必須)</span><br> (全角)</th>
			<td><input name="city2" type="text" class="w95p" value="<?= h($city2) ?>" placeholder="例：西新宿７－１５－１０" maxlength="100" required></td>
		</tr>
		<tr>
			<th>建物名・様方<br> (全角)</th>
			<td><input name="city3" type="text" class="w95p" value="<?= h($city3) ?>" placeholder="例：大山ビル２Ｆ" maxlength="100"></td>
		</tr>
		<tr>
			<th>部署名<br> (全角)</th>
			<td><input name="city4" type="text" class="w95p" value="<?= h($city4) ?>" placeholder="例：検定事務局" maxlength="100"></td>
		</tr>
	</table>

	<h3 class="form_sub_tit">電話番号</h3>
	<table class="main_table">
		<tr>
			<th>電話番号<span class="red">(必須)</span><br>(半角)</th>
			<td><input name="tel" type="tel" class="w95p" value="<?= h($tel) ?>" data-tel maxlength="13" required></td>
		</tr>
	</table>

	<h3 class="form_sub_tit">メールアドレス</h3>
	<table class="main_table">
		<tr>
			<th>メールアドレス<span class="red">(必須)</span></th>
			<td><input name="mail" type="email" class="w95p" value="<?= h($mail) ?>" maxlength="100" required></td>
		</tr>
	</table>

	<div class="form_regal">
            <table cellpadding="0" cellspacing="15">
              <tbody><tr>
                <td colspan="2"><b>個人情報の取扱いについて</b></td>
              </tr>
              <tr valign="top">
                <td colspan="2">一般社団法人FLAネットワーク協会（以下、当協会という。）では、お預かりする個人情報を以下のような目的で適切に取扱います。<br>ご提供いただきました個人情報については、1年間当協会にて保管し、当協会にて責任をもって廃棄させていただきます。16歳未満の方は保護者の同意を得た上でお申し込みください。
</td>

              </tr>
              <tr valign="top">
                <td colspan="2">【事業者の名称】<br>一般社団法人FLAネットワーク協会</td>
              </tr>
              <tr valign="top">
                <td colspan="2">【利用目的】<br>受験票の作成、受講票の作成、合否通知の作成、各種情報の提供及びサービス向上のため</td>
              </tr>
              <tr valign="top">
                <td colspan="2">【委託について】<br>前項の利用目的の達成に必要な範囲内で個人情報を外部に委託することがあります。</td>
              </tr>
              <tr>
                <td><table cellpadding="0" cellspacing="2">
                    <tbody><tr valign="top">
                      <td>【提供について】　当協会は、以下のいずれかに該当する場合を除き、第三者への提供はいたしません。</td>
                    </tr>
                    <tr valign="top">
                      <td>●貴殿が事前に承諾された場合。　<br>●法令に基づく場合。　<br>●人の生命、身体又は財産の保護のために必要がある場合であって、本人の同意を得ることが困難であるとき。　<br>●公衆衛生の向上又は児童の健全な育成の推進のために特に必要がある場合であって、本人の同意を得ることが困難であるとき。　<br>●国の機関若しくは地方公共団体又はその委託を受けた者が法令の定める事務を遂行することに対して協力する必要がある場合であって、本人の同意を得ることによって当該事務の遂行に支障を及ぼすおそれがあるとき。</td>
                    </tr>
                  </tbody></table></td>
              </tr>
              <tr valign="top">
                <td colspan="2">【個人情報を与えることの任意性について】　<br>個人情報のご提供は任意ですが、【利用目的】を遂行するために必要なものです。 ご入力いただけない項目がある場合、お申込をお受けできない場合がございます。 ※必須項目をご入力いただけない場合、送信することができませんのでご了承ください。 </td>
              </tr>
              <tr valign="top">
                <td colspan="2">【個人情報の開示等の窓口】　<br>ご提供いただいた個人情報の開示等に関して下記のお問合せ窓口に申し出ることができます。<br> 一般社団法人FLAネットワーク協会　<br>電話番号：03-3371-3550　<br>住所：〒160-0023　<br>東京都新宿区西新宿7-15-10　<br>大山ビル2F </td>
              </tr>
              <tr valign="top">
                <td colspan="2">【個人情報保護管理者】　<br>一般社団法人FLAネットワーク協会 事務局長　<br>電話番号：03-3371-3550 </td>
              </tr>
            </tbody></table>
          </div>
					
					<div class="regal_checl align_c">
						<p><input type="checkbox" name="check" value="1" id="check1" <?= ($data['check'] ?? '') === '1' ? 'checked' : '' ?> required><label for="check1">個人情報の取扱いについて同意する</label>
					</p>
				</div>
				
				<div id="contact_btn">
					<button type="submit" class="next_btn">確認画面</button>
				</div>

				<!--全ページカウンター--> 
				<img src="http://www.flanet.jp/daycount11/daycount.cgi?gif" width="0" height="0" border="0" alt="all"> <img src="http://www.flanet.jp/daycount11/daycount.cgi?today" width="0" height="0" border="0" alt="today"> <img src="http://www.flanet.jp/daycount11/daycount.cgi?yes" width="0" height="0" border="0" alt="yes"> 
				<!---->
				<!--shinobi1--><script type="text/javascript" src="//xa.shinobi.jp/ufo/19079641r"></script><noscript><a href="//xa.shinobi.jp/bin/gg?19079641r" target="_blank"><img src="//xa.shinobi.jp/bin/ll?19079641r" border="0"></a><br><span style="font-size:9px"><img style="margin:0;vertical-align:text-bottom;" src="//img.shinobi.jp/tadaima/fj.gif" width="19" height="11"> </span></noscript><!--shinobi2-->
</form>
<div id="footer">
    <p id="footer_copy">Copyright(C) 2019 FLAネットワーク協会 All Rights Reserved</p>
  </div>
</body>
</html>