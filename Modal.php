<?php
    session_start();
    header('Content-type: text/html; charset=Shift_JIS');  	
?>
<?php
    //初期設定
    require_once ("f_Button.php");
    require_once ("f_DB.php");
    require_once ("f_Form.php");
    require_once ("f_SQL.php");
    require_once("f_Construct.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    
    if(($filename == "PROGRESSINFO_1" || $filename == "PROGRESSINFO_3" || $filename == "TOP_1" || $filename == "TOP_3") && isset($_GET['tablenum']))
    {
        $array = explode('_',$_GET['tablenum']);
        $tablenum = $array[0];
        $form_number = $array[1];
    }
    elseif(isset($_GET['tablenum']))
    {
        $tablenum = $_GET['tablenum'];
        $form_number = "";
    }
    else
    {
        $tablenum = $_POST['tablenum'];
        $form_number = $_POST['form_number'];
    }
    $result_num = explode(',',$form_ini[$tablenum]['result_num']);
    $sech_form_num = explode(',',$form_ini[$tablenum]['sech_form_num']);
    
    //変数
    $form = "";
    $list = "";
    $form_drop = "";
    
    //検索条件欄作成
    $form .= "<table>";
    for($i = 0; $i < count($sech_form_num); $i++)
    {
        if(isset($_POST[$sech_form_num[$i]]))
        {
            $value = $_POST[$sech_form_num[$i]];
        }
        else
        {
            $value = "";
        }
        $form .= "<tr>";
        $form .= "<td>".$form_ini[$sech_form_num[$i]]['item_name']."</td>";
        if(($filename == "PROGRESSINFO_1" || $filename == "TOP_1") && $sech_form_num[$i] == '402')
        {
            $form .= "<td><input type='text' value='".$_SESSION['user']['STAFFID']."' size='".$form_ini[$sech_form_num[$i]]['form_size']."' name='".$sech_form_num[$i]."' class='form_text disabled'></td>";
        }
        elseif(($filename == "PROGRESSINFO_1" || $filename == "TOP_1") && $sech_form_num[$i] == '403')
        {
            $form .= "<td><input type='text' value='".$_SESSION['user']['STAFFNAME']."' size='".$form_ini[$sech_form_num[$i]]['form_size']."' name='".$sech_form_num[$i]."' class='form_text disabled'></td>";
        }
        elseif(($filename == "PROGRESSINFO_3" || $filename == "TOP_3") && $sech_form_num[$i] == '402')
        {
            $form .= "<td><input type='text' value='".$_SESSION['edit']['402']."' size='".$form_ini[$sech_form_num[$i]]['form_size']."' name='".$sech_form_num[$i]."' class='form_text disabled'></td>";
        }
        elseif(($filename == "PROGRESSINFO_3" || $filename == "TOP_3") && $sech_form_num[$i] == '403')
        {
            $form .= "<td><input type='text' value='".$_SESSION['edit']['403']."' size='".$form_ini[$sech_form_num[$i]]['form_size']."' name='".$sech_form_num[$i]."' class='form_text disabled'></td>";
        }
        else
        {
            $form .= "<td><input type='text' value='".$value."' size='".$form_ini[$sech_form_num[$i]]['form_size']."' name='".$sech_form_num[$i]."' class='form_text'></td>";
        }
        $form .= "</tr>";
    }
    if(($filename == "PJLIST_2" || $filename == "ENDPJLIST_2" || $filename == "MONTHLIST_2") && $tablenum == "5")
    {
        if(isset($_POST['period']))
        {
            $post['period'] = $_POST['period'];
        }
        else
        {
                //現在の期を取得
                $today = explode('/',date("Y/m/d"));
                $post['period'] = getperiod($today[1],$today[0]);
        }
        $form .= "<tr>";
        $form .= "<td>期</td>";
        $form .= "<td>";
        $form .= period_pulldown_set($post);
        $form .= "</td>";
        $form .= "</tr>";
    }
    $form .= "</table>";
    $form .= "<input type='submit' class='list_button' value='検索'>";
    
    //一覧表作成処理
    $con = dbconect();
    $sql = $SQL_ini[$tablenum]['select_sql'];
    for($i = 0; $i < count($sech_form_num); $i++)
    {
        if((isset($_POST[$sech_form_num[$i]])) && ($_POST[$sech_form_num[$i]] != ""))
        {
            $sql .= " AND ".$form_ini[$sech_form_num[$i]]['column']." LIKE '%".$_POST[$sech_form_num[$i]]."%' "; 
        }
    }
    if($tablenum == "4")
    {
        if($filename == "insertUser_5")
        {
            $sql .= " AND LUSERNAME IS NULL AND LUSERPASS IS NULL ";
        }
        else
        {
            $sql .= " AND LUSERNAME IS NOT NULL AND LUSERPASS IS NOT NULL ";
        }
    }
    if($tablenum == "5")
    {
        if($filename != 'PJLIST_2' && $filename != 'ENDPJLIST_2' && $filename != 'MONTHLIST_2')
        {
            $sql .= " AND 5PJSTAT != '1' ";
        }
    }
    if($tablenum == "6")
    {
        if($filename == "PROGRESSINFO_1" || $filename == "TOP_1")
        {
            $sql .= " AND STAFFID = '".$_SESSION['user']['STAFFID']."' AND 5PJSTAT != '1' ";
        }
        elseif($filename == "PROGRESSINFO_3" || $filename == "TOP_3")
        {
            $sql .= " AND STAFFID = '".$_SESSION['edit']['402']."' AND 5PJSTAT != '1' ";
        }
    }
    
    if(($filename == "PJLIST_2" || $filename == "ENDPJLIST_2" || $filename == "MONTHLIST_2") && $tablenum == "5")
    {
        if($post['period'] < 10)
        {
            $sql .= " AND KOKYAKUID LIKE '%0".$post['period']."%' ";
        }
        else
        {
            $sql .= " AND KOKYAKUID LIKE '%".$post['period']."%' ";
        }
    }
    //ソート条件追記
    $orderby_array = array(' DESC ',' ASC ');
    $orderby_columns = explode(',',$form_ini[$tablenum]['orderby_columns']);
    $orderby_type = explode(',',$form_ini[$tablenum]['orderby_type']);
    $sql .= " ORDER BY ";
    for($i = 0; $i < count($orderby_columns); $i++)
    {
        $sql .= " ".$form_ini[$orderby_columns[$i]]['column']." ".$orderby_array[$orderby_type[$i]];
    }
    
    $result = $con->query($sql);
    $list .= '<div style="margin-top: 5px; margin-bottom: 5px;">';
    $rownum = $result->num_rows;
    $list .= $rownum.'件中 1件~'.$rownum.'件 表示中';
    $list .= '</div>';
    $list .= "<div class='list_scroll'>";
    $list .= "<table id='radiolist'>";
    $list .= "<thead><tr>";
    $list .= "<th>選択</th>";
    //項目名作成
    for($i = 0; $i < count($result_num); $i++)
    {
        $list .= "<th>".$form_ini[$result_num[$i]]['item_name']."</th>";
    }
    $list .= "</tr>";
    $counter = 0;
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $form_value = array();
        $form_value[0] = $result_row[$tablenum.'CODE'];
        $form_value[1] = $tablenum.'01';        
        for($i = 0; $i < count($result_num); $i++)
        {
            $form_value[0] .= '#$'.$result_row[$form_ini[$result_num[$i]]['column']];
            $form_value[1] .= ','.$result_num[$i];
        }
        $list .= "<tr>";
        $onclick = 'select_value("'.$form_value[0].'","'.$form_value[1].'");';
        $list .= "<td onmousemove='mouseMove(this.parentNode.rowIndex,radiolist);' onmouseout='mouseOut(this.parentNode.rowIndex,radiolist);'><label for='radio".$counter."' style='display:block;width:100%;height:100%;'><input type='radio' id='radio".$counter."' name='radio' onclick='".$onclick."' class='radio_style'></td>";
        for($i = 0; $i < count($result_num); $i++)
        {
            $list .= "<td>".$result_row[$form_ini[$result_num[$i]]['column']]."</td>";
        }
        $list .= "</tr>";
        $counter++;
    }
    $list .= "</table>";
    $list .= "</div>";
    
    //選択内容表示欄
    $form_drop .= "<table>";
    for($i = 0; $i < count($result_num); $i++)
    {
        $form_drop .= "<tr>";
        $form_drop .= "<td>".$form_ini[$result_num[$i]]['item_name']."</td>";
        $form_drop .= "<td><input type='text' size='".$form_ini[$result_num[$i]]['form_size']."' class='form_text disabled' id='".$result_num[$i]."_select'></td>";
        $form_drop .= "</tr>";
    }
    $form_drop .= "</table>";
    $form_drop .= "<input type='button' class='list_button' value='決定' onclick='toMainWin(".$tablenum.");'>";
    $form_drop .= "<input type='button' class='list_button' value='閉じる' onclick='close_dailog();'>";
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./js/style_change.js'></script>
        <script>
        </script>
    </head>
    <body>
        <div class="body_area" style="margin-top: 10px;">
            <form action="Modal.php" method="post">
                <?php echo $form; ?>
                <input type="hidden" name="tablenum" value="<?php echo $tablenum; ?>">
                <input type="hidden" name="form_number" id="form_number" value="<?php echo $form_number; ?>">
            </form>
            <?php echo $list; ?>
            <?php echo $form_drop; ?>
            <input type="hidden" id="<?php echo $tablenum.'01_select'; ?>" value="">
        </div>
    </body>
</html>