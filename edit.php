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
    require_once ("f_Button.php");
    require_once ("f_DB.php");
    require_once ("f_Form.php");
    require_once ("f_SQL.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $inputcheck_data = array();
    $form = "";
    
    //ブラウザバック、リロード対策
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //入力欄作成
    if($filename == "PROGRESSINFO_3")
    {
        $_SESSION['edit'] = get_progress_data($_SESSION['edit_id']);
        $form = makePROGRESSlist($_SESSION['edit'],$_SESSION['user']);
    }
    elseif($filename == "TOP_3")
    {
        $con = dbconect();
        $sql = "SELECT MIN(7CODE) AS 7CODE FROM progressinfo AS progressinfo ";
        $sql .= "LEFT JOIN projectditealinfo AS projectditealinfo ON progressinfo.6CODE = projectditealinfo.6CODE ";
        $sql .= "WHERE 4CODE = '".$_SESSION['user']['4CODE']."' AND SAGYOUDATE = '".$_SESSION['edit']['704']."';"; 
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $_SESSION['edit_id'] = $result_row['7CODE'];
        }
        $_SESSION['edit'] = get_progress_data($_SESSION['edit_id']);
        $form = makePROGRESSlist($_SESSION['edit'],$_SESSION['user']);
    }
    else
    {
        $_SESSION['edit'] = get_edit_value($_SESSION['edit_id']);
        $form = makeformEdit_set($_SESSION['edit']);
        $inputcheck_data = get_inputcheck_data("edit_form_num");        
    }
    
    //情報取得
    $kokyaku = get_kokyaku();
    $team = get_team();
    $syain = get_syain();
    $koutei = get_koutei();
    
    //月次処理最終日取得
    $min = lastEndMonth();
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
        <script src='./js/style_change.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
            var inputcheck_data = JSON.parse('<?php echo json_encode($inputcheck_data); ?>');
            window.onload = function(){
                if(filename == 'PJTOUROKU_3')
                {
                    kingaku_goukei();
                }
                if(filename == 'PROGRESSINFO_3' || filename == "TOP_3")
                {
                    time_total();
                }
            }
        </script>
    </head>
    <body>
        <div class="title"><?php echo $form_ini[$filename]['title']; ?></div>
        <div class="body_area">
            <form action="listJump.php" method="post">
                <?php echo $form; ?>
                <?php
                    if($filename == "PROGRESSINFO_3" || $filename == "TOP_3")
                    {
                        if($_SESSION['edit']['704'] < $min || $_SESSION['edit']['pjstat'] == "1")
                        {
                            echo '<div style="width: 1200px;">';   
                            echo '<input type="submit" name = "back" value = "戻る" class="list_button" onClick ="isCancel = true;">';   
                            echo '<input type="text" id="TEIZI_GOUKEI" name="TEIZI_GOUKEI" class="form_text disabled" style="width:90px; margin-left: 835px;">';
                            echo '<input type="text" id="ZANGYOU_GOUKEI" name="ZANGYOU_GOUKEI" class="form_text disabled" style="width:90px; margin-left: 2px;">';
                            echo '</div>';
                        }
                        else
                        {
                            echo '<div style="width: 1200px;">';
                            echo '<input type="submit" name = "edit" value = "更新" class="list_button" onclick="return progress_check();">';
                            echo '<input type="submit" name="delete" value="削除" class="list_button" onClick ="isCancel = true;">';
                            echo '<input type="submit" name = "back" value = "戻る" class="list_button" onClick ="isCancel = true;">';   
                            echo '<input type="text" id="TEIZI_GOUKEI" name="TEIZI_GOUKEI" class="form_text disabled" style="width:90px; margin-left: 625px;">';
                            echo '<input type="text" id="ZANGYOU_GOUKEI" name="ZANGYOU_GOUKEI" class="form_text disabled" style="width:90px; margin-left: 2px;">';
                            echo '</div>';
                        }
                    }
                    elseif($filename == 'PJTOUROKU_3')
                    {
                        echo '<div style="width: 1200px;">';
                        echo '<input type="hidden" name="edit_id" value="'.$_SESSION['edit_id'].'">';
                        echo '<input type="submit" name = "edit" value = "更新" class="list_button" onclick="return check();">';
                        echo '<input type="submit" name = "clear" value = "クリア" class="list_button" onClick ="isCancel = true;">';
                        echo '<input type="submit" name = "kobetu_delete" value = "社員別金額削除" class="list_button" style="width: 135px;" onclick="return kobetu_delete_check();">';
                        echo '<input type="submit" name="delete" value="プロジェクト削除" class="list_button" style="width: 135px;">';
                        echo '<input type="submit" name = "back" value = "戻る" class="list_button" onClick ="isCancel = true;">';
                        echo '</div>';                    
                    }
                    else
                    {
                        echo '<div style="width: 1200px;">';
                        echo '<input type="hidden" name="edit_id" value="'.$_SESSION['edit_id'].'">';
                        echo '<input type="submit" name = "edit" value = "更新" class="list_button" onclick="return check();">';
                        echo '<input type="submit" name = "clear" value = "クリア" class="list_button" onClick ="isCancel = true;">';
                        echo '<input type="submit" name="delete" value="削除" class="list_button" onClick ="isCancel = true;">';
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