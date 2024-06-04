#!/usr/bin/perl
use CGI::Carp qw(fatalsToBrowser);
use lib 'sys-perl/lib';
use utf8;
use HTML::Template;
use Unicode::Japanese qw(unijp);

our ( %dir, %cmd, %in, %replace, $TMPL, %error, $sid_path, @mail_template );

%dir = (
    'db'     => 'sys-perl/db',
    'script' => 'sys-perl/script',
);

%cmd = (
    ''        => '01_form.pl',
    'conf'    => '02_conf.pl',
    'send'    => '03_thanks.pl',
    'KOUMOKU' => 'KOUMOKU.pl',
);

# メールテンプレート
@mail_template = (
    './tmpl-admin.txt',    # 管理者に送信

    './tmpl-user.txt',     # 投稿者に送信
);

if ( !-w "$dir{'db'}" ) {
    print "Content-Type: text/html; charset=utf-8\n\n";
    print qq| $dir{'db'} の パーミッションを 777にしてください |;
    exit;
}
if ( grep { !-e $_ } @mail_template ) {
    print "Content-Type: text/html; charset=utf-8\n\n";
    print 'メールテンプレートが見つかりません';
    exit;
}

require "$dir{'script'}/lib.pl";

main();

#===============
# HTML出力
#===============
my $template = HTML::Template->new(
    loop_context_vars => 1,
    die_on_bad_params => 0,
    filename          => $TMPL,
);

for my $key ( keys %replace ) {
    $template->param( $key => $replace{$key} );
}

print "Content-Type: text/html; charset=utf-8\n\n";
print $template->output;
exit;

1;
