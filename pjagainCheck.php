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
    require_once ("f_Form.php");
    require_once ("f_Button.php");
    require_once ("f_SQL.php");
    require_once ("f_DB.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
    
    //�ϐ�
    $list = "";
    $judge = true;
    
    //�u���E�U�o�b�N�A�����[�h�΍�
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //�I����e�\��
    $list .= "<table>";
    $list .= "<tr><td>�v���W�F�N�g�R�[�h</td><td>".$_SESSION['pjagain']['PJCODE']."</td></tr>";
    $list .= "<tr><td>�v���W�F�N�g��</td><td>".$_SESSION['pjagain']['PJNAME']."</td></tr>";
    $list .= "</table>";
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title>PJ�I���L�����Z�������`�F�b�N</title>
        <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <script>
            window.onload = function(){
                var judge = '<?php echo $judge ?>';
                if(judge)
                {
                    if(confirm("�������e����m�F�B\n�v���W�F�N�g���I���L�����Z�����܂�����낵���ł����H"))
                    {
                        location.href = "./pjagainComp.php";
                    }
                }
        }
        </script>
    </head>
    <body>
        <div class="title">PJ�I���L�����Z�������`�F�b�N</div>
        <div class="body_area">
            <form action="listJump.php" method="post">
                <?php echo $list; ?>
                <input type="submit" name = "pjagain_5_button" value = "�߂�" class="list_button">
            </form>    
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>