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

# ���[���e���v���[�g
@mail_template = (
    './tmpl-admin.txt',    # �Ǘ��҂ɑ��M

    './tmpl-user.txt',     # ���e�҂ɑ��M
);

if ( !-w "$dir{'db'}" ) {
    print "Content-Type: text/html; charset=utf-8\n\n";
    print qq| $dir{'db'} �� �p�[�~�b�V������ 777�ɂ��Ă������� |;
    exit;
}
if ( grep { !-e $_ } @mail_template ) {
    print "Content-Type: text/html; charset=utf-8\n\n";
    print '���[���e���v���[�g��������܂���';
    exit;
}

require "$dir{'script'}/lib.pl";

main();

#===============
# HTML�o��
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
