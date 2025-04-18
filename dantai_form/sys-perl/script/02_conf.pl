#=========================
# 入力内容の確認
#=========================

#--------------------
# 【エラーチェック】
#--------------------

$error{'q1'} = '必須回答です' if ( $sid{'q1'} eq '' );
$error{'q2'} = '必須回答です' if ( $sid{'q2'} eq '' );
$error{'group_name'} = '必須回答です' if ( $sid{'group_name'} eq '' );
$error{'group_num'} = '必須回答です' if ( $sid{'group_num'} eq '' );

# ■名前
# ・全角のみ
$error{'name1'} = '必須回答です' if ( $sid{'name1_1'} eq '' );
$error{'name1'} = '必須回答です' if ( $sid{'name1_2'} eq '' );
$error{'name1'} = '全角で入力してください' if ( !is_ZenkakuOnly( $sid{'name1_1'} ) );
$error{'name1'} = '全角で入力してください' if ( !is_ZenkakuOnly( $sid{'name1_2'} ) );

# ■ふりがな
# ・全角のみ
$error{'name2'} = '必須回答です' if ( $sid{'name2_1'} eq '' );
$error{'name2'} = '必須回答です' if ( $sid{'name2_2'} eq '' );

$error{'name2'} = '全角で入力してください'     if ( !is_ZenkakuOnly( $sid{'name2_1'} ) );
$error{'name2'} = '全角で入力してください'     if ( !is_ZenkakuOnly( $sid{'name2_2'} ) );
$error{'name2'} = 'カタカナで入力してください' if ( $sid{'name2_1'} && !is_KANA( $sid{'name2_1'}, 'ZENKANA' ) );
$error{'name2'} = 'カタカナで入力してください' if ( $sid{'name2_2'} && !is_KANA( $sid{'name2_2'}, 'ZENKANA' ) );

# ■郵便
# ・半角数字のみ
$error{'zip'} = '必須回答です' if ( $sid{'zip1'} eq '' );
$error{'zip'} = '必須回答です' if ( $sid{'zip2'} eq '' );
$error{'zip'} = '半角数字を入力してください' if ( !is_SuujiOnly( $sid{'zip1'} ) );
$error{'zip'} = '半角数字を入力してください' if ( !is_SuujiOnly( $sid{'zip2'} ) );

# ■都道府県
# ・全角のみ
$error{'todouhuken'} = '必須回答です' if ( $sid{'todouhuken'} eq '' );
$error{'todouhuken'} = '全角で入力してください' if ( !is_ZenkakuOnly( $sid{'todouhuken'} ) );

# ■市町村
# ・全角のみ
$error{'city'} = '必須回答です' if ( $sid{'city'} eq '' );
$error{'city2'} = '必須回答です' if ( $sid{'city2'} eq '' );
$error{'city'} = '全角で入力してください' if ( !is_ZenkakuOnly( $sid{'city'} ) );
$error{'city2'} = '全角で入力してください' if ( !is_ZenkakuOnly( $sid{'city2'} ) );
$error{'city3'} = '全角で入力してください' if ( !is_ZenkakuOnly( $sid{'city3'} ) );
$error{'city4'} = '全角で入力してください' if ( !is_ZenkakuOnly( $sid{'city4'} ) );

$error{'tel'} = '必須回答です' if ( $sid{'tel'} eq '' );
$error{'tel'} = '正しい番号で入力してください' if ( !is_Tel( $sid{'tel'} ) );

# ■メールアドレス
# ・不正なアドレスのチェック
$error{'mail'} = '必須回答です' if ( $sid{'mail'} eq '' );
$error{'mail'} = '正しく入力してください' if ( !is_Mail( $sid{'mail'} ) );

# ■チェック
$error{'check'} = '必須選択です' if ( $sid{'check'} eq '' );

# --------------------
# タグ入力チェック
# --------------------
$error{'name1'} = 'タグが含まれています' if ( is_included_tag( $sid{'name1_1'} ) || is_included_tag( $sid{'name1_2'} ) );
$error{'name2'} = 'タグが含まれています' if ( is_included_tag( $sid{'name2_1'} ) || is_included_tag( $sid{'name2_2'} ) );
$error{'group_name'} = 'タグが含まれています' if ( is_included_tag( $sid{'group_name'} ) );
$error{'todouhuken'} = 'タグが含まれています' if ( is_included_tag( $sid{'todouhuken'} ) );
$error{'city'} = 'タグが含まれています' if ( is_included_tag( $sid{'city'} ) );
$error{'city2'} = 'タグが含まれています' if ( is_included_tag( $sid{'city2'} ) );
$error{'city3'} = 'タグが含まれています' if ( is_included_tag( $sid{'city3'} ) );
$error{'city4'} = 'タグが含まれています' if ( is_included_tag( $sid{'city4'} ) );

# --------------------
# 【改行表示処理】
# 改行したい値名の最後に「-br」を付けて、1を設定する。
# 例）：改行したいnameが「textarea4」 → $sid{'textarea4-br'} = 1;
#       大文字でも可能  $sid{'TEXTAREA4-BR'} = 1;
# --------------------
1;

__END__

┌───────────────┐
│       判定文の概要           │
└───────────────┘

  ●メールアドレスの書式チェック
    is_Mail()

  ●全角文字の検証
    is_ZenkakuOnly()

  ●数字のみ  [0123456789]
    is_SuujiOnly()

  ●数値のみ  [ - , . 0123456789 ]
    is_Suuji()

  ●電話番号の書式チェック  999999999 9999-99-9999 など
    is_Tel()

  ●ひらがなのみチェック
    is_KANA( $string, 'HIRAGANA' )
    
  ●全角カタカナのみチェック
    is_KANA( $string, 'ZENKANA' )
    
  ●半角カタカナのみチェック
    is_KANA( $string, 'HANKANA' )

  ●タグ入力チェック
  is_included_tag( $string ) 