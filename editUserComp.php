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

    //�ϐ�
    $judge = false;
    
    //�X�V����
    $con = dbconect();
    $sql = "UPDATE syaininfo SET LUSERNAME = '".$_SESSION['edit']['luserid']."',LUSERPASS = '".$_SESSION['edit']['luserpass']."'";
    $sql .= " WHERE 4CODE = '".$_SESSION['edit']['edit_id']."'";
    $result = $con->query($sql) or ($judge = true); 
    insert_sousarireki('1', $_SESSION['edit']);
    
    //�y�[�W�ړ�����
    $_SESSION['pre_post'] = $_SESSION['post'];
    $_SESSION['post'] = null;
    unset($_SESSION['edit']);
    $_SESSION['filename'] = 'listUser_5';
    header("location:".(empty($_SERVER['HTTPS'])? "http://" : "https://")
            .$_SERVER['HTTP_HOST'].dirname($_SERVER["REQUEST_URI"])."/listUser.php");
?>