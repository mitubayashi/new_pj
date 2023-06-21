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
    require_once("f_DB.php");    
    $form_ini = parse_ini_file('./ini/form.ini', true);    
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $error = array();
    
    //PJ終了キャンセル処理
    pjagain($_SESSION['pjagain']);
    insert_sousarireki('5', $_SESSION['pjagain']);
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title>PJ終了キャンセル処理完了</title>
        <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">        
    </head>
    <body>
        <div class="title">PJ終了キャンセル処理完了</div>
        <div class="body_area">
            <form action="listJump.php" method="post">
                <input type="submit" name = "pjagain_5_button" value = "戻る" class="list_button" style="margin-top: 10px;">
            </form>      
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>