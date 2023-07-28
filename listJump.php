<?php
    session_start();
    header('Expires:-1'); 
    header('Cache-Control:'); 
    header('Pragma:'); 
    header('Content-type: text/html; charset=Shift_JIS'); 
?>
<?php
    //初期設定
    require_once("f_DB.php");
    require_once("f_Construct.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);

    //ブラウザバック対策
    startJump($_POST);
    session_regenerate_id();
    $_SESSION['pre_post'] = $_SESSION['post'];
    $_SESSION['post'] = null;
    
    //定数
    $filename = $_SESSION['filename'];
    $filename_array = explode('_',$filename);
    $keyarray = array_keys($_POST);
    $url = 'retry';
    
    //ページ移動処理
    foreach($keyarray as $key)
    {
        if(strstr($key, 'serch'))
        {
            $_SESSION['list'] = $_POST;
            $_SESSION['post'] = null;
            if($filename == "pjend_5")
            {
                $url = "pjend";
            }
            else if($filename == "pjagain_5")
            {
                $url = "pjagain";
            }
            elseif($filename == "keihi_5")
            {
                $url = "keihi";
            }
            else
            {
                $url = 'list';
            }
        }
        if($key == 'insert')
        {
            $_SESSION['insert'] = $_POST;
            $url = "insertCheck";            
        }
        if($key == 'cancel')
        {
            unset($_SESSION['insert']);
            $url = "insert";
        }
        if($key == 'back')
        {
            $_SESSION['filename'] = $filename_array[0]."_2";
            if($filename_array[0] == 'TOP')
            {
                $_SESSION['insert'] = null;
                $url = "TOP";
            }
            elseif($filename_array[0] == 'keihifileinsert')
            {
                $_SESSION['insert'] = null;
                $url = "keihi";
                $_SESSION['filename'] = "keihi_5";
            }
            else
            {
                $url = "list";
            }
        }
        if($key == 'edit')
        {
            $_SESSION['edit'] = $_POST;
            $_SESSION['edit_id'] = $_POST['edit_id'];
            $url = "editCheck";      
        }
        if($key == 'delete')
        {
            $_SESSION['delete'] = $_POST;
            $url = "deleteCheck";
        }
        if($key == 'clear')
        {
            unset($_SESSION['edit']);
            $url = "edit";
        }
        if($key == 'genka')
        {
            $_SESSION['genka'] = $_POST;
            $url = "genkaComp";           
        }
        if($key == 'teijicheck')
        {
            $_SESSION['teiji'] = $_POST;
            $url = "teijiComp";
        }
        if($key == 'fileinsert')
        {
            move_uploaded_file( $_FILES['inpath']['tmp_name'],"./temp/tempfileinsert.txt");
            $_SESSION['files'] = $_FILES;
            $_SESSION['fileinsert'] = $_POST;
            if($filename == "keihifileinsert_5")
            {
                $url = "keihifileCheck";
            }
            else
            {
                $url = "FileinsertCheck";  
            }
        }
        if($key == 'pjend')
        {
            $_SESSION['pjend'] = $_POST;
            $url = "pjendCheck";  
        }
        if($key == 'pjagain')
        {
            $_SESSION['pjagain'] = $_POST;
            $url = "pjagainCheck";
        }
        if($key == 'PROGRESSINFO_6_button')
        {
            $_SESSION['filename'] = $filename_array[0]."_6";
            $url = "Fileinsert";
        }
        if($key == 'teiji_5_button')
        {
            $_SESSION['filename'] = $filename_array[0]."_5";
            $_SESSION['startdate'] = $_POST['startdate'];
            $_SESSION['enddate'] = $_POST['enddate'];
            $url = "teiji";
        }
        if($key == 'pjend_5_button')
        {
            $_SESSION['filename'] = $filename_array[0]."_5";
            $url = "pjend";
        }
        if($key == 'pjagain_5_button')
        {
            $_SESSION['filename'] = $filename_array[0]."_5";
            $url = "pjagain";
        }
        if($key == 'getuzi')
        {
            $_SESSION['getuzi'] = $_POST;
            $url = "getuziCheck";
        }
        if($key == 'getuzi_5_button')
        {
            $_SESSION['filename'] = $filename_array[0]."_5";
            $url = "getuzi";
        }
        if($key == 'nenzi')
        {
            $_SESSION['nenzi'] = $_POST;
            $url = "nenziCheck";
        }
        if($key == 'nenzi_5_button')
        {
            $_SESSION['filename'] = $filename_array[0]."_5";
            $url = "nenzi";
        }
        if($key == 'pjdata_delete')
        {
            $_SESSION['filename'] = "pjdelete_5";
            pjdelete();
            insert_sousarireki('2', $_POST);
            $url = "pjdelete";
        }
        if($key == 'kobetu_delete')
        {
            kobetu_delete($_POST['edit_id']);
            $url = "edit";
        }
        if($key == "keihi")
        {
            $_SESSION['keihi'] = $_POST;
            $url = "keihiComp";
        }
        if($key == "keihi_5_button")
        {
            $_SESSION['filename'] = "keihi_5";
            $url = "keihi";
        }
        if($key == "keihifileinsert_5_button")
        {
            $_SESSION['filename'] = "keihifileinsert_5";
            $url = "keihifileinsert";
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