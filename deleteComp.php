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
    require_once("f_Form.php");
    require_once("f_DB.php");
	
    //�萔
    $filename = $_SESSION['filename'];
    
    //�ϐ�
    $judge = true;
    
    //�폜����
    if($filename == "PROGRESSINFO_3" || $filename == "TOP_3")
    {
        progress_delete($_SESSION['delete']);
    }
    else
    {
        delete($_SESSION['delete']);
    }
    //�y�[�W�ړ�����
    $_SESSION['pre_post'] = $_SESSION['post'];
    $_SESSION['post'] = null;
    unset($_SESSION['delete']);
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