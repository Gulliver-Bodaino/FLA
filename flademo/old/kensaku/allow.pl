#=============================================================================#
#                 allow.pl - ����ǥå������������̥⥸�塼��                 #
#        Copyright (C) 2000-2005, Katsushi Matsuda. All Right Reserved.       #
#=============================================================================#

################
### �ѿ���� ###
################

### ����ǥå���������ե�������֤����Ȥ���Ĥ���ǥ��쥯�ȥ�ΰ���
### ɬ����"/"�ǽ�λ���Ʋ�������
@g_allow = (
	    "./",          # �����ɬ���Ĥ��Ƥ����Ʋ�����
	    "testdir/",
	    );

###
### ����ǥå���������ե�����γ���(���Ը���)
###
sub identifyindex {
    my $i;
    my $flag;
    my $filename;

    ### ����ǥå����γ���
    if($qarg->{'index'} eq "") {
	$g_indexfile = "./default.idx";
    } else {
	if($qarg->{'index'} =~ tr/a-zA-Z0-9//dc) {
	    printerror("index���Ϥ������Ǥ�");
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

    ### ����ե�����γ���
    if($qarg->{'config'} ne "") {
	if($qarg->{'config'} =~ tr/a-zA-Z0-9//dc) {
	    printerror("config���Ϥ������Ǥ�");
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
