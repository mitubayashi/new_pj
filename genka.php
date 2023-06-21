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
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $list = "";
    $sql = array();
    $inputcheck_data = array();
    $counter = 0;
    
    //ブラウザバック、リロード対策
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //原価入力欄作成    
    $sql[0] = $SQL_ini[$filename]['select_sql'];
    $sql[0] .= ' AND LUSERNAME IS NOT NULL AND LUSERPASS IS NOT NULL ORDER BY STAFFID ASC;';
    $sql[1] = $SQL_ini[$filename]['count_sql'];
    $sql[1] .= ' AND LUSERNAME IS NOT NULL AND LUSERPASS IS NOT NULL;';
    $list .= '<div style="margin-top: 5px; margin-bottom: 5px;">';
    $list .= makeList_count($sql[1]);
    $list .= makeGenka_item($sql);
    $list .= '</div>';
    
    //入力チェック情報取得
    $con = dbconect();
    $result = $con->query($sql[0]);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $inputcheck_data[$counter] = '1402_'.$result_row['4CODE'];       
        $counter++;
        $inputcheck_data[$counter] = '1403_'.$result_row['4CODE'];       
        $counter++;
    }
    
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title><?php echo $form_ini[$filename]['title']; ?></title>
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <script src='./js/modal.js'></script>
        <script src='./js/inputcheck.js'></script>
        <script>
            var inputcheck_data = JSON.parse('<?php echo json_encode($inputcheck_data); ?>');
            var filename = '<?php echo $filename; ?>';
        </script>
    </head>
    <body>
        <div class="title"><?php echo $form_ini[$filename]['title']; ?></div>          
        <div class="body_area">
            <form action='listJump.php' method='post'>                 
                <?php echo $list; ?>
                <input type="submit" name = "genka" value = "設定" class="list_button" onclick="return genka_setting_check();">            
            </form>
        </div>
        <form action='pageJump.php' method='post'>
            <?php echo makebutton(); ?>
        </form>
    </body>
</html>