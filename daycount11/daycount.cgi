#!/usr/bin/perl

#============#
#  �ݒ荀��  #
#============#

# �摜�A�����C�u�����捞��
require './gifcat.pl';

$img_file = "log.gif";

# ���J�E���g���̌���
$digit1 = 5;

# �{/����J�E���g���̌���
$digit2 = 3;

# �L�^�t�@�C��
$logfile = './daycount.dat';

# ���J�E���g�pGIF�摜�̃f�B���N�g��
#  --> �t���p�X���� / ����n��p�X
$gifdir1 = './gif1';

# �{/����J�E���g�pGIF�摜�̃f�B���N�g��
#  --> �t���p�X���� / ����n��p�X
$gifdir2 = './gif2';

# IP�A�h���X�̓�d�J�E���g�`�F�b�N
#   0 : �`�F�b�N���Ȃ�
#   1 : �`�F�b�N����
$ip_check = 0;

# �t�@�C�����b�N�`��
#  �� 0=no 1=symlink�֐� 2=mkdir�֐�
$lockkey = 1;

# ���b�N�t�@�C����
$lockfile = './lock/daycount.lock';

# �J�E���^�̋@�\�^�C�v
#   0 : ���J�E���g���s�v�i����^�{���̂݁j
#   1 : �W���^�C�v
$type = 1;

#============#
#  �ݒ芮��  #
#============#

# ����������
$mode = $ENV{'QUERY_STRING'};

# �`�F�b�N���[�h
if (!$mode || $mode eq 'check') { &check; }

# �X�V�n�����łȂ��Ȃ�΂Q�b�҂�����
if ($type == 1 && $mode ne "gif") { sleep(2); }
elsif ($type == 0 && $mode eq "yes") { sleep(2); }

# ���b�N�J�n
$lockflag=0;
if (($type == 1 && $mode eq "gif" && $lockkey) || ($type == 0 && $mode eq "today" && $lockkey)) { &lock; $lockflag=1; }

# �L�^�t�@�C������ǂݍ���
open(IN,"$logfile") || &error("LK");
$data = <IN>;
close(IN);

# �L�^�t�@�C���𕪉�
($key,$yes,$today,$count,$ip) = split(/<>/, $data);

# �������擾
$ENV{'TZ'} = "JST-9";
($sec,$min,$hour,$mday,$mon,$year) = localtime(time);

# IP�`�F�b�N
$flag=0;
if ($ip_check) {
	$addr = $ENV{'REMOTE_ADDR'};
	if ($addr eq "$ip") { $flag=1; }
}

# �{���̃J�E���g�����L�[�ɂ��ăJ�E���g�A�b�v
if ((!$flag && $type && $mode eq "gif") || (!$flag && !$type && $mode eq "today")) {

	if ($key eq "$mday") { $today++; }
	else {
		$yes   = $today;
		$today = 1;
	}

	# �J�E���g�A�b�v����
	$count++;

	# �L�^�t�@�C�����X�V����
	$data = "$mday<>$yes<>$today<>$count<>$addr<>";
	open(OUT,">$logfile") || &error("LK");
	print OUT $data;
	close(OUT);
}

# ���b�N����
if ($lockflag) { &unlock; }

# �摜�\��
&count_view;
exit;

#-------------------#
# �J�E���^�o�͏���  #
#-------------------#
sub count_view {

print "Content-type: image/gif\n\n";
open(IN,"$img_file");
print <IN>;
close(IN);

#	local($length, $fig, $n, $gifdir, @GIF);
#	if ($mode eq "gif") {
#		$fig = $digit1;
#		$gifdir = $gifdir1;
#	} else {
#		$fig = $digit2;
#		$gifdir = $gifdir2;
#		if ($mode eq "today") { $count = $today; }
#		else { $count = $yes; }
#	}

	# �\���摜��z��
#	while (length($count) < $fig) { $count = '0' . $count; }
#	$length = length($count);
#	@GIF=();
#	foreach (0 .. $length-1) {
#		$n = substr($count,$_,1);
#		push(@GIF, "$gifdir/$n\.gif");
#	}

	# �A���摜���o��
#	print "Content-type: image/gif\n\n";
#	binmode(STDOUT);
#	print &gifcat'gifcat(@GIF);
}

#--------------#
#  ���b�N����  #
#--------------#
sub lock {
	local($retry)=5;
	# 3���ȏ�Â����b�N�͍폜����
	if (-e $lockfile) {
		($mtime) = (stat($lockfile))[9];
		if ($mtime && $mtime < time - 180) { &unlock; }
	}
	# symlink�֐������b�N
	if ($lockkey == 1) {
		while (!symlink(".", $lockfile)) {
			if (--$retry <= 0) { &error; }
			sleep(1);
		}
	# mkdir�֐������b�N
	} elsif ($lockkey == 2) {
		while (!mkdir($lockfile, 0755)) {
			if (--$retry <= 0) { &error; }
			sleep(1);
		}
	}
}

