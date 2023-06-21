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
    
    //変数
    $sql = "";
    $judge = false;
    $list = "";
    
    //5CODEを取得する
    $code_5 = $_GET["code"];
    
    //PJの情報を取得
    $con = dbconect();
    $sql .= "SELECT 5CODE,CONCAT(KOKYAKUID,'-',TEAMID,'-',ANKENID,'-',EDABAN) AS PJCODE,PJNAME,CHAEGE,date_format(URIAGEMONTH, '%Y-%m') AS URIAGEMONTH FROM projectinfo AS projectinfo ";
    $sql .= "LEFT JOIN kokyakuinfo AS kokyakuinfo ON projectinfo.12CODE = kokyakuinfo.12CODE ";
    $sql .= "LEFT JOIN teaminfo AS teaminfo ON projectinfo.13CODE = teaminfo.13CODE WHERE 1=1 ";
    $result = $con->query($sql) or ($judge = true);	
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $pjcode = $result_row['PJCODE'];
        $pjname = $result_row['PJNAME'];
    }
    
    //詳細情報作成
    $sql = "SELECT *FROM projectditealinfo left join syaininfo ON projectditealinfo.4CODE = syaininfo.4CODE where 5CODE = ".$code_5.";";
    $result = $con->query($sql) or ($judge = true);
    $list .= '<div>登録社員数　'.($result->num_rows).'名</div>';
    $list .= '<div class="list_scroll">';
    $list .= '<table>';
    $list .= '<tr><th>社員名</th><th>金額</th><th>時間</th></tr>';
    
    //社員ごとの情報を取得
    $counter = 0;
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        if(($counter % 2) == 1)
        {
            $list .= "<tr class='list_stripe'>";
        }
        else
        {
            $list .= "<tr>";
        }
        
        //社員名と金額
        $list .= "<td>".$result_row['STAFFNAME']."</td>";
        $list .= "<td align='right'>".$result_row['DETALECHARGE']."</td>";
        
        //時間
        $item_sql = "SELECT SUM(TEIZITIME)+SUM(ZANGYOUTIME) AS SAGYOUTIME FROM progressinfo WHERE 6CODE = ".$result_row["6CODE"].";";
        $item_result = $con->query($item_sql) or ($judge = true);
        $item_row = $item_result->fetch_array(MYSQLI_ASSOC);
        if(isset($item_row['SAGYOUTIME']))
        {
            $list .= "<td align='right'>".$item_row['SAGYOUTIME']."</td>";
        }
        else
        {
            $list .= "<td align='right'>0</td>";
        }
        $list .= "</tr>";
        $counter++;
    }
     
    
    $list .= '</table>';
    $list .= '</div>';
    
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <script src='./js/modal.js'></script>
    </head>
    <body>
        <div class="title" style="margin-top: 0px;">PJ詳細</div>
        <div class="body_area">
            <table>
                <tr>
                    <td>プロジェクトコード</td>
                    <td><input type='text' size='30' class='form_text disabled' value='<?php echo $pjcode; ?>'></td>
                </tr>
                <tr>
                    <td>プロジェクト名</td>
                    <td><input type='text' size='60' class='form_text disabled' value='<?php echo $pjname; ?>'></td>
                </tr>
            </table>
            <?php echo $list; ?>
            <input type='button' value='閉じる' class="list_button" onclick='close_dailog();' style="margin-top: 2px;">
        </div>
    </body>
</html>