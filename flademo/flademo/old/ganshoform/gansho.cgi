#!/usr/bin/perl

# --------------------------------
# モジュール読込
# --------------------------------
use CGI::Carp qw(fatalsToBrowser);
use lib './lib';
use File::Copy;
use File::Basename;
use Unicode::Japanese;
require 'lib.pl';

# ------------------
require 'cgi-lib.pl';
require 'jcode.pl';

# --------------------------------
# 初期設定
# --------------------------------

# 自スクリプト
my $script = 'https://ssl.flanet.jp/ganshoform/gansho.cgi';    # 本番用

# my $script = 'gansho.cgi';

# 出力CSV
$csv_dir     = '../csvdata';
$backup_dir  = '../csvdata/csvbackup';
$csv_data    = 'gansho.csv';
$backup_data = 'backupgansho.csv';

# 本番用
$to1  = 'fla.entry@flanet.jp';    # 管理者アドレス
$from = 'fla.entry@flanet.jp';    # from用管理者アドレス

# 検証用
# $to1  = 'ishii-k@ww-system.com';                                # 管理者アドレス
# $from = 'ishii-k@ww-system.com';                                # from用管理者アドレス

# メールタイトル
$subject  = '様から願書の請求がありました。';              # 管理者用
$subject2 = '食生活アドバイザー検定　願書請求登録完了';    # ユーザ用

# --------------------------------
# 処理
# --------------------------------
&kensa;

if ( $kensa eq "a" ) {

    # 確認処理
    &inputdata;                                            # データ整形
    &privacydata;                                          # 個人情報取り扱い同意チェック
    &checkdata;                                            # 入力データチェック
    &outputshori;                                          # タグ調整
    &outputdata;                                           # 確認画面表示
}

elsif ( $kensa eq "b" ) {

    # 送信処理
    &time;                                                 # 時間取得
    &inputdata;                                            # データ整形
    &fileinput;                                            # CSV出力
    &mailmail;                                             # メールデータ作成
    &maildata1;                                            # 管理者へのメール送信
    &maildata2;                                            # ユーザへのメール送信
    &success;                                              # 完了画面表示
}
else {

    # エラー処理
    &errormax;
}

##########検査##############
sub kensa {
    &ReadParse(*formdata);
    $kensa = $formdata{'kensa'};
}

##########年月日週##########
sub time {
    ( $sec, $min, $hour, $day, $mon, $year, $wday ) = localtime(time);
    $mon++;
    @tm = ( "日", "月", "火", "水", "木", "金", "土" );
    $year = ( $year + 1900 );
}

##########項目＆文字コード指定##########
sub inputdata {
    &ReadParse(*formdata);

    # 個人情報データ
    $shitsumon1 = $formdata{'shitsumon1'};
    $shitsumon2 = $formdata{'shitsumon2'};

    $name1       = $formdata{'name1'};
    $name2       = $formdata{'name2'};
    $kana1       = $formdata{'kana1'};
    $kana2       = $formdata{'kana2'};
    $birth_year  = $formdata{'birth_year'};
    $birth_month = $formdata{'birth_month'};
    $birth_day   = $formdata{'birth_day'};
    $sei         = $formdata{'sei'};

    $add1    = $formdata{'add1'};
    $add2    = $formdata{'add2'};
    $ken     = $formdata{'ken'};
    $juusho1 = $formdata{'juusho1'};
    $juusho2 = $formdata{'juusho2'};
    $juusho3 = $formdata{'juusho3'};
    $kaisha  = $formdata{'kaisha'};
    $bu      = $formdata{'bu'};

    $telshu = $formdata{'telshu'};
    $tel1   = $formdata{'tel1'};
    $tel2   = $formdata{'tel2'};
    $tel3   = $formdata{'tel3'};

    $mail1 = $formdata{'mail1'};
    $mail2 = $formdata{'mail2'};
    $mail3 = $formdata{'mail3'};
    $mail4 = $formdata{'mail4'};
    
    $doui = $formdata{'doui'};

    # ----------------------------------------------
    # 追加処理
    # ----------------------------------------------
    # 小文字を大文字に
    $name1   = Unicode::Japanese->new( $name1,   'sjis' )->h2zKana->sjis;
    $name2   = Unicode::Japanese->new( $name2,   'sjis' )->h2zKana->sjis;
    $kana1   = Unicode::Japanese->new( $kana1,   'sjis' )->h2zKana->sjis;
    $kana2   = Unicode::Japanese->new( $kana2,   'sjis' )->h2zKana->sjis;
    $juusho1 = Unicode::Japanese->new( $juusho1, 'sjis' )->h2zKana->sjis;
    $juusho2 = Unicode::Japanese->new( $juusho2, 'sjis' )->h2zKana->sjis;
    $juusho3 = Unicode::Japanese->new( $juusho3, 'sjis' )->h2zKana->sjis;
    $kaisha  = Unicode::Japanese->new( $kaisha,  'sjis' )->h2zKana->sjis;
    $bu      = Unicode::Japanese->new( $bu,      'sjis' )->h2zKana->sjis;

    &jcode'convert( *subject,  'jis' );
    &jcode'convert( *subject2, 'jis' );

    jcode::convert( \shitsumon1,  "sjis", "", "z" );
    jcode::convert( \shitsumon2,  "sjis", "", "z" );
    jcode::convert( \name1,       "sjis", "", "z" );
    jcode::convert( \name2,       "sjis", "", "z" );
    jcode::convert( \kana1,       "sjis", "", "z" );
    jcode::convert( \kana2,       "sjis", "", "z" );
    jcode::convert( \birth_year,  "sjis", "", "z" );
    jcode::convert( \birth_month, "sjis", "", "z" );
    jcode::convert( \birth_day,   "sjis", "", "z" );
    jcode::convert( \sei,         "sjis", "", "z" );

    jcode::convert( \add1,    "sjis", "", "z" );
    jcode::convert( \add2,    "sjis", "", "z" );
    jcode::convert( \ken,     "sjis", "", "z" );
    jcode::convert( \juusho1, "sjis", "", "z" );
    jcode::convert( \juusho2, "sjis", "", "z" );
    jcode::convert( \juusho3, "sjis", "", "z" );
    jcode::convert( \kaisha,  "sjis", "", "z" );
    jcode::convert( \bu,      "sjis", "", "z" );

    jcode::convert( \telshu, "sjis", "", "z" );
    jcode::convert( \tel1,   "sjis", "", "z" );
    jcode::convert( \tel2,   "sjis", "", "z" );
    jcode::convert( \tel3,   "sjis", "", "z" );

    jcode::convert( \mail1, "sjis", "", "z" );
    jcode::convert( \mail2, "sjis", "", "z" );
    jcode::convert( \mail3, "sjis", "", "z" );
    jcode::convert( \mail4, "sjis", "", "z" );
    
    jcode::convert( \doui, "sjis", "", "z" );

}

