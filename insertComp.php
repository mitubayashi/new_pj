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
    require_once("f_DB.php");
    require_once("f_Button.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
    
    //�o�^����
    if($filename == "KOKYAKUTEAM_1")
    {
        kokyakuteam_insert($_SESSION['insert']);
    }
    elseif($filename == "PJTOUROKU_1")
    {
       pjtouroku_insert($_SESSION['insert']);
    }
    elseif($filename == "PROGRESSINFO_1" || $filename == "TOP_1")
    {
        progress_insert($_SESSION['insert']);
    }
    else
    {
        insert($_SESSION['insert']);
    }
    insert_sousarireki('0', $_SESSION['insert']);
    
    //�y�[�W�ړ�����
    $_SESSION['pre_post'] = $_SESSION['post'];
    $_SESSION['post'] = null;
    unset($_SESSION['insert']);
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