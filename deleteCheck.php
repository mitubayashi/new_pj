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
    $main_table = $form_ini[$filename]['main_table'];
    
    //変数
    $judge = true;
    $error_type = "";
    $error_data = array();
    $form = "";
    
    //ブラウザバック対策
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //削除チェック
    $con = dbconect();
    if($filename == "PJTOUROKU_3")
    {
        $sql = "SELECT *FROM progressinfo AS a LEFT JOIN projectditealinfo AS b ON a.6CODE = b.6CODE WHERE 5CODE = '".$_SESSION['delete']['edit_id']."';";
        $result = $con->query($sql);
        if($result->num_rows != 0)
        {
            $judge = 2;
        }
    }
    elseif($filename == "KOUTEIINFO_3")
    {
        $sql = "SELECT *FROM progressinfo WHERE 3CODE = '".$_SESSION['delete']['edit_id']."';";
        $result = $con->query($sql);
        if($result->num_rows != 0)
        {
            $judge = false;
        }
    }
    elseif($filename == "KOKYAKUTEAM_3")
    {
        $sql = "SELECT *FROM projectinfo WHERE 13CODE = '".$_SESSION['delete']['edit_id']."';";
        $result = $con->query($sql);
        if($result->num_rows != 0)
        {
            $judge = false;
        }
    }
    elseif($filename == "SYAINNINFO_3")
    {
        $judge = false;
    }
    
    //情報作成
    if($filename == "PROGRESSINFO_3" || $filename == "TOP_3")
    {
        $form = makePROGRESSlist($_SESSION['edit'],$_SESSION['user']);
    }
    else
    {
        $form = makeformEdit_set($_SESSION['edit']);
    }
    
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title>削除確認</title>
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./js/progress.js'></script>
        <script src='./jquery/jquery-1.8.3.min.js'></script>
        <script>
            window.onload = function(){
                var judge = '<?php echo $judge ?>';
                if(judge == 2)
                {
                    if(confirm("工数情報が登録されています。\n工数情報も削除されますがよろしいですか？" +
                        "\n再度確認する場合は「キャンセル」ボタンを押してください。"))
                    {
                        location.href = "./deleteComp.php";
                    }
                }
                else if(judge == true)
                {
                    if(confirm("入力内容正常確認。\n情報削除しますがよろしいですか？" +
                        "\n再度確認する場合は「キャンセル」ボタンを押してください。"))
                    {
                        location.href = "./deleteComp.php";
                    }
                }
                else
                {
                    alert("このマスターは他のテーブルで使用されているので削除できません。");
                }
            }
        </script>
    </head>
    <body>
        <div class="title">削除確認</div>
        <div class="body_area">
            <form action="listJump.php" method="post">
                <?php echo $form; ?>
                <input type="hidden" name="edit_id" value="<?php echo $_SESSION['delete']['edit_id'] ?>">
                <input type="submit" name = "delete" value = "削除" class="list_button">
                <input type="submit" name = "back" value = "戻る" class="list_button" onClick ="isCancel = true;">                    
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>