use Encode qw(decode);

sub my_open {
    local ( *FILE, $name ) = @_;
    die("$name‚ªƒI[ƒvƒ“‚Å‚«‚Ü‚¹‚ñB") if ( !open( FILE, $name ) );
    flock( FILE, 2 );
    seek( FILE, 0, 0 );
}

sub my_close {
    local (*FILE) = @_;
    close(FILE);
}

sub mkdirs {
    use File::Path;
    my $path = shift;
    my @dirs = split( /\//, $path );
    pop @dirs if ( $dirs[-1] =~ /\./ );
    mkpath( join( "/", @dirs ), 0, 0777 );
}


sub mail_check {
    ( $_[0] =~ /^[a-zA-Z0-9_\/\-.\+\?\[\]]+\@[a-zA-Z0-9_\.\-]+\.[a-z]+$/i );
}

sub is_Tel {
    ( $_[0] =~ /^[0-9]+\-?[0-9]+\-?[0-9]+$/s );
}

sub juusho_check {
    $juusho = Unicode::Japanese->new($_[0], 'sjis')->utf8;
    $len = length(decode('utf-8', $juusho));
    if ($len > 15) {
        return 0;
    } else {
        return 1;
    }
}

sub cleanFile {
    my ( $dir, $term ) = @_;
    for my $file ( glob("$dir/*.*") ) {
        next if ( ( stat $file )[9] > ( time - $term ) );    # >
        unlink $file;
    }
}

sub make_csv {
    my (@values) = @_;
    return join( ',', map { ( s/"/""/g or /[\r\n,]/ ) ? qq("$_") : $_ } @values ) . "\n";
}


sub z2h {
    my $str = shift;
    jcode::tr( \$str, '‚O-‚X‚`-‚y‚-‚š',    '0-9A-Za-z' );
    jcode::tr( \$str, 'DCF^—H”•|', '.,:/@?#&-' );
    $str;
}

1;
