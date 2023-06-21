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
    require_once ("f_DB.php");
    require_once ("f_Form.php");
    require_once ("f_SQL.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
        
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $inputcheck_data = array();
    
    //ブラウザバック、リロード対策
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //入力欄作成
    if(isset($_POST) && $filename != "TOP_1")
    {
        $_SESSION['insert'] = $_POST;
    }
    else if($filename != "TOP_1")
    {
        $_SESSION['insert'] = array();
    }
    
    if($filename == "PROGRESSINFO_1" || $filename == "TOP_1")
    {
        $form = makePROGRESSlist($_SESSION['insert'],$_SESSION['user']);
    }
    else
    {
        $form = makeformInsert_set($_SESSION['insert']);
        $inputcheck_data = get_inputcheck_data("insert_form_num");
    }
    //情報取得
    $kokyaku = get_kokyaku();
    $team = get_team();
    $syain = get_syain();
    $koutei = get_koutei();
?>
<html>
    <head>
        <title><?php echo $form_ini[$filename]['title']; ?></title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./js/progress.js'></script>
        <script src='./jquery/jquery-1.8.3.min.js'></script>
        <script>
            var inputcheck_data = JSON.parse('<?php echo json_encode($inputcheck_data); ?>');
            var filename = '<?php echo $filename; ?>';
            window.onload = function(){
                if(filename == 'PJTOUROKU_1')
                {
                    kingaku_goukei();
                    period_select();
                }
                if(filename == 'PROGRESSINFO_1')
                {
                    time_total();
                }
            }
        </script>
    </head>
    <body>
        <div class="title"><?php echo $form_ini[$filename]['title']; ?></div>
        <div class="body_area">
            <form name="insert" action="listJump.php" method="post">
                <?php echo $form; ?>
                <?php
                    if($filename == "PROGRESSINFO_1" || $filename == "TOP_1")
                    {
                        echo '<div style="width: 1200px;">';
                        echo '<input type="submit" name = "insert" value = "登録" class="list_button" onclick="return progress_check();">';
                        echo '<input type="submit" name = "back" value = "戻る" class="list_button" onClick ="isCancel = true;">';
                        echo '<input type="text" id="TEIZI_GOUKEI" name="TEIZI_GOUKEI" class="form_text disabled" style="width:90px; margin-left: 730px;">';
                        echo '<input type="text" id="ZANGYOU_GOUKEI" name="ZANGYOU_GOUKEI" class="form_text disabled" style="width:90px; margin-left: 2px;">';
                        echo '</div>';
                    }
                    else
                    {
                        echo '<div style="width: 1200px;">';
                        echo '<input type="submit" name = "insert" value = "登録" class="list_button" onclick="return check();">';
                        echo '<input type="submit" name = "cancel" value = "クリア" class="list_button" onClick ="isCancel = true;">';
                        echo '<input type="submit" name = "back" value = "戻る" class="list_button" onClick ="isCancel = true;">';     
                        echo '</div>';
                    }
                ?>
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
        <input type='hidden' id='kokyakulist' value='<?php echo $kokyaku; ?>'>
        <input type='hidden' id='teamlist' value='<?php echo $team; ?>'>
        <input type='hidden' id='syainlist' value='<?php echo $syain; ?>'>
        <input type='hidden' id='kouteilist' value='<?php echo $koutei; ?>'>
    </body>
</html>