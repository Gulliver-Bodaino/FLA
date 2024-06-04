sub main {

    Parse();

    $in{'sid'} =~ tr/a-zA-Z//cd;

    $sid_path = "$dir{'db'}/$in{'sid'}.dat";

    #=========================
    # 入力フォーム
    #=========================
    if ( $in{'c'} eq '' ) {
        $TMPL = 'tmpl-01_form.htm';
    }

    #=========================
    # 入力内容の確認
    #=========================
    elsif ( $in{'c'} eq 'conf' ) {
        cleanFile( "$dir{'db'}", 60 * 30 );
        hash_IO( \%sid, "$sid_path", 'e' );

        for my $key ( keys %sid ) {
            $in{$key} ||= '';
        }

        %sid = ( %sid, %in );
        hash_IO( \%sid, ">$sid_path" );
        $TMPL = 'tmpl-02_conf.htm';

    }

    #=========================
    # メール送信
    #=========================
    elsif ( $in{'c'} eq 'send' ) {
        if ( $in{'.back'} || $in{'.back.x'} || $in{'.back.y'} ) {
            $in{'c'} = '';
            hash_IO( \%sid, $sid_path, 'e' );

        }
        elsif ( -e "$sid_path" ) {
            hash_IO( \%sid, $sid_path );
            unlink $sid_path;

            for my $mail_template (@mail_template) {
                template_Send_Mail( \%sid, $mail_template );    # 管理者へメール通知
            }

            unlink glob("$dir{'db'}/$sid{'sid'}-*.*");

            $TMPL = 'tmpl-03_thanks.htm';
        }
        else {
            $in{'c'} = '';
        }

    }
    else {
        $in{'c'} = '';
    }

    exe( $in{'c'} );

    #=========================
    # 入力エラーチェック
    #=========================
    if ( $in{'c'} eq 'conf' ) {
        if (%error) {
            $in{'c'} = '';
        }
        else {
            map { $replace{$_} = norm_br( $replace{$_} ) } keys %replace;
        }
        %replace = HTML_escape( \%sid );

        # 改行表示処理
        foreach my $key (%replace) {

            # 「-br」が名前の最後に付く値を処理
            if ( $key =~ /-br$/i ) {
                next if ( !$replace{"$key"} );
                $key =~ s/-br//gi;
                $replace{"$key"} = norm_br( $replace{"$key"} );
            }
        }

    }

    if ( $in{'c'} eq '' ) {

        $sid{'sid'} ||= rand_str(10);
        %replace = HTML_escape( \%sid );
        while ( my ( $key, $value ) = each %replace ) {
            map { $replace{"$name-$_"} = 1 } split( /,/, $value );
        }

        exe('KOUMOKU');
        while ( my ( $key, $array ) = each %KOUMOKU ) {
            my $i = 0;
            my %checked;
            @checked{ split( /,/, $sid{"$key"} ) } = ();

            for my $val ( array($array) ) {
                if ( $sid{"$key"} eq $val ) {
                    $replace{"select.$key"}   .= qq|<option value="$val" selected="selected">$val</option>\n|;
                    $replace{"radio.$key.$i"} .= qq|<input type="radio" name="$key" value="$val" id="$key-$val" checked="checked" /><label for="$key-$val">$val</label>\n|;

                }
                else {
                    $replace{"select.$key"}   .= qq|<option value="$val">$val</option>\n|;
                    $replace{"radio.$key.$i"} .= qq|<input type="radio" name="$key" value="$val" id="$key-$val" /><label for="$key-$val">$val</label>\n|;
                }

                if ( exists $checked{$val} ) {
                    $replace{"checkbox.$key.$i"} .= qq|<input type="checkbox" name="$key" value="$val" id="$key-$val" checked="checked" /><label for="$key-$val">$val</label>\n|;
                }
                else {
                    $replace{"checkbox.$key.$i"} .= qq|<input type="checkbox" name="$key" value="$val" id="$key-$val" /><label for="$key-$val">$val</label>\n|;
                }
                $i++;
            }
        }

        $TMPL = 'tmpl-01_form.htm';
    }

    map { $replace{"ERROR.$_"} = $error{$_} } keys %error;

}

