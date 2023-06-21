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
    
    //定数
    $filename = $_SESSION['filename'];
        
    //変数
    $errordata = array();
    $message = '';
    $message2 = '';
    $message3 = '';
    $list = '';
        
    //ブラウザバック対策
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //チェック処理
    $errordata = teijicheck($_SESSION['teiji']);
    
    //エラーリスト作成
    if(!empty($errordata))
    {
        $message = "<br><a style='color:red;'>修正が必要なデータがあります</a>";
        $message2 = "<a style='color:red;'>以下のデータを修正してください</a>";
        $message3 = "<table style='margin:auto;'><tr><td style='width:100px;;'>エラー理由</td><td>1.定時時間が7.75でない</td></tr>
                                  <tr><td></td><td>2.総作業時間が24時間を超えている</td></tr></table>";
        $list = make_teijicomplist($errordata);
    }
    else
    {
        $message = "<br><a>修正が必要なデータはありませんでした</a>";
    }
    
    //日付情報
    if($_SESSION['teiji']['startdate'] == "")
    {
        $_SESSION['teiji']['startdate'] = date('Y-m-d');
    }
    if($_SESSION['teiji']['enddate'] == "")
    {
        $_SESSION['teiji']['enddate'] = date('Y-m-d');
    }
?>
<html>
    <head>
        <title>定時チェック完了</title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./js/progress.js'></script>
        <script src='./jquery/jquery-1.8.3.min.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
        </script>
    </head>
    <body>
        <div class="title">定時チェック完了</div>
        <CENTER>
        <div class="body_area">
            <?php echo $message; ?>
            <?php
                echo "<fieldset style='width:50%; margin:auto;'>";
                echo "<legend>チェック条件</legend>";
                echo "<table style='margin:auto;'><tbody>";
                if($_SESSION['teiji']['startdate'] == $_SESSION['teiji']['enddate'])
                {
                    echo "<tr><td style='width:80px'>開始日付<br>終了日付</td>";
                    echo "<td>全期間</td></tr>";
                }
                else
                {
                    $startdate = explode('-',$_SESSION['teiji']['startdate']);
                    $enddate = explode('-',$_SESSION['teiji']['enddate']);
                    echo "<tr><td style='width:80px'>開始日付</td><td>".$startdate[0]."年".$startdate[1]."月".$startdate[2]."日</td></tr>";
                    echo "<tr><td>終了日付</td><td>".$enddate[0]."年".$enddate[1]."月".$enddate[2]."日</td></tr>";
                }
                echo "<tr><td>社員</td><td>".$_SESSION['teijicheck']['syain'][0]."</td></tr>";
                for($i = 1 ; $i < count($_SESSION['teijicheck']['syain']) ; $i++)
                {
                    echo "<tr><td></td><td>".$_SESSION['teijicheck']['syain'][$i]."</td></tr>";
                }
                echo "</table></tbody>";
                echo "</fieldset>";
            ?>
            <form name="insert" action="listJump.php" method="post">
                <?php echo $message2; ?>
                <?php echo $list; ?>
                <?php echo $message3; ?>
                <input type ='submit' name = 'teiji_5_button' class='list_button' value = '戻る'>
                <input type='hidden' name='startdate' id='startdate' value='<?php echo $_SESSION['teiji']['startdate']; ?>'>
                <input type='hidden' name='enddate' id='enddate' value='<?php echo $_SESSION['teiji']['enddate']; ?>'>
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
        </CENTER>
    </body>
</html>