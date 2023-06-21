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
    
    //ブラウザバック対策
    startJump($_POST);
    session_regenerate_id();
    
    //変数
    $user = $_SESSION['user'];
    $listArray = $_SESSION['list'];
    $filename = $_SESSION['filename'];
    $_SESSION = array();
    $_SESSION['user'] = $user;
    $_SESSION['pre_post'] = $_POST;
    $_SESSION['files'] = $_FILES;
    $keyarray = array_keys($_POST);
    $url = 'retry';
    
    //ページ移動処理
    foreach($keyarray as $key)
    {
        if (strstr($key, '_button') != false )
        {
            $pre_url = explode('_',$key);
            switch($pre_url[1])
            {
                case 1:
                    if( empty($listArray) !== FLASE){
                        $_SESSION['list'] = $listArray;
                    }
                    $url = 'insert';
                    $_SESSION['filename'] = $pre_url[0]."_".$pre_url[1];
                    break;
                case 2:
                    $url = 'list';
                    $_SESSION['filename'] = $pre_url[0]."_".$pre_url[1];
                    break;
                case 3:
                    if( empty($listArray) !== FLASE){
                        $_SESSION['list'] = $listArray;
                    }
                    $url = 'edit';
                    $_SESSION['filename'] = $pre_url[0]."_".$pre_url[1];
                    $_SESSION['edit_id'] = $pre_url[3];
                    break;
                case 4:
                    $url = 'mainmenu';
                    $_SESSION['filename'] = $pre_url[0]."_".$pre_url[1];
                    break;
                case 5:
                    $url = $pre_url[0];
                    $_SESSION['filename'] = $pre_url[0]."_".$pre_url[1];
                    if( empty($listArray) !== FLASE){
                        $_SESSION['list'] = $listArray;
                    }
                    if($_SESSION['filename'] == "editUser_5")
                    {
                        $_SESSION['edit_id'] = $pre_url[3];
                    }
                    break;
                case 6:
                    $url = 'Fileinsert';
                    $_SESSION['filename'] = $pre_url[0]."_6";
                    if( empty($listArray) !== FLASE){
                        $_SESSION['list'] = $listArray;
                    }
                    if(isset($pre_url[3]))
                    {
                        $_SESSION['history'] = 'TOP_4';
                    }
                    break;
                case 7:
                    $url = 'csv_output';
                    $_SESSION['filename'] = $pre_url[0]."_7";
                    break;
                case 'MENU':
                    $url = 'mainmenu';
                    $_SESSION['filename'] = $pre_url[0]."_".$pre_url[1];
                    break;
                case 'MENTEMENU':
                    $url = 'mentemenu';
                    $_SESSION['filename'] = $pre_url[0]."_".$pre_url[1];
                    break;
                case '':
                    $url = 'login';
                    $_SESSION['filename'] = $pre_url[0]."_".$pre_url[1];
                    break;
                default:
                    $url = $pre_url[0];
                    break;
            }
        } 
        if($key == 'rireki_delete')
        {
            $_SESSION['filename'] = 'rireki_2';
            rireki_delete($_POST);
            $url = 'list';
        }
        if($key == 'pagenum')
        {
            $_SESSION['filename'] = $filename;
            $_SESSION['list'] = $listArray;
            $_SESSION['list']['pagenum'] = $_POST['pagenum'];
            $url = 'list';
        }
    }
    header("location:".(empty($_SERVER['HTTPS'])? "http://" : "https://")
            .$_SERVER['HTTP_HOST'].dirname($_SERVER["REQUEST_URI"])."/".$url.".php");
?>

<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
        <script language="JavaScript">
            history.forward();
        </script>
    </head>
    <body>
    </body>
</html>