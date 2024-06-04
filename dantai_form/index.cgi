#!/usr/bin/perl

require 'cgi-lib.pl';
require 'jcode.pl';


&shutoku;

&outputdata;


sub shutoku {

$formin = $ENV{'QUERY_STRING'};

@indata = split (/&/,$formin); #受け取ったデータを＆で区切り、配列へ

##ここから配列のサンプルはコメントアウト

#$zzz = @indata;

#$aaa = $indata[0];
#$bbb = $indata[1];
#$ccc = $indata[2];
#$ddd = $indata[3];

#$eee = $aaa + $bbb + $ccc+ $ddd;

#if($eee == 1000){

#$fff = "good";

#}



foreach $tmp (@indata) #フォームの要素分（配列分）以下の処理を繰り返す
{
	$unique = $tmp; 
}



}


##########確認ページ##########
sub outputdata {
print "Content-type: text/html\n\n";

print <<EOM;



<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=Shift_JIS">
		<meta http-equiv="Content-Style-Type" content="text/css">
		<meta http-equiv="Content-Script-Type" content="text/javascript">
		<meta name="description" content="">
		<meta name="keywords" content="">
		<title>アンケート</title>
		<link rel="Stylesheet" title="Plain" href="g.css" type="text/css">
<script language="javascript">
var NN = (navigator.appName == "Netscape") ;
var IE = (navigator.appName.charAt(0) == "M") ;
var ver4 = (navigator.appVersion.charAt(0) == "4") ;
var ver3 = (navigator.appVersion.charAt(0) == "3") ;
var NN4 = NN && ver4 ;
var NN3 = NN && ver3 ;
var IE4 = IE && ver4 ;
function Pull_Jump()
{
//	Sel=document.itiran.pull_menu.selectedIndex;
//	Ms=document.itiran.pull_menu.options[Sel].value;

	Ms="../index.html";
	window.parent.top.location.href=Ms;
}

function IN (n,m)
{
	if (NN4 || NN3 || IE4)
	{
		if (m == 0)
		{
			document.images[n].src = "img/mi" + n + ".gif";
		}
		else
		{
			document.images[n].src = "img/mo" + n + ".gif";
		}
	}
}
</script>



	</head>
	<body onload="Pull_Jump()">
	</body>
</html>



EOM
}