##########ファイルにデータの出力をする。##############################

sub fileinput {

    # my $filedata = sprintf( '%s/csv/%04d-%02d.csv', $dir{'db'}, &YY, &MM );

    # データ調整
    if ( $mon == "1" ) { $mon = "01"; }
    if ( $mon == "2" ) { $mon = "02"; }
    if ( $mon == "3" ) { $mon = "03"; }
    if ( $mon == "4" ) { $mon = "04"; }
    if ( $mon == "5" ) { $mon = "05"; }
    if ( $mon == "6" ) { $mon = "06"; }
    if ( $mon == "7" ) { $mon = "07"; }
    if ( $mon == "8" ) { $mon = "08"; }
    if ( $mon == "9" ) { $mon = "09"; }

    if ( $day == "1" ) { $day = "01"; }
    if ( $day == "2" ) { $day = "02"; }
    if ( $day == "3" ) { $day = "03"; }
    if ( $day == "4" ) { $day = "04"; }
    if ( $day == "5" ) { $day = "05"; }
    if ( $day == "6" ) { $day = "06"; }
    if ( $day == "7" ) { $day = "07"; }
    if ( $day == "8" ) { $day = "08"; }
    if ( $day == "9" ) { $day = "09"; }

    $timenow  = "$year/$mon/$day";
    $yobinow  = "@tm[$wday]曜日";
    $jikannew = "$hour$min";
    $namenew  = "$name1 $name2";
    $kananew  = "$kana1 $kana2";
    $birthnew = sprintf( '%04d/%02d/%02d', $birth_year, $birth_month, $birth_day );
    $addnew   = "$add1$add2";
    $telnew   = "$tel1-$tel2-$tel3";
    $mailnew  = "$mail1\@$mail2";
    $kennew   = "$ken$juusho";

    # CSVデータ
    my $csv;
    my $csv_back;

    if ( !-e qq|$csv_dir/$csv_data| ) {
        $csv = &make_csv(    # *
            '登録日',
            '時間',
            '曜日',
            '過去に請求',
            '過去に受験',
            '氏名',
            'フリガナ',
            '生年月日',
            '性別',
            '郵便番号',
            '都道府県',
            '市区町村',
            '番地',
            '建物名・様方',
            '勤務先名',
            '部署名',
            '電話番号先',
            '電話番号',
            'メールアドレス',
        );
    }

    $csv .= &make_csv(    # *
        "$timenow",
        "$jikannew",
        "$yobinow",
        "$shitsumon1",
        "$shitsumon2",
        "$namenew",
        "$kananew",
        "$birthnew",
        "$sei",
        "$addnew",
        "$ken",
        "$juusho1",
        "$juusho2",
        "$juusho3",
        "$kaisha",
        "$bu",
        "$telshu",
        "$telnew",
        "$mailnew",
    );

    if ( !-e qq|$backup_dir/$backup_data| ) {
        $csv_back = &make_csv(    # *
            '登録日',
            '時間',
            '曜日',
            '過去に請求',
            '過去に受験',
            '氏名',
            'フリガナ',
            '生年月日',
            '性別',
            '郵便番号',
            '都道府県',
            '市区町村',
            '番地',
            '建物名・様方',
            '勤務先名',
            '部署名',
            '電話番号先',
            '電話番号',
            'メールアドレス',
        );
    }

    $csv_back .= &make_csv(    # *
        "$timenow",
        "$jikannew",
        "$yobinow",
        "$shitsumon1",
        "$shitsumon2",
        "$namenew",
        "$kananew",
        "$birthnew",
        "$sei",
        "$addnew",
        "$ken",
        "$juusho1",
        "$juusho2",
        "$juusho3",
        "$kaisha",
        "$bu",
        "$telshu",
        "$telnew",
        "$mailnew",
    );
    
    # 追記
    my_open( OUT, ">>$csv_dir/$csv_data" );
    binmode OUT;
    print OUT $csv;
    my_close(OUT);

    # バックアップCSV追記
    my_open( OUT, ">>$backup_dir/$backup_data" );
    binmode OUT;
    print OUT $csv_back;
    my_close(OUT);
}

