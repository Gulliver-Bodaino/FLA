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
my $script = 'https://ssl.flanet.jp/memberform/member.cgi';    # 本番用
#my $script = 'member.cgi';

# 出力CSV
$csv_dir     = '../csvdata';                                    # 本番用
$backup_dir  = '../csvdata/csvbackup';                          # 本番用
$csv_data    = 'member.csv';                                    # 本番用
$backup_data = 'backupmember.csv';                              # 本番用

# メールアドレス(本番用)
# $to1  = 'fla.entry@flanet.jp';    # [本番用] 管理者アドレス
# $from = 'fla.entry@flanet.jp';    # [本番用] from用管理者アドレス

# 検証用
$to1  = 'ishii-k@ww-system.com';                                # 管理者アドレス
$from = 'ishii-k@ww-system.com';                                # from用管理者アドレス


# メールタイトル
$subject  = '様から登録情報の変更がありました。';          # 管理者用
$subject2 = '食生活アドバイザー検定　登録情報変更完了';    # ユーザ用

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
    my $is_exists = &fileinput;                               # CSV出力
    # 2016/03/25 分岐追加
    # 既にデータが存在する場合、メール送信処理をしない。
    if ( $is_exists == 1 ) {
        &mailmail;                                         # メールデータ作成
        &maildata1;                                        # 管理者へのメール送信
        &maildata2;                                        # ユーザへのメール送信
    }
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
    $name1   = $formdata{'name1'};
    $name2   = $formdata{'name2'};
    $kana1   = $formdata{'kana1'};
    $kana2   = $formdata{'kana2'};
    $add1    = $formdata{'add1'};
    $add2    = $formdata{'add2'};
    $ken     = $formdata{'ken'};
    $juusho1 = $formdata{'juusho1'};
    $juusho2 = $formdata{'juusho2'};
    $juusho3 = $formdata{'juusho3'};
    $kaisha  = $formdata{'kaisha'};
    $bu      = $formdata{'bu'};
    $tel1    = $formdata{'tel1'};
    $tel2    = $formdata{'tel2'};
    $tel3    = $formdata{'tel3'};
    $tbangou = $formdata{'tbangou'};

    $shinnamecheck = $formdata{'shinnamecheck'};
    $shinname1     = $formdata{'shinname1'};
    $shinname2     = $formdata{'shinname2'};
    $shinkana1     = $formdata{'shinkana1'};
    $shinkana2     = $formdata{'shinkana2'};

    $shinjuushocheck = $formdata{'shinjuushocheck'};
    $shinadd1        = $formdata{'shinadd1'};
    $shinadd2        = $formdata{'shinadd2'};
    $shinken         = $formdata{'shinken'};
    $shinjuusho1     = $formdata{'shinjuusho1'};
    $shinjuusho2     = $formdata{'shinjuusho2'};
    $shinjuusho3     = $formdata{'shinjuusho3'};
    $shinkaisha      = $formdata{'shinkaisha'};
    $shinbu          = $formdata{'shinbu'};

    $shintelcheck = $formdata{'shintelcheck'};
    $shintelshu   = $formdata{'shintelshu'};
    $shintel1     = $formdata{'shintel1'};
    $shintel2     = $formdata{'shintel2'};
    $shintel3     = $formdata{'shintel3'};

    $shinmailcheck = $formdata{'shinmailcheck'};
    $shinmail1     = $formdata{'shinmail1'};
    $shinmail2     = $formdata{'shinmail2'};
    $shinmail3     = $formdata{'shinmail3'};
    $shinmail4     = $formdata{'shinmail4'};

    $saisou = $formdata{'saisou'};

    $doui = $formdata{'doui'};

    # ----------------------------------------------
    # 追加処理
    # ----------------------------------------------
    # 小文字を大文字に

    # ご本人確認
    $name1   = Unicode::Japanese->new( $name1,   'sjis' )->h2zKana->sjis;
    $name2   = Unicode::Japanese->new( $name2,   'sjis' )->h2zKana->sjis;
    $kana1   = Unicode::Japanese->new( $kana1,   'sjis' )->h2zKana->sjis;
    $kana2   = Unicode::Japanese->new( $kana2,   'sjis' )->h2zKana->sjis;
    $juusho1 = Unicode::Japanese->new( $juusho1, 'sjis' )->h2zKana->sjis;
    $juusho2 = Unicode::Japanese->new( $juusho2, 'sjis' )->h2zKana->sjis;
    $juusho3 = Unicode::Japanese->new( $juusho3, 'sjis' )->h2zKana->sjis;
    $kaisha  = Unicode::Japanese->new( $kaisha,  'sjis' )->h2zKana->sjis;
    $bu      = Unicode::Japanese->new( $bu,      'sjis' )->h2zKana->sjis;
    $tbangou = Unicode::Japanese->new( $tbangou, 'sjis' )->h2zKana->sjis;

    # 氏名
    $shinname1 = Unicode::Japanese->new( $shinname1, 'sjis' )->h2zKana->sjis;
    $shinname2 = Unicode::Japanese->new( $shinname2, 'sjis' )->h2zKana->sjis;
    $shinkana1 = Unicode::Japanese->new( $shinkana1, 'sjis' )->h2zKana->sjis;
    $shinkana2 = Unicode::Japanese->new( $shinkana2, 'sjis' )->h2zKana->sjis;

    # 住所関連
    $shinjuusho1 = Unicode::Japanese->new( $shinjuusho1, 'sjis' )->h2zKana->sjis;
    $shinjuusho2 = Unicode::Japanese->new( $shinjuusho2, 'sjis' )->h2zKana->sjis;
    $shinjuusho3 = Unicode::Japanese->new( $shinjuusho3, 'sjis' )->h2zKana->sjis;
    $shinkaisha  = Unicode::Japanese->new( $shinkaisha,  'sjis' )->h2zKana->sjis;
    $shinbu      = Unicode::Japanese->new( $shinbu,      'sjis' )->h2zKana->sjis;

    &jcode'convert( *subject,  'jis' );
    &jcode'convert( *subject2, 'jis' );

    jcode::convert( \name1, "sjis", "", "z" );
    jcode::convert( \name2, "sjis", "", "z" );
    jcode::convert( \kana1, "sjis", "", "z" );
    jcode::convert( \kana2, "sjis", "", "z" );

    jcode::convert( \add1,    "sjis", "", "z" );
    jcode::convert( \add2,    "sjis", "", "z" );
    jcode::convert( \ken,     "sjis", "", "z" );
    jcode::convert( \juusho1, "sjis", "", "z" );
    jcode::convert( \juusho2, "sjis", "", "z" );
    jcode::convert( \juusho3, "sjis", "", "z" );
    jcode::convert( \kaisha,  "sjis", "", "z" );
    jcode::convert( \bu,      "sjis", "", "z" );

    jcode::convert( \tel1,    "sjis", "", "z" );
    jcode::convert( \tel2,    "sjis", "", "z" );
    jcode::convert( \tel3,    "sjis", "", "z" );
    jcode::convert( \tbangou, "sjis", "", "z" );

    jcode::convert( \shinnamecheck, "sjis", "", "z" );
    jcode::convert( \shinname1,     "sjis", "", "z" );
    jcode::convert( \shinname2,     "sjis", "", "z" );
    jcode::convert( \shinkana1,     "sjis", "", "z" );
    jcode::convert( \shinkana2,     "sjis", "", "z" );

    jcode::convert( \shinjuushocheck, "sjis", "", "z" );
    jcode::convert( \shinadd1,        "sjis", "", "z" );
    jcode::convert( \shinadd2,        "sjis", "", "z" );
    jcode::convert( \shinken,         "sjis", "", "z" );
    jcode::convert( \shinjuusho1,     "sjis", "", "z" );
    jcode::convert( \shinjuusho2,     "sjis", "", "z" );
    jcode::convert( \shinjuusho3,     "sjis", "", "z" );
    jcode::convert( \shinkaisha,      "sjis", "", "z" );
    jcode::convert( \shinbu,          "sjis", "", "z" );

    jcode::convert( \shintelcheck, "sjis", "", "z" );
    jcode::convert( \shintelshu,   "sjis", "", "z" );
    jcode::convert( \shintel1,     "sjis", "", "z" );
    jcode::convert( \shintel2,     "sjis", "", "z" );
    jcode::convert( \shintel3,     "sjis", "", "z" );

    jcode::convert( \shinmailcheck, "sjis", "", "z" );
    jcode::convert( \shinmail1,     "sjis", "", "z" );
    jcode::convert( \shinmail2,     "sjis", "", "z" );
    jcode::convert( \shinmail3,     "sjis", "", "z" );
    jcode::convert( \shinmail4,     "sjis", "", "z" );

    jcode::convert( \saisou, "sjis", "", "z" );
    
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

    $timenow     = "$year/$mon/$day";
    $yobinow     = "@tm[$wday]曜日";
    $jikannew    = "$hour$min";
    $namenew     = "$name1 $name2";
    $kananew     = "$kana1 $kana2";
    $addnew      = "$add1$add2";
    $telnew      = "$tel1-$tel2-$tel3";
    $shinnamenew = "$shinname1 $shinname2";
    $shinkananew = "$shinkana1 $shinkana2";
    $shinaddnew  = "$shinadd1$shinadd2";
    $shintelnew  = "$shintel1-$shintel2-$shintel3";
    $shinmailnew = "$shinmail1\@$shinmail2";
    $kennew      = "$ken$juusho";

    # CSVデータ
    my $csv;
    my $csv_back;

    if ( !-e qq|$csv_dir/$csv_data| ) {
        $csv = &make_csv(    # *
            '変更日',
            '時間',
            '曜日',
            '氏名',
            'フリガナ',
            '郵便番号',
            '都道府県',
            '市区町村',
            '番地',
            '建物名・様方',
            '勤務先名',
            '部署名',
            '電話番号',
            '登録番号',
            '変更カテゴリー1 氏名',
            '変更カテゴリー2 住所',
            '変更カテゴリー3 電話番号',
            '変更カテゴリー4 メールアドレス',
            '変更後の氏名',
            '変更後のフリガナ',
            '変更後の郵便番号',
            '変更後の都道府県',
            '変更後の市区町村',
            '変更後の番地',
            '変更後の建物名・様方',
            '変更後の勤務先名',
            '変更後の部署名',
            '変更後の電話番号先',
            '変更後の電話番号',
            '変更後のメールアドレス',
            '再送',
        );
    }

    $csv .= &make_csv(    # *
        "$timenow",
        "$jikannew",
        "$yobinow",
        "$namenew",
        "$kananew",
        "$addnew",
        "$ken",
        "$juusho1",
        "$juusho2",
        "$juusho3",
        "$kaisha",
        "$bu",
        "$telnew",
        "$tbangou",
        "$shinnamecheck",
        "$shinjuushocheck",
        "$shintelcheck",
        "$shinmailcheck",
        "$shinnamenew",
        "$shinkananew",
        "$shinaddnew",
        "$shinken",
        "$shinjuusho1",
        "$shinjuusho2",
        "$shinjuusho3",
        "$shinkaisha",
        "$shinbu",
        "$shintelshu",
        "$shintelnew",
        "$shinmailnew",
        "$saisou",
    );


    if ( !-e qq|$backup_dir/$backup_data| ) {
        $csv_back = &make_csv(    # *
            '変更日',
            '時間',
            '曜日',
            '氏名',
            'フリガナ',
            '郵便番号',
            '都道府県',
            '市区町村',
            '番地',
            '建物名・様方',
            '勤務先名',
            '部署名',
            '電話番号',
            '登録番号',
            '変更カテゴリー1 氏名',
            '変更カテゴリー2 住所',
            '変更カテゴリー3 電話番号',
            '変更カテゴリー4 メールアドレス',
            '変更後の氏名',
            '変更後のフリガナ',
            '変更後の郵便番号',
            '変更後の都道府県',
            '変更後の市区町村',
            '変更後の番地',
            '変更後の建物名・様方',
            '変更後の勤務先名',
            '変更後の部署名',
            '変更後の電話番号先',
            '変更後の電話番号',
            '変更後のメールアドレス',
            '再送',
        );
    }

    $csv_back .= &make_csv(    # *
        "$timenow",
        "$jikannew",
        "$yobinow",
        "$namenew",
        "$kananew",
        "$addnew",
        "$ken",
        "$juusho1",
        "$juusho2",
        "$juusho3",
        "$kaisha",
        "$bu",
        "$telnew",
        "$tbangou",
        "$shinnamecheck",
        "$shinjuushocheck",
        "$shintelcheck",
        "$shinmailcheck",
        "$shinnamenew",
        "$shinkananew",
        "$shinaddnew",
        "$shinken",
        "$shinjuusho1",
        "$shinjuusho2",
        "$shinjuusho3",
        "$shinkaisha",
        "$shinbu",
        "$shintelshu",
        "$shintelnew",
        "$shinmailnew",
        "$saisou",
    );
    
    # 2016/03/25 追加
    # 二重送信チェック用文字列生成
    $chk_post_str =
          "$namenew"
        . "$kananew"
        . "$addnew"
        . "$ken"
        . "$juusho1"
        . "$juusho2"
        . "$juusho3"
        . "$kaisha"
        . "$bu"
        . "$telnew"
        . "$tbangou"
        . "$shinnamecheck"
        . "$shinjuushocheck"
        . "$shintelcheck"
        . "$shinmailcheck"
        . "$shinnamenew"
        . "$shinkananew"
        . "$shinaddnew"
        . "$shinken"
        . "$shinjuusho1"
        . "$shinjuusho2"
        . "$shinjuusho3"
        . "$shinkaisha"
        . "$shinbu"
        . "$shintelshu"
        . "$shintelnew"
        . "$shinmailnew"
        . "$saisou";

    # 2016/03/25 追加
    # CSVデータ存在チェック
    # 二重送信チェック用文字列とCSV文字列を比較
    open( IN, "$csv_dir/$csv_data" );
    my $is_exists = 0;
    while (<IN>) {
        
        # 改行コード削除
        #s/[\r\n]*$//;
        chomp;
        
        # カンマ区切り文字列を配列に分割
        @cols = split(/,/);
        
        # 比較用文字列生成
        $chk_csv_str = "";
        for ($index = 3; $index <= 30; $index++) {
            $chk_csv_str = $chk_csv_str . $cols[$index];
        }
        
        # 比較してデータが存在すれば、フラグを立ててループを抜ける。
        if ( $chk_csv_str eq $chk_post_str ) {
            $is_exists = 1;
            last;
        }
    }
    close(IN);
    
    # データが存在すれば、0を返す。
    if ( $is_exists == 1 ) {
        return 0;
    }

    # CSV追記
    my_open( OUT, ">>$csv_dir/$csv_data" );
    binmode OUT;
    print OUT $csv;
    my_close(OUT);

    # バックアップCSV追記
    my_open( OUT, ">>$backup_dir/$backup_data" );
    binmode OUT;
    print OUT $csv_back;
    my_close(OUT);

    # 2016/03/25 追加
    # データが存在しなければ、1を返す。
    return 1;
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
    $mailhantei = "$shinmail1$shinmail2";
    $mail       = "$shinmail1\@$shinmail2";

    # 受信用
    $odata1  = "\n・受付日時：$timenow$yobinow$hour時$min分";
    $odata2  = "\n\n■ご本人確認のため既登録情報";
    $odata3  = "\n・氏名：$name1 $name2";
    $odata4  = "\n・フリガナ：$kana1 $kana2";
    $odata5  = "\n・郵便番号：$add1-$add2";
    $odata6  = "\n・住所：\n$ken";
    $odata7  = "\n$juusho1";
    $odata8  = "\n$juusho2";
    $odata9  = "\n$juusho3";
    $odata10 = "\n・勤務先名：$kaisha";
    $odata11 = "\n・部署名：$bu";
    $odata12 = "\n・電話番号：$tel1-$tel2-$tel3";
    $odata13 = "\n・登録番号：$tbangou";
    $odata14 = "\n\n■項目1：$shinnamecheck";
    $odata15 = "\n・氏名：$shinname1$shinname2";
    $odata16 = "\n・フリガナ：$shinkana1$shinkana2";
    $odata17 = "\n\n■項目2：$shinjuushocheck";
    $odata18 = "\n・郵便番号：$shinadd1-$shinadd2";
    $odata19 = "\n・住所：\n$shinken";
    $odata20 = "\n$shinjuusho1";
    $odata21 = "\n$shinjuusho2";
    $odata22 = "\n$shinjuusho3";
    $odata23 = "\n・勤務先名：$shinkaisha";
    $odata24 = "\n・部署名：$shinbu";
    $odata25 = "\n\n■項目3：$shintelcheck";
    $odata26 = "\n・連絡先：$shintelshu";
    $odata27 = "\n・電話番号：$shintel1-$shintel2-$shintel3";
    $odata28 = "\n\n■項目4：$shinmailcheck";
    $odata29 = "\n・E-MAIL：$shinmail1\@$shinmail2";

    # 返信用
    $hdata1 = "$name1$name2様\n\n";

