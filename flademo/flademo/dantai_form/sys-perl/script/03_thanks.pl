#=========================
# 完了ページ
#=========================

#--------------
# ■CSV保存機能
# 必要であればコメントアウトを外してください
#--------------

{
    my $path  = '../../csvdata/dantai_form.csv';
#     my $path2 = '../../csvdata/csvbackup/backupdantai_form.csv';
    my $csv;

    if ( !-e $path ) {
        mkdirs($path);
        mkdirs($path2);
        $csv = &make_csv(    # *
            '登録日',
            '過去に団体受験案内の願書請求をしたことがありますか？',
            '過去に団体受験受験をしたことがありますか？',
            '企業・学校等の団体名',
            '氏名',
            'フリガナ',
            '郵便番号',
            '住所',
            '電話番号',
            'メールアドレス',
        );
    }

    $csv .= &make_csv(    # *
        &DATETIME,                                                                  #         '登録日',
        "$sid{'q1'}",                                                               #         '過去に団体受験案内の願書請求をしたことがありますか？',
        "$sid{'q2'}",                                                               #         '過去に団体受験受験をしたことがありますか？',
        "$sid{'group_name'}",                                                       #         '企業・学校等の団体名',
        "$sid{'name1_1'} $sid{'name1_2'}",                                          #         '氏名',
        "$sid{'name2_1'} $sid{'name2_2'}",                                          #         'フリガナ',
        "$sid{'zip1'}-$sid{'zip2'}",                                                #         '郵便番号',
        "$sid{'todouhuken'}$sid{'city'}$sid{'city2'}$sid{'city3'}$sid{'city4'}",    #         '住所',
        "$sid{'tel'}",                                                              #         '電話番号',
        "$sid{'mail'}",                                                             #         'メールアドレス',
    );

    my_open( OUT, ">>$path" );
    binmode OUT;
    print OUT unijp( $csv, 'utf8' )->sjis;
    my_close(OUT);

    # バックアップ用
#     my_open( OUT, ">>$path2" );
#     binmode OUT;
#     print OUT unijp( $csv, 'utf8' )->sjis;
#     my_close(OUT);

}

1;