sub Parse {
    my ( $query, $key, $val );
    binmode(STDIN);
    read( STDIN, $query, $ENV{'CONTENT_LENGTH'} );
    $query .= '&' . $ENV{'QUERY_STRING'} if ( $ENV{'QUERY_STRING'} ne '' );

    if ( $ENV{'CONTENT_TYPE'} =~ /multipart/i ) {
        my $separater = quotemeta( ( split( /boundary=/, $ENV{'CONTENT_TYPE'} ) )[-1] );
        my @cell = split( /[-]*$separater/, $query );
        undef $query;
        shift @cell;
        pop @cell;

        my ($br);
        while ( my $str = shift @cell ) {
            ($br) = $str =~ /(\s*)/sg if ( !$br );
            my ( $name, $filename, $bin ) = multipart_form( $str, $br );
            $in{$name} .= ',' if ( defined( $in{$name} ) );

            if ($filename) {
                $in{$name} .= $filename;
                $in{"BIN/$name"} = $bin;
            }
            else {
                $bin =~ s/\x0D\x0A/\n/g;
                $bin =~ tr/\x0D\x0A/\n\n/;
                $in{$name} .= $bin;
            }
        }
    }
    else {
        for ( split( /&/, $query ) ) {
            tr/+/ /;
            ( $key, $val ) = split(/=/);
            $key =~ s/%([A-Fa-f0-9][A-Fa-f0-9])/pack("H2",$1)/eg;
            $val =~ s/%([A-Fa-f0-9][A-Fa-f0-9])/pack("H2",$1)/eg;
            $val =~ s/\x0D\x0A/\n/g;
            $val =~ tr/\x0D\x0A/\n\n/;
            $in{$key} .= ',' if ( defined( $in{$key} ) );
            $in{$key} .= $val;
        }
    }
    return ( keys %in );
}

sub multipart_form {
    my $str = shift;
    my $br  = shift;
    $str =~ s/^$br(.*)$br$/$1/s;
    my %tmp;
    ( $tmp{'head'}, $tmp{'body'} ) = split( /$br$br/, $str, 2 );
    ( $tmp{'name'} )     = $tmp{'head'} =~ /name="(.+?)"/gi;
    ( $tmp{'filename'} ) = $tmp{'head'} =~ /filename="(.+?)"/gi;
    return ( @tmp{ 'name', 'filename', 'body' } );
}

