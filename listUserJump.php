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
    
    //ブラウザバック対策
    startJump($_POST);
    session_regenerate_id();
    $_SESSION['pre_post'] = $_SESSION['post'];
    $_SESSION['post'] = null;
    
    $keyarray = array_keys($_POST);
    $url = 'retry';
    foreach($keyarray as $key)
    {
        if ($key == 'serch')
        {
            $_SESSION['list'] = $_POST;
            $_SESSION['post'] = null;
            $url = 'listUser';
        }
        if($key == 'insert')
        {
            $_SESSION['insert'] = $_POST;
            $url = 'insertUserCheck';            
        }
        if($key == 'cancel')
        {
            unset($_SESSION['insert']);
            $url = 'insertUser';            
        }
        if($key == 'delete')
        {
            $_SESSION['delete'] = $_POST;
            $url = 'deleteUserCheck';
        }
        if($key == 'back')
        {
            $_SESSION['filename'] = 'listUser_5';
            $url = 'listUser';
        }   
        if($key == 'clear')
        {
            unset($_SESSION['edit']);
            $url = 'editUser';
        }
        if($key == 'edit')
        {
            $_SESSION['edit'] = $_POST;
            $url = 'editUserCheck';
        }
    }
    header("location:".(empty($_SERVER['HTTPS'])? "http://" : "https://")
        .$_SERVER['HTTP_HOST'].dirname($_SERVER["REQUEST_URI"])."/".$url.".php");
?>