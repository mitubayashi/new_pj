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
    
    //定数
    $filename = $_SESSION['filename'];
    $button_onclick = "";
    
    //ブラウザバック、リロード対策
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;

?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title><?php echo $form_ini[$filename]['title']; ?></title>
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <script src='./js/modal.js'></script>
        <script src='./js/inputcheck.js'></script>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script>
            var filename = '<?php echo $filename; ?>';
            function csv_download()
            {
                //document.getElementById("loading").style.visibility ="visible";
                location.href='download_csv.php?filename=' + filename;      
                //document.getElementById("loading").style.visibility ="hidden";
            }
        </script>
    </head>
    <body>
        <div class="title"><?php echo $form_ini[$filename]['title']; ?></div>
        <div class="body_area">
            <input type='button' class='list_button' value='CSV出力' onclick="csv_download();">
        </div>
        <form action='pageJump.php' method='post'>
            <?php echo makebutton(); ?>
        </form>
        <div class='loading' id='loading'>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="48" height="48" fill="#2589d0">
                <circle cx="12" cy="2" r="2" opacity=".1">
                    <animate attributeName="opacity" from="1" to=".1" dur="1s" repeatCount="indefinite" begin="0"/>
                </circle>
                <circle transform="rotate(45 12 12)" cx="12" cy="2" r="2" opacity=".1">
                    <animate attributeName="opacity" from="1" to=".1" dur="1s" repeatCount="indefinite" begin=".125s"/>
                </circle>
                <circle transform="rotate(90 12 12)" cx="12" cy="2" r="2" opacity=".1">
                    <animate attributeName="opacity" from="1" to=".1" dur="1s" repeatCount="indefinite" begin=".25s"/>
                </circle>
                <circle transform="rotate(135 12 12)" cx="12" cy="2" r="2" opacity=".1">
                    <animate attributeName="opacity" from="1" to=".1" dur="1s" repeatCount="indefinite" begin=".375s"/>
                </circle>
                <circle transform="rotate(180 12 12)" cx="12" cy="2" r="2" opacity=".1">
                    <animate attributeName="opacity" from="1" to=".1" dur="1s" repeatCount="indefinite" begin=".5s"/>
                </circle>
                <circle transform="rotate(225 12 12)" cx="12" cy="2" r="2" opacity=".1">
                    <animate attributeName="opacity" from="1" to=".1" dur="1s" repeatCount="indefinite" begin=".625s"/>
                </circle>
                <circle transform="rotate(270 12 12)" cx="12" cy="2" r="2" opacity=".1">
                    <animate attributeName="opacity" from="1" to=".1" dur="1s" repeatCount="indefinite" begin=".75s"/>
                </circle>
                <circle transform="rotate(315 12 12)" cx="12" cy="2" r="2" opacity=".1">
                    <animate attributeName="opacity" from="1" to=".1" dur="1s" repeatCount="indefinite" begin=".875s"/>
                </circle>
            </svg>
        </div>
    </body>
</html>