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
    require_once("f_Form.php");
    require_once("f_DB.php");
	
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $judge = true;
    
    //登録処理
    keihi_fileinsert($_SESSION['fileinsert']);
    insert_sousarireki('0',$_SESSION['fileinsert']);
    
    //ページ移動処理
    $_SESSION['pre_post'] = $_SESSION['post'];
    $_SESSION['post'] = null;
    unset($_SESSION['fileinsert']);
    $_SESSION['filename'] = "keihi_5";
    header("location:".(empty($_SERVER['HTTPS'])? "http://" : "https://")
            .$_SERVER['HTTP_HOST'].dirname($_SERVER["REQUEST_URI"])."/keihi.php");
    
?>