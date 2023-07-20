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
    
    //選択内容表示
    $list .= "<table>";
    $list .= "<tr><td>プロジェクトコード</td><td>".$_SESSION['pjagain']['PJCODE']."</td></tr>";
    $list .= "<tr><td>プロジェクト名</td><td>".$_SESSION['pjagain']['PJNAME']."</td></tr>";
    $list .= "</table>";
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title>PJ終了キャンセル処理チェック</title>
        <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <script>
            window.onload = function(){
                var judge = '<?php echo $judge ?>';
                if(judge)
                {
                    if(confirm("処理内容正常確認。\nプロジェクトを終了キャンセルしますがよろしいですか？"))
                    {
                        location.href = "./pjagainComp.php";
                    }
                }
        }
        </script>
    </head>
    <body>
        <div class="title">PJ終了キャンセル処理チェック</div>
        <div class="body_area">
            <form action="listJump.php" method="post">
                <?php echo $list; ?>
                <input type="submit" name = "pjagain_5_button" value = "戻る" class="list_button">
            </form>    
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>