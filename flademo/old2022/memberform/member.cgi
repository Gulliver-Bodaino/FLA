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
my $script = 'https://ssl.flanet.jp/memberform/member.cgi';    # �{�ԗp
#my $script = 'member.cgi';

# �o��CSV
$csv_dir     = '../csvdata';                                    # �{�ԗp
$backup_dir  = '../csvdata/csvbackup';                          # �{�ԗp
$csv_data    = 'member.csv';                                    # �{�ԗp
$backup_data = 'backupmember.csv';                              # �{�ԗp

# ���[���A�h���X(�{�ԗp)
# $to1  = 'fla.entry@flanet.jp';    # [�{�ԗp] �Ǘ��҃A�h���X
# $from = 'fla.entry@flanet.jp';    # [�{�ԗp] from�p�Ǘ��҃A�h���X

# ���ؗp
$to1  = 'ishii-k@ww-system.com';                                # �Ǘ��҃A�h���X
$from = 'ishii-k@ww-system.com';                                # from�p�Ǘ��҃A�h���X


# ���[���^�C�g��
$subject  = '�l����o�^���̕ύX������܂����B';          # �Ǘ��җp
$subject2 = '�H�����A�h�o�C�U�[����@�o�^���ύX����';    # ���[�U�p

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
    my $is_exists = &fileinput;                               # CSV�o��
    # 2016/03/25 ����ǉ�
    # ���Ƀf�[�^�����݂���ꍇ�A���[�����M���������Ȃ��B
    if ( $is_exists == 1 ) {
        &mailmail;                                         # ���[���f�[�^�쐬
        &maildata1;                                        # �Ǘ��҂ւ̃��[�����M
        &maildata2;                                        # ���[�U�ւ̃��[�����M
    }
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
    # �ǉ�����
    # ----------------------------------------------
    # ��������啶����

    # ���{�l�m�F
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

    # ����
    $shinname1 = Unicode::Japanese->new( $shinname1, 'sjis' )->h2zKana->sjis;
    $shinname2 = Unicode::Japanese->new( $shinname2, 'sjis' )->h2zKana->sjis;
    $shinkana1 = Unicode::Japanese->new( $shinkana1, 'sjis' )->h2zKana->sjis;
    $shinkana2 = Unicode::Japanese->new( $shinkana2, 'sjis' )->h2zKana->sjis;

    # �Z���֘A
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

    $timenow     = "$year/$mon/$day";
    $yobinow     = "@tm[$wday]�j��";
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

    # CSV�f�[�^
    my $csv;
    my $csv_back;

    if ( !-e qq|$csv_dir/$csv_data| ) {
        $csv = &make_csv(    # *
            '�ύX��',
            '����',
            '�j��',
            '����',
            '�t���K�i',
            '�X�֔ԍ�',
            '�s���{��',
            '�s�撬��',
            '�Ԓn',
            '�������E�l��',
            '�Ζ��於',
            '������',
            '�d�b�ԍ�',
            '�o�^�ԍ�',
            '�ύX�J�e�S���[1 ����',
            '�ύX�J�e�S���[2 �Z��',
            '�ύX�J�e�S���[3 �d�b�ԍ�',
            '�ύX�J�e�S���[4 ���[���A�h���X',
            '�ύX��̎���',
            '�ύX��̃t���K�i',
            '�ύX��̗X�֔ԍ�',
            '�ύX��̓s���{��',
            '�ύX��̎s�撬��',
            '�ύX��̔Ԓn',
            '�ύX��̌������E�l��',
            '�ύX��̋Ζ��於',
            '�ύX��̕�����',
            '�ύX��̓d�b�ԍ���',
            '�ύX��̓d�b�ԍ�',
            '�ύX��̃��[���A�h���X',
            '�đ�',
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
            '�ύX��',
            '����',
            '�j��',
            '����',
            '�t���K�i',
            '�X�֔ԍ�',
            '�s���{��',
            '�s�撬��',
            '�Ԓn',
            '�������E�l��',
            '�Ζ��於',
            '������',
            '�d�b�ԍ�',
            '�o�^�ԍ�',
            '�ύX�J�e�S���[1 ����',
            '�ύX�J�e�S���[2 �Z��',
            '�ύX�J�e�S���[3 �d�b�ԍ�',
            '�ύX�J�e�S���[4 ���[���A�h���X',
            '�ύX��̎���',
            '�ύX��̃t���K�i',
            '�ύX��̗X�֔ԍ�',
            '�ύX��̓s���{��',
            '�ύX��̎s�撬��',
            '�ύX��̔Ԓn',
            '�ύX��̌������E�l��',
            '�ύX��̋Ζ��於',
            '�ύX��̕�����',
            '�ύX��̓d�b�ԍ���',
            '�ύX��̓d�b�ԍ�',
            '�ύX��̃��[���A�h���X',
            '�đ�',
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
    
    # 2016/03/25 �ǉ�
    # ��d���M�`�F�b�N�p�����񐶐�
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

    # 2016/03/25 �ǉ�
    # CSV�f�[�^���݃`�F�b�N
    # ��d���M�`�F�b�N�p�������CSV��������r
    open( IN, "$csv_dir/$csv_data" );
    my $is_exists = 0;
    while (<IN>) {
        
        # ���s�R�[�h�폜
        #s/[\r\n]*$//;
        chomp;
        
        # �J���}��؂蕶�����z��ɕ���
        @cols = split(/,/);
        
        # ��r�p�����񐶐�
        $chk_csv_str = "";
        for ($index = 3; $index <= 30; $index++) {
            $chk_csv_str = $chk_csv_str . $cols[$index];
        }
        
        # ��r���ăf�[�^�����݂���΁A�t���O�𗧂Ăă��[�v�𔲂���B
        if ( $chk_csv_str eq $chk_post_str ) {
            $is_exists = 1;
            last;
        }
    }
    close(IN);
    
    # �f�[�^�����݂���΁A0��Ԃ��B
    if ( $is_exists == 1 ) {
        return 0;
    }

    # CSV�ǋL
    my_open( OUT, ">>$csv_dir/$csv_data" );
    binmode OUT;
    print OUT $csv;
    my_close(OUT);

    # �o�b�N�A�b�vCSV�ǋL
    my_open( OUT, ">>$backup_dir/$backup_data" );
    binmode OUT;
    print OUT $csv_back;
    my_close(OUT);

    # 2016/03/25 �ǉ�
    # �f�[�^�����݂��Ȃ���΁A1��Ԃ��B
    return 1;
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
    $mailhantei = "$shinmail1$shinmail2";
    $mail       = "$shinmail1\@$shinmail2";

    # ��M�p
    $odata1  = "\n�E��t�����F$timenow$yobinow$hour��$min��";
    $odata2  = "\n\n�����{�l�m�F�̂��ߊ��o�^���";
    $odata3  = "\n�E�����F$name1 $name2";
    $odata4  = "\n�E�t���K�i�F$kana1 $kana2";
    $odata5  = "\n�E�X�֔ԍ��F$add1-$add2";
    $odata6  = "\n�E�Z���F\n$ken";
    $odata7  = "\n$juusho1";
    $odata8  = "\n$juusho2";
    $odata9  = "\n$juusho3";
    $odata10 = "\n�E�Ζ��於�F$kaisha";
    $odata11 = "\n�E�������F$bu";
    $odata12 = "\n�E�d�b�ԍ��F$tel1-$tel2-$tel3";
    $odata13 = "\n�E�o�^�ԍ��F$tbangou";
    $odata14 = "\n\n������1�F$shinnamecheck";
    $odata15 = "\n�E�����F$shinname1$shinname2";
    $odata16 = "\n�E�t���K�i�F$shinkana1$shinkana2";
    $odata17 = "\n\n������2�F$shinjuushocheck";
    $odata18 = "\n�E�X�֔ԍ��F$shinadd1-$shinadd2";
    $odata19 = "\n�E�Z���F\n$shinken";
    $odata20 = "\n$shinjuusho1";
    $odata21 = "\n$shinjuusho2";
    $odata22 = "\n$shinjuusho3";
    $odata23 = "\n�E�Ζ��於�F$shinkaisha";
    $odata24 = "\n�E�������F$shinbu";
    $odata25 = "\n\n������3�F$shintelcheck";
    $odata26 = "\n�E�A����F$shintelshu";
    $odata27 = "\n�E�d�b�ԍ��F$shintel1-$shintel2-$shintel3";
    $odata28 = "\n\n������4�F$shinmailcheck";
    $odata29 = "\n�EE-MAIL�F$shinmail1\@$shinmail2";

    # �ԐM�p
    $hdata1 = "$name1$name2�l\n\n";

#    $hdata2 = "�o�^���ύX�̂��o�^�A�L�������܂��B\n�o�^���e�͉��L�̒ʂ�ƂȂ�܂��B���m�F���肢���܂��B\n���A���̃��[���͑��M��p�A�h���X�ő��M���Ă���܂��B\n���̃��[���ɕԐM����Ă��A�ԐM���e�̊m�F����т��ԓ����ł��܂���̂ł����ӂ��������B\n";
    $hdata2
        = "�o�^���ύX�̂��A�������������܂��Ă��肪�Ƃ��������܂��B\n���₩�Ƀf�[�^�̏C���������Ă��������܂��B\n�菑�đ�������]�̕��́A�����܂ō����΂炭���҂����������B\n���A���̃��[���͑��M��p�A�h���X�Ŏ������M���Ă���܂��B\n���̃��[���ɕԐM����Ă��A�ԐM���e�̊m�F����т��ԓ����ł��܂���̂ł����ӂ��������B\n";
    $hdata3 = "��낵�����肢�������܂��B";
    $hdata4 = "\n----------------------------\n";
    $hdata5 = "��ʎВc�@�lFLA�l�b�g���[�N����\n�H�����A�h�o�C�U�[���莖����\nTEL:0120-86-3593";
    $hdata6 = "\n----------------------------\n";

    # �S���p
    #    $tdata1 = "$name1$name2�l����o�^���ύX��\�\\��������܂����B\n";
    $tdata1 = "WEB����o�^���ύX��\�\\��������܂����B";

}

##########���[���o�͌���?@�i�����̃��[���ցj##########
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

##########�m�F�y�[�W������������##########
sub outputshori {

    $newbikou = $bikou;
    $newbikou = &no_tag2($newbikou);

    if ( $shinnamecheck eq "" ) {
        $shinnamecheck = "�����ύX�Ȃ�";
    }
    if ( $shinjuushocheck eq "" ) {
        $shinjuushocheck = "�Z���֘A�ύX�Ȃ�";
    }
    if ( $shintelcheck eq "" ) {
        $shintelcheck = "�d�b�ԍ��֘A�ύX�Ȃ�";
    }
    if ( $shinmailcheck eq "" ) {
        $shinmailcheck = "���[���A�h���X�ύX�Ȃ�";
    }

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
<meta name="description" content="�o�^���ύX�b�H�����A�h�o�C�U�[����">
<meta name="keywords" content="�o�^���ύX�b�H�����A�h�o�C�U�[����">
<title>�o�^���ύX�b�H�����A�h�o�C�U�[����</title>
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
		<tr><td><font class="t16"><b>�o�^���ύX�t�H�[��</b></font><font class="t16" color="red"><b>���e�m�F�y�[�W</b></font></td></tr>
		<tr><td></td></tr>
		<tr><td><font class="t12">���e���C������ꍇ�̓y�[�W�����ɂ���߂�œ��̓y�[�W�ɖ߂�C�����Ă��������B</font></td></tr>
		<tr><td></td></tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>


<!--�l-->
		<table border="0" cellpadding="0" cellspacing="0" width="716">
		<tr>
			<td><font class="t14">�� <b>���{�l�m�F�̂��ߊ��o�^���������͂�������</b></font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#d2de84" width="120"><font class="t12">���� (��)</font></td>
			<td bgcolor="#eaf1bf" width="500"><font class="t12"><input type="hidden" name="name1" value="$name1">$name1�@<input type="hidden" name="name2" value="$name2">$name2</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">�t���K�i (��)</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="kana1" value="$kana1">$kana1�@<input type="hidden" name="kana2" value="$kana2">$kana2</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84" width="120"><font class="t12">�X�֔ԍ� (��)</font></td>
			<td bgcolor="#eaf1bf" width="500"><font class="t12">��<input type="hidden" name="add1" value="$add1">$add1-<input type="hidden" name="add2" value="$add2">$add2�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">�s���{�� (��)</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="ken" value="$ken">$ken�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">�s�撬�� (��)</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="juusho1" value="$juusho1">$juusho1�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">����Ԓn (��)</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="juusho2" value="$juusho2">$juusho2�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">�������E�l��</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="juusho3" value="$juusho3">$juusho3�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">�Ζ��於</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="kaisha" value="$kaisha">$kaisha�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">������</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="bu" value="$bu">$bu�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">�d�b�ԍ�</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="tel1" value="$tel1">$tel1-<input type="hidden" name="tel2" value="$tel2">$tel2-<input type="hidden" name="tel3" value="$tel3">$tel3�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#d2de84"><font class="t12">�o�^�ԍ�</font></td>
			<td bgcolor="#eaf1bf"><font class="t12"><input type="hidden" name="tbangou" value="$tbangou">$tbangou�@</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--�ύX����-->
		<table border="0" cellpadding="0" cellspacing="0" width="716">
		<tr>
			<td><font class="t14">�� <b>�ύX�̂��鍀�ڂ��`�F�b�N���ē��͂��Ă��������i�����j</b></font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fc907d" colspan="2"><font class="t12"><input type="hidden" name="shinnamecheck" value="$shinnamecheck">$shinnamecheck�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">����</font></td>
			<td bgcolor="#fde7e3" width="500"><font class="t12"><input type="hidden" name="shinname1" value="$shinname1">$shinname1�@<input type="hidden" name="shinname2" value="$shinname2">$shinname2</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">�t���K�i</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="shinkana1" value="$shinkana1">$shinkana1�@<input type="hidden" name="shinkana2" value="$shinkana2">$shinkana2</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--�Z��-->
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fc907d" colspan="2"><font class="t12"><input type="hidden" name="shinjuushocheck" value="$shinjuushocheck">$shinjuushocheck�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">�X�֔ԍ�</font></td>
			<td bgcolor="#fde7e3" width="500">
<font class="t12">��<input type="hidden" name="shinadd1" value="$shinadd1">$shinadd1-<input type="hidden" name="shinadd2" value="$shinadd2">$shinadd2�@</font>
			</td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">�s���{��</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="shinken" value="$shinken">$shinken�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">�s�撬��</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="shinjuusho1" value="$shinjuusho1">$shinjuusho1�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">����Ԓn</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="shinjuusho2" value="$shinjuusho2">$shinjuusho2�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">�������E�l��</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="shinjuusho3" value="$shinjuusho3">$shinjuusho3�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5" colspan="2"><font class="t12"><b>��Ј��ɂ����t��]�̕��͋Ζ���E�������̂����͂��Y�ꂸ�ɂ��肢���܂��B</b></font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">�Ζ��於</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="shinkaisha" value="$shinkaisha">$shinkaisha�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">������</font></td>
			<td bgcolor="#fde7e3"><font class="t12"><input type="hidden" name="shinbu" value="$shinbu">$shinbu�@</font></td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--�s�d�k-->
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fc907d" colspan="2"><font class="t12"><input type="hidden" name="shintelcheck" value="$shintelcheck">$shintelcheck�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">�A����</font></td>
			<td bgcolor="#fde7e3" width="500"><font class="t12"><input type="hidden" name="shintelshu" value="$shintelshu">$shintelshu�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5"><font class="t12">�d�b�ԍ�</font></td>
			<td bgcolor="#fde7e3">
<font class="t12"><input type="hidden" name="shintel1" value="$shintel1">$shintel1-<input type="hidden" name="shintel2" value="$shintel2">$shintel2-<input type="hidden" name="shintel3" value="$shintel3">$shintel3�@</font>
			</td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" summary="top">
		<tr>
			<td height="20"><img src="img/s.gif" border="0" width="1" height="1"></td>
		</tr>
		</table>
<!--���[��-->
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fc907d" colspan="2"><font class="t12"><input type="hidden" name="shinmailcheck" value="$shinmailcheck">$shinmailcheck�@</font></td>
		</tr>
		<tr>
			<td bgcolor="#fdc0b5" width="120"><font class="t12">���[���A�h���X</font></td>
			<td bgcolor="#fde7e3" width="500"><font class="t12"><input type="hidden" name="shinmail1" value="$shinmail1">$shinmail1\@<input type="hidden" name="shinmail2" value="$shinmail2">$shinmail2�@</font></td>
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
			<td><font class="t14">�� <b>������</b></font></td>
		</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="2" width="720">
		<tr>
			<td bgcolor="#fdc0b5" width="350"><font class="t12">�ύX�������e�Ɋ菑�̍đ�����]���܂����H (��)</font></td>
			<td bgcolor="#fde7e3" width="370"><font class="t12"><input type="hidden" name="saisou" value="$saisou">$saisou�@</font></td>
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
			<td align="center" width="150"><input type="image" src="img/send.gif" width="93" height="21" border="0"  id="submitButton"></td>
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
<tr><td align="center"><font class="t12">�o�^���ύX�b�H�����A�h�o�C�U�[����</font></td></tr>
</table>
</form>
</div>

<!-- 2016/03/25 �ǉ� -->
<script type="text/javascript">
function disableSubmit() {
    document.getElementById("submitButton").disabled = true;
}
</script>

</body>
</html>

EOM
}

##########�����ԐM�錾##########
# 2016/03/25 �ǉ�
sub success {
    print "Location: success.html\n\n";
}

# 2016/03/25
# �����[�h�΍�̂��߁A�ȉ���HTML�o�͂�success.html�ֈړ����A
# &success��&success_old�֕ύX
sub success_old {
    print "Content-type: text/html\n\n";
    print <<EOM;
<!DOCtype HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=Shift_JIS">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="description" content="�o�^���ύX�b�H�����A�h�o�C�U�[����">
<meta name="keywords" content="�o�^���ύX�b�H�����A�h�o�C�U�[����">
<title>�o�^���ύX�b�H�����A�h�o�C�U�[����</title>
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
		<tr><td><font class="t16"><b>�o�^���ύX�t�H�[��</b></font><font class="t16" color="red"><b>�o�^����</b></font></td></tr>
		<tr><td height="30"></td></tr>
		<tr><td><font class="t12">�o�^���̕ύX����t�܂����B���o�^���肪�Ƃ��������܂��B<br>
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
<tr><td align="center"><font class="t12">�o�^���ύX�b�H�����A�h�o�C�U�[����</font></td></tr>
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
<tr><td><div class="title">�o�^���ύX�t�H�[���l���̎�舵���ɂ���
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
<p style="font-size:12px; text-align:center; padding-top:20px;">�o�^���ύX�b�H�����A�h�o�C�U�[</p>
</div>
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
<meta name="description" content="�o�^���ύX�b�H�����A�h�o�C�U�[����">
<meta name="keywords" content="�o�^���ύX�b�H�����A�h�o�C�U�[����">
<title>�o�^���ύX�b�H�����A�h�o�C�U�[����</title>
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
<tr><td align="center"><font class="t12">�o�^���ύX�b�H�����A�h�o�C�U�[����</font></td></tr>
</table>
</form>
</div>
</body>
</html>



EOM

}

##########�G���[�錾##########
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
        $checkname = "[���{�l�m�F���ڂ̎���]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }

    if ( $name2 eq "" ) {
        $checkname = "[���{�l�m�F���ڂ̎���]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }

    if ( $kana1 eq "" ) {
        $checkkana = "[���{�l�m�F���ڂ̃t���K�i]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }

    if ( $kana2 eq "" ) {
        $checkkana = "[���{�l�m�F���ڂ̃t���K�i]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }

    if ( ( $add1 =~ /\D/g ) || ( $add1 eq "" ) ) {
        $checkadd  = "[���{�l�m�F���ڂ̗X�֔ԍ�]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }
    if ( ( $add2 =~ /\D/g ) || ( $add2 eq "" ) ) {
        $checkadd  = "[���{�l�m�F���ڂ̗X�֔ԍ�]�����������͂���Ă��܂���B";
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
        $checkken  = "[���{�l�m�F���ڂ̏Z��]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }

    if ( $juusho1 eq "" ) {
        $checkken  = "[���{�l�m�F���ڂ̏Z��]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    } elsif (&juusho_check($juusho1) == 0) {
        $checkjuusho1 = "[���{�l�m�F���ڂ̎s�撬��]��15�����ȓ��œ��͂��Ă��������B";
        $checkkazu = "1";
    }

    if ( $juusho2 eq "" ) {
        $checkken  = "[���{�l�m�F���ڂ̏Z��]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    } elsif (&juusho_check($juusho2) == 0) {
        $checkjuusho2 = "[���{�l�m�F���ڂ̒���Ԓn]��15�����ȓ��œ��͂��Ă��������B";
        $checkkazu = "1";
    }

    if (&juusho_check($juusho3) == 0) {
        $checkjuusho3 = "[���{�l�m�F���ڂ̌������E�l��]��15�����ȓ��œ��͂��Ă��������B";
        $checkkazu = "1";
    }

    if ( $tel1 =~ /\D/g ) {
        $checktel  = "[���{�l�m�F���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }
    if ( $tel2 =~ /\D/g ) {
        $checktel  = "[���{�l�m�F���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }
    if ( $tel3 =~ /\D/g ) {
        $checktel  = "[���{�l�m�F���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
        $checkkazu = "1";
    }

    if ( $tel1 ne "" ) {
        if ( $tel2 eq "" ) {
            $checktel  = "[���{�l�m�F���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
            $checkkazu = "1";
        }
        if ( $tel3 eq "" ) {
            $checktel  = "[���{�l�m�F���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
            $checkkazu = "1";
        }
    }
    if ( $tel2 ne "" ) {
        if ( $tel1 eq "" ) {
            $checktel  = "[���{�l�m�F���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
            $checkkazu = "1";
        }
        if ( $tel3 eq "" ) {
            $checktel  = "[���{�l�m�F���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
            $checkkazu = "1";
        }
    }
    if ( $tel3 ne "" ) {
        if ( $tel1 eq "" ) {
            $checktel  = "[���{�l�m�F���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
            $checkkazu = "1";
        }
        if ( $tel2 eq "" ) {
            $checktel  = "[���{�l�m�F���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
            $checkkazu = "1";
        }
    }
    if ( $tbangou ne "" ) {
        if ( $tbangou =~ /^[\w\-\.]+$/ ) {
            $nodata1 = "yes";
        }
        else {
            $checktbangou = "[���{�l�m�F���ڂ̓o�^�ԍ�]�����������͂���Ă��܂���B";
            $checkkazu    = "1";
        }
    }

    if ( $shinnamecheck ne "" ) {
        if ( ( $shinname1 eq "" ) && ( $shinname2 eq "" ) && ( $shinkana1 eq "" ) && ( $shinkana2 eq "" ) ) {
            $checkshinnamecheck = "[�����ύX]���`�F�b�N����Ă��܂������������͂���Ă��܂���B";
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
            $checkshinjuushocheck = "[�Z���֘A�ύX]���`�F�b�N����Ă��܂������������͂���Ă��܂���B";
            $checkkazu            = "1";
        }

        if (&juusho_check($shinjuusho1) == 0) {
            $checkshinjuusho1 = "[�Z���֘A�̎s�撬��]��15�����ȓ��œ��͂��Ă��������B";
            $checkkazu = "1";
        }
        if (&juusho_check($shinjuusho2) == 0) {
            $checkshinjuusho2 = "[�Z���֘A�̒���Ԓn]��15�����ȓ��œ��͂��Ă��������B";
            $checkkazu = "1";
        }
        if (&juusho_check($shinjuusho3) == 0) {
            $checkshinjuusho3 = "[�Z���֘A�̌������E�l��]��15�����ȓ��œ��͂��Ă��������B";
            $checkkazu = "1";
        }
    }
    if ( $shinadd1 =~ /\D/g ) {
        $checkshinadd = "[�Z���֘A�ύX���ڂ̗X�֔ԍ�]�����������͂���Ă��܂���B";
        $checkkazu    = "1";
    }
    if ( $shinadd2 =~ /\D/g ) {
        $checkshinadd = "[�Z���֘A�ύX���ڂ̗X�֔ԍ�]�����������͂���Ă��܂���B";
        $checkkazu    = "1";
    }
    if ( $shinadd1 ne "" ) {
        if ( $shinadd2 eq "" ) {
            $checkshinadd = "[�Z���֘A�ύX���ڂ̗X�֔ԍ�]�����������͂���Ă��܂���B";
            $checkkazu    = "1";
        }
    }
    if ( $shinadd2 ne "" ) {
        if ( $shinadd1 eq "" ) {
            $checkshinadd = "[�Z���֘A�ύX���ڂ̗X�֔ԍ�]�����������͂���Ă��܂���B";
            $checkkazu    = "1";
        }
    }

    # �ǉ�����
    my $shinadd_chk_flg;
    $shinadd_chk_flg = 1 if ( $shinadd1 && length($shinadd1) ne 3 );
    $shinadd_chk_flg = 1 if ( $shinadd2 && length($shinadd2) ne 4 );
    if ($shinadd_chk_flg) {
        $checktel  = "[�Z���֘A�ύX���ڂ̗X�֔ԍ�]�̌���������������܂���B";
        $checkkazu = "1";
    }

    if ( $shintelcheck ne "" ) {
        if ( ( $shintelshu eq "" ) && ( $shintel1 eq "" ) && ( $shintel2 eq "" ) && ( $shintel3 eq "" ) ) {
            $checkshintelcheck = "[�d�b�ԍ��֘A�ύX]���`�F�b�N����Ă��܂������������͂���Ă��܂���B";
            $checkkazu         = "1";
        }
    }
    if ( $shintel1 =~ /\D/g ) {
        $checkshintel = "[�d�b�ԍ��֘A�ύX���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
        $checkkazu    = "1";
    }
    if ( $shintel2 =~ /\D/g ) {
        $checkshintel = "[�d�b�ԍ��֘A�ύX���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
        $checkkazu    = "1";
    }
    if ( $shintel3 =~ /\D/g ) {
        $checkshintel = "[�d�b�ԍ��֘A�ύX���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
        $checkkazu    = "1";
    }

    if ( $shintel1 ne "" ) {
        if ( $shintel2 eq "" ) {
            $checkshintel = "[�d�b�ԍ��֘A�ύX���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
            $checkkazu    = "1";
        }
        if ( $shintel3 eq "" ) {
            $checkshintel = "[�d�b�ԍ��֘A�ύX���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
            $checkkazu    = "1";
        }
    }
    if ( $shintel2 ne "" ) {
        if ( $shintel1 eq "" ) {
            $checkshintel = "[�d�b�ԍ��֘A�ύX���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
            $checkkazu    = "1";
        }
        if ( $shintel3 eq "" ) {
            $checkshintel = "[�d�b�ԍ��֘A�ύX���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
            $checkkazu    = "1";
        }
    }
    if ( $shintel3 ne "" ) {
        if ( $shintel1 eq "" ) {
            $checkshintel = "[�d�b�ԍ��֘A�ύX���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
            $checkkazu    = "1";
        }
        if ( $shintel2 eq "" ) {
            $checkshintel = "[�d�b�ԍ��֘A�ύX���ڂ̓d�b�ԍ�]�����������͂���Ă��܂���B";
            $checkkazu    = "1";
        }
    }

    if ( $shinmailcheck ne "" ) {
        if ( ( $shinmail1 eq "" ) && ( $shinmail2 eq "" ) ) {
            $checkshinmailcheck = "[���[���A�h���X�ύX]���`�F�b�N����Ă��܂������������͂���Ă��܂���B";
            $checkkazu          = "1";
        }
    }

    if ( $shinmail1 ne "" ) {
        if ( $shinmail1 =~ /^[\w\-\.]+$/ ) {
            $nodata1 = "yes";
        }
        else {
            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
            $checkkazu     = "1";
        }
    }
    if ( $shinmail2 ne "" ) {
        if ( $shinmail2 =~ /^[\w\-\.]+$/ ) {
            $nodata1 = "yes";
        }
        else {
            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
            $checkkazu     = "1";
        }
    }

    #    if ( $shinmail3 ne "" ) {
    #        if ( $shinmail3 =~ /^[\w\-\.]+$/ ) {
    #            $nodata1 = "yes";
    #        }
    #        else {
    #            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
    #            $checkkazu     = "1";
    #        }
    #    }
    #    if ( $shinmail4 ne "" ) {
    #        if ( $shinmail4 =~ /^[\w\-\.]+$/ ) {
    #            $nodata1 = "yes";
    #        }
    #        else {
    #            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
    #            $checkkazu     = "1";
    #        }
    #    }

    if ( $shinmail1 ne "" ) {
        if ( $shinmail2 eq "" ) {
            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
            $checkkazu     = "1";
        }

        #        if ( $shinmail3 eq "" ) {
        #            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
        #            $checkkazu     = "1";
        #        }
        #        if ( $shinmail4 eq "" ) {
        #            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
        #            $checkkazu     = "1";
        #        }
    }
    if ( $shinmail2 ne "" ) {
        if ( $shinmail1 eq "" ) {
            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
            $checkkazu     = "1";
        }

        #        if ( $shinmail3 eq "" ) {
        #            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
        #            $checkkazu     = "1";
        #        }
        #        if ( $shinmail4 eq "" ) {
        #            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
        #            $checkkazu     = "1";
        #       }
    }

    #    if ( $shinmail3 ne "" ) {
    #        if ( $shinmail1 eq "" ) {
    #            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
    #            $checkkazu     = "1";
    #        }
    #        if ( $shinmail2 eq "" ) {
    #            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
    #            $checkkazu     = "1";
    #        }
    #        if ( $shinmail4 eq "" ) {
    #            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
    #            $checkkazu     = "1";
    #        }
    #    }
    #    if ( $shinmail4 ne "" ) {
    #        if ( $shinmail1 eq "" ) {
    #            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
    #            $checkkazu     = "1";
    #        }
    #        if ( $shinmail2 eq "" ) {
    #            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
    #            $checkkazu     = "1";
    #        }
    #        if ( $shinmail3 eq "" ) {
    #            $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
    #            $checkkazu     = "1";
    #        }
    #    }

    #    if ( $shinmail1 ne $shinmail3 ) {
    #        $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
    #        $checkkazu     = "1";
    #    }

    #    if ( $shinmail2 ne $shinmail4 ) {
    #        $checkshinmail = "[���[���A�h���X�ύX���ڂ̃��[���A�h���X]�����������͂���Ă��܂���B";
    #        $checkkazu     = "1";
    #    }

    #	if( $mail1 =~ /^[\w\-\.]+$/){
    #		$nodata1="yes";
    #	}
    #	else{
    #		$checkmail		=	"[���[���A�h���X]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}

    #
    #	if(( $tel1 eq "" )&&( $tel2 eq "" )&&( $tel3 eq "" )){
    #		$checksp		=	"[����]�̎���]���������I������Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}
    #	if( $telshu eq "" ){
    #		$checktelshu		=	"[TEL�A����]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}
    #
    #	if(( $tel1  =~ /\D/g )||( $tel1 eq "" )){
    #		$checktel		=	"[�d�b�ԍ�]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}
    #	if(( $tel2  =~ /\D/g )||( $tel2 eq "" )){
    #		$checktel		=	"[�d�b�ԍ�]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}
    #	if(( $tel3  =~ /\D/g )||( $tel3 eq "" )){
    #		$checktel		=	"[�d�b�ԍ�]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}

    #	if( $mail1 eq "" ){
    #		$checkmail		=	"[���[���A�h���X]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}
    #	if( $mail1 =~ /^[\w\-\.]+$/){
    #		$nodata1="yes";
    #	}
    #	else{
    #		$checkmail		=	"[���[���A�h���X]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}
    #
    #	if( $mail2 eq "" ){
    #		$checkmail		=	"[���[���A�h���X]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}
    #	if( $mail2 =~ /^[\w\-\.]+$/){
    #		$nodata2="no";
    #	}
    #	else{
    #		$checkmail		=	"[���[���A�h���X]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}
    #
    #	if( $mail3 eq "" ){
    #		$checkmailk		=	"[�m�F�p���[���A�h���X]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}
    #	if( $mail3 =~ /^[\w\-\.]+$/){
    #		$nodata1="yes";
    #	}
    #	else{
    #		$checkmailk		=	"[�m�F�p���[���A�h���X]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}
    #
    #	if( $mail4 eq "" ){
    #		$checkmailk		=	"[�m�F�p���[���A�h���X]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}
    #	if( $mail4 =~ /^[\w\-\.]+$/){
    #		$nodata2="no";
    #	}
    #	else{
    #		$checkmailk		=	"[�m�F�p���[���A�h���X]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}
    #
    #	if( $mail1 ne $mail3 ){
    #		$checkmailkakunin		=	"[���[���A�h���X�Ɗm�F�̂��ߍē��͂������[���A�h���X]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}
    #
    #	if( $mail2 ne $mail4 ){
    #		$checkmailkakunin		=	"[���[���A�h���X�Ɗm�F�̂��ߍē��͂������[���A�h���X]�����������͂���Ă��܂���B";
    #		$checkkazu	=	"1";
    #	}

    if ( $saisou eq "" ) {
        $checksaisou = "[������]���������I������Ă��܂���B";
        $checkkazu   = "1";
    }

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