# ---------------------------------------------------------------
# メールデータ作成
# ---------------------------------------------------------------
sub mailmail {

    #件名名前処理
    #スペース
    $space = " ";

    #件名文字コード
    &jcode'convert( *space, 'jis' );
    &jcode'convert( *name1, 'jis' );
    &jcode'convert( *name2, 'jis' );

    $subject    = "$name1$space$name2$subject";
    $mailhantei = "$mail1$mail2";
    $mail       = "$mail1\@$mail2";
    $birth      = sprintf( '%04d/%02d/%02d', $birth_year, $birth_month, $birth_day );

    # 受信用
    $odata1  = "\n・受付日時：$timenow$yobinow$hour時$min分";
    $odata2  = "\n・質問1 過去に願書請求をしたことがありますか？：$shitsumon1";
    $odata3  = "\n・質問2 過去に受験をしたことがありますか？：$shitsumon2";
    $odata4  = "\n・氏名：$name1 $name2";
    $odata5  = "\n・フリガナ：$kana1 $kana2";
    $odata6  = "\n・生年月日：$birth";
    $odata7  = "\n・性別：$sei";
    $odata8  = "\n・郵便番号：$add1-$add2";
    $odata9  = "\n・住所：\n$ken";
    $odata10 = "\n$juusho1";
    $odata11 = "\n$juusho2";
    $odata12 = "\n$juusho3";
    $odata13 = "\n・勤務先名：$kaisha";
    $odata14 = "\n・部署名：$bu";
    $odata15 = "\n・連絡先：$telshu";
    $odata16 = "\n・電話番号：$tel1-$tel2-$tel3";
    $odata17 = "\n・E-MAIL：$mail1\@$mail2";

    # 返信用
    $hdata1 = "$name1$name2様\n\n";

#    $hdata2 = "願書請求を頂きましてありがとうございます。\n発送まで今しばらくお待ちください。\n登録内容は下記の通りとなります。ご確認お願いします。\n尚、このメールは送信専用アドレスで自動送信しております。\nこのメールに返信されても、返信内容の確認およびご返答ができませんのでご注意ください。\n";
    $hdata2
        = "願書請求を頂きましてありがとうございます。\n発送まで今しばらくお待ちください。\n尚、このメールは送信専用アドレスで自動送信しております。\nこのメールに返信されても、返信内容の確認およびご返答ができませんのでご注意ください。";
    $hdata3 = "よろしくお願いいたします。";
    $hdata4 = "\n----------------------------\n";
    $hdata5 = "一般社団法人FLAネットワーク協会\n食生活アドバイザー検定事務局\nTEL:0120-86-3593";
    $hdata6 = "\n----------------------------\n";

    # 担当用
    #    $tdata1 = "$name1$name2様から願書の請求がありました。\n";
    $tdata1 = "WEBから願書の請求がありました。";
}

# ---------------------------------------------------------------
# メール出力結果?@（自分のメールへ）
# ---------------------------------------------------------------
sub maildata1 {

    my $sendmail
        = ( -e '/usr/lib/sendmail' ) ? '/usr/lib/sendmail'
        : ( -e '/usr/sbin/sendmail' ) ? '/usr/sbin/sendmail'
        :                               '';

    # メールBODY
    my $body;
    $body .= qq|$tdata1|;
    $body .= qq|$odata1|;

    #    $body .= qq|$odata2|;
    #    $body .= qq|$odata3|;
    #    $body .= qq|$odata4|;
    #    $body .= qq|$odata5|;
    #    $body .= qq|$odata6|;
    #    $body .= qq|$odata7|;
    #    $body .= qq|$odata8|;
    #    $body .= qq|$odata9|;
    #    $body .= qq|$odata10|;
    #    $body .= qq|$odata11|;
    #    $body .= qq|$odata12| if ($juusho3);
    #    $body .= qq|$odata13| if ($kaisha);
    #    $body .= qq|$odata14| if ($bu);
    #    $body .= qq|$odata15|;
    #    $body .= qq|$odata16|;
    #    $body .= qq|$odata17| if ($mail1);

    jcode::convert( \$body, 'jis' );

    if ($sendmail) {
        open( MAIL, "| $sendmail -t -f$from" ) || die('send error please once again');
    }
    else {
        open( MAIL, ">./$to1.txt" );
    }

    print MAIL qq|To: $to1\n|
        . qq|From: $from\n|
        . qq|Subject: $subject\n|
        . qq|MIME-Version: 1.0\n|
        . qq|Content-Type: text/plain;\n|
        . qq|	charset="iso-2022-jp"\n|
        . qq|Content-Transfer-Encoding: 7bit\n| . qq|\n|
        . qq|$body\n|;

    close(MAIL);

}

# ---------------------------------------------------------------
# メール出力結果?A（登録者様への返信メール）
# ---------------------------------------------------------------
sub maildata2 {

    if ( $mailhantei ne "" ) {

        # ($mail =~ /^[a-zA-Z0-9_\-\.a-zA-Z0-9\-\.]+\@[a-zA-Z0-9_\-\.a-zA-Z0-9\-\.]+$/))
        # ※↑上の記述の省略版は　( $mail =~ /^[\w\-\.]+\@[\w\-\.]+$/)　になる！！

        my $sendmail
            = ( -e '/usr/lib/sendmail' ) ? '/usr/lib/sendmail'
            : ( -e '/usr/sbin/sendmail' ) ? '/usr/sbin/sendmail'
            :                               '';

        # メールBODY
        my $body;

        #        $body .= qq|$hdata1|;
        $body .= qq|$hdata2|;

        #        $body .= qq|$odata1|;
        #        $body .= qq|$odata2|;
        #        $body .= qq|$odata3|;
        #        $body .= qq|$odata4|;
        #        $body .= qq|$odata5|;
        #        $body .= qq|$odata6|;
        #        $body .= qq|$odata7|;
        #        $body .= qq|$odata8|;
        #        $body .= qq|$odata9|;
        #        $body .= qq|$odata10|;
        #        $body .= qq|$odata11|;
        #        $body .= qq|$odata12| if ($juusho3);
        #        $body .= qq|$odata13| if ($kaisha);
        #        $body .= qq|$odata14| if ($bu);
        #        $body .= qq|$odata15|;
        #        $body .= qq|$odata16|;
        #        $body .= qq|$odata17| if ($mail1);
        $body .= qq|\n\n$hdata3|;
        $body .= qq|$hdata4|;
        $body .= qq|$hdata5|;
        $body .= qq|$hdata6|;

        jcode::convert( \$body, 'jis' );

        if ($sendmail) {
            open( MAIL, "| $sendmail -t -f$from" ) || die('send error please once again');
        }
        else {
            open( MAIL, ">./$mail.txt" );
        }

        print MAIL qq|To: $mail\n|
            . qq|From: $from\n|
            . qq|Subject: $subject2\n|
            . qq|MIME-Version: 1.0\n|
            . qq|Content-Type: text/plain;\n|
            . qq|	charset="iso-2022-jp"\n|
            . qq|Content-Transfer-Encoding: 7bit\n| . qq|\n|
            . qq|$body\n|;

        close(MAIL);
    }
}

