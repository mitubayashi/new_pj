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
        
    //ブラウザバック、リロード対策
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;

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
        <script>
            var filename = '<?php echo $filename; ?>';
            function check()
            {
                var judge = true;
                if(document.getElementsByName('inpath')[(document.getElementsByName('inpath').length-1)].value == "")
                {
                        judge = false;
                        alert('ファイルを選択して下さい');
                }
                return judge;
            }
        </script>
    </head>
    <body>
        <div class="title"><?php echo $form_ini[$filename]['title']; ?></div>
        <div class="body_area">
            <form action="listJump.php" method="post" enctype="multipart/form-data">
                <?php
                    echo '<br><input type="file" name="inpath" size="300"><br><br>';
                    echo '<input type="submit" name = "fileinsert" value = "取込" class="list_button" onclick="return check();">';
                    echo "<input type ='submit' value = '戻る' name = 'back' class = 'list_button' onClick ='isCancel = true;'>";
                    echo "<br><br><br>";
                    echo "<FONT color='red'>ユーザ単位の工数情報が取り込みできます。取り込み情報は</FONT><br><br>";
                    echo "<FONT color='red'>社員番号、日付(yyyy/mm/dd)、PJコード(12桁ハイフンあり)、工程番号、定時時間、残業時間<br>をCSV形式で作成してください。</FONT><br><br>";
                    echo "<FONT color='red'>同一日付にすでに登録データが存在する場合は存在しているデータは破棄され取り込みデータが正しいデータとして登録されます。</FONT><br><br>";
                ?>
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>