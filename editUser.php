<?php
    session_start();
    header('Expires:-1'); 
    header('Cache-Control:'); 
    header('Pragma:');
    header('Content-type: text/html; charset=Shift_JIS'); 
?>
<?php
    //�����ݒ�
    require_once("f_Construct.php");
    require_once("f_Button.php");        
    require_once ("f_DB.php");
    require_once ("f_Form.php");
    require_once ("f_SQL.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    
    //�u���E�U�o�b�N�A�����[�h�΍�
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //�萔
    $filename = $_SESSION['filename'];

    //�ϐ�
    $judge = false;
    
    //�Ј����擾
    $con = dbconect();
    $sql = "SELECT *FROM syaininfo WHERE 4CODE = '".$_SESSION['edit_id']."';";
    $result = $con->query($sql) or ($judge = true);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $staff_name = $result_row['STAFFNAME'];
        $staff_id = $result_row['STAFFID'];
        $luser_id = $result_row['LUSERNAME'];
        $luser_pass = $result_row['LUSERPASS'];
    }
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title><?php echo $form_ini[$filename]['title']; ?></title>
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <script src='./js/inputcheck.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
        </script>
    </head>
    <body>
        <div class="title"><?php echo $form_ini[$filename]['title']; ?></div>
        <div class="body_area">
            <form action="listUserJump.php" method="post">
                <table>
                    <tr>
                        <td>�Ј��ԍ�</td>
                        <td><input type="text" size="30" value="<?php echo $staff_id; ?>" name="staff_id" class="form_text disabled"></td>
                    </tr>
                    <tr>
                        <td>�Ј���</td>
                        <td><input type="text" size="60" value="<?php echo $staff_name; ?>"  name="staff_name" class="form_text disabled"></td>
                    </tr>
                    <tr>
                        <td>���[�U�[ID</td>
                        <td><input type="text" size="30" placeholder="���p�p����60���ȓ�" value="<?php echo $luser_id; ?>" name="luserid" id="luserid" class="form_text" onchange="inputcheck('luserid',60,3,1,0)"><a id="luserid_errormsg" class="errormsg"></a></td>
                    </tr>
                    <tr>
                        <td>���݂̃p�X���[�h</td>
                        <td><input type="password" size="30" value="" name="nowpass" id="nowpass" class="form_text"><a id="nowpass_errormsg" class="errormsg"></a></td>
                    </tr>
                    <tr>
                        <td>�ύX��p�X���[�h</td>
                        <td><input type="password" size="30" placeholder="���p�p����60���ȓ�" value="" name="luserpass" id="luserpass" class="form_text" onchange="passinput_check(this.id);"><a id="luserpass_errormsg" class="errormsg"></a></td>
                    </tr>
                    <tr>
                        <td>�m�F�p�p�X���[�h</td>
                        <td><input type="password" size="30" placeholder="���p�p����60���ȓ�" value="" name="luserpass_check" id="luserpass_check" class="form_text" onchange="passinput_check(this.id);"><a id="luserpass_check_errormsg" class="errormsg"></a></td>
                    </tr>
                </table>
                <input type="hidden" name="edit_id" value="<?php echo $_SESSION['edit_id']; ?>">
                <input type="hidden" id="nowpass_check" value="<?php echo $luser_pass; ?>">
                <input type="submit" name = "edit" value = "�X�V" class="list_button" onclick="return pass_check('edit');">
                <input type="submit" name = "clear" value = "�N���A" class="list_button" onClick ="isCancel = true;">
                <input type="submit" name = "delete" value = "�폜" class="list_button" onClick ="isCancel = true;">
                <input type="submit" name = "back" value = "�߂�" class="list_button" onClick ="isCancel = true;">            
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>