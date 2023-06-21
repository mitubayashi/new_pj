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
    
    //PJチェック処理
    $error = pjCheck($_SESSION['pjend']);
    
    //PJ終了処理
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
        <title>PJ終了処理完了</title>
        <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">        
    </head>
    <body>
        <div class="title">PJ終了処理完了</div>
        <div class="body_area">
            <form action="listJump.php" method="post">
                <input type="submit" name = "pjend_5_button" value = "戻る" class="list_button">
            </form>     
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>