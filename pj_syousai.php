<?php
    session_start();
    header('Content-type: text/html; charset=Shift_JIS'); 
?>
<?php
    //�����ݒ�
    require_once ("f_Button.php");
    require_once ("f_DB.php");
    require_once ("f_Form.php");
    require_once ("f_SQL.php");
    require_once("f_Construct.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    
    //�ϐ�
    $sql = "";
    $judge = false;
    $list = "";
    
    //5CODE���擾����
    $code_5 = $_GET["code"];
    
    //PJ�̏����擾
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
    
    //�ڍ׏��쐬
    $sql = "SELECT *FROM projectditealinfo left join syaininfo ON projectditealinfo.4CODE = syaininfo.4CODE where 5CODE = ".$code_5.";";
    $result = $con->query($sql) or ($judge = true);
    $list .= '<div>�o�^�Ј����@'.($result->num_rows).'��</div>';
    $list .= '<div class="list_scroll">';
    $list .= '<table>';
    $list .= '<tr><th>�Ј���</th><th>���z</th><th>����</th></tr>';
    
    //�Ј����Ƃ̏����擾
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
        
        //�Ј����Ƌ��z
        $list .= "<td>".$result_row['STAFFNAME']."</td>";
        $list .= "<td align='right'>".$result_row['DETALECHARGE']."</td>";
        
        //����
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
        <div class="title" style="margin-top: 0px;">PJ�ڍ�</div>
        <div class="body_area">
            <table>
                <tr>
                    <td>�v���W�F�N�g�R�[�h</td>
                    <td><input type='text' size='30' class='form_text disabled' value='<?php echo $pjcode; ?>'></td>
                </tr>
                <tr>
                    <td>�v���W�F�N�g��</td>
                    <td><input type='text' size='60' class='form_text disabled' value='<?php echo $pjname; ?>'></td>
                </tr>
            </table>
            <?php echo $list; ?>
            <input type='button' value='����' class="list_button" onclick='close_dailog();' style="margin-top: 2px;">
        </div>
    </body>
</html>