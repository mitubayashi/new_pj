<?php
    session_start();
    header('Content-type: text/html; charset=Shift_JIS'); 
?>
<?php
    //�����ݒ�
    require_once("f_Construct.php");
    require_once("f_Button.php");        
    require_once("f_DB.php");
    require_once ("f_Form.php");
    require_once ("f_SQL.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
    
    //�ϐ�
    $list = "";
    $sql = array();
    $inputcheck_data = array();
    $counter = 0;
    
    //�u���E�U�o�b�N�A�����[�h�΍�
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //�o����͗�
    $list .=  makekeihi_form($_SESSION['edit_id']);
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title><?php echo $form_ini[$filename]['title']; ?></title>
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <script src='./js/modal.js'></script>
        <script src='./js/inputcheck.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
        </script>
    </head>
    <body>
        <div class="title"><?php echo $form_ini[$filename]['title']; ?></div>          
        <div class="body_area">
            <form action='listJump.php' method='post'>                 
                <?php echo $list; ?>                
                <input type="submit" name = "keihi" value = "�ݒ�" class="list_button" onclick="return keihi_check();">         
                <input type="submit" name = "keihi_5_button" value = "�߂�" class="list_button" onclick="">
            </form>
        </div>
        <form action='pageJump.php' method='post'>
            <?php echo makebutton(); ?>
        </form>
    </body>
</html>