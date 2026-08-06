<?php
function h($str){
	return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function q($a)
{
	if ($a == 0) {
		$text = 'いいえ';
	} elseif ($a == 1) {
		$text = 'はい';
	} else {
		header('Location: ./');
		exit;
	}
	return $text;
}

/**
 * 文字列の必須チェック＋最大文字数チェック
 * $required = false の場合は空文字を許可(その場合、値があれば文字数チェックのみ行う)
 */
function text_max($a, $max, $required = true)
{
	$a = trim($a ?? '');

	if ($required && $a === '') {
		header('Location: ./');
		exit;
	}

	if (mb_strlen($a) > $max) {
		header('Location: ./');
		exit;
	}

	return $a;
}

/**
 * 数値の必須チェック＋最大値チェック(受験者数などに使用)
 * 半角数字のみを許可し、0または未入力は不正とする
 */
function num_max($a, $max, $required = true)
{
	$a = trim($a ?? '');

	if ($a === '') {
		if ($required) {
			header('Location: ./');
			exit;
		}
		return '';
	}

	if (!ctype_digit($a) || (int)$a < 1 || (int)$a > $max) {
		header('Location: ./');
		exit;
	}

	return (int)$a;
}

/**
 * 電話番号の必須チェック＋形式チェック
 * ハイフンあり/なし両方OK。桁数チェックはハイフンを除いた数字部分で行う。
 * 戻り値は入力されたそのままの文字列(ハイフンの有無を保持)。
 */
function tel_check($a, $required = true)
{
	$a = trim($a ?? '');

	if ($a === '') {
		if ($required) {
			header('Location: ./');
			exit;
		}
		return '';
	}

	// 数字とハイフンのみで構成されているかチェック
	if (!preg_match('/^[0-9\-]+$/', $a)) {
		header('Location: ./');
		exit;
	}

	// 桁数チェックはハイフンを除いた数字部分で行う
	$digits = str_replace('-', '', $a);

	if (!ctype_digit($digits) || mb_strlen($digits) < 9 || mb_strlen($digits) > 11) {
		header('Location: ./');
		exit;
	}

	return $a; // 入力された形式(ハイフンあり/なし)をそのまま返す
}

/**
 * メールアドレスの必須チェック＋形式チェック
 */
function mail_check($a, $max = 100, $required = true)
{
	$a = trim($a ?? '');

	if ($a === '') {
		if ($required) {
			header('Location: ./');
			exit;
		}
		return '';
	}

	if (mb_strlen($a) > $max) {
		header('Location: ./');
		exit;
	}

	if (!filter_var($a, FILTER_VALIDATE_EMAIL)) {
		header('Location: ./');
		exit;
	}

	return $a;
}

/**
 * 郵便番号(数字部分)の必須チェック＋桁数チェック
 * $len は必須桁数(zip1=3, zip2=4 を想定)
 */
function zip_check($a, $len, $required = true)
{
	$a = trim($a ?? '');

	if ($a === '') {
		if ($required) {
			header('Location: ./');
			exit;
		}
		return '';
	}

	if (!ctype_digit($a) || mb_strlen($a) !== $len) {
		header('Location: ./');
		exit;
	}

	return $a;
}

function check($a)
{
	if ($a == 0) {
		header('Location: ./');
		exit;
	} elseif ($a == 1) {
		$text = '個人情報の取扱いについて同意する';
	} else {
		header('Location: ./');
		exit;
	}
	return $text;
}

/**
 * 日本語メールを送信する(UTF-8のまま送信。sendmail経由)
 * 件名はMIMEエンコードし、本文はUTF-8のまま送る。
 * 主要メーラー(Gmail, Outlook, Thunderbird等)であれば問題なく表示される。
 * $from_name / $reply_to_name を指定すると、差出人・返信先に表示名を付与する。
 */
function send_jp_mail($to, $subject, $body, $from, $reply_to = null, $from_name = null, $reply_to_name = null)
{
	$subject_enc = mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n");

	$from_header = $from_name
		? mb_encode_mimeheader($from_name, 'UTF-8', 'B', "\r\n") . " <{$from}>"
		: $from;

	$headers  = "From: {$from_header}\r\n";

	if ($reply_to) {
		$reply_header = $reply_to_name
			? mb_encode_mimeheader($reply_to_name, 'UTF-8', 'B', "\r\n") . " <{$reply_to}>"
			: $reply_to;
		$headers .= "Reply-To: {$reply_header}\r\n";
	}

	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
	$headers .= "Content-Transfer-Encoding: 8bit";

	return mail($to, $subject_enc, $body, $headers);
}