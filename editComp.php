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
    require_once("f_DB.php");
    require_once("f_Button.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    
    //編集処理
    if($filename == "KOKYAKUTEAM_3")
    {
        kokyakuteam_update($_SESSION['edit']);
    }
    elseif($filename == "PJTOUROKU_3")
    {
        pjtouroku_update($_SESSION['edit']);
    }
    elseif($filename == "PROGRESSINFO_3" || $filename == "TOP_3")
    {
        progress_update($_SESSION['edit']);
    }
    else
    {
        update($_SESSION['edit']);
    }
    insert_sousarireki('1', $_SESSION['edit']);
    
    //ページ移動処理
    $_SESSION['pre_post'] = $_SESSION['post'];
    $_SESSION['post'] = null;
    unset($_SESSION['edit']);
    $filename = $_SESSION['filename'];
    $filename_array = explode('_',$filename);
    $_SESSION['filename'] = $filename_array[0]."_2";
    if($filename_array[0] == "TOP")
    {
        header("location:".(empty($_SERVER['HTTPS'])? "http://" : "https://")
                .$_SERVER['HTTP_HOST'].dirname($_SERVER["REQUEST_URI"])."/TOP.php");
    }
    else
    {
        header("location:".(empty($_SERVER['HTTPS'])? "http://" : "https://")
                .$_SERVER['HTTP_HOST'].dirname($_SERVER["REQUEST_URI"])."/list.php");       
    }
?>