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
    require_once("f_DB.php");    
    $form_ini = parse_ini_file('./ini/form.ini', true);    
    
    //�萔
    $filename = $_SESSION['filename'];
    
    //�ϐ�
    $error = array();
    
    //PJ�`�F�b�N����
    $error = pjCheck($_SESSION['pjend']);
    
    //PJ�I������
    if($filename == "pjend_5")
    {
        if(count($error) == 0)
        {
            pjend($_SESSION['pjend']);
            insert_sousarireki('4', $_SESSION['pjend']);
        }
    }
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title>PJ�I����������</title>
        <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">        
    </head>
    <body>
        <div class="title">PJ�I����������</div>
        <div class="body_area">
            <form action="listJump.php" method="post">
                <input type="submit" name = "pjend_5_button" value = "�߂�" class="list_button">
            </form>     
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>