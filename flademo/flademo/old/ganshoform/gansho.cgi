#!/usr/bin/perl

# --------------------------------
# ���W���[���Ǎ�
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
# �����ݒ�
# --------------------------------

# ���X�N���v�g
my $script = 'https://ssl.flanet.jp/ganshoform/gansho.cgi';    # �{�ԗp

# my $script = 'gansho.cgi';

# �o��CSV
$csv_dir     = '../csvdata';
$backup_dir  = '../csvdata/csvbackup';
$csv_data    = 'gansho.csv';
$backup_data = 'backupgansho.csv';

# �{�ԗp
$to1  = 'fla.entry@flanet.jp';    # �Ǘ��҃A�h���X
$from = 'fla.entry@flanet.jp';    # from�p�Ǘ��҃A�h���X

# ���ؗp
# $to1  = 'ishii-k@ww-system.com';                                # �Ǘ��҃A�h���X
# $from = 'ishii-k@ww-system.com';                                # from�p�Ǘ��҃A�h���X

# ���[���^�C�g��
$subject  = '�l����菑�̐���������܂����B';              # �Ǘ��җp
$subject2 = '�H�����A�h�o�C�U�[����@�菑�����o�^����';    # ���[�U�p

# --------------------------------
# ����
# --------------------------------
&kensa;

if ( $kensa eq "a" ) {

    # �m�F����
    &inputdata;                                            # �f�[�^���`
    &privacydata;                                          # �l����舵�����Ӄ`�F�b�N
    &checkdata;                                            # ���̓f�[�^�`�F�b�N
    &outputshori;                                          # �^�O����
    &outputdata;                                           # �m�F��ʕ\��
}

elsif ( $kensa eq "b" ) {

    # ���M����
    &time;                                                 # ���Ԏ擾
    &inputdata;                                            # �f�[�^���`
    &fileinput;                                            # CSV�o��
    &mailmail;                                             # ���[���f�[�^�쐬
    &maildata1;                                            # �Ǘ��҂ւ̃��[�����M
    &maildata2;                                            # ���[�U�ւ̃��[�����M
    &success;                                              # ������ʕ\��
}
else {

    # �G���[����
    &errormax;
}

##########����##############
sub kensa {
    &ReadParse(*formdata);
    $kensa = $formdata{'kensa'};
}

##########�N�����T##########
sub time {
    ( $sec, $min, $hour, $day, $mon, $year, $wday ) = localtime(time);
    $mon++;
    @tm = ( "��", "��", "��", "��", "��", "��", "�y" );
    $year = ( $year + 1900 );
}

