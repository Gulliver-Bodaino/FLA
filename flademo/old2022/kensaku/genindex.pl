#!/usr/local/bin/perl
#=============================================================================#
#                                                                             #
#                        msearch - mat's search program                       #
#                                version 1.52                                 #
#                                                                             #
#                  Ķ�ʰץ���ǥå����Ǹ������󥸥�ץ����                 #
#        Copyright (C) 2000-2005, Katsushi Matsuda. All Right Reserved.       #
#                                                                             #
#=============================================================================#

#=============================================================================#
#                         ����ǥå��������ץ����                          #
#=============================================================================#

require './jcode.pl';		# �����������Ѵ��ѥå�����
require './indexing.pl';        # ����ǥå��������⥸�塼��

####################
### �ѹ���ǽ�ѿ� ###
####################

### �ǥХå����ˤΤ����ꤷ�Ʋ�������
### 0 - �ǥХå�������Ϥʤ�
### 1 - ���Ϥ���
$debug = 0;


##################
### �ᥤ����� ###
##################

### ɽ��
print "genindex.pl -- index generator for msearch\n";
print "Copyright (c) 2000-2005 Katsushi Matsuda. All Right Reserved.\n";
print "Display kanji code is ";

### ���ϴ��������ɤη���
if($ARGV[0] =~ /-s/i || $ARGV[0] =~ /-sjis/i) {
    $kanji = "SJIS";
    print "SJIS";
} elsif($ARGV[0] =~ /-j/i || $ARGV[0] =~ /-jis/i) {
    $kanji = "JIS";
    print "JIS";
} else {
    $kanji = "EUC";
    print "EUC";
}
print "\n\n";

### �����μ����ȥѡ�����
printout("����ǥå�����̾���ϡ�\n> ");
$qarg->{'index'} = <STDIN>;
chomp($qarg->{'index'});
$qarg->{'index'} =~ s/ //g;

printout("����ǥå����оݥǥ��쥯�ȥ�ϡ�(ɬ��)\n> ");
$qarg->{'includedir'} = <STDIN>;
chomp($qarg->{'includedir'});
$qarg->{'includedir'} =~ s/ //g;

printout("����ǥå����оݥǥ��쥯�ȥ��URL�ϡ�(ɬ��)\n> ");
$qarg->{'includeurl'} = <STDIN>;
chomp($qarg->{'includeurl'});
$qarg->{'includeurl'} =~ s/ //g;

printout("����ǥå����оݥե�����γ�ĥ�Ҥϡ�(ɬ��)\n> ");
$qarg->{'suffix'} = <STDIN>;
chomp($qarg->{'suffix'});
$qarg->{'suffix'} =~ s/ //g;

printout("�󥤥�ǥå����оݥǥ��쥯�ȥ�ϡ�\n> ");
$qarg->{'excludedir'} = <STDIN>;
chomp($qarg->{'excludedir'});
$qarg->{'excludedir'} =~ s/ //g;

printout("�󥤥�ǥå����оݥե�����ϡ�\n> ");
$qarg->{'excludefile'} = <STDIN>;
chomp($qarg->{'excludefile'});
$qarg->{'excludedir'} =~ s/ //g;

printout("�󥤥�ǥå����оݥ�����ɤϡ�\n> ");
my $tmpstr = <STDIN>;
if($kanji eq "SJIS") {
    &jcode::convert(\$tmpstr,"euc");
}
$qarg->{'excludekey'} = $tmpstr;
chomp($qarg->{'excludekey'});
$qarg->{'excludedir'} =~ s/ //g;

printout("��󥭥���ˡ�ϡ�\n");
printout("[1] �ǽ���������-�߽�\n");
printout("[2] �ǽ���������-����\n");
printout("[3] �����ȥ�-�߽�\n");
printout("[4] �����ȥ�-����\n");
printout("[5] URL-�߽�\n");
printout("[6] URL-����\n");
printout("[0] �ʤ�\n");
printout("�ɤ�ˤ��ޤ�����[0��6] > ");
$tmp = <STDIN>;
chomp($tmp);
if($tmp == 1) {
    $qarg->{'sort'} = "MODIFY-DESC";
} elsif($tmp == 2) {
    $qarg->{'sort'} = "MODIFY-ASC";
} elsif($tmp == 3) {
    $qarg->{'sort'} = "TITLE-DESC";
} elsif($tmp == 4) {
    $qarg->{'sort'} = "TITLE-ASC";
} elsif($tmp == 5) {
    $qarg->{'sort'} = "URL-DESC";
} elsif($tmp == 6) {
    $qarg->{'sort'} = "URL-ASC";
} else {
    $qarg->{'sort'} = "NONE";
}

printout("alt°����ʸ���򥤥�ǥå����˴ޤ�ޤ�����\n");
printout("[1] �ޤ��\n");
printout("[0] �ޤ�ʤ�\n");
printout("�ɤ�ˤ��ޤ�����[0��1] > ");
$tmp = <STDIN>;
chomp($tmp);
if($tmp == 1) {
    $qarg->{'rescuealt'} = "1";
}

if(argtest() <= 0) {
    printout("���Ϥ��ѤǤ���\n");
    exit;
}

### ����ǥå�������
makeindex();

### ��λ
exit;

###############################
### genindex.pl�˸�ͭ�δؿ� ###
###############################

###
### �����Υƥ���
###
sub argtest {
    return(-1) if($qarg->{'includedir'} eq "");
    return(-2) if($qarg->{'includeurl'} eq "");
    return(-3) if($qarg->{'suffix'} eq "");

    return(1);
}

###
### ���ϴؿ�
###
sub printout {
    my $str = $_[0];		# ʸ����(����)

    if($kanji eq "EUC") {
	# EUC�ξ��
	print $str;
    } elsif($kanji eq "JIS") {
	# JIS�ξ��
	&jcode::convert(\$str,"jis");
	print $str;
    } else {
	# SJIS�ξ��
	&jcode::convert(\$str,"sjis");
	print $str;
    }
}