#    $hdata2 = "登録情報変更のご登録、有難う御座います。\n登録内容は下記の通りとなります。ご確認お願いします。\n尚、このメールは送信専用アドレスで送信しております。\nこのメールに返信されても、返信内容の確認およびご返答ができませんのでご注意ください。\n";
    $hdata2
        = "登録情報変更のご連絡をいただきましてありがとうございます。\n速やかにデータの修正をさせていただきます。\n願書再送をご希望の方は、発送まで今しばらくお待ちください。\n尚、このメールは送信専用アドレスで自動送信しております。\nこのメールに返信されても、返信内容の確認およびご返答ができませんのでご注意ください。\n";
    $hdata3 = "よろしくお願いいたします。";
    $hdata4 = "\n----------------------------\n";
    $hdata5 = "一般社団法人FLAネットワーク協会\n食生活アドバイザー検定事務局\nTEL:0120-86-3593";
    $hdata6 = "\n----------------------------\n";

    # 担当用
    #    $tdata1 = "$name1$name2様から登録情報変更の\申\込がありました。\n";
    $tdata1 = "WEBから登録情報変更の\申\込がありました。";

}

##########メール出力結果?@（自分のメールへ）##########
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
    #    $body .= qq|$odata9| if ($juusho3);
    #    $body .= qq|$odata10| if ($kaisha);
    #    $body .= qq|$odata11| if ($bu);
    #    $body .= qq|$odata12| if ($tel1);
    #    $body .= qq|$odata13| if ($tbangou);
    #    $body .= qq|$odata14| if ($shinnamecheck);
    #    $body .= qq|$odata15| if ($shinname1);
    #    $body .= qq|$odata16| if ($shinkana1);
    #    $body .= qq|$odata17| if ($shinjuushocheck);
    #    $body .= qq|$odata18| if ($shinadd1);
    #    $body .= qq|$odata19| if ($shinken);
    #    $body .= qq|$odata20| if ($shinjuusho1);
    #    $body .= qq|$odata21| if ($shinjuusho2);
    #    $body .= qq|$odata22| if ($shinjuusho3);
    #    $body .= qq|$odata23| if ($shinkaisha);
    #    $body .= qq|$odata24| if ($shinbu);
    #    $body .= qq|$odata25| if ($shintelcheck);
    #    $body .= qq|$odata26| if ($shintelshu);
    #    $body .= qq|$odata27| if ($shintel1);
    #    $body .= qq|$odata28| if ($shinmailcheck);
    #    $body .= qq|$odata29| if ($shinmail1);

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
        #        $body .= qq|$odata9| if ($juusho3);
        #        $body .= qq|$odata10| if ($kaisha);
        #        $body .= qq|$odata11| if ($bu);
        #        $body .= qq|$odata12| if ($tel1);
        #        $body .= qq|$odata13| if ($tbangou);
        #        $body .= qq|$odata14| if ($shinnamecheck);
        #        $body .= qq|$odata15| if ($shinname1);
        #        $body .= qq|$odata16| if ($shinkana1);
        #        $body .= qq|$odata17| if ($shinjuushocheck);
        #        $body .= qq|$odata18| if ($shinadd1);
        #        $body .= qq|$odata19| if ($shinken);
        #        $body .= qq|$odata20| if ($shinjuusho1);
        #        $body .= qq|$odata21| if ($shinjuusho2);
        #        $body .= qq|$odata22| if ($shinjuusho3);
        #        $body .= qq|$odata23| if ($shinkaisha);
        #        $body .= qq|$odata24| if ($shinbu);
        #        $body .= qq|$odata25| if ($shintelcheck);
        #        $body .= qq|$odata26| if ($shintelshu);
        #        $body .= qq|$odata27| if ($shintel1);
        #        $body .= qq|$odata28| if ($shinmailcheck);
        #        $body .= qq|$odata29| if ($shinmail1);
        $body .= qq|\n$hdata3|;
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

    if ( $shinnamecheck eq "" ) {
        $shinnamecheck = "氏名変更なし";
    }
    if ( $shinjuushocheck eq "" ) {
        $shinjuushocheck = "住所関連変更なし";
    }
    if ( $shintelcheck eq "" ) {
        $shintelcheck = "電話番号関連変更なし";
    }
    if ( $shinmailcheck eq "" ) {
        $shinmailcheck = "メールアドレス変更なし";
    }

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
<meta name="description" content="登録情報変更｜食生活アドバイザー検定">
<meta name="keywords" content="登録情報変更｜食生活アドバイザー検定">
<title>登録情報変更｜食生活アドバイザー検定</title>
<link rel="Stylesheet" title="Plain" href="g.css" type="text/css">
</head>
<body bgcolor="#ffffff" text="#333333" link="#ffffff" alink="#ffffff" vlink="#ffffff">
<div align="center">
<form action="$script" method="post" name="itiran" onSubmit="disableSubmit()">
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
		<tr><td><font class="t16"><b>登録情報変更フォーム</b></font><font class="t16" color="red"><b>内容確認ページ</b></font></td></tr>
		<tr><td></td></tr>
		<tr><td><font class="t12">内容を修正する場合はページ下部にある戻るで入力ページに戻り修正してください。</font></td></tr>
		<tr><td></td></tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>


