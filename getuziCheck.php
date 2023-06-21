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
    $judge = true;
    
    //ブラウザバック、リロード対策
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //月次未処理チェック
    $con = dbconect();
    $sql = "SELECT *FROM endmonthinfo WHERE PERIOD = '".$_SESSION['getuzi']['period']."' AND MONTH = '".$_SESSION['getuzi']['month']."';";
    $result = $con->query($sql);
    if($result->num_rows > 0)
    {
        $judge = false;
    }
    
    //PJチェック
    if($judge)
    {
        $error = pjCheck($_SESSION['getuzi']);

        //エラー内容表示
        if(count($error) > 0)
        {
            $judge = false;
            $counter = 1;
            $list .= "<div class='list_scroll' style='margin-top:10px;'>";
            $list .= "<table>";
            $list .= "<tr><th>No</th><th>日付</th><th>作業者</th><th>工程</th><th>原因</th></tr>";
            for($i = 0; $i < count($error); $i++)
            {
                $list .= "<tr>";
                $list .= "<td>".$counter."</td>";
                $list .= "<td>".$error[$i]['SAGYOUDATE']."</td>";
                $list .= "<td>".$error[$i]['STAFFNAME']."</td>";
                $list .= "<td>".$error[$i]['KOUTEINAME']."</td>";
                $list .= "<td>".$error[$i]['GENIN']."</td>";
                $counter++;
            }
            $list .= "</table>";
            $list .= "</div>";
        }
    }
    
?>
<html>
    <head>
        <title>月次処理チェック</title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./jquery/jquery-1.8.3.min.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
            window.onload = function(){
                var judge = '<?php echo $judge ?>';
                if(judge)
                {
                    if(confirm("処理内容正常確認。\月次処理を行いますがよろしいですか？"))
                    {
                        location.href = "./getuziComp.php";
                    }
                }
        }
        </script>
    </head>
    <body>
        <div class="title">月次処理チェック</div>
        <div class="body_area">
            <form action="listJump.php" method="post">
                <table>
                    <tr><td>月次実行月</td><td><?php echo $_SESSION['getuzi']['period']; ?>期　<?php echo $_SESSION['getuzi']['month']; ?>月</td></tr>
                </table>
                <?php echo $list; ?>
                <input type="submit" name="getuzi_5_button" value="戻る" class="list_button" style="margin-top: 10px;">
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>