##########���ځ������R�[�h�w��##########
sub inputdata {
    &ReadParse(*formdata);

    # �l���f�[�^
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
    # �ǉ�����
    # ----------------------------------------------
    # ��������啶����
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

##########�t�@�C���Ƀf�[�^�̏o�͂�����B##############################

sub fileinput {

    # my $filedata = sprintf( '%s/csv/%04d-%02d.csv', $dir{'db'}, &YY, &MM );

    # �f�[�^����
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
    $yobinow  = "@tm[$wday]�j��";
    $jikannew = "$hour$min";
    $namenew  = "$name1 $name2";
    $kananew  = "$kana1 $kana2";
    $birthnew = sprintf( '%04d/%02d/%02d', $birth_year, $birth_month, $birth_day );
    $addnew   = "$add1$add2";
    $telnew   = "$tel1-$tel2-$tel3";
    $mailnew  = "$mail1\@$mail2";
    $kennew   = "$ken$juusho";

    # CSV�f�[�^
    my $csv;
    my $csv_back;

    if ( !-e qq|$csv_dir/$csv_data| ) {
        $csv = &make_csv(    # *
            '�o�^��',
            '����',
            '�j��',
            '�ߋ��ɐ���',
            '�ߋ��Ɏ�',
            '����',
            '�t���K�i',
            '���N����',
            '����',
            '�X�֔ԍ�',
            '�s���{��',
            '�s�撬��',
            '�Ԓn',
            '�������E�l��',
            '�Ζ��於',
            '������',
            '�d�b�ԍ���',
            '�d�b�ԍ�',
            '���[���A�h���X',
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
            '�o�^��',
            '����',
            '�j��',
            '�ߋ��ɐ���',
            '�ߋ��Ɏ�',
            '����',
            '�t���K�i',
            '���N����',
            '����',
            '�X�֔ԍ�',
            '�s���{��',
            '�s�撬��',
            '�Ԓn',
            '�������E�l��',
            '�Ζ��於',
            '������',
            '�d�b�ԍ���',
            '�d�b�ԍ�',
            '���[���A�h���X',
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
    
    # �ǋL
    my_open( OUT, ">>$csv_dir/$csv_data" );
    binmode OUT;
    print OUT $csv;
    my_close(OUT);

    # �o�b�N�A�b�vCSV�ǋL
    my_open( OUT, ">>$backup_dir/$backup_data" );
    binmode OUT;
    print OUT $csv_back;
    my_close(OUT);
}

# ---------------------------------------------------------------
# ���[���f�[�^�쐬
# ---------------------------------------------------------------
sub mailmail {

    #�������O����
    #�X�y�[�X
    $space = " ";

    #���������R�[�h
    &jcode'convert( *space, 'jis' );
    &jcode'convert( *name1, 'jis' );
    &jcode'convert( *name2, 'jis' );

    $subject    = "$name1$space$name2$subject";
    $mailhantei = "$mail1$mail2";
    $mail       = "$mail1\@$mail2";
    $birth      = sprintf( '%04d/%02d/%02d', $birth_year, $birth_month, $birth_day );

    # ��M�p
    $odata1  = "\n�E��t�����F$timenow$yobinow$hour��$min��";
    $odata2  = "\n�E����1 �ߋ��Ɋ菑�������������Ƃ�����܂����H�F$shitsumon1";
    $odata3  = "\n�E����2 �ߋ��Ɏ󌱂��������Ƃ�����܂����H�F$shitsumon2";
    $odata4  = "\n�E�����F$name1 $name2";
    $odata5  = "\n�E�t���K�i�F$kana1 $kana2";
    $odata6  = "\n�E���N�����F$birth";
    $odata7  = "\n�E���ʁF$sei";
    $odata8  = "\n�E�X�֔ԍ��F$add1-$add2";
    $odata9  = "\n�E�Z���F\n$ken";
    $odata10 = "\n$juusho1";
    $odata11 = "\n$juusho2";
    $odata12 = "\n$juusho3";
    $odata13 = "\n�E�Ζ��於�F$kaisha";
    $odata14 = "\n�E�������F$bu";
    $odata15 = "\n�E�A����F$telshu";
    $odata16 = "\n�E�d�b�ԍ��F$tel1-$tel2-$tel3";
    $odata17 = "\n�EE-MAIL�F$mail1\@$mail2";

    # �ԐM�p
    $hdata1 = "$name1$name2�l\n\n";

#    $hdata2 = "�菑�����𒸂��܂��Ă��肪�Ƃ��������܂��B\n�����܂ō����΂炭���҂����������B\n�o�^���e�͉��L�̒ʂ�ƂȂ�܂��B���m�F���肢���܂��B\n���A���̃��[���͑��M��p�A�h���X�Ŏ������M���Ă���܂��B\n���̃��[���ɕԐM����Ă��A�ԐM���e�̊m�F����т��ԓ����ł��܂���̂ł����ӂ��������B\n";
    $hdata2
        = "�菑�����𒸂��܂��Ă��肪�Ƃ��������܂��B\n�����܂ō����΂炭���҂����������B\n���A���̃��[���͑��M��p�A�h���X�Ŏ������M���Ă���܂��B\n���̃��[���ɕԐM����Ă��A�ԐM���e�̊m�F����т��ԓ����ł��܂���̂ł����ӂ��������B";
    $hdata3 = "��낵�����肢�������܂��B";
    $hdata4 = "\n----------------------------\n";
    $hdata5 = "��ʎВc�@�lFLA�l�b�g���[�N����\n�H�����A�h�o�C�U�[���莖����\nTEL:0120-86-3593";
    $hdata6 = "\n----------------------------\n";

    # �S���p
    #    $tdata1 = "$name1$name2�l����菑�̐���������܂����B\n";
    $tdata1 = "WEB����菑�̐���������܂����B";
}

# ---------------------------------------------------------------
# ���[���o�͌���?@�i�����̃��[���ցj
# ---------------------------------------------------------------
sub maildata1 {

    my $sendmail
        = ( -e '/usr/lib/sendmail' ) ? '/usr/lib/sendmail'
        : ( -e '/usr/sbin/sendmail' ) ? '/usr/sbin/sendmail'
        :                               '';

    # ���[��BODY
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
# ���[���o�͌���?A�i�o�^�җl�ւ̕ԐM���[���j
# ---------------------------------------------------------------
sub maildata2 {

    if ( $mailhantei ne "" ) {

        # ($mail =~ /^[a-zA-Z0-9_\-\.a-zA-Z0-9\-\.]+\@[a-zA-Z0-9_\-\.a-zA-Z0-9\-\.]+$/))
        # ������̋L�q�̏ȗ��ł́@( $mail =~ /^[\w\-\.]+\@[\w\-\.]+$/)�@�ɂȂ�I�I

        my $sendmail
            = ( -e '/usr/lib/sendmail' ) ? '/usr/lib/sendmail'
            : ( -e '/usr/sbin/sendmail' ) ? '/usr/sbin/sendmail'
            :                               '';

        # ���[��BODY
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

##########�m�F�y�[�W������������##########
sub outputshori {

    $newbikou = $bikou;

    $newbikou = &no_tag2($newbikou);

}

##########�m�F�y�[�W##########
sub outputdata {
    print "Content-type: text/html\n\n";
    print <<EOM;

<!DOCtype HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=Shift_JIS">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="description" content="�菑�����b�H�����A�h�o�C�U�[����">
<meta name="keywords" content="�菑�����b�H�����A�h�o�C�U�[����">
<title>�菑�����b�H�����A�h�o�C�U�[����</title>
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
				<td align="right">		<span id="ss_img_wrapper_115-57_flash_ja"><a href="http://jp.globalsign.com/" target=_blank><img alt="SSL�@�O���[�o���T�C���̃T�C�g�V�[��" border=0 id="ss_img" src="//seal.globalsign.com/SiteSeal/images/gs_noscript_115-57_ja.gif"></a></span><script type="text/javascript" src="//seal.globalsign.com/SiteSeal/gs_flash_115-57_ja.js"></script></td>
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
<!--------------------���C���J�n------------------------>
		<table width="720" border="0" cellpadding="2" cellspacing="0">
		<tr><td><font class="t16"><b>�菑�����t�H�[��</b></font><font class="t16" color="red"><b>���e�m�F�y�[�W</b></font></td></tr>
		<tr><td></td></tr>
		<tr><td><font class="t12">���e���C������ꍇ�̓y�[�W�����ɂ���߂�œ��̓y�[�W�ɖ߂�C�����Ă��������B</font></td></tr>
		<tr><td></td></tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--����-->
		<table border="0" cellpadding="0" cellspacing="0" width="716">
		<tr>
			<td><font class="t12">��������</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fdc0b5" width="320"><font class="t12">�ߋ��Ɋ菑�������������Ƃ�����܂����H (��)</font></td>
			<td bgcolor="#fde7e3" width="400"><font class="t12"><input type="hidden" name="shitsumon1" value="$shitsumon1">$shitsumon1�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5" width="400"><font class="t12">�ߋ��Ɏ󌱂��������Ƃ�����܂����H (��)</font></td>
			<td bgcolor="#fde7e3" width="320"><font class="t12"><input type="hidden" name="shitsumon2" value="$shitsumon2">$shitsumon2�@</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--�l-->
		<table border="0" cellpadding="0" cellspacing="0" width="716">
		<tr>
			<td><font class="t12">��������</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">���� (��)</font></td>
			<td bgcolor="#fde7e3" width="500"><font class="t12"><input type="hidden" name="name1" value="$name1">$name1�@<input type="hidden" name="name2" value="$name2">$name2</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">�t���K�i (��)</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="kana1" value="$kana1">$kana1�@<input type="hidden" name="kana2" value="$kana2">$kana2</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">���N���� (��)</font></td>
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
			<td bgcolor="#fdc0b5"><font class="t12">���� (��)</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="sei" value="$sei">$sei�@</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--�Z��-->
		<table border="0" cellpadding="0" cellspacing="0" width="716">
		<tr>
			<td><font class="t12">���Z����</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">�X�֔ԍ� (��)</font></td>
			<td bgcolor="#fde7e3" width="500"><font class="t12">��<input type="hidden" name="add1" value="$add1">$add1-<input type="hidden" name="add2" value="$add2">$add2�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">�s���{�� (��)</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="ken" value="$ken">$ken�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">�s�撬�� (��)</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="juusho1" value="$juusho1">$juusho1�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">����Ԓn (��)</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="juusho2" value="$juusho2">$juusho2�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">�������E�l��</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="juusho3" value="$juusho3">$juusho3�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5" colspan="2"><font class="t12"><b>��Ј��ɂ����t��]�̕��͋Ζ���E�������̂����͂��Y�ꂸ�ɂ��肢���܂��B</b></font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">�Ζ��於</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="kaisha" value="$kaisha">$kaisha�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">������</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="bu" value="$bu">$bu�@</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--�s�d�k-->
		<table border="0" cellpadding="0" cellspacing="0" width="716">
		<tr>
			<td><font class="t12">���d�b�ԍ�</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">�A���� (��)</font></td>
			<td bgcolor="#fde7e3" width="500"><font class="t12"><input type="hidden" name="telshu" value="$telshu">$telshu�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">�d�b�ԍ� (��)</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="tel1" value="$tel1">$tel1-<input type="hidden" name="tel2" value="$tel2">$tel2-<input type="hidden" name="tel3" value="$tel3">$tel3�@</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--���[��-->
		<table border="0" cellpadding="0" cellspacing="0" width="716">
		<tr>
			<td><font class="t12">�����[���A�h���X</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">���[���A�h���X</font></td>
			<td bgcolor="#fde7e3" width="500"><font class="t12"><input type="hidden" name="mail1" value="$mail1">$mail1\@<input type="hidden" name="mail2" value="$mail2">$mail2�@</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="1" cellspacing="0">
			<tr>
				<td><font class="t12">�@</font></td>
			</tr>
		</table>
		<table border="0" cellpadding="3" cellspacing="0">
		<tr><td></td></tr>
		<tr>
			<td align="center" width="150"><input type="image" src="img/send.gif" width="93" height="21" border="0"></td>
			<td align="center" width="150"><a href="javaScript:history.back()"><img src="img/back.gif" width="88" height="21" border="0"></a></td>
		</tr>
		</table>
<!-------------------���C������----------------------------->
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
<tr><td align="center"><font class="t12">�菑�����b�H�����A�h�o�C�U�[����</font></td></tr>
</table>
</form>
</div>
</body>
</html>

EOM
}

##########�����ԐM�錾##########
sub success {
    print "Content-type: text/html\n\n";

    print <<EOM;


<!DOCtype HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=Shift_JIS">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="description" content="�菑�����b�H�����A�h�o�C�U�[����">
<meta name="keywords" content="�菑�����b�H�����A�h�o�C�U�[����">
<title>�菑�����b�H�����A�h�o�C�U�[����</title>
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
				<td align="right">		<span id="ss_img_wrapper_115-57_flash_ja"><a href="http://jp.globalsign.com/" target=_blank><img alt="SSL�@�O���[�o���T�C���̃T�C�g�V�[��" border=0 id="ss_img" src="//seal.globalsign.com/SiteSeal/images/gs_noscript_115-57_ja.gif"></a></span><script type="text/javascript" src="//seal.globalsign.com/SiteSeal/gs_flash_115-57_ja.js"></script></td>
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
<!--------------------���C���J�n------------------------>
		<table width="720" border="0" cellpadding="2" cellspacing="0">
		<tr><td><font class="t16"><b>�菑�����t�H�[��</b></font><font class="t16" color="red"><b>�o�^����</b></font></td></tr>
		<tr><td height="30"></td></tr>
		<tr><td><font class="t12">�菑�̂��������肪�Ƃ��������܂����B<br>�菑��3�����{��蔭�����J�n���Ă���܂��B<br>
    ��������A1�T�Ԍo���Ă����茳�ɓ͂��Ă��Ȃ��ꍇ�́A���莖���ǂ܂ł��A�����������B�i�������A�A�x���͂��ޏꍇ�͒x��邱�Ƃ�����܂��̂ł��������������B)<br>
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
			<td><font class="t12">[ <a href="http://www.flanet.jp/" target="_top"><font color="blue"><u>�s�n�o�y�[�W�֖߂�</u></font></a> ]</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!-------------------���C������----------------------------->
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
<tr><td align="center"><font class="t12">�菑�����b�H�����A�h�o�C�U�[����</font></td></tr>
</table>
</form>
</div>
</body>
</html>


EOM
}

##########���ڃ����N��\��ꂽ�ꍇ�̃G���[##########
sub errormax {
    print "Content-type: text/html\n\n";

    print <<EOM;
<html>
<head>
<title>�G���[</title>
</head>
<body bgcolor="white" link="red" vlink="red" alink="red" topmargin="20" marginheight="20">
<table width="400" border="0" cellpadding="0" cellspacing="0">
<tr align="center">
	<td>
		<table border="0" cellpadding="3">
		<tr>
			<td><b>���̃y�[�W�𒼐ډ{�����邱�Ƃ͂ł��܂���B</b></td>
		</tr>
		</table>
	</td>
</tr>
</table>
</body>
</html>
EOM
}

##########�l����舵��############
sub privacydata {
  if($doui ne '���ӂ���'){
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
              <a href="http://jp.globalsign.com/" target="_blank"><img alt="SSL�@�O���[�o���T�C���̃T�C�g�V�[��" border="0" id="ss_img" src="//seal.globalsign.com/SiteSeal/images/gs_noscript_115-57_ja.gif"></a>
            </span>
            <script type="text/javascript" src="//seal.globalsign.com/SiteSeal/gs_flash_115-57_ja.js"></script>
          </td>
        </tr>
      </tbody></table>
<table width="630" border="0" cellpadding="0" cellspacing="0" style="border:#ff6446 1px solid;">
<tr><td><img src="img/ber_reqest.gif" width="100%" height="5" border="0"></td></tr>
<tr><td><div class="title">�菑�����t�H�[��<span style="color:#C00;">�l���̎�舵���ɂ���</span>
</div></td></tr>
<tr><td height="10"></td></tr>
<tr>
	<td align="center">
		<!---PAGE COMMENT--->
		<table width="100%" border="0" cellpadding="0" cellspacing="0" style=" padding:0 20px;">
			<tr>
				<td><font class="t12">������́w�l���̎�舵���x�ɂ����ӂ��Ă��������Ȃ��ꍇ�͂��̃V�X�e�����炲�o�^�͂ł��܂���B</font></td>
			</tr>
			<tr>
				<td><font class="t12">�O�y�[�W�ɖ߂�A������̂��⍇�������ɂ��₢���킹���������B</font></td>
			</tr>
			<tr>
				<td><font class="t12">�@</font></td>
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
<p style="font-size:12px; text-align:center; padding-top:20px;">�菑�����b�H�����A�h�o�C�U�[</p>
</body>
</html>
EOM
        exit(0);
  }
  return 0;
}

##########�����G���[�ԐM�錾##########
sub error {

    print "Content-type: text/html\n\n";

    print <<EOM;


<!DOCtype HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=Shift_JIS">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="description" content="�菑�����b�H�����A�h�o�C�U�[����">
<meta name="keywords" content="�菑�����b�H�����A�h�o�C�U�[����">
<title>�菑�����b�H�����A�h�o�C�U�[����</title>
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
				<td align="right">		<span id="ss_img_wrapper_115-57_flash_ja"><a href="http://jp.globalsign.com/" target=_blank><img alt="SSL�@�O���[�o���T�C���̃T�C�g�V�[��" border=0 id="ss_img" src="//seal.globalsign.com/SiteSeal/images/gs_noscript_115-57_ja.gif"></a></span><script type="text/javascript" src="//seal.globalsign.com/SiteSeal/gs_flash_115-57_ja.js"></script></td>
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
<!--------------------���C���J�n------------------------>
		<table width="720" border="0" cellpadding="2" cellspacing="0">
		<tr><td><font class="t16"><b>�菑�����t�H�[��</b></font><font class="t16" color="red"><b>���̓G���[</b></font></td></tr>
		<tr><td></td></tr>
		<tr><td><font class="t12">���̓G���[������܂��B�w�߂�x�{�^���őO�y�[�W�ɖ߂�A�ē��͂��Ă��������B</font></td></tr>
		<tr><td></td></tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="5"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--�l-->
		<table border="0" cellpadding="5" cellspacing="0">
			<tr>
				<td><font class="t12">�@</font></td>
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
				<td><font class="t12">�@</font></td>
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
<tr><td align="center"><font class="t12">�菑�����b�H�����A�h�o�C�U�[����</font></td></tr>
</table>
</form>
</div>
</body>
</html>



EOM

}

##########�G���[�錾##########
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
        $checkshitsumon1 = "[�ߋ��Ɋ菑�������������Ƃ�����܂����H]���������I������Ă��܂���B";
        $checkkazu       = "1";
    }

    if ( $shitsumon2 eq "" ) {
        $checkshitsumon2 = "[�ߋ��Ɏ󌱂��������Ƃ�����܂����H]���������I������Ă��܂���B";
        $checkkazu       = "1";
    }

    if ( $name1 eq "" ) {
        $checkname = "[����]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }

    if ( $name2 eq "" ) {
        $checkname = "[����]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }

    if ( $kana1 eq "" ) {
        $checkkana = "[�t���K�i]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }

    if ( $kana2 eq "" ) {
        $checkkana = "[�t���K�i]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }

    if ( $birth_year eq "" ) {
        $checkbirth_year = "[���N����]�����������͂���Ă��܂���B";
        $checkkazu       = "1";
    }

    if ( $birth_month eq "" ) {
        $checkbirth_month = "[���N����]�����������͂���Ă��܂���B";
        $checkkazu        = "1";
    }

    if ( $birth_day eq "" ) {
        $checkbirth_day = "[���N����]�����������͂���Ă��܂���B";
        $checkkazu      = "1";
    }

    if ( $sei eq "" ) {
        $checksei  = "[����]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }

    if ( ( $add1 =~ /\D/g ) || ( $add1 eq "" ) ) {
        $checkadd  = "[�X�֔ԍ�]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }
    if ( ( $add2 =~ /\D/g ) || ( $add2 eq "" ) ) {
        $checkadd  = "[�X�֔ԍ�]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }

    # �ǉ�����
    my $zip_chk_flg;
    $zip_chk_flg = 1 if ( $add1 && length($add1) ne 3 );
    $zip_chk_flg = 1 if ( $add2 && length($add2) ne 4 );
    if ($zip_chk_flg) {
        $checktel  = "[�X�֔ԍ�]�̌���������������܂���B";
        $checkkazu = "1";
    }

    if ( $ken eq "" ) {
        $checkken  = "[�Z��]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }

    if ( $juusho1 eq "" ) {
        $checkken  = "[�Z��]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    } elsif (&juusho_check($juusho1) == 0) {
        $checkjuusho1 = "[�s�撬��]��15�����ȓ��œ��͂��Ă��������B";
        $checkkazu = "1";
    }

    if ( $juusho2 eq "" ) {
        $checkken  = "[�Z��]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    } elsif (&juusho_check($juusho2) == 0) {
        $checkjuusho2 = "[����Ԓn]��15�����ȓ��œ��͂��Ă��������B";
        $checkkazu = "1";
    }

    if (&juusho_check($juusho3) == 0) {
        $checkjuusho3 = "[�������E�l��]��15�����ȓ��œ��͂��Ă��������B";
        $checkkazu = "1";
    }

    #	if( $kaisha eq "" ){
    #		$checkkaisha	=	"[�Ζ��於]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}

    #	if( $bu eq "" ){
    #		$checkbu	=	"[������]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}

    if ( $telshu eq "" ) {
        $checktelshu = "[TEL�A����]�����������͂���Ă��܂���B";
        $checkkazu   = "1";
    }

    if ( ( $tel1 =~ /\D/g ) || ( $tel1 eq "" ) ) {
        $checktel  = "[�d�b�ԍ�]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }
    if ( ( $tel2 =~ /\D/g ) || ( $tel2 eq "" ) ) {
        $checktel  = "[�d�b�ԍ�]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }
    if ( ( $tel3 =~ /\D/g ) || ( $tel3 eq "" ) ) {
        $checktel  = "[�d�b�ԍ�]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }

    if ( $mail1 ne "" ) {
        if ( $mail1 =~ /^[\w\-\.]+$/ ) {
            $nodata1 = "yes";
        }
        else {
            $checkmail = "[���[���A�h���X]�����������͂���Ă��܂���B";
            $checkkazu = "1";
        }
    }
    if ( $mail2 ne "" ) {
        if ( $mail2 =~ /^[\w\-\.]+$/ ) {
            $nodata1 = "yes";
        }
        else {
            $checkmail = "[���[���A�h���X�����������͂���Ă��܂���B";
            $checkkazu = "1";
        }
    }

    #    if ( $mail3 ne "" ) {
    #        if ( $mail3 =~ /^[\w\-\.]+$/ ) {
    #            $nodata1 = "yes";
    #        }
    #        else {
    #            $checkmail = "[���[���A�h���X]�����������͂���Ă��܂���B";
    #            $checkkazu = "1";
    #        }
    #    }
    #    if ( $mail4 ne "" ) {
    #        if ( $mail4 =~ /^[\w\-\.]+$/ ) {
    #            $nodata1 = "yes";
    #        }
    #        else {
    #            $checkmail = "[���[���A�h���X]�����������͂���Ă��܂���B";
    #            $checkkazu = "1";
    #        }
    #    }

    if ( $mail1 ne "" ) {
        if ( $mail2 eq "" ) {
            $checkmail = "[���[���A�h���X]�����������͂���Ă��܂���B";
            $checkkazu = "1";
        }

        #        if ( $mail3 eq "" ) {
        #            $checkmail = "[���[���A�h���X]�����������͂���Ă��܂���B";
        #            $checkkazu = "1";
        #        }
        #        if ( $mail4 eq "" ) {
        #            $checkmail = "[���[���A�h���X]�����������͂���Ă��܂���B";
        #            $checkkazu = "1";
        #        }
    }
    if ( $mail2 ne "" ) {
        if ( $mail1 eq "" ) {
            $checkmail = "[���[���A�h���X]�����������͂���Ă��܂���B";
            $checkkazu = "1";
        }

        #        if ( $mail3 eq "" ) {
        #            $checkmail = "[���[���A�h���X]�����������͂���Ă��܂���B";
        #            $checkkazu = "1";
        #        }
        #        if ( $mail4 eq "" ) {
        #            $checkmail = "[���[���A�h���X]�����������͂���Ă��܂���B";
        #            $checkkazu = "1";
        #        }
    }

    #    if ( $mail3 ne "" ) {
    #        if ( $mail1 eq "" ) {
    #            $checkmail = "[���[���A�h���X(�m�F�p���܂�)]�����������͂���Ă��܂���B";
    #            $checkkazu = "1";
    #        }
    #        if ( $mail2 eq "" ) {
    #            $checkmail = "[���[���A�h���X(�m�F�p���܂�)]�����������͂���Ă��܂���B";
    #            $checkkazu = "1";
    #        }
    #        if ( $mail4 eq "" ) {
    #            $checkmail = "[���[���A�h���X(�m�F�p���܂�)]�����������͂���Ă��܂���B";
    #            $checkkazu = "1";
    #        }
    #    }
    #    if ( $mail4 ne "" ) {
    #        if ( $mail1 eq "" ) {
    #            $checkmail = "[���[���A�h���X(�m�F�p���܂�)]�����������͂���Ă��܂���B";
    #            $checkkazu = "1";
    #        }
    #        if ( $mail2 eq "" ) {
    #            $checkmail = "[���[���A�h���X(�m�F�p���܂�)]�����������͂���Ă��܂���B";
    #            $checkkazu = "1";
    #        }
    #        if ( $mail3 eq "" ) {
    #            $checkmail = "[���[���A�h���X(�m�F�p���܂�)]�����������͂���Ă��܂���B";
    #            $checkkazu = "1";
    #        }
    #    }

    #    if ( $mail1 ne $mail3 ) {
    #        $checkmail = "[���[���A�h���X(�m�F�p���܂�)]�����������͂���Ă��܂���B";
    #        $checkkazu = "1";
    #    }

    #    if ( $mail2 ne $mail4 ) {
    #        $checkmail = "[���[���A�h���X(�m�F�p���܂�)]�����������͂���Ă��܂���B";
    #        $checkkazu = "1";
    #    }

    if ( $checkkazu == 1 ) {
        &error;
        exit(0);
    }

}

##########�O���ďo##########
sub no_tag {
    local ($input_data) = @_;

    $input_data =~ s/&/&amp;/g;
    $input_data =~ s/"/&quot;/g;
    $input_data =~ s/</&lt;/g;
    $input_data =~ s/>/&gt;/g;
    $input_data =~ s/,/&#44;/g;

    $input_data =~ s/\r\n/\n/g;    #Mac�̉��s�R�[�hCR+LF��LF��
    $input_data =~ s/\r/\n/g;      #DOS�̉��s�R�[�hCR��LF��

    return $input_data;
}

##########�O���ďo##########
sub no_tag2 {
    local ($input_data) = @_;

    $input_data =~ s/\n/<BR>/g;                                       #���sLF��<BR>�^�O��
    $input_data =~ s/\n\n/<BR><BR>/g;                                 #���s2���<BR>�^�O�Q��̓���Ȍ`
    $input_data =~ s/\n\n\n/<BR><BR><BR>/g;
    $input_data =~ s/\n\n\n\n/<BR><BR><BR><BR>/g;
    $input_data =~ s/\n\n\n\n\n/<BR><BR><BR><BR><BR>/g;
    $input_data =~ s/\n\n\n\n\n\n/<BR><BR><BR><BR><BR><BR>/g;
    $input_data =~ s/\n\n\n\n\n\n\n/<BR><BR><BR><BR><BR><BR><BR>/g;

    return $input_data;
}

##########�O���ďo##########
sub no_tag3 {
    local ($input_data) = @_;

    $input_data =~ s/\r\n/\n/g;    #Mac�̉��s�R�[�hCR+LF��LF��
    $input_data =~ s/\n/ /g;       #DOS�̉��s�R�[�hCR��LF��

    return $input_data;
}
