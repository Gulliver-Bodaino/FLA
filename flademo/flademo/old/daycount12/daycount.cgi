#!/usr/bin/perl

#============#
#  設定項目  #
#============#

# 画像連結ライブラリ取込み
require './gifcat.pl';

$img_file = "log.gif";

# 総カウント数の桁数
$digit1 = 5;

# 本/昨日カウント数の桁数
$digit2 = 3;

# 記録ファイル
$logfile = './daycount.dat';

# 総カウント用GIF画像のディレクトリ
#  --> フルパスだと / から始るパス
$gifdir1 = './gif1';

# 本/昨日カウント用GIF画像のディレクトリ
#  --> フルパスだと / から始るパス
$gifdir2 = './gif2';

# IPアドレスの二重カウントチェック
#   0 : チェックしない
#   1 : チェックする
$ip_check = 0;

# ファイルロック形式
#  → 0=no 1=symlink関数 2=mkdir関数
$lockkey = 1;

# ロックファイル名
$lockfile = './lock/daycount.lock';

# カウンタの機能タイプ
#   0 : 総カウント数不要（昨日／本日のみ）
#   1 : 標準タイプ
$type = 1;

#============#
#  設定完了  #
#============#

# 引数を解釈
$mode = $ENV{'QUERY_STRING'};

# チェックモード
if (!$mode || $mode eq 'check') { &check; }

# 更新系処理でないならば２秒待たせる
if ($type == 1 && $mode ne "gif") { sleep(2); }
elsif ($type == 0 && $mode eq "yes") { sleep(2); }

# ロック開始
$lockflag=0;
if (($type == 1 && $mode eq "gif" && $lockkey) || ($type == 0 && $mode eq "today" && $lockkey)) { &lock; $lockflag=1; }

# 記録ファイルから読み込み
open(IN,"$logfile") || &error("LK");
$data = <IN>;
close(IN);

# 記録ファイルを分解
($key,$yes,$today,$count,$ip) = split(/<>/, $data);

# 日時を取得
$ENV{'TZ'} = "JST-9";
($sec,$min,$hour,$mday,$mon,$year) = localtime(time);

# IPチェック
$flag=0;
if ($ip_check) {
	$addr = $ENV{'REMOTE_ADDR'};
	if ($addr eq "$ip") { $flag=1; }
}

# 本日のカウント数をキーにしてカウントアップ
if ((!$flag && $type && $mode eq "gif") || (!$flag && !$type && $mode eq "today")) {

	if ($key eq "$mday") { $today++; }
	else {
		$yes   = $today;
		$today = 1;
	}

	# カウントアップ処理
	$count++;

	# 記録ファイルを更新する
	$data = "$mday<>$yes<>$today<>$count<>$addr<>";
	open(OUT,">$logfile") || &error("LK");
	print OUT $data;
	close(OUT);
}

# ロック解除
if ($lockflag) { &unlock; }

# 画像表示
&count_view;
exit;

#-------------------#
# カウンタ出力処理  #
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

	# 表示画像を配列化
#	while (length($count) < $fig) { $count = '0' . $count; }
#	$length = length($count);
#	@GIF=();
#	foreach (0 .. $length-1) {
#		$n = substr($count,$_,1);
#		push(@GIF, "$gifdir/$n\.gif");
#	}

	# 連結画像を出力
#	print "Content-type: image/gif\n\n";
#	binmode(STDOUT);
#	print &gifcat'gifcat(@GIF);
}

#--------------#
#  ロック処理  #
#--------------#
sub lock {
	local($retry)=5;
	# 3分以上古いロックは削除する
	if (-e $lockfile) {
		($mtime) = (stat($lockfile))[9];
		if ($mtime && $mtime < time - 180) { &unlock; }
	}
	# symlink関数式ロック
	if ($lockkey == 1) {
		while (!symlink(".", $lockfile)) {
			if (--$retry <= 0) { &error; }
			sleep(1);
		}
	# mkdir関数式ロック
	} elsif ($lockkey == 2) {
		while (!mkdir($lockfile, 0755)) {
			if (--$retry <= 0) { &error; }
			sleep(1);
		}
	}
}

#--------------#
#  ロック解除  #
#--------------#
sub unlock {
	if ($lockkey == 1) { unlink($lockfile); }
	elsif ($lockkey == 2) { rmdir($lockfile); }
}

#--------------#
#  エラー処理  #
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
#  チェックモード  #
#------------------#
sub check {
	print "Content-type: text/html\n\n";
	print "<html><head><title>DAY COUNTER</title></head>\n";
	print "<body>\n<h2>Check Mode</h2>\n<UL>\n";

	# ログファイルのパス確認
	if (-e $logfile) { print "<LI>ログファイルのパス : OK!\n"; }
	else { print "<LI>ログファイルのパス : NG → $logfile\n"; }

	# ログファイルのパーミッション
	if (-r $logfile && -w $logfile) {
		print "<LI>ログファイルのパーミッション : OK!\n";
	} else {
		print "<LI>ログファイルのパーミッション : NG\n";
	}

	# 画像ディレクトリ１のパス確認
	if (-d $gifdir1) { print "<LI>gif1ディレクトリのパス : OK!\n"; }
	else { print "<LI>gif1ディレクトリのパス : NG → $gifdir1\n"; }

	# 画像ディレクトリ２のパス確認
	if (-d $gifdir2) { print "<LI>gif2ディレクトリのパス : OK!\n"; }
	else { print "<LI>gif2ディレクトリのパス : NG → $gifdir2\n"; }

	# 画像チェック(1)
	$flag1=0;
	foreach (0 .. 9) {
		unless (-e "$gifdir1\/$_\.gif") {
			$flag1=1;
			print "<LI>画像が存在しません → $gifdir1\/$_\.gif";
		}
	}
	if (!$flag1) { print "<LI>gif1ディレクトリのGIF画像 : OK!\n"; }

	# 画像チェック(2)
	$flag2=0;
	foreach (0 .. 9) {
		unless (-e "$gifdir2\/$_\.gif") {
			$flag2=1;
			print "<LI>画像が存在しません → $gifdir2\/$_\.gif";
		}
	}
	if (!$flag2) { print "<LI>gif2ディレクトリのGIF画像 : OK!\n"; }

	# ロックディレクトリ
	print "<LI>ロック形式：";
	if ($lockkey == 0) { print "ロック設定なし\n"; }
	else {
		if ($lockkey == 1) { print "symlink\n"; }
		else { print "mkdir\n"; }
		$lockfile =~ s/(.*)[\\\/].*$/$lockdir = $1/e;
		print "<LI>ロックディレクトリ：$lockdir\n";

		if (-d $lockdir) { print "<LI>ロックディレクトリのパス：OK\n"; }
		else { print "<LI>ロックディレクトリのパス：NG → $lockdir\n"; }

		if (-r $lockdir && -w $lockdir && -x $lockdir) {
			print "<LI>ロックディレクトリのパーミッション：OK\n";
		} else {
			print "<LI>ロックディレクトリのパーミッション：NG → $lockdir\n";
		}
	}

	exit;
}
