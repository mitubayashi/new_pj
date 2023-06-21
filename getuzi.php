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
    require_once ("f_Form.php");
    require_once ("f_Button.php");
    require_once ("f_SQL.php");
    require_once ("f_DB.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $list = "";
    
    //ブラウザバック、リロード対策
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //月次処理画面HTML作成
    $list .= makeEndMonth();
    
    //現在の期と月を取得
    $today = explode('/',date("Y/m/d"));
    if($today[1] == '6')
    {
        $post['period'] = getperiod($today[1],$today[0]) - 1;
    }
    else
    {
        $post['period'] = getperiod($today[1],$today[0]);
    }
    $post['month'] = $today[1]-1;
    
?>
<html>
    <head>
        <title>月次処理</title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./jquery/jquery-1.8.3.min.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
            function output_getuzicsv()
            {
                var period = document.getElementById('period').value;
                var month = document.getElementById('month').value;
                location.href='download_csv.php?filename=getuzi_5&period=' + period + '&month=' + month;
            }
        </script>
    </head>
    <body>
        <div class="title">月次処理</div>
        <div class="body_area">
            <form action="listJump.php" method="post">
                前回実施月：<?php echo getuzi_rireki(); ?>
                <table>
                    <tr>
                        <td>月次処理対象期</td>
                        <td><?php echo period_pulldown_set($post);  ?></td>
                    </tr>
                    <tr>
                        <td>月次処理対象月</td>
                        <td><?php echo month_pulldown_set($post); ?></td>
                    </tr>
                </table>
                <?php echo $list; ?>
                <input type='submit' name='getuzi' value = '月次処理' class='list_button' onclick='return check();'>
                <input type ='button' class='list_button' value = 'CSVファイル生成' style ='width:140px;' onclick='output_getuzicsv();'>
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>