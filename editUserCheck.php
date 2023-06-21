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
    $edit = $_SESSION['edit'];
    $staff_name = $edit['staff_name'];
    $staff_id = $edit['staff_id'];
    $luser_id = $edit['luserid'];
    $luser_pass = $edit['luserpass'];
    $now_pass = $edit['nowpass'];
    $luserpass_check = $edit['luserpass_check'];    
    
    //変数
    $judge = true;
    $error_data = array();
        
    //ブラウザバック、リロード対策
    start();  
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //ユーザーID重複チェック
    $con = dbconect();
    $sql = "SELECT *FROM syaininfo WHERE 4CODE != '".$edit['edit_id']."' AND LUSERNAME = '".$edit['luserid']."';";
    $result = $con->query($sql);
    if($result->num_rows > 0)
    {
        $judge = false;
        $error_data[0]['name'] = "luserid";
        $error_data[0]['error_type'] = "3";
    }
    
    //現在のパスワードを取得
    $sql = "SELECT *FROM syaininfo WHERE 4CODE = '".$edit['edit_id']."';";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $nowpass_check = $result_row['LUSERPASS'];
    }
?>
<html>
    <head>
        <title><?php echo $form_ini[$filename]['title']; ?></title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <script src='./js/inputcheck.js'></script>
        <script>
            var error_data = JSON.parse('<?php echo json_encode($error_data); ?>');
            var filename = '<?php echo $filename; ?>';
        </script>
        <script>
            window.onload = function(){
                var judge = '<?php echo $judge ?>';
                error_data_set();
                if(judge)
                {
                    if(confirm("入力内容正常確認。\n情報更新しますがよろしいですか？" +
                        "\n再度確認する場合は「キャンセル」ボタンを押してください。"))
                    {
                        location.href = "./editUserComp.php";
                    }
                }
            }
        </script>
    </head>
    <body>
        <div class="title"><?php echo $form_ini[$filename]['title']; ?></div>
        <div class="body_area">
            <form action="listUserJump.php" method="post">
                <table>
                    <tr>
                        <td>社員番号</td>
                        <td><input type="text" size="30" value="<?php echo $staff_id; ?>" name="staff_id" class="form_text disabled"></td>
                    </tr>
                    <tr>
                        <td>社員名</td>
                        <td><input type="text" size="60" value="<?php echo $staff_name; ?>" name="staff_name" class="form_text disabled"></td>
                    </tr>
                    <tr>
                        <td>ユーザーID</td>
                        <td><input type="text" size="30" placeholder="半角英数字60字以内" value="<?php echo $luser_id; ?>" name="luserid" id="luserid" class="form_text" onchange="inputcheck('luserid',60,3,1,0)"><a id="luserid_errormsg" class="errormsg"></a></td>
                    </tr>
                    <tr>
                        <td>現在のパスワード</td>
                        <td><input type="password" size="30" value="<?php echo $now_pass; ?>" name="nowpass" id="nowpass" class="form_text"><a id="nowpass_errormsg" class="errormsg"></a></td>
                    </tr>
                    <tr>
                        <td>変更後パスワード</td>
                        <td><input type="password" size="30" placeholder="半角英数字60字以内" value="<?php echo $luser_pass; ?>" name="luserpass" id="luserpass" class="form_text" onchange="passinput_check(this.id);"><a id="luserpass_errormsg" class="errormsg"></a></td>
                    </tr>
                    <tr>
                        <td>確認用パスワード</td>
                        <td><input type="password" size="30" placeholder="半角英数字60字以内" value="<?php echo $luserpass_check; ?>" name="luserpass_check" id="luserpass_check" class="form_text" onchange="passinput_check(this.id);"><a id="luserpass_check_errormsg" class="errormsg"></a></td>
                    </tr>
                </table>
                <input type="hidden" name="edit_id" value="<?php echo $edit['edit_id']; ?>">
                <input type="hidden" id="nowpass_check" value="<?php echo $nowpass_check; ?>">
                <input type="submit" name = "edit" value = "更新" class="list_button" onclick="return pass_check('edit');">
                <input type="submit" name = "clear" value = "クリア" class="list_button" onClick ="isCancel = true;">
                <input type="submit" name = "back" value = "戻る" class="list_button" onClick ="isCancel = true;">            
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>