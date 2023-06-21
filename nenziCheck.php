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
    $error = array();
    $endmonth = array();
    $judge = true;
    $monthjudge = false;
    
    //ブラウザバック、リロード対策
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //年次処理済みチェック
    $con = dbconect();
    $sql = "SELECT *FROM endperiodinfo WHERE PERIOD = '".$_SESSION['nenzi']['period']."';";
    $result = $con->query($sql);
    if($result->num_rows > 0)
    {
        $judge = false;
        $list .= $_SESSION['nenzi']['period']."期は既に年次処理済みです。<br>";
    }
    
    //月次チェック
    if($judge)
    {
        $arrayMonth = explode(',',"6,7,8,9,10,11,12,1,2,3,4,5");
        $error_month = "";
        $count = 0;
        $sql = "SELECT *FROM endmonthinfo WHERE PERIOD = '".$_SESSION['nenzi']['period']."';";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $endmonth[$count] = $result_row['MONTH'];
            $count++;
        }
        for($i = 0; $i < 12; $i++)
        {
            for($j = 0; $j < count($endmonth); $j++)
            {
                if($arrayMonth[$i] == $endmonth[$j])
                {
                    $monthjudge = true;
                }
            }
            if(!$monthjudge)
            {
                //月次を行っていない月を集計
                $error_month .= $arrayMonth[$i].',';
                $judge = false;
            }
            $monthjudge = false;
        }
        $error_month = rtrim($error_month,',');
        if($error_month != "")
        {
            $list .= $error_month."月の月次処理が完了していません。<br>";
        }
    }
    //PJチェック
    if($judge)
    {
        $error = nenjiCheck($_SESSION['nenzi']['period']);
    }
    
    //PJエラー表示
    if(count($error) > 0)
    {
        $judge = false;
        $counter = 1;
        $list .= "以下のプロジェクトの終了処理が行われていません。";
        $list .= "<div class='list_scroll' style='margin-top:10px;'>";
        $list .= "<table>";
        $list .= "<tr><th>No</th><th>プロジェクトコード</th><th>プロジェクト名</th></tr>";
        for($i = 0; $i < count($error); $i++)
        {
            $list .= "<tr>";
            $list .= "<td>".$counter."</td>";
            $list .= "<td>".$error[$i]['PJCODE']."</td>";
            $list .= "<td>".$error[$i]['PJNAME']."</td>";
            $list .= "</tr>";
            $counter++;
        }
        $list .= "</table>";
        $list .= "</div>";
    }
?>
<html>
    <head>
        <title>年次処理チェック</title>
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
                    if(confirm("処理内容正常確認。\年次処理を行いますがよろしいですか？"))
                    {
                        location.href = "./nenziComp.php";
                    }
                }
        }
        </script>
    </head>
    <body>
        <div class="title">年次処理チェック</div>
         <div class="body_area">
            <form action="listJump.php" method="post">
                <table>
                    <tr><td>年次実行期</td><td><?php echo $_SESSION['nenzi']['period']; ?>期</td></tr>
                </table>
                <?php echo $list; ?>
                <input type="submit" name="nenzi_5_button" value="戻る" class="list_button" style="margin-top: 10px;">
            </form>
         </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>