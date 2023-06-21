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

    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $calendar_html = "";
    
    //ブラウザバック、リロード対策
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //カレンダー作成処理
    $calendar_html = makeCalendarHtml();
    
    //最後に締め処理された月を取得
    $min = lastEndMonth();
    
    //工数情報取得
    $workDate = get_calendar_data("");
    $workDate_key = array_keys($workDate);
    $workDate_keys = json_encode($workDate_key);    

?>
<html>
    <head>
        <title>TOP</title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/modal.js'></script>
        <script src='./js/inputcheck.js'></script>
        <script src='./js/progress.js'></script>
        <script src='./jquery/jquery-1.8.3.min.js'></script>
        <script src='./jquery/jquery.corner.js'></script>
        <script src='./jquery/jquery.flatshadow.js'></script>
        <script src='./jquery/button_size.js'></script>
        <script>
            let worklist = <?php echo $workDate_keys; ?>;
            let progress_data = JSON.parse('<?php echo json_encode($workDate); ?>');
        </script>
    </head>
    <body>
        <div class="title">TOP</div>
        <div class="body_area">
            <form action='TOPexe.php' method='post'>
                <?php echo $calendar_html; ?>
                <?php
                    echo "<dialog id='dgl'>";
                    echo "<div class='dlgtitle'>コピー先選択</div>";
                    echo "<table class='dlgtable'>";
                    echo "<tr><td style='width:40%;'>開始日付</td><td><input type='date' id='startdate' min='".$min."' class='form_text'></td></tr>";
                    echo "<tr><td style='width:40%;'>終了日付</td><td><input type='date' id='enddate' min='".$min."' class='form_text'></td></tr>";
                    echo "</table>";
                    echo "<input type='button' class='dlgbtn' value='戻る' style='margin: 10px 0 0 25px;' onclick='document.getElementById(".'"dgl"'.").close()'>";
                    echo "<input type='button' class='dlgbtn' value='登録' style='margin: 10px 25px 0 0; float: right;' onclick='copy()'>";
                    echo "</dialog>";
                    echo "<br><br>";
                ?>
            </form>
        </div>
        <form action='pageJump.php' method='post'>
            <?php echo makebutton(); ?>
        </form>
    </body>
</html>