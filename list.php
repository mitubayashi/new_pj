<?php
    session_start();
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
    
    //定数
    $filename = $_SESSION['filename'];
    $isCSV = $form_ini[$filename]['isCSV'];
    $filename_array = explode('_',$filename);
    $filename_insert = $filename_array[0]."_1";
    
    //変数
    $list = "";
    $sech_modal_html = "";
    $startdate = "";
    
    //ブラウザバック、リロード対策
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //SQL作成処理
    if(isset($_SESSION['list']) == false)
    {
        $_SESSION['list'] = array();
    }
    $sql = itemListSQL($_SESSION['list']);
    $sql = SQLsetOrderby($_SESSION['list'],$sql);    
    
    //一覧表作成処理
    $list .= makeList_item($sql,$_SESSION['list']);
    
    //検索条件画面作成処理
    if($form_ini[$filename]['sech_form_num'] != "")
    {
        $sech_modal_html = makeModalHtml($_SESSION['list']);
    }
    
    //期首日取得
    $startdate = get_startdate();

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
            var startdate = '<?php echo $startdate ?>';
        </script>
    </head>
    <body>
        <div class="title"><?php echo $form_ini[$filename]['title']; ?></div>
        <form action='pageJump.php' method='post'>
            <div class="body_area">
                <?php echo $list; ?>
            </div>
            <?php echo makebutton(); ?>
        </form>
        <form action="listJump.php" method="post">
            <dialog id="dialog" class="modal_body">     
                <?php echo $sech_modal_html; ?>
            </dialog>
        </form>
    </body>
</html>