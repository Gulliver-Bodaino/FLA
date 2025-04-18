#=============================================================================#
#                 allow.pl - インデックス特定用特別モジュール                 #
#        Copyright (C) 2000-2005, Katsushi Matsuda. All Right Reserved.       #
#=============================================================================#

################
### 変数定義 ###
################

### インデックスと設定ファイルを置くことを許可するディレクトリの一覧
### 必ず，"/"で終了して下さい．
@g_allow = (
	    "./",          # これは必ず残しておいて下さい
	    "testdir/",
	    );

###
### インデックスと設定ファイルの確定(上級者向け)
###
sub identifyindex {
    my $i;
    my $flag;
    my $filename;

    ### インデックスの確定
    if($qarg->{'index'} eq "") {
	$g_indexfile = "./default.idx";
    } else {
	if($qarg->{'index'} =~ tr/a-zA-Z0-9//dc) {
	    printerror("index入力が不正です");
	}
	for($i=0,$flag=0;$i<@g_allow;$i++) {
	    $filename = $g_allow[$i] . $qarg->{'index'} . ".idx";
	    if(-e $filename) {
		$flag = 1;
		last;
	    }
	}
	if($flag == 1) {
	    $g_indexfile = $filename;
	} else {
	    $g_indexfile = "./" . $qarg->{'index'} . ".idx";
	}
	$v_index = $qarg->{'index'};
    }

    ### 設定ファイルの確定
    if($qarg->{'config'} ne "") {
	if($qarg->{'config'} =~ tr/a-zA-Z0-9//dc) {
	    printerror("config入力が不正です");
	}
	for($i=0,$flag=0;$i<@g_allow;$i++) {
	    $filename = $g_allow[$i] . $qarg->{'config'} . ".cfg";
	    if(-e $filename) {
		$flag = 1;
		last;
	    }
	}
	if($flag == 1) {
	    $g_config = $filename;
	} else {
	    $g_config = "./" . $qarg->{'config'} . ".cfg";
	}
	$v_config = $qarg->{'config'};
    } else {
	if($qarg->{'index'} ne "") {
	    $g_config = $g_indexfile;
	    $g_config =~ s/idx$/cfg/;
	    unless(-e $g_config) {
		$g_config = "./default.cfg";
	    }
	} else {
	    $g_config = "./default.cfg";
	}
    }
}

1;