<!--個人-->
		<table border="0" cellpadding="0" cellspacing="0" width="716">
		<tr>
			<td><font class="t14">■ <b>ご本人確認のため既登録情報をご入力ください</b></font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#d2de84" width="120"><font class="t12">氏名 (※)</font></td>
			<td bgcolor="#eaf1bf" width="500"><font class="t12"><input type="hidden" name="name1" value="$name1">$name1　<input type="hidden" name="name2" value="$name2">$name2</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">フリガナ (※)</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="kana1" value="$kana1">$kana1　<input type="hidden" name="kana2" value="$kana2">$kana2</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84" width="120"><font class="t12">郵便番号 (※)</font></td>
			<td bgcolor="#eaf1bf" width="500"><font class="t12">〒<input type="hidden" name="add1" value="$add1">$add1-<input type="hidden" name="add2" value="$add2">$add2　</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">都道府県 (※)</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="ken" value="$ken">$ken　</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">市区町村 (※)</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="juusho1" value="$juusho1">$juusho1　</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">町域番地 (※)</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="juusho2" value="$juusho2">$juusho2　</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">建物名・様方</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="juusho3" value="$juusho3">$juusho3　</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">勤務先名</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="kaisha" value="$kaisha">$kaisha　</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">部署名</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="bu" value="$bu">$bu　</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">電話番号</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="tel1" value="$tel1">$tel1-<input type="hidden" name="tel2" value="$tel2">$tel2-<input type="hidden" name="tel3" value="$tel3">$tel3　</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">登録番号</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="tbangou" value="$tbangou">$tbangou　</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--変更項目-->
		<table border="0" cellpadding="0" cellspacing="0" width="716">
		<tr>
			<td><font class="t14">■ <b>変更のある項目をチェックして入力してください（複数可）</b></font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fc907d" colspan="2"><font class="t12"><input type="hidden" name="shinnamecheck" value="$shinnamecheck">$shinnamecheck　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">氏名</font></td>
			<td bgcolor="#fde7e3" width="500"><font class="t12"><input type="hidden" name="shinname1" value="$shinname1">$shinname1　<input type="hidden" name="shinname2" value="$shinname2">$shinname2</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">フリガナ</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="shinkana1" value="$shinkana1">$shinkana1　<input type="hidden" name="shinkana2" value="$shinkana2">$shinkana2</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--住所-->
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fc907d" colspan="2"><font class="t12"><input type="hidden" name="shinjuushocheck" value="$shinjuushocheck">$shinjuushocheck　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">郵便番号</font></td>
			<td bgcolor="#fde7e3" width="500">
