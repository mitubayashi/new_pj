<?php
    session_start();
    header('Expires:-1'); 
    header('Cache-Control:'); 
    header('Pragma:'); 
    header('Content-type: text/html; charset=Shift_JIS'); 
?>
<?php
    //初期設定
    require_once("f_Construct.php");
    require_once("f_Button.php");        
    require_once ("f_DB.php");
    require_once ("f_Form.php");
    require_once ("f_SQL.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    
    //ブラウザバック対策
    start();
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
?>
<html>
    <head>
        <title>ユーザー登録</title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
        </script>
    </head>
    <body>
        <div class="title"><?php echo $form_ini[$filename]['title']; ?></div>
        <div class="body_area">
            <form action="listUserJump.php" method="post">
                <table>
                    <tr><td></td><td><input type="button" value="社員選択" onclick="popup_modal(4);"><input type="hidden" name="401" id="401" value=""><a id="401_errormsg" class="errormsg"></a></td></tr>
                    <tr>
                        <td>社員番号</td>
                        <td><input type="text" value="" size="30" id="402" name="402" class="form_text disabled"></td>
                    </tr>
                    <tr>
                        <td>社員名</td>
                        <td><input type="text" value="" size="60" id="403" name="403" class="form_text disabled"></td>
                    </tr>
                    <tr>
                        <td>ユーザーID</td>
                        <td><input type="text" placeholder="半角英数字60字以内" size="30" value="" name="luserid" id="luserid" class="form_text" onchange="inputcheck('luserid',60,3,1,0)"><a id="luserid_errormsg" class="errormsg"></a></td>
                    </tr>
                    <tr>
                        <td>パスワード</td>
                        <td><input type="password" size="30" placeholder="半角英数字60字以内" value="" name="luserpass" id="luserpass" class="form_text" onchange="passinput_check(this.id);"><a id="luserpass_errormsg" class="errormsg"></a></td>
                    </tr>
                    <tr>
                        <td>確認用パスワード</td>
                        <td><input type="password" size="30" placeholder="半角英数字60字以内" value="" name="luserpass_check" id="luserpass_check" class="form_text" onchange="passinput_check(this.id);"><a id="luserpass_check_errormsg" class="errormsg"></a></td>
                    </tr>
                </table>
                <input type='submit' name = 'insert' value = '登録' class='list_button' onclick="return pass_check('insert');">
                <input type='submit' name = 'cancel' value = 'クリア' class='list_button'>
                <input type="submit" name = "back" value = "戻る" class="list_button" onClick ="isCancel = true;"> 
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>