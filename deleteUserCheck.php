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

    //�萔
    $filename = $_SESSION['filename'];
    $delete = $_SESSION['delete'];
    
    //�ϐ�
    $judge = true;
          
    //�u���E�U�o�b�N�A�����[�h�΍�
    start();  
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //�ݐЎЈ��`�F�b�N
    $con = dbconect();
    $sql = "SELECT *FROM syaininfo WHERE LUSERNAME IS NOT NULL AND LUSERPASS IS NOT NULL;";
    $result = $con->query($sql);
    if($result->num_rows <= 1)
    {
        $judge = false;
    }
?>
<html>
    <head>
        <title><?php echo $form_ini[$filename]['title']; ?></title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <script src='./js/inputcheck.js'></script>
        <script>
            window.onload = function(){
                var judge = '<?php echo $judge ?>';
                if(judge)
                {
                    if(confirm("���͓��e����m�F�B\n���폜���܂�����낵���ł����H" +
                        "\n�ēx�m�F����ꍇ�́u�L�����Z���v�{�^���������Ă��������B"))
                    {
                        location.href = "./deleteUserComp.php";
                    }
                }
            }
        </script>
    </head>
    <body>
        <div class="title">���[�U�[�폜</div>
        <div class="body_area">
            <form action="listUserJump.php" method="post">
                <table>
                    <tr>
                        <td>�Ј��ԍ�</td>
                        <td><?php echo $delete['staff_id']; ?></td>
                    </tr>
                    <tr>
                        <td>�Ј���</td>
                        <td><?php echo $delete['staff_name']; ?></td>
                    </tr>       
                    <tr>
                        <td>���[�U�[ID</td>
                        <td><?php echo $delete['luserid']; ?></td>
                    </tr>
                    <tr>
                        <td>�p�X���[�h</td>
                        <td><?php echo $delete['luserpass']; ?></td>
                    </tr>                
                </table>
                <input type="hidden" name="edit_id" value="<?php echo $delete['edit_id']; ?>">
                <input type="submit" name = "delete" value = "�폜" class="list_button">
                <input type="submit" name = "back" value = "�߂�" class="list_button" onClick ="isCancel = true;">            
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>