<font class="t12">〒<input type="hidden" name="shinadd1" value="$shinadd1">$shinadd1-<input type="hidden" name="shinadd2" value="$shinadd2">$shinadd2　</font>
			</td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">都道府県</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="shinken" value="$shinken">$shinken　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">市区町村</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="shinjuusho1" value="$shinjuusho1">$shinjuusho1　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">町域番地</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="shinjuusho2" value="$shinjuusho2">$shinjuusho2　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">建物名・様方</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="shinjuusho3" value="$shinjuusho3">$shinjuusho3　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5" colspan="2"><font class="t12"><b>会社宛にご送付希望の方は勤務先・部署名のご入力も忘れずにお願いします。</b></font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">勤務先名</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="shinkaisha" value="$shinkaisha">$shinkaisha　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">部署名</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="shinbu" value="$shinbu">$shinbu　</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--ＴＥＬ-->
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fc907d" colspan="2"><font class="t12"><input type="hidden" name="shintelcheck" value="$shintelcheck">$shintelcheck　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">連絡先</font></td>
			<td bgcolor="#fde7e3" width="500"><font class="t12"><input type="hidden" name="shintelshu" value="$shintelshu">$shintelshu　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">電話番号</font></td>
			<td bgcolor="#fde7e3">
<font class="t12"><input type="hidden" name="shintel1" value="$shintel1">$shintel1-<input type="hidden" name="shintel2" value="$shintel2">$shintel2-<input type="hidden" name="shintel3" value="$shintel3">$shintel3　</font>
			</td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--メール-->
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fc907d" colspan="2"><font class="t12"><input type="hidden" name="shinmailcheck" value="$shinmailcheck">$shinmailcheck　</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">メールアドレス</font></td>
			<td bgcolor="#fde7e3" width="500"><font class="t12"><input type="hidden" name="shinmail1" value="$shinmail1">$shinmail1\@<input type="hidden" name="shinmail2" value="$shinmail2">$shinmail2　</font></td>
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
			<td><font class="t14">■ <b>ご質問</b></font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fdc0b5" width="350"><font class="t12">変更した内容に願書の再送を希望しますか？ (※)</font></td>
			<td bgcolor="#fde7e3" width="370"><font class="t12"><input type="hidden" name="saisou" value="$saisou">$saisou　</font></td>
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
			<td align="center" width="150"><input type="image" src="img/send.gif" width="93" height="21" border="0"  id="submitButton"></td>
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
<tr><td align="center"><font class="t12">登録情報変更｜食生活アドバイザー検定</font></td></tr>
</table>
</form>
</div>

