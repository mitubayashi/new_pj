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
    require_once ("f_Button.php");
    require_once ("f_DB.php");
    require_once ("f_Form.php");
    require_once ("f_SQL.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
     
    //�ϐ�
    
    //�u���E�U�o�b�N�A�����[�h�΍�
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title>PJ�f�[�^�폜</title>
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./js/progress.js'></script>
        <script>
        </script>
    </head>
    <body>
        <div class="title">PJ�f�[�^�폜</div>
        <div class="body_area">
            <form action="listJump.php" method="post" enctype="multipart/form-data">
                �ŏI�폜���F<?php echo delete_rireki(); ?><br>
                <input type='submit' value='PJ�f�[�^�폜' name='pjdata_delete' class='list_button'>
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>