<?php
    session_start();
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
    $startdate = "";
    $enddate = "";
    $checkbox = array();
    
    //ブラウザバック対策
    start();
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //日付入力値
    if(isset($_SESSION['startdate']))
    {
        $startdate = $_SESSION['startdate'];
        unset($_SESSION['startdate']);
    }
    else
    {
        $startdate = date('Y-m-d');
    }
    
    if(isset($_SESSION['enddate']))
    {
        $enddate = $_SESSION['enddate'];
        unset($_SESSION['enddate']);
    }
    else
    {
        $enddate = date('Y-m-d');
    }
    
    //社員選択情報
    if(isset($_SESSION['teiji']['checkbox']))
    {
        $checkbox = $_SESSION['teiji']['checkbox'];
    }
    //社員リスト作成
    $list = make_teiji_list();
?>
<html>
    <head>
        <title>定時チェック処理</title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./js/progress.js'></script>
        <script src='./js/teiji.js'></script>
        <script src='./jquery/jquery-1.8.3.min.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
            var checkbox = JSON.parse('<?php echo json_encode($checkbox); ?>');
            window.onload = function(){
                //選択社員保持
                check_checkbox();
            }

            //ラジオボタン、チェックボックスのあるセルにカーソルを合わせた時の動作
            function mouseMove(row)
            {
                var tabledata = document.getElementById("checkboxlist");
                for(var i = 0; i < tabledata.rows[row].cells.length; i++)
                {
                    tabledata.rows[row].cells[i].style.backgroundColor = "#f7ca79";
                }
            }

            //ラジオボタン、チェックボックスのあるセルからカーソルが離れた時の動作
            function mouseOut(row)
            {
                var tabledata = document.getElementById("checkboxlist");
                for(var i = 0; i < tabledata.rows[row].cells.length; i++)
                {
                    tabledata.rows[row].cells[i].style.backgroundColor = "";
                }
            }
        </script>
    </head>
    <body>
        <div class="title">定時チェック処理</div>
        <div class="body_area">
            <form name="insert" action="listJump.php" method="post">
                <table>
                    <tr>
                        <td>開始日付</td>
                        <td><input type="date" name="startdate" id="startdate" class="form_text" value="<?php echo $startdate; ?>"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>終了日付</td>
                        <td><input type="date" name="enddate" id="enddate" class="form_text" value="<?php echo $enddate; ?>"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td valign="top">社員</td>
                        <td><?php echo $list; ?></td>
                        <td valign="bottom">
                            <input type='button' value='全選択' class='list_button' style='margin-left: 10px;' onclick='checkAll();'><br>
                            <input type='button' value='選択解除' class='list_button' style='margin-left: 10px;' onclick='clearAll();'>
                        </td>
                    </tr>
                </table>
                <input type ='submit' name = 'teijicheck' class = 'list_button' value = '実行' onclick="return teiji_check();">
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>