<!-- 2016/03/25 追加 -->
<script type="text/javascript">
function disableSubmit() {
    document.getElementById("submitButton").disabled = true;
}
</script>

</body>
</html>

EOM
}

##########自動返信宣言##########
# 2016/03/25 追加
sub success {
    print "Location: success.html\n\n";
}

# 2016/03/25
# リロード対策のため、以下のHTML出力をsuccess.htmlへ移動し、
# &successを&success_oldへ変更
sub success_old {
    print "Content-type: text/html\n\n";
    print <<EOM;
<!DOCtype HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=Shift_JIS">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="description" content="登録情報変更｜食生活アドバイザー検定">
<meta name="keywords" content="登録情報変更｜食生活アドバイザー検定">
<title>登録情報変更｜食生活アドバイザー検定</title>
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
		<tr><td><font class="t16"><b>登録情報変更フォーム</b></font><font class="t16" color="red"><b>登録完了</b></font></td></tr>
		<tr><td height="30"></td></tr>
		<tr><td><font class="t12">登録情報の変更を受付ました。ご登録ありがとうございます。<br>
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
<tr><td align="center"><font class="t12">登録情報変更｜食生活アドバイザー検定</font></td></tr>
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
<tr><td><div class="title">登録情報変更フォーム個人情報の取り扱いについて
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
<p style="font-size:12px; text-align:center; padding-top:20px;">登録情報変更｜食生活アドバイザー</p>
</div>
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
<meta name="description" content="登録情報変更｜食生活アドバイザー検定">
<meta name="keywords" content="登録情報変更｜食生活アドバイザー検定">
<title>登録情報変更｜食生活アドバイザー検定</title>
<link rel="Stylesheet" title="Plain" href="g.css" type="text/css">
</head>
<body bgcolor="#ffffff" text="#333333" link="#ffffff" alink="#ffffff" vlink="#ffffff">
<div align="center">
<form action="https://ssl.flanet.jp/memberform/member.cgi" method="post" name="itiran">
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

    if ( $checkname ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkname</font></td></tr>";
    }
    if ( $checkkana ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkkana</font></td></tr>";
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
    if ( $checkshinjuusho1 ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkshinjuusho1</font></td></tr>";
    }
    if ( $checkshinjuusho2 ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkshinjuusho2</font></td></tr>";
    }
    if ( $checkshinjuusho3 ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkshinjuusho3</font></td></tr>";
    }
    if ( $checktel ne "" ) {
        print "<tr><td><font class=t12 color=red>$checktel</font></td></tr>";
    }
    if ( $checktbangou ne "" ) {
        print "<tr><td><font class=t12 color=red>$checktbangou</font></td></tr>";
    }
    if ( $checkshinnamecheck ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkshinnamecheck</font></td></tr>";
    }
    if ( $checkshinjuushocheck ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkshinjuushocheck</font></td></tr>";
    }
    if ( $checkshinadd ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkshinadd</font></td></tr>";
    }
    if ( $checkshintelcheck ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkshintelcheck</font></td></tr>";
    }
    if ( $checkshintel ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkshintel</font></td></tr>";
    }
    if ( $checkshinmailcheck ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkshinmailcheck</font></td></tr>";
    }
    if ( $checkshinmail ne "" ) {
        print "<tr><td><font class=t12 color=red>$checkshinmail</font></td></tr>";
    }
    if ( $checksaisou ne "" ) {
        print "<tr><td><font class=t12 color=red>$checksaisou</font></td></tr>";
    }

    #	if($checkmail ne ""){
    #		print "<tr><td><font class=t12 color=red>$checkmail</font></td></tr>";
    #	}
    #	if($checkmailk ne ""){
    #		print "<tr><td><font class=t12 color=red>$checkmailk</font></td></tr>";
    #	}
    #	if($checkmailkakunin ne ""){
    #		print "<tr><td><font class=t12 color=red>$checkmailkakunin</font></td></tr>";
    #	}

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
<tr><td align="center"><font class="t12">登録情報変更｜食生活アドバイザー検定</font></td></tr>
</table>
</form>
</div>
</body>
</html>



EOM

}

##########エラー宣言##########
sub checkdata {

    $name1   = &no_tag($name1);
    $name2   = &no_tag($name2);
    $kana1   = &no_tag($kana1);
    $kana2   = &no_tag($kana2);
    $add1    = &no_tag($add1);
    $add2    = &no_tag($add2);
    $ken     = &no_tag($ken);
    $juusho1 = &no_tag($juusho1);
    $juusho2 = &no_tag($juusho2);
    $juusho3 = &no_tag($juusho3);
    $kaisha  = &no_tag($kaisha);
    $bu      = &no_tag($bu);
    $tel1    = &no_tag($tel1);
    $tel2    = &no_tag($tel2);
    $tel3    = &no_tag($tel3);
    $tbangou = &no_tag($tbangou);

    $shinnamecheck   = &no_tag($shinnamecheck);
    $shinname1       = &no_tag($shinname1);
    $shinname2       = &no_tag($shinname2);
    $shinkana1       = &no_tag($shinkana1);
    $shinkana2       = &no_tag($shinkana2);
    $shinjuushocheck = &no_tag($shinjuushocheck);
    $shinadd1        = &no_tag($shinadd1);
    $shinadd2        = &no_tag($shinadd2);
    $shinken         = &no_tag($shinken);
    $shinjuusho1     = &no_tag($shinjuusho1);
    $shinjuusho2     = &no_tag($shinjuusho2);
    $shinjuusho3     = &no_tag($shinjuusho3);
    $shinkaisha      = &no_tag($shinkaisha);
    $shinbu          = &no_tag($shinbu);
    $shintelcheck    = &no_tag($shintelcheck);
    $shintel1        = &no_tag($shintel1);
    $shintel2        = &no_tag($shintel2);
    $shintel3        = &no_tag($shintel3);
    $shinmailcheck   = &no_tag($shinmailcheck);
    $shinmail1       = &no_tag($shinmail1);
    $shinmail2       = &no_tag($shinmail2);
    $shinmail3       = &no_tag($shinmail3);
    $shinmail4       = &no_tag($shinmail4);
    $saisou          = &no_tag($saisou);

    $telshu = &no_tag($telshu);

    if ( $name1 eq "" ) {
        $checkname = "[ご本人確認項目の氏名]が正しく入力されていません。";
        $checkkazu = "1";
    }

    if ( $name2 eq "" ) {
        $checkname = "[ご本人確認項目の氏名]が正しく入力されていません。";
        $checkkazu = "1";
    }

    if ( $kana1 eq "" ) {
        $checkkana = "[ご本人確認項目のフリガナ]が正しく入力されていません。";
        $checkkazu = "1";
    }

    if ( $kana2 eq "" ) {
        $checkkana = "[ご本人確認項目のフリガナ]が正しく入力されていません。";
        $checkkazu = "1";
    }

    if ( ( $add1 =~ /\D/g ) || ( $add1 eq "" ) ) {
        $checkadd  = "[ご本人確認項目の郵便番号]が正しく入力されていません。";
        $checkkazu = "1";
    }
    if ( ( $add2 =~ /\D/g ) || ( $add2 eq "" ) ) {
        $checkadd  = "[ご本人確認項目の郵便番号]が正しく入力されていません。";
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
        $checkken  = "[ご本人確認項目の住所]が正しく入力されていません。";
        $checkkazu = "1";
    }

    if ( $juusho1 eq "" ) {
        $checkken  = "[ご本人確認項目の住所]が正しく入力されていません。";
        $checkkazu = "1";
    } elsif (&juusho_check($juusho1) == 0) {
        $checkjuusho1 = "[ご本人確認項目の市区町村]は15文字以内で入力してください。";
        $checkkazu = "1";
    }

    if ( $juusho2 eq "" ) {
        $checkken  = "[ご本人確認項目の住所]が正しく入力されていません。";
        $checkkazu = "1";
    } elsif (&juusho_check($juusho2) == 0) {
        $checkjuusho2 = "[ご本人確認項目の町域番地]は15文字以内で入力してください。";
        $checkkazu = "1";
    }

    if (&juusho_check($juusho3) == 0) {
        $checkjuusho3 = "[ご本人確認項目の建物名・様方]は15文字以内で入力してください。";
        $checkkazu = "1";
    }

    if ( $tel1 =~ /\D/g ) {
        $checktel  = "[ご本人確認項目の電話番号]が正しく入力されていません。";
        $checkkazu = "1";
    }
    if ( $tel2 =~ /\D/g ) {
        $checktel  = "[ご本人確認項目の電話番号]が正しく入力されていません。";
        $checkkazu = "1";
    }
    if ( $tel3 =~ /\D/g ) {
        $checktel  = "[ご本人確認項目の電話番号]が正しく入力されていません。";
        $checkkazu = "1";
    }

    if ( $tel1 ne "" ) {
        if ( $tel2 eq "" ) {
            $checktel  = "[ご本人確認項目の電話番号]が正しく入力されていません。";
            $checkkazu = "1";
        }
        if ( $tel3 eq "" ) {
            $checktel  = "[ご本人確認項目の電話番号]が正しく入力されていません。";
            $checkkazu = "1";
        }
    }
    if ( $tel2 ne "" ) {
        if ( $tel1 eq "" ) {
            $checktel  = "[ご本人確認項目の電話番号]が正しく入力されていません。";
            $checkkazu = "1";
        }
        if ( $tel3 eq "" ) {
            $checktel  = "[ご本人確認項目の電話番号]が正しく入力されていません。";
            $checkkazu = "1";
        }
    }
    if ( $tel3 ne "" ) {
        if ( $tel1 eq "" ) {
            $checktel  = "[ご本人確認項目の電話番号]が正しく入力されていません。";
            $checkkazu = "1";
        }
        if ( $tel2 eq "" ) {
            $checktel  = "[ご本人確認項目の電話番号]が正しく入力されていません。";
            $checkkazu = "1";
        }
    }
    if ( $tbangou ne "" ) {
        if ( $tbangou =~ /^[\w\-\.]+$/ ) {
            $nodata1 = "yes";
        }
        else {
            $checktbangou = "[ご本人確認項目の登録番号]が正しく入力されていません。";
            $checkkazu    = "1";
        }
    }

    if ( $shinnamecheck ne "" ) {
        if ( ( $shinname1 eq "" ) && ( $shinname2 eq "" ) && ( $shinkana1 eq "" ) && ( $shinkana2 eq "" ) ) {
            $checkshinnamecheck = "[氏名変更]がチェックされていますが正しく入力されていません。";
            $checkkazu          = "1";
        }
    }

    if ( $shinjuushocheck ne "" ) {
        if (   ( $shinadd1 eq "" )
            && ( $shinadd2    eq "" )
            && ( $shinken     eq "" )
            && ( $shinjuusho1 eq "" )
            && ( $shinjuusho2 eq "" )
            && ( $shinjuusho3 eq "" )
            && ( $shinkaisha  eq "" )
            && ( $shinbu      eq "" ) )
        {
            $checkshinjuushocheck = "[住所関連変更]がチェックされていますが正しく入力されていません。";
            $checkkazu            = "1";
        }

        if (&juusho_check($shinjuusho1) == 0) {
            $checkshinjuusho1 = "[住所関連の市区町村]は15文字以内で入力してください。";
            $checkkazu = "1";
        }
        if (&juusho_check($shinjuusho2) == 0) {
            $checkshinjuusho2 = "[住所関連の町域番地]は15文字以内で入力してください。";
            $checkkazu = "1";
        }
        if (&juusho_check($shinjuusho3) == 0) {
            $checkshinjuusho3 = "[住所関連の建物名・様方]は15文字以内で入力してください。";
            $checkkazu = "1";
        }
    }
    if ( $shinadd1 =~ /\D/g ) {
        $checkshinadd = "[住所関連変更項目の郵便番号]が正しく入力されていません。";
        $checkkazu    = "1";
    }
    if ( $shinadd2 =~ /\D/g ) {
        $checkshinadd = "[住所関連変更項目の郵便番号]が正しく入力されていません。";
        $checkkazu    = "1";
    }
    if ( $shinadd1 ne "" ) {
        if ( $shinadd2 eq "" ) {
            $checkshinadd = "[住所関連変更項目の郵便番号]が正しく入力されていません。";
            $checkkazu    = "1";
        }
    }
    if ( $shinadd2 ne "" ) {
        if ( $shinadd1 eq "" ) {
            $checkshinadd = "[住所関連変更項目の郵便番号]が正しく入力されていません。";
            $checkkazu    = "1";
        }
    }

    # 追加処理
    my $shinadd_chk_flg;
    $shinadd_chk_flg = 1 if ( $shinadd1 && length($shinadd1) ne 3 );
    $shinadd_chk_flg = 1 if ( $shinadd2 && length($shinadd2) ne 4 );
    if ($shinadd_chk_flg) {
        $checktel  = "[住所関連変更項目の郵便番号]の桁数が正しくありません。";
        $checkkazu = "1";
    }

    if ( $shintelcheck ne "" ) {
        if ( ( $shintelshu eq "" ) && ( $shintel1 eq "" ) && ( $shintel2 eq "" ) && ( $shintel3 eq "" ) ) {
            $checkshintelcheck = "[電話番号関連変更]がチェックされていますが正しく入力されていません。";
            $checkkazu         = "1";
        }
    }
    if ( $shintel1 =~ /\D/g ) {
        $checkshintel = "[電話番号関連変更項目の電話番号]が正しく入力されていません。";
        $checkkazu    = "1";
    }
    if ( $shintel2 =~ /\D/g ) {
        $checkshintel = "[電話番号関連変更項目の電話番号]が正しく入力されていません。";
        $checkkazu    = "1";
    }
    if ( $shintel3 =~ /\D/g ) {
        $checkshintel = "[電話番号関連変更項目の電話番号]が正しく入力されていません。";
        $checkkazu    = "1";
    }

    if ( $shintel1 ne "" ) {
        if ( $shintel2 eq "" ) {
            $checkshintel = "[電話番号関連変更項目の電話番号]が正しく入力されていません。";
            $checkkazu    = "1";
        }
        if ( $shintel3 eq "" ) {
            $checkshintel = "[電話番号関連変更項目の電話番号]が正しく入力されていません。";
            $checkkazu    = "1";
        }
    }
    if ( $shintel2 ne "" ) {
        if ( $shintel1 eq "" ) {
            $checkshintel = "[電話番号関連変更項目の電話番号]が正しく入力されていません。";
            $checkkazu    = "1";
        }
        if ( $shintel3 eq "" ) {
            $checkshintel = "[電話番号関連変更項目の電話番号]が正しく入力されていません。";
            $checkkazu    = "1";
        }
    }
    if ( $shintel3 ne "" ) {
        if ( $shintel1 eq "" ) {
            $checkshintel = "[電話番号関連変更項目の電話番号]が正しく入力されていません。";
            $checkkazu    = "1";
        }
        if ( $shintel2 eq "" ) {
            $checkshintel = "[電話番号関連変更項目の電話番号]が正しく入力されていません。";
            $checkkazu    = "1";
        }
    }

    if ( $shinmailcheck ne "" ) {
        if ( ( $shinmail1 eq "" ) && ( $shinmail2 eq "" ) ) {
            $checkshinmailcheck = "[メールアドレス変更]がチェックされていますが正しく入力されていません。";
            $checkkazu          = "1";
        }
    }

    if ( $shinmail1 ne "" ) {
        if ( $shinmail1 =~ /^[\w\-\.]+$/ ) {
            $nodata1 = "yes";
        }
        else {
            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
            $checkkazu     = "1";
        }
    }
    if ( $shinmail2 ne "" ) {
        if ( $shinmail2 =~ /^[\w\-\.]+$/ ) {
            $nodata1 = "yes";
        }
        else {
            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
            $checkkazu     = "1";
        }
    }

    #    if ( $shinmail3 ne "" ) {
    #        if ( $shinmail3 =~ /^[\w\-\.]+$/ ) {
    #            $nodata1 = "yes";
    #        }
    #        else {
    #            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
    #            $checkkazu     = "1";
    #        }
    #    }
    #    if ( $shinmail4 ne "" ) {
    #        if ( $shinmail4 =~ /^[\w\-\.]+$/ ) {
    #            $nodata1 = "yes";
    #        }
    #        else {
    #            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
    #            $checkkazu     = "1";
    #        }
    #    }

    if ( $shinmail1 ne "" ) {
        if ( $shinmail2 eq "" ) {
            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
            $checkkazu     = "1";
        }

        #        if ( $shinmail3 eq "" ) {
        #            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
        #            $checkkazu     = "1";
        #        }
        #        if ( $shinmail4 eq "" ) {
        #            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
        #            $checkkazu     = "1";
        #        }
    }
    if ( $shinmail2 ne "" ) {
        if ( $shinmail1 eq "" ) {
            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
            $checkkazu     = "1";
        }

        #        if ( $shinmail3 eq "" ) {
        #            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
        #            $checkkazu     = "1";
        #        }
        #        if ( $shinmail4 eq "" ) {
        #            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
        #            $checkkazu     = "1";
        #       }
    }

    #    if ( $shinmail3 ne "" ) {
    #        if ( $shinmail1 eq "" ) {
    #            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
    #            $checkkazu     = "1";
    #        }
    #        if ( $shinmail2 eq "" ) {
    #            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
    #            $checkkazu     = "1";
    #        }
    #        if ( $shinmail4 eq "" ) {
    #            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
    #            $checkkazu     = "1";
    #        }
    #    }
    #    if ( $shinmail4 ne "" ) {
    #        if ( $shinmail1 eq "" ) {
    #            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
    #            $checkkazu     = "1";
    #        }
    #        if ( $shinmail2 eq "" ) {
    #            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
    #            $checkkazu     = "1";
    #        }
    #        if ( $shinmail3 eq "" ) {
    #            $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
    #            $checkkazu     = "1";
    #        }
    #    }

    #    if ( $shinmail1 ne $shinmail3 ) {
    #        $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
    #        $checkkazu     = "1";
    #    }

    #    if ( $shinmail2 ne $shinmail4 ) {
    #        $checkshinmail = "[メールアドレス変更項目のメールアドレス]が正しく入力されていません。";
    #        $checkkazu     = "1";
    #    }

    #	if( $mail1 =~ /^[\w\-\.]+$/){
    #		$nodata1="yes";
    #	}
    #	else{
    #		$checkmail		=	"[メールアドレス]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}

    #
    #	if(( $tel1 eq "" )&&( $tel2 eq "" )&&( $tel3 eq "" )){
    #		$checksp		=	"[ご希望の資料]が正しく選択されていません。";
    #		$checkkazu	=	"1";
    #	}
    #	if( $telshu eq "" ){
    #		$checktelshu		=	"[TEL連絡先]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}
    #
    #	if(( $tel1  =~ /\D/g )||( $tel1 eq "" )){
    #		$checktel		=	"[電話番号]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}
    #	if(( $tel2  =~ /\D/g )||( $tel2 eq "" )){
    #		$checktel		=	"[電話番号]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}
    #	if(( $tel3  =~ /\D/g )||( $tel3 eq "" )){
    #		$checktel		=	"[電話番号]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}

    #	if( $mail1 eq "" ){
    #		$checkmail		=	"[メールアドレス]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}
    #	if( $mail1 =~ /^[\w\-\.]+$/){
    #		$nodata1="yes";
    #	}
    #	else{
    #		$checkmail		=	"[メールアドレス]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}
    #
    #	if( $mail2 eq "" ){
    #		$checkmail		=	"[メールアドレス]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}
    #	if( $mail2 =~ /^[\w\-\.]+$/){
    #		$nodata2="no";
    #	}
    #	else{
    #		$checkmail		=	"[メールアドレス]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}
    #
    #	if( $mail3 eq "" ){
    #		$checkmailk		=	"[確認用メールアドレス]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}
    #	if( $mail3 =~ /^[\w\-\.]+$/){
    #		$nodata1="yes";
    #	}
    #	else{
    #		$checkmailk		=	"[確認用メールアドレス]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}
    #
    #	if( $mail4 eq "" ){
    #		$checkmailk		=	"[確認用メールアドレス]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}
    #	if( $mail4 =~ /^[\w\-\.]+$/){
    #		$nodata2="no";
    #	}
    #	else{
    #		$checkmailk		=	"[確認用メールアドレス]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}
    #
    #	if( $mail1 ne $mail3 ){
    #		$checkmailkakunin		=	"[メールアドレスと確認のため再入力したメールアドレス]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}
    #
    #	if( $mail2 ne $mail4 ){
    #		$checkmailkakunin		=	"[メールアドレスと確認のため再入力したメールアドレス]が正しく入力されていません。";
    #		$checkkazu	=	"1";
    #	}

    if ( $saisou eq "" ) {
        $checksaisou = "[ご質問]が正しく選択されていません。";
        $checkkazu   = "1";
    }

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