##########確認ページ文字化け処理##########
sub outputshori {

    $newbikou = $bikou;

    $newbikou = &no_tag2($newbikou);

}

##########確認ページ##########
sub outputdata {
    print "Content-type: text/html\n\n";
    print <<EOM;

<!DOCtype HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=Shift_JIS">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="description" content="願書請求｜食生活アドバイザー検定">
<meta name="keywords" content="願書請求｜食生活アドバイザー検定">
<title>願書請求｜食生活アドバイザー検定</title>
<link rel="Stylesheet" title="Plain" href="g.css" type="text/css">
</head>
<body bgcolor="#ffffff" text="#333333" link="#ffffff" alink="#ffffff" vlink="#ffffff">
<div align="center">
<form action="$script" method="post" name="itiran">
<input type="hidden" name="kensa" value="b">

<table border="0" cellpadding="0" cellspacing="0" summary="top">
<tr>
	<td height="5"><img src="img/s.gif" border="0" width="1" height="1"></td>
</tr>
<tr>
	<td>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><img src="img/logo.gif" border="0"></td>
				<td align="right">		<span id="ss_img_wrapper_115-57_flash_ja"><a href="http://jp.globalsign.com/" target=_blank><img alt="SSL　グローバルサインのサイトシール" border=0 id="ss_img" src="//seal.globalsign.com/SiteSeal/images/gs_noscript_115-57_ja.gif"></a></span><script type="text/javascript" src="//seal.globalsign.com/SiteSeal/gs_flash_115-57_ja.js"></script></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td height="10"><img src="img/s.gif" border="0" width="1" height="1"></td>
</tr>
<tr>
	<td height="5" bgcolor="#ff6446"><img src="img/s.gif" width="1" height="1" border="0"></td>
</tr>
<tr><td bgcolor="#ff6446">
	<table border="0" cellpadding="0" cellspacing="1" summary="k">
	<tr><td align="center" valign="bottom" width="750" bgcolor="#ffffff">
<!---->
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td height="20"><img src="img/s.gif" width="1" height="1" border="0"></td>
			</tr>
		</table>
<!--------------------メイン開始------------------------>
		<table width="720" border="0" cellpadding="2" cellspacing="0">
		<tr><td><font class="t16"><b>願書請求フォーム</b></font><font class="t16" color="red"><b>内容確認ページ</b></font></td></tr>
		<tr><td></td></tr>
		<tr><td><font class="t12">内容を修正する場合はページ下部にある戻るで入力ページに戻り修正してください。</font></td></tr>
		<tr><td></td></tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--質問-->
		<table border="0" cellpadding="0" cellspacing="0" width="716">
		<tr>
			<td><font class="t12">■ご質問</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fdc0b5" width="320"><font class="t12">過去に願書請求をしたことがありますか？ (※)</font></td>
			<td bgcolor="#fde7e3" width="400"><font class="t12"><input type="hidden" name="shitsumon1" value="$shitsumon1">$shitsumon1　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5" width="400"><font class="t12">過去に受験をしたことがありますか？ (※)</font></td>
			<td bgcolor="#fde7e3" width="320"><font class="t12"><input type="hidden" name="shitsumon2" value="$shitsumon2">$shitsumon2　</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--個人-->
		<table border="0" cellpadding="0" cellspacing="0" width="716">
		<tr>
			<td><font class="t12">■氏名等</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">氏名 (※)</font></td>
			<td bgcolor="#fde7e3" width="500"><font class="t12"><input type="hidden" name="name1" value="$name1">$name1　<input type="hidden" name="name2" value="$name2">$name2</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">フリガナ (※)</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="kana1" value="$kana1">$kana1　<input type="hidden" name="kana2" value="$kana2">$kana2</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">生年月日 (※)</font></td>
			<td bgcolor="#fde7e3">
      <font class="t12">
        <input type="hidden" name="birth_year" value="$birth_year">
        <input type="hidden" name="birth_month" value="$birth_month">
        <input type="hidden" name="birth_day" value="$birth_day">
        $birth_year/$birth_month/$birth_day
      </font>
			</td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">性別 (※)</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="sei" value="$sei">$sei　</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--住所-->
		<table border="0" cellpadding="0" cellspacing="0" width="716">
		<tr>
			<td><font class="t12">■住所等</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">郵便番号 (※)</font></td>
			<td bgcolor="#fde7e3" width="500"><font class="t12">〒<input type="hidden" name="add1" value="$add1">$add1-<input type="hidden" name="add2" value="$add2">$add2　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">都道府県 (※)</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="ken" value="$ken">$ken　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">市区町村 (※)</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="juusho1" value="$juusho1">$juusho1　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">町域番地 (※)</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="juusho2" value="$juusho2">$juusho2　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">建物名・様方</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="juusho3" value="$juusho3">$juusho3　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5" colspan="2"><font class="t12"><b>会社宛にご送付希望の方は勤務先・部署名のご入力も忘れずにお願いします。</b></font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">勤務先名</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="kaisha" value="$kaisha">$kaisha　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">部署名</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="bu" value="$bu">$bu　</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--ＴＥＬ-->
		<table border="0" cellpadding="0" cellspacing="0" width="716">
		<tr>
			<td><font class="t12">■電話番号</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">連絡先 (※)</font></td>
			<td bgcolor="#fde7e3" width="500"><font class="t12"><input type="hidden" name="telshu" value="$telshu">$telshu　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">電話番号 (※)</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="tel1" value="$tel1">$tel1-<input type="hidden" name="tel2" value="$tel2">$tel2-<input type="hidden" name="tel3" value="$tel3">$tel3　</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--メール-->
		<table border="0" cellpadding="0" cellspacing="0" width="716">
		<tr>
			<td><font class="t12">■メールアドレス</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">メールアドレス</font></td>
			<td bgcolor="#fde7e3" width="500"><font class="t12"><input type="hidden" name="mail1" value="$mail1">$mail1\@<input type="hidden" name="mail2" value="$mail2">$mail2　</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="1" cellspacing="0">
			<tr>
				<td><font class="t12">　</font></td>
			</tr>
		</table>
		<table border="0" cellpadding="3" cellspacing="0">
		<tr><td></td></tr>
		<tr>
			<td align="center" width="150"><input type="image" src="img/send.gif" width="93" height="21" border="0"></td>
			<td align="center" width="150"><a href="javaScript:history.back()"><img src="img/back.gif" width="88" height="21" border="0"></a></td>
		</tr>
		</table>
<!-------------------メイン完了----------------------------->
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td height="20"><img src="img/s.gif" width="1" height="1" border="0"></td>
			</tr>
		</table>
<!---->
	</td></tr>
	</table>
</td></tr>
<tr><td height="10"><img src="img/s.gif" width="1" height="1" border="0"></td></tr>
<tr><td align="center"><font class="t12">願書請求｜食生活アドバイザー検定</font></td></tr>
</table>
</form>
</div>
</body>
</html>

EOM
}

