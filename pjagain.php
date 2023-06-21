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
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $sql = array();
    $list = "";
    $select_list = "";
    
    //ブラウザバック、リロード対策
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //一覧表SQL作成処理
    if(isset($_SESSION['list']) == false)
    {
        $_SESSION['list']['limitstart'] = 0;
    }
    $sql = itemListSQL($_SESSION['list']);
    $sql = SQLsetOrderby($_SESSION['list'],$sql);
    
    //表示件数表示
    $list .= '<div style="margin-top: 5px; margin-bottom: 5px;">';
    $list .= makeList_count($sql[1]);
    
    //一覧表ボタン作成処理
    $list .= makeList_button();
    $list .= '</div>';
    
    //一覧表作成処理
    $list .= make_pjagain_list($sql,$_SESSION['list']);
    
    //検索条件画面作成処理
    if($form_ini[$filename]['sech_form_num'] != "")
    {
        $sech_modal_html = makeModalHtml($_SESSION['list']);
    }
    
    //選択PJ一覧作成処理
    $select_list .= "<table>";
    $select_list .= "<tr><td>プロジェクトコード</td><td><input type='text' size='30' id='PJCODE' name='PJCODE' value='' class='form_text disabled'></td></tr>";
    $select_list .= "<tr><td>プロジェクト名</td><td><input type='text' size='60' id='PJNAME' name='PJNAME' value='' class='form_text disabled'></td></tr>";
    $select_list .= "</table>";
    $select_list .= "<input type='submit' value='PJ終了キャンセル' name='pjagain' onclick='return pjagain_check();'>";
    $select_list .= "<input type='hidden' value='' id='5CODE' name='5CODE'>";
?>
<html>
    <head>
        <title>PJ終了キャンセル処理</title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./js/pjagain.js'></script>
        <script src='./jquery/jquery-1.8.3.min.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
            
            //ラジオボタン、チェックボックスのあるセルにカーソルを合わせた時の動作
            function mouseMove(row)
            {
                var tabledata = document.getElementById("radiolist");
                for(var i = 0; i < tabledata.rows[row].cells.length; i++)
                {
                    tabledata.rows[row].cells[i].style.backgroundColor = "#f7ca79";
                }
            }

            //ラジオボタン、チェックボックスのあるセルからカーソルが離れた時の動作
            function mouseOut(row)
            {
                var tabledata = document.getElementById("radiolist");
                for(var i = 0; i < tabledata.rows[row].cells.length; i++)
                {
                    tabledata.rows[row].cells[i].style.backgroundColor = "";
                }
            }
        </script>
    </head>
    <body>
        <div class="title">PJ終了キャンセル処理</div>
        <div class="body_area">
            <form action="listJump.php" method="post">
                <?php echo $list; ?>
                <dialog id="dialog" class="modal_body"> 
                    <?php echo $sech_modal_html; ?>
                </dialog>
                <?php echo $select_list; ?>
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>