#--------------#
#  ���b�N����  #
#--------------#
sub unlock {
	if ($lockkey == 1) { unlink($lockfile); }
	elsif ($lockkey == 2) { rmdir($lockfile); }
}

#--------------#
#  �G���[����  #
#--------------#
sub error {
	if ($lockflag && $_[0] eq "LK") { &unlock; }

	@err_gif = ('47','49','46','38','39','61','2d','00','0f','00','80','00','00','00','00','00','ff','ff','ff','2c', '00','00','00','00','2d','00','0f','00','00','02','49','8c','8f','a9','cb','ed','0f','a3','9c','34', '81','7b','03','ce','7a','23','7c','6c','00','c4','19','5c','76','8e','dd','ca','96','8c','9b','b6', '63','89','aa','ee','22','ca','3a','3d','db','6a','03','f3','74','40','ac','55','ee','11','dc','f9', '42','bd','22','f0','a7','34','2d','63','4e','9c','87','c7','93','fe','b2','95','ae','f7','0b','0e', '8b','c7','de','02','00','3b');

	print "Content-type: image/gif\n\n";
	foreach (@err_gif) {
		$data = pack('C*',hex($_));
		print $data;
	}
	exit;
}

#------------------#
#  �`�F�b�N���[�h  #
#------------------#
sub check {
	print "Content-type: text/html\n\n";
	print "<html><head><title>DAY COUNTER</title></head>\n";
	print "<body>\n<h2>Check Mode</h2>\n<UL>\n";

	# ���O�t�@�C���̃p�X�m�F
	if (-e $logfile) { print "<LI>���O�t�@�C���̃p�X : OK!\n"; }
	else { print "<LI>���O�t�@�C���̃p�X : NG �� $logfile\n"; }

	# ���O�t�@�C���̃p�[�~�b�V����
	if (-r $logfile && -w $logfile) {
		print "<LI>���O�t�@�C���̃p�[�~�b�V���� : OK!\n";
	} else {
		print "<LI>���O�t�@�C���̃p�[�~�b�V���� : NG\n";
	}

	# �摜�f�B���N�g���P�̃p�X�m�F
	if (-d $gifdir1) { print "<LI>gif1�f�B���N�g���̃p�X : OK!\n"; }
	else { print "<LI>gif1�f�B���N�g���̃p�X : NG �� $gifdir1\n"; }

	# �摜�f�B���N�g���Q�̃p�X�m�F
	if (-d $gifdir2) { print "<LI>gif2�f�B���N�g���̃p�X : OK!\n"; }
	else { print "<LI>gif2�f�B���N�g���̃p�X : NG �� $gifdir2\n"; }

	# �摜�`�F�b�N(1)
	$flag1=0;
	foreach (0 .. 9) {
		unless (-e "$gifdir1\/$_\.gif") {
			$flag1=1;
			print "<LI>�摜�����݂��܂��� �� $gifdir1\/$_\.gif";
		}
	}
	if (!$flag1) { print "<LI>gif1�f�B���N�g����GIF�摜 : OK!\n"; }

	# �摜�`�F�b�N(2)
	$flag2=0;
	foreach (0 .. 9) {
		unless (-e "$gifdir2\/$_\.gif") {
			$flag2=1;
			print "<LI>�摜�����݂��܂��� �� $gifdir2\/$_\.gif";
		}
	}
	if (!$flag2) { print "<LI>gif2�f�B���N�g����GIF�摜 : OK!\n"; }

	# ���b�N�f�B���N�g��
	print "<LI>���b�N�`���F";
	if ($lockkey == 0) { print "���b�N�ݒ�Ȃ�\n"; }
	else {
		if ($lockkey == 1) { print "symlink\n"; }
		else { print "mkdir\n"; }
		$lockfile =~ s/(.*)[\\\/].*$/$lockdir = $1/e;
		print "<LI>���b�N�f�B���N�g���F$lockdir\n";

		if (-d $lockdir) { print "<LI>���b�N�f�B���N�g���̃p�X�FOK\n"; }
		else { print "<LI>���b�N�f�B���N�g���̃p�X�FNG �� $lockdir\n"; }

		if (-r $lockdir && -w $lockdir && -x $lockdir) {
			print "<LI>���b�N�f�B���N�g���̃p�[�~�b�V�����FOK\n";
		} else {
			print "<LI>���b�N�f�B���N�g���̃p�[�~�b�V�����FNG �� $lockdir\n";
		}
	}

	exit;
}