##########自動返信宣言##########
sub success {
    print "Content-type: text/html\n\n";

    print <<EOM;


<!DOCtype HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=Shift_JIS">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="description" content="願書請求｜食生活アドバイザー検定">
<meta name="keywords" content="願書請求｜食生活アドバイザー検定">
<title>願書請求｜食生活アドバイザー検定</title>
<link rel="Stylesheet" title="Plain" href="g.css" type="text/css">
</head>
<body bgcolor="#ffffff" text="#333333" link="#ffffff" alink="#ffffff" vlink="#ffffff">
<div align="center">
<form action="https://ssl.flanet.jp/ganshoform/gansho.cgi" method="post" name="itiran">
<input type="hidden" name="kensa" value="b">

<table border="0" cellpadding="0" cellspacing="0" summary="top">
<tr>
	<td height="5"><img src="img/s.gif" border="0" width="1" height="1"></td>
</tr>
<tr>
	<td>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><img src="img/logo.gif" border="0"></td>
				<td align="right">		<span id="ss_img_wrapper_115-57_flash_ja"><a href="http://jp.globalsign.com/" target=_blank><img alt="SSL　グローバルサインのサイトシール" border=0 id="ss_img" src="//seal.globalsign.com/SiteSeal/images/gs_noscript_115-57_ja.gif"></a></span><script type="text/javascript" src="//seal.globalsign.com/SiteSeal/gs_flash_115-57_ja.js"></script></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td height="10"><img src="img/s.gif" border="0" width="1" height="1"></td>
</tr>
<tr>
	<td height="5" bgcolor="#ff6446"><img src="img/s.gif" width="1" height="1" border="0"></td>
</tr>
<tr><td bgcolor="#ff6446">
	<table border="0" cellpadding="0" cellspacing="1" summary="k">
	<tr><td align="center" valign="bottom" width="750" bgcolor="#ffffff">
<!---->
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td height="20"><img src="img/s.gif" width="1" height="1" border="0"></td>
			</tr>
		</table>
<!--------------------メイン開始------------------------>
		<table width="720" border="0" cellpadding="2" cellspacing="0">
		<tr><td><font class="t16"><b>願書請求フォーム</b></font><font class="t16" color="red"><b>登録完了</b></font></td></tr>
		<tr><td height="30"></td></tr>
		<tr><td><font class="t12">願書のご請求ありがとうございました。<br>願書は3月下旬より発送を開始しております。<br>
    ご請求後、1週間経ってもお手元に届いていない場合は、検定事務局までご連絡ください。（ただし、連休をはさむ場合は遅れることがありますのでご了承ください。)<br>
</font></td></tr>
		<tr><td></td></tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td><font class="t12">[ <a href="http://www.flanet.jp/" target="_top"><font color="blue"><u>ＴＯＰページへ戻る</u></font></a> ]</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!-------------------メイン完了----------------------------->
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td height="20"><img src="img/s.gif" width="1" height="1" border="0"></td>
			</tr>
		</table>
<!---->
	</td></tr>
	</table>
</td></tr>
<tr><td height="10"><img src="img/s.gif" width="1" height="1" border="0"></td></tr>
<tr><td align="center"><font class="t12">願書請求｜食生活アドバイザー検定</font></td></tr>
</table>
</form>
</div>
</body>
</html>


EOM
}

##########直接リンクを貼られた場合のエラー##########
sub errormax {
    print "Content-type: text/html\n\n";

    print <<EOM;
<html>
<head>
<title>エラー</title>
</head>
<body bgcolor="white" link="red" vlink="red" alink="red" topmargin="20" marginheight="20">
<table width="400" border="0" cellpadding="0" cellspacing="0">
<tr align="center">
	<td>
		<table border="0" cellpadding="3">
		<tr>
			<td><b>このページを直接閲覧することはできません。</b></td>
		</tr>
		</table>
	</td>
</tr>
</table>
</body>
</html>
EOM
}

##########個人情報取り扱い############
sub privacydata {
  if($doui ne '同意する'){
    print "Content-type: text/html\n\n";
    print <<EOM;
<html>
<head>
<link rel="Stylesheet" title="." href="f.css" type="text/css">
<title>Gulliver</title>
<meta http-equiv="content-type" content="text/html;charset=x-sjis">
</head>
<style type="text/css">
.title { font-size: 13pt; color: #444444; font-weight: bold; padding:20px; }
.comt { font-size: 11pt; color: #444444; font-weight: bold; }
.ko { font-size: 12pt; color: #000000; font-weight: bold; }
.t1 { font-size: 10pt; color: #002200; }
.t2 { font-size: 9pt; color: #003300; }
.t12 { font-size: 12px; line-height:180%;}
.re { font-size: 10pt; color: navy; font-weight: bold; }
</style>
<body bgcolor="white" marginheight="10" topmargin="10" marginwidth="20" leftmargin="20">
<div style="width:630px; margin:0 auto;">

<table border="0" cellpadding="0" cellspacing="0" width="630" style=" padding:0 0 10px 0;">
        <tbody><tr>
          <td><img src="img/logo.gif" border="0"></td>
          <td align="right">
            <span id="ss_img_wrapper_115-57_flash_ja">
              <a href="http://jp.globalsign.com/" target="_blank"><img alt="SSL　グローバルサインのサイトシール" border="0" id="ss_img" src="//seal.globalsign.com/SiteSeal/images/gs_noscript_115-57_ja.gif"></a>
            </span>
            <script type="text/javascript" src="//seal.globalsign.com/SiteSeal/gs_flash_115-57_ja.js"></script>
          </td>
        </tr>
      </tbody></table>
<table width="630" border="0" cellpadding="0" cellspacing="0" style="border:#ff6446 1px solid;">
<tr><td><img src="img/ber_reqest.gif" width="100%" height="5" border="0"></td></tr>
<tr><td><div class="title">願書請求フォーム<span style="color:#C00;">個人情報の取り扱いについて</span>
</div></td></tr>
<tr><td height="10"></td></tr>
<tr>
	<td align="center">
		<!---PAGE COMMENT--->
		<table width="100%" border="0" cellpadding="0" cellspacing="0" style=" padding:0 20px;">
			<tr>
				<td><font class="t12">当協会の『個人情報の取り扱い』にご同意していただけない場合はこのシステムからご登録はできません。</font></td>
			</tr>
			<tr>
				<td><font class="t12">前ページに戻り、当協会のお問合せ窓口にお問い合わせください。</font></td>
			</tr>
			<tr>
				<td><font class="t12">　</font></td>
			</tr>
		</table>
		<table border="0" cellpadding="3" cellspacing="0" style=" padding:0 0 20px 0;">
			<tr>
				<td><a href="javaScript:history.back()"><img src="img/back.gif" width="88" height="21" border="0"></a></td>
			</tr>
		</table>
	</td>
</tr>
</table>
<p style="font-size:12px; text-align:center; padding-top:20px;">願書請求｜食生活アドバイザー</p>
</body>
</html>
EOM
        exit(0);
  }
  return 0;
}

##########自動エラー返信宣言##########
sub error {

    print "Content-type: text/html\n\n";

    print <<EOM;


<!DOCtype HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=Shift_JIS">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="description" content="願書請求｜食生活アドバイザー検定">
<meta name="keywords" content="願書請求｜食生活アドバイザー検定">
<title>願書請求｜食生活アドバイザー検定</title>
<link rel="Stylesheet" title="Plain" href="g.css" type="text/css">
</head>
<body bgcolor="#ffffff" text="#333333" link="#ffffff" alink="#ffffff" vlink="#ffffff">
<div align="center">
<form action="https://ssl.flanet.jp/ganshoform/gansho.cgi" method="post" name="itiran">
<input type="hidden" name="kensa" value="b">

<table border="0" cellpadding="0" cellspacing="0" summary="top">
<tr>
	<td height="5"><img src="img/s.gif" border="0" width="1" height="1"></td>
</tr>
<tr>
	<td>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><img src="img/logo.gif" border="0"></td>
				<td align="right">		<span id="ss_img_wrapper_115-57_flash_ja"><a href="http://jp.globalsign.com/" target=_blank><img alt="SSL　グローバルサインのサイトシール" border=0 id="ss_img" src="//seal.globalsign.com/SiteSeal/images/gs_noscript_115-57_ja.gif"></a></span><script type="text/javascript" src="//seal.globalsign.com/SiteSeal/gs_flash_115-57_ja.js"></script></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td height="10"><img src="img/s.gif" border="0" width="1" height="1"></td>
</tr>
<tr>
	<td height="5" bgcolor="#ff6446"><img src="img/s.gif" width="1" height="1" border="0"></td>
</tr>
<tr><td bgcolor="#ff6446">
	<table border="0" cellpadding="0" cellspacing="1" summary="k">
	<tr><td align="center" valign="bottom" width="750" bgcolor="#ffffff">
<!---->
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td height="20"><img src="img/s.gif" width="1" height="1" border="0"></td>
			</tr>
		</table>
<!--------------------メイン開始------------------------>
		<table width="720" border="0" cellpadding="2" cellspacing="0">
		<tr><td><font class="t16"><b>願書請求フォーム</b></font><font class="t16" color="red"><b>入力エラー</b></font></td></tr>
		<tr><td></td></tr>
		<tr><td><font class="t12">入力エラーがあります。『戻る』ボタンで前ページに戻り、再入力してください。</font></td></tr>
		<tr><td></td></tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="5"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--個人-->
		<table border="0" cellpadding="5" cellspacing="0">
			<tr>
				<td><font class="t12">　</font></td>
			</tr>

EOM

    if ( $checkshitsumon1 ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkshitsumon1</font></td></tr>";
    }
    if ( $checkshitsumon2 ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkshitsumon2</font></td></tr>";
    }
    if ( $checkname ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkname</font></td></tr>";
    }
    if ( $checkkana ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkkana</font></td></tr>";
    }
    if ( $checkbirth_year ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkbirth_year</font></td></tr>";
    }
    if ( $checkbirth_month ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkbirth_month</font></td></tr>";
    }
    if ( $checkbirth_day ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkbirth_day</font></td></tr>";
    }
    if ( $checksei ne "" ) {
        print "<tr><td><font class=t12 color=red>$checksei</font></td></tr>";
    }
    if ( $checkadd ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkadd</font></td></tr>";
    }
    if ( $checkken ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkken</font></td></tr>";
    }
    if ( $checkjuusho1 ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkjuusho1</font></td></tr>";
    }
    if ( $checkjuusho2 ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkjuusho2</font></td></tr>";
    }
    if ( $checkjuusho3 ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkjuusho3</font></td></tr>";
    }
    if ( $checktelshu ne "" ) {
        print "<tr><td><font class=t12 color=red>$checktelshu</font></td></tr>";
    }
    if ( $checktel ne "" ) {
        print "<tr><td><font class=t12 color=red>$checktel</font></td></tr>";
    }
    if ( $checkmail ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkmail</font></td></tr>";
    }
    if ( $checkmailk ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkmailk</font></td></tr>";
    }
    if ( $checkmailkakunin ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkmailkakunin</font></td></tr>";
    }

    print <<EOM;
			<tr>
				<td><font class="t12">　</font></td>
			</tr>
		</table>
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td><a href="javaScript:history.back()"><img src="img/back.gif" width="88" height="21" border="0"></a></td>
			</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td height="20"><img src="img/s.gif" width="1" height="1" border="0"></td>
			</tr>
		</table>
<!---->
	</td></tr>
	</table>
</td></tr>
<tr><td height="10"><img src="img/s.gif" width="1" height="1" border="0"></td></tr>
<tr><td align="center"><font class="t12">願書請求｜食生活アドバイザー検定</font></td></tr>
</table>
</form>
</div>
</body>
</html>



EOM

}

##########エラー宣言##########
sub checkdata {

    $shitsumon1  = &no_tag($shitsumon1);
    $shitsumon2  = &no_tag($shitsumon2);
    $name1       = &no_tag($name1);
    $name2       = &no_tag($name2);
    $kana1       = &no_tag($kana1);
    $kana2       = &no_tag($kana2);
    $birth_year  = &no_tag($birth_year);
    $birth_month = &no_tag($birth_month);
    $birth_day   = &no_tag($birth_day);
    $sei         = &no_tag($sei);
    $add1        = &no_tag($add1);
    $add2        = &no_tag($add2);
    $ken         = &no_tag($ken);
    $juusho1     = &no_tag($juusho1);
    $juusho2     = &no_tag($juusho2);
    $juusho3     = &no_tag($juusho3);
    $kaisha      = &no_tag($kaisha);
    $bu          = &no_tag($bu);
    $telshu      = &no_tag($telshu);
    $tel1        = &no_tag($tel1);
    $tel2        = &no_tag($tel2);
    $tel3        = &no_tag($tel3);
    $mail1       = &no_tag($mail1);
    $mail2       = &no_tag($mail2);
    $mail3       = &no_tag($mail3);
    $mail4       = &no_tag($mail4);

    if ( $shitsumon1 eq "" ) {
        $checkshitsumon1 = "[過去に願書請求をしたことがありますか？]が正しく選択されていません。";
        $checkkazu       = "1";
    }

    if ( $shitsumon2 eq "" ) {
        $checkshitsumon2 = "[過去に受験をしたことがありますか？]が正しく選択されていません。";
        $checkkazu       = "1";
    }

    if ( $name1 eq "" ) {
        $checkname = "[氏名]が正しく入力されていません。";
        $checkkazu = "1";
    }

    if ( $name2 eq "" ) {
        $checkname = "[氏名]が正しく入力されていません。";
        $checkkazu = "1";
    }

    if ( $kana1 eq "" ) {
        $checkkana = "[フリガナ]が正しく入力されていません。";
        $checkkazu = "1";
    }

    if ( $kana2 eq "" ) {
        $checkkana = "[フリガナ]が正しく入力されていません。";
        $checkkazu = "1";
    }

    if ( $birth_year eq "" ) {
        $checkbirth_year = "[生年月日]が正しく入力されていません。";
        $checkkazu       = "1";
    }

    if ( $birth_month eq "" ) {
        $checkbirth_month = "[生年月日]が正しく入力されていません。";
        $checkkazu        = "1";
    }

    if ( $birth_day eq "" ) {
        $checkbirth_day = "[生年月日]が正しく入力されていません。";
        $checkkazu      = "1";
    }

    if ( $sei eq "" ) {
        $checksei  = "[性別]が正しく入力されていません。";
        $checkkazu = "1";
    }

    if ( ( $add1 =~ /\D/g ) || ( $add1 eq "" ) ) {
        $checkadd  = "[郵便番号]が正しく入力されていません。";
        $checkkazu = "1";
    }
    if ( ( $add2 =~ /\D/g ) || ( $add2 eq "" ) ) {
        $checkadd  = "[郵便番号]が正しく入力されていません。";
        $checkkazu = "1";
    }

    # 追加処理
    my $zip_chk_flg;
    $zip_chk_flg = 1 if ( $add1 && length($add1) ne 3 );
    $zip_chk_flg = 1 if ( $add2 && length($add2) ne 4 );
    if ($zip_chk_flg) {
        $checktel  = "[郵便番号]の桁数が正しくありません。";
        $checkkazu = "1";
    }

    if ( $ken eq "" ) {
        $checkken  = "[住所]が正しく入力されていません。";
        $checkkazu = "1";
    }

    if ( $juusho1 eq "" ) {
        $checkken  = "[住所]が正しく入力されていません。";
        $checkkazu = "1";
    } elsif (&juusho_check($juusho1) == 0) {
        $checkjuusho1 = "[市区町村]は15文字以内で入力してください。";
        $checkkazu = "1";
    }

    if ( $juusho2 eq "" ) {
        $checkken  = "[住所]が正しく入力されていません。";
        $checkkazu = "1";
    } elsif (&juusho_check($juusho2) == 0) {
        $checkjuusho2 = "[町域番地]は15文字以内で入力してください。";
        $checkkazu = "1";
    }

    if (&juusho_check($juusho3) == 0) {
        $checkjuusho3 = "[建物名・様方]は15文字以内で入力してください。";
        $checkkazu = "1";
    }

    #	if( $kaisha eq "" ){
    #		$checkkaisha	=	"[勤務先名]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}

    #	if( $bu eq "" ){
    #		$checkbu	=	"[部署名]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}

    if ( $telshu eq "" ) {
        $checktelshu = "[TEL連絡先]が正しく入力されていません。";
        $checkkazu   = "1";
    }

    if ( ( $tel1 =~ /\D/g ) || ( $tel1 eq "" ) ) {
        $checktel  = "[電話番号]が正しく入力されていません。";
        $checkkazu = "1";
    }
    if ( ( $tel2 =~ /\D/g ) || ( $tel2 eq "" ) ) {
        $checktel  = "[電話番号]が正しく入力されていません。";
        $checkkazu = "1";
    }
    if ( ( $tel3 =~ /\D/g ) || ( $tel3 eq "" ) ) {
        $checktel  = "[電話番号]が正しく入力されていません。";
        $checkkazu = "1";
    }

    if ( $mail1 ne "" ) {
        if ( $mail1 =~ /^[\w\-\.]+$/ ) {
            $nodata1 = "yes";
        }
        else {
            $checkmail = "[メールアドレス]が正しく入力されていません。";
            $checkkazu = "1";
        }
    }
    if ( $mail2 ne "" ) {
        if ( $mail2 =~ /^[\w\-\.]+$/ ) {
            $nodata1 = "yes";
        }
        else {
            $checkmail = "[メールアドレスが正しく入力されていません。";
            $checkkazu = "1";
        }
    }

    #    if ( $mail3 ne "" ) {
    #        if ( $mail3 =~ /^[\w\-\.]+$/ ) {
    #            $nodata1 = "yes";
    #        }
    #        else {
    #            $checkmail = "[メールアドレス]が正しく入力されていません。";
    #            $checkkazu = "1";
    #        }
    #    }
    #    if ( $mail4 ne "" ) {
    #        if ( $mail4 =~ /^[\w\-\.]+$/ ) {
    #            $nodata1 = "yes";
    #        }
    #        else {
    #            $checkmail = "[メールアドレス]が正しく入力されていません。";
    #            $checkkazu = "1";
    #        }
    #    }

    if ( $mail1 ne "" ) {
        if ( $mail2 eq "" ) {
            $checkmail = "[メールアドレス]が正しく入力されていません。";
            $checkkazu = "1";
        }

        #        if ( $mail3 eq "" ) {
        #            $checkmail = "[メールアドレス]が正しく入力されていません。";
        #            $checkkazu = "1";
        #        }
        #        if ( $mail4 eq "" ) {
        #            $checkmail = "[メールアドレス]が正しく入力されていません。";
        #            $checkkazu = "1";
        #        }
    }
    if ( $mail2 ne "" ) {
        if ( $mail1 eq "" ) {
            $checkmail = "[メールアドレス]が正しく入力されていません。";
            $checkkazu = "1";
        }

        #        if ( $mail3 eq "" ) {
        #            $checkmail = "[メールアドレス]が正しく入力されていません。";
        #            $checkkazu = "1";
        #        }
        #        if ( $mail4 eq "" ) {
        #            $checkmail = "[メールアドレス]が正しく入力されていません。";
        #            $checkkazu = "1";
        #        }
    }

    #    if ( $mail3 ne "" ) {
    #        if ( $mail1 eq "" ) {
    #            $checkmail = "[メールアドレス(確認用も含む)]が正しく入力されていません。";
    #            $checkkazu = "1";
    #        }
    #        if ( $mail2 eq "" ) {
    #            $checkmail = "[メールアドレス(確認用も含む)]が正しく入力されていません。";
    #            $checkkazu = "1";
    #        }
    #        if ( $mail4 eq "" ) {
    #            $checkmail = "[メールアドレス(確認用も含む)]が正しく入力されていません。";
    #            $checkkazu = "1";
    #        }
    #    }
    #    if ( $mail4 ne "" ) {
    #        if ( $mail1 eq "" ) {
    #            $checkmail = "[メールアドレス(確認用も含む)]が正しく入力されていません。";
    #            $checkkazu = "1";
    #        }
    #        if ( $mail2 eq "" ) {
    #            $checkmail = "[メールアドレス(確認用も含む)]が正しく入力されていません。";
    #            $checkkazu = "1";
    #        }
    #        if ( $mail3 eq "" ) {
    #            $checkmail = "[メールアドレス(確認用も含む)]が正しく入力されていません。";
    #            $checkkazu = "1";
    #        }
    #    }

    #    if ( $mail1 ne $mail3 ) {
    #        $checkmail = "[メールアドレス(確認用も含む)]が正しく入力されていません。";
    #        $checkkazu = "1";
    #    }

    #    if ( $mail2 ne $mail4 ) {
    #        $checkmail = "[メールアドレス(確認用も含む)]が正しく入力されていません。";
    #        $checkkazu = "1";
    #    }

    if ( $checkkazu == 1 ) {
        &error;
        exit(0);
    }

}

##########外部呼出##########
sub no_tag {
    local ($input_data) = @_;

    $input_data =~ s/&/&amp;/g;
    $input_data =~ s/"/&quot;/g;
    $input_data =~ s/</&lt;/g;
    $input_data =~ s/>/&gt;/g;
    $input_data =~ s/,/&#44;/g;

    $input_data =~ s/\r\n/\n/g;    #Macの改行コードCR+LFをLFに
    $input_data =~ s/\r/\n/g;      #DOSの改行コードCRをLFに

    return $input_data;
}

##########外部呼出##########
sub no_tag2 {
    local ($input_data) = @_;

    $input_data =~ s/\n/<BR>/g;                                       #改行LFを<BR>タグに
    $input_data =~ s/\n\n/<BR><BR>/g;                                 #改行2回は<BR>タグ２回の特殊な形
    $input_data =~ s/\n\n\n/<BR><BR><BR>/g;
    $input_data =~ s/\n\n\n\n/<BR><BR><BR><BR>/g;
    $input_data =~ s/\n\n\n\n\n/<BR><BR><BR><BR><BR>/g;
    $input_data =~ s/\n\n\n\n\n\n/<BR><BR><BR><BR><BR><BR>/g;
    $input_data =~ s/\n\n\n\n\n\n\n/<BR><BR><BR><BR><BR><BR><BR>/g;

    return $input_data;
}

##########外部呼出##########
sub no_tag3 {
    local ($input_data) = @_;

    $input_data =~ s/\r\n/\n/g;    #Macの改行コードCR+LFをLFに
    $input_data =~ s/\n/ /g;       #DOSの改行コードCRをLFに

    return $input_data;
}
