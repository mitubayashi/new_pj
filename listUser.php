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
    require_once ("f_Form.php");
    require_once ("f_SQL.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //ブラウザバック、リロード対策
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $sql = array();
    $list = "";
    $sech_modal_html = "";
    
    //SQL作成処理
    if(isset($_SESSION['list']) == false)
    {
        $_SESSION['list']['limitstart'] = 0;
    }
    $sql = listUser_SQL($_SESSION['list']);
    
    //表示件数表示
    $list .= '<div style="margin-top: 5px; margin-bottom: 5px;">';
    $list .= makeList_count($sql[1]);
    
    //一覧表ボタン作成処理
    $list .= "<input type='button' value='検索条件' class='list_button' onclick='open_sech_modal();'>";
    $list .= '<input type="submit" name="insertUser_5_button" class="list_button" value="新規作成">';
    $list .= '</div>';
    
    //一覧表作成処理
    $list .= make_listUser($sql,$_SESSION['list']);

    //検索条件画面作成処理
    $sech_modal_html = make_listUser_modal($_SESSION['list']);
    
?>
<html>
    <head>
    <link rel="icon" type="image/png" href="./img/favicon.ico">
    <title><?php echo $form_ini[$filename]['title']; ?></title>
    <link rel="stylesheet" type="text/css" href="./css/list_css.css">
    <script src='./js/modal.js'></script>
    <script src='./js/inputcheck.js'></script>
    <script>
        var filename = '<?php echo $filename; ?>';
    </script>
    </head>
    <body>
        <form action='pageJump.php' method='post'>
            <div class="title"><?php echo $form_ini[$filename]['title']; ?></div>
            <div class="body_area">
                <?php echo $list; ?>
            </div>
            <?php echo makebutton(); ?>
        </form>
        <form action="listUserJump.php" method="post">
            <dialog id="dialog" class="modal_body">     
                <?php echo $sech_modal_html; ?>
            </dialog>
        </form>
    </body>
</html>