sub rand_str {
    my @str = ( a .. z, A .. Z );
    my $str;
    $str .= $str[ rand($#str) ] for ( 1 .. $_[0] );
    return $str;
}

sub my_open {
    local ( *FILE, $name ) = @_;
    die("$nameがオープンできません。") if ( !open( FILE, $name ) );
    flock( FILE, 2 );
    seek( FILE, 0, 0 );
}

sub my_close {
    local (*FILE) = @_;
    close(FILE);
}

sub hash_IO {
    my $hash   = shift;
    my $path   = shift;
    my $option = shift;

    return if ( $option =~ /e/ && !-e $path );    # ignore file is not exist.

    if ( $path =~ tr/>//d ) {

        # write mode

        # check target dir
        mkdirs($path) if ( !-e $path );

        # execute output data
        my_open( HASH, ">$path.$$" );
        binmode HASH;
        while ( my ( $key, $val ) = each %{$hash} ) {
            $val =~ s/(%|\s)/'%'.unpack('H2', $1)/eg;
            print HASH "$key\t$val\x0A";
        }
        my_close(HASH);

        rename "$path.$$", "$path";
        chmod oct(666), $path;

    }
    else {

        # read mode
        my_open( HASH, $path );
        undef %{$hash};
        while (<HASH>) {
            chomp;
            my ( $key, $val ) = split( /\t/, $_, 2 );
            $val =~ s/%([0-9A-Fa-f][0-9A-Fa-f])/pack('H2', $1)/eg;
            $hash->{$key} = $val;
        }
        my_close(HASH);
    }
}

sub HTML_escape {
    my %object;
    while ( my ( $key, $val ) = each %{ $_[0] } ) {
        $object{$key} = norm_input($val);
    }
    %object;
}

sub _HTML_escape {
    %{ $_[0] };
}

sub array {
    @{ $_[0] };
}

sub norm_input {
    $_ = $_[0];
    s/&/&amp;/g;
    s/"/&quot;/g;
    s/</&lt;/g;
    s/>/&gt;/g;
    $_;
}

sub norm_br {
    $_ = $_[0];
    s/\x0D\x0A/<BR>/g;
    s/\x0D/<BR>/g;
    s/\x0A/<BR>/g;
    $_;
}

sub cleanFile {
    my ( $dir, $term ) = @_;
    for my $file ( glob("$dir/*.*") ) {
        next if ( ( stat $file )[9] > ( time - $term ) );
        unlink $file;
    }
}

sub exe {
    my $cmd = shift;
    $cmd = '' if ( !exists $cmd{$cmd} );
    require "$dir{'script'}/$cmd{$cmd}";
}

sub mkdirs {
    use File::Path;
    my $path = shift;
    my @dirs = split( /\//, $path );
    pop @dirs if ( $dirs[-1] =~ /\./ );
    mkpath( join( "/", @dirs ), 0, 0777 );
}

sub template_Send_Mail {
    use MIME::Lite;
    use MIME::Base64 qw(encode_base64);

    my ( $rep, $path ) = @_;
    my %rep = _HTML_escape($rep);

    for my $key ( keys %rep ) {
        map { $rep{"$key-$_"} = 1 } split( /,/, $rep{$key} ) if ( $rep{$key} =~ /\,/ );
    }

    my %mail;
    my @mail_cc;
    my ( $head, $body ) = split( /&Body&/, &tmpl_and_rep( \%rep, $path ), 2 );
    #my $head = &tmpl_and_rep( \%rep, $path );
    ( $mail{'from'} ) = $head =~ /From:\s*(.*)/gi;
    ( $mail{'to'} )   = $head =~ /To:\s*(.*)/gi;
    @mail_cc = $head =~ /Cc:\s*(.*)/gi;
    ( $mail{'subject'} ) = $head =~ /Subject:\s*(.*)/gi;
    $mail{'body'} = $body;
    $mail{'subject'} = add_encoded_word( unijp( $mail{'subject'}, 'utf8' )->euc );

    my @bin_key = sort { $a cmp $b } grep { $rep{"BIN/$_"} } grep { $_ !~ /BIN\// } keys %rep;

    my $msg;
    if (@bin_key) {
        $msg = new MIME::Lite(
            'From'     => $mail{'from'},
            'To'       => $mail{'to'},
            'Cc'       => join( ', ', @mail_cc ),
            'Subject'  => $mail{'subject'},
            'Type'     => 'multipart/mixed',
            'Encoding' => 'binary',
        );

        $msg->attach(
            'Type'     => 'text/plain; charset="ISO-2022-JP"',
            'Encoding' => '7bit',
            'Data'     => unijp( $mail{'body'}, 'utf8' )->jis
        );

        for my $bin_key (@bin_key) {
            my $ext = ( split( /\./, $rep{$bin_key} ) )[-1];
            my $tmp_path = "$dir{'db'}/$rep{'sid'}-$bin_key.$ext";
            open( OUT, ">$tmp_path" );
            binmode OUT;
            print OUT $rep{"BIN/$bin_key"};
            close(OUT);

            $msg->attach(
                'Type'         => 'binary',
                'Content-type' => 'application/octet-stream',
                'Encoding'     => 'Base64',
                'Path'         => $tmp_path,
                'Filename'     => "$bin_key.$ext"
            );

        }

    }
    else {
        $msg = new MIME::Lite(
            'From'     => $mail{'from'},
            'To'       => $mail{'to'},
            'Cc'       => join( ', ', @mail_cc ),
            'Subject'  => $mail{'subject'},
            'Type'     => 'text/plain; charset="ISO-2022-JP"',
            'Encoding' => '7bit',
            'Data'     => unijp( $mail{'body'}, 'utf8' )->jis,
        );
    }

    my $sendmail
        = ( -e '/usr/lib/sendmail' ) ? '/usr/lib/sendmail'
        : ( -e '/usr/sbin/sendmail' ) ? '/usr/sbin/sendmail'
        :                               '';

    my $glob;

    if ($sendmail) {
        open( $glob, "| $sendmail -t -f$mail{'from'}" ) || die('sendmailがオープンできません');
    }
    else {
        open( $glob, ">./$mail{'to'}.txt" );
    }

    $msg->print($glob);

    close($glob);
}

sub tmpl_and_rep {
    my ( $hash_ref, $templ_src_path ) = @_;
    use HTML::Template;
    my $template = HTML::Template->new(
        filename          => "$templ_src_path",
        die_on_bad_params => 0,
    );
    for my $key ( keys %{$hash_ref} ) {
        $template->param( $key => $hash_ref->{$key} );
    }
    return $template->output;
}

sub add_encoded_word {
    my ( $str, $line ) = @_;
    my $result;

    my $ascii      = '[\x00-\x7F]';
    my $twoBytes   = '[\x8E\xA1-\xFE][\xA1-\xFE]';
    my $threeBytes = '\x8F[\xA1-\xFE][\xA1-\xFE]';

    while ( length($str) ) {
        my $target = $str;
        $str = '';
        if ( length($line) + 22 + ( $target =~ /^(?:$twoBytes|$threeBytes)/o ) * 8 > 76 ) {
            $line =~ s/[ \t\n\r]*$/\n/;
            $result .= $line;
            $line = ' ';
        }
        while (1) {
            my $encoded = '=?ISO-2022-JP?B?' . encode_base64( unijp( $target, 'euc' )->h2z->jis, '' ) . '?=';
            if ( length($encoded) + length($line) > 76 ) {
                $target =~ s/($threeBytes|$twoBytes|$ascii)$//o;
                $str = $1 . $str;
            }
            else {
                $line .= $encoded;
                last;
            }
        }
    }
    $result . $line;
}

sub YY { ( localtime( shift || time ) )[5] + 1900 }
sub MM { sprintf( "%02d", ( localtime( shift || time ) )[4] + 1 ) }
sub DD { sprintf( "%02d", ( localtime( shift || time ) )[3] ) }
sub hh { sprintf( "%02d", ( localtime( shift || time ) )[2] ) }
sub mm { sprintf( "%02d", ( localtime( shift || time ) )[1] ) }
sub ss { sprintf( "%02d", ( localtime( shift || time ) )[0] ) }

sub DATETIME {
    my $time = ( shift || time );
    sprintf( "%04d-%02d-%02d %02d:%02d:%02d", &YY($time), &MM($time), &DD($time), &hh($time), &mm($time), &ss($time) );
}

sub YYMMDD {
    my $time = ( shift || time );
    sprintf( "%04d-%02d-%02d", &YY($time), &MM($time), &DD($time) );
}

sub make_csv {
    my (@values) = @_;
    return join( ',', map { ( s/"/""/g or /[\r\n,]/ ) ? qq("$_") : $_ } @values ) . "\x0D\x0A";
}

sub is_Mail {
    ( $_[0] =~ /^[a-zA-Z0-9_\/\-.\+\?\[\]]+\@[a-zA-Z0-9_\.\-]+\.\w+$/ );
}

# EUC-JP文字
my $ascii      = '[\x00-\x7F]';                         # 1バイト EUC-JP文字
my $twoBytes   = '(?:[\x8E\xA1-\xFE][\xA1-\xFE])';      # 2バイト EUC-JP文字
my $threeBytes = '(?:\x8F[\xA1-\xFE][\xA1-\xFE])';      # 3バイト EUC-JP文字
my $character  = "(?:$ascii|$twoBytes|$threeBytes)";    # EUC-JP文字

my $Hkatakana = '(?:\x8E[\xA6-\xDF])';                  # EUC-JP 半角カナ

sub is_ZenkakuOnly {
    my $str = shift;

    my $euc_str = unijp( "$str", 'utf8' )->euc;

    # 半角カナを禁止
    if ( $euc_str =~ /$Hkatakana/ ) {
        return 0;
    }

    # 英数字を禁止
    if ( $euc_str =~ /$ascii/ ) {
        return 0;
    }

    return 1

}

sub is_SuujiOnly {
    ( $_[0] =~ /^[0-9]*$/s );
}

sub is_Suuji {
    ( $_[0] =~ /^\-?[0-9]*,?[0-9]+\.?$/s );
}

sub is_Tel {
    ( $_[0] =~ /^[0-9]+\-?[0-9]+\-?[0-9]+$/s );
}

# ----------------------------------
# かなチェック
# Unicode::Japaneseモジュール使用
# ----------------------------------
sub is_KANA {
    my $str  = shift;
    my $mode = shift;
    my $code = shift || 'utf8';    # utf8で渡ってくることが前提
    use Unicode::Japanese qw(unijp);

    $code = lc $code;
    $mode = uc $mode;

    # 文字の正規表現
    my %Reg = (
        'space'        => '\x20',                                          # 半角スペース
        'Zspace'       => '(?:\xA1\xA1)',                                  # 全角スペース
        'ZhiraganaExt' => '(?:\xA4[\xA1-\xF3]|\xA1[\xAB\xAC\xB5\xB6])',    # 全角ひらがな(拡張) [ぁ-ん゛゜ゝゞ]
        'ZkatakanaExt' => '(?:\xA5[\xA1-\xF6]|\xA1[\xA6\xBC\xB3\xB4])',    # 全角カタカナ(拡張) [ァ-ヶ・ーヽヾ]
        'Hkatakana'    => '(?:\x8E[\xA6-\xDF])',                           # 半角カタカナ [ヲ-゜]
    );
    my $space = "$Reg{'space'}|$Reg{'Zspace'}";

    $str = unijp( $str, $code )->euc;

    # 文字列の前後空白文字は無いものとして判定する
    $str =~ s/^($space)+//;
    $str =~ s/($space)+$//;

    if ( $mode eq 'HIRAGANA' ) {
        return ( $str =~ /^($space|$Reg{'ZhiraganaExt'})+$/ ) ? 1 : 0;
    }
    elsif ( $mode eq 'ZENKANA' ) {
        return ( $str =~ /^($space|$Reg{'ZkatakanaExt'})+$/ ) ? 1 : 0;
    }
    elsif ( $mode eq 'HANKANA' ) {
        return ( $str =~ /^($space|$Reg{'Hkatakana'})+$/ ) ? 1 : 0;
    }
    else {
        die "Unkown KANA-MODE `$mode`.";
    }

}

#------------------
# タグ入力チェック
#------------------
sub is_included_tag {
    my $val        = shift;
    my $tag_regexp = '<\/?[a-z][a-z0-9]*[^>]*>';

    my $flag = 0;
    if ( ref $val eq 'HASH' ) {
        while ( my ( $key, $v ) = each %{$val} ) {
            $flag += 1 if ( $v =~ /$tag_regexp/gi );
        }
    }
    elsif ( ref $val eq 'ARRAY' ) {
        for my $v ( @{$val} ) {
            $flag += 1 if ( $v =~ /$tag_regexp/gi );
        }
    }
    else {
        $flag = ( $val =~ /$tag_regexp/gi ) ? 1 : 0;
    }
    return $flag;
}

1;
