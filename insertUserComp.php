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

    //変数
    $judge = false;
    
    //更新処理
    $con = dbconect();
    $sql = "UPDATE syaininfo SET LUSERNAME = '".$_SESSION['insert']['luserid']."',LUSERPASS = '".$_SESSION['insert']['luserpass']."'";
    $sql .= " WHERE 4CODE = '".$_SESSION['insert']['401']."'";
    $result = $con->query($sql) or ($judge = true); 
    insert_sousarireki('0', $_SESSION['insert']);
    
    //ページ移動処理
    $_SESSION['pre_post'] = $_SESSION['post'];
    $_SESSION['post'] = null;
    unset($_SESSION['insert']);
    $_SESSION['filename'] = 'listUser_5';
    header("location:".(empty($_SERVER['HTTPS'])? "http://" : "https://")
            .$_SERVER['HTTP_HOST'].dirname($_SERVER["REQUEST_URI"])."/listUser.php");
?>