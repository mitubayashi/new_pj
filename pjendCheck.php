<?php
	session_start(); 
	header('Expires:-1'); 
	header('Cache-Control:'); 
	header('Pragma:'); 
	header('Content-type: text/html; charset=Shift_JIS'); 
?>
<?php
    //�����ݒ�
    require_once("f_Construct.php");
    require_once ("f_Form.php");
    require_once ("f_Button.php");
    require_once ("f_SQL.php");
    require_once ("f_DB.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
    
    //�ϐ�
    $list = "";
    $judge = true;
    
    //�u���E�U�o�b�N�A�����[�h�΍�
    start();
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //�v���W�F�N�g�`�F�b�N
    $con = dbconect();
    $error = pjCheck($_SESSION['pjend']);
    
    //���茋�ʕ\��
    if(count($error) > 0)
    {
        $judge = false;
        //�莞���Ԓ��߃G���[�ꗗ�쐬
        $counter = 1;
        $list .= "<div class='list_scroll' style='max-height: 35%; margin-top:10px;'>";
        $list .= "<table>";
        $list .= "<tr><th>No</th><th>���t</th><th>��Ǝ�</th><th>�H��</th><th>����</th></tr>";
        for($i = 0; $i < count($error); $i++)
        {
            $list .= "<tr>";
            $list .= "<td>".$counter."</td>";
            $list .= "<td>".$error[$i]['SAGYOUDATE']."</td>";
            $list .= "<td>".$error[$i]['STAFFNAME']."</td>";
            $list .= "<td>".$error[$i]['KOUTEINAME']."</td>";
            $list .= "<td>".$error[$i]['GENIN']."</td>";
            $counter++;
        }
        $list .= "</table>";
        $list .= "</div>";
    }
    
    //�H����񖢓o�^�G���[
    if(isset($_SESSION['error_code5']))
    {
        $counter = 1;
        $judge = false;
        $list .= "<div class='list_scroll' style='max-height: 35%; margin-top: 10px;'>";
        $list .= "<table>";
        $code5 = $_SESSION['error_code5'];
        $genin = $_SESSION['error_GENIN'];
        unset($_SESSION['error_code5']);
        unset($_SESSION['error_GENIN']);
        $list .= "<tr><th>No</th><th>�v���W�F�N�g�R�[�h</th><th>�v���W�F�N�g��</th><th>�I�����t</th><th>����</th></tr>";
        for($i = 0; $i < count($code5); $i++)
        {
            $list .= "<tr>";
            $list .= "<td>".$counter."</td>";
            //PJCODE�ƃv���W�F�N�g����\������
            $sql = "SELECT 5CODE,KOKYAKUID,KOKYAKUNAME,TEAMID,TEAMNAME,ANKENID,EDABAN,5STARTDATE,substring(KOKYAKUID,1,2) AS PERIOD,CONCAT(KOKYAKUID,'-',TEAMID,'-',ANKENID,'-',EDABAN) AS PJCODE,PJNAME,CHAEGE,date_format(URIAGEMONTH, '%Y-%m') AS URIAGEMONTH FROM projectinfo AS projectinfo ";
            $sql .= "LEFT JOIN kokyakuinfo AS kokyakuinfo ON projectinfo.12CODE = kokyakuinfo.12CODE ";
            $sql .= "LEFT JOIN teaminfo AS teaminfo ON projectinfo.13CODE = teaminfo.13CODE WHERE 5CODE = '".$code5[$i]."' ;";
            $result = $con->query($sql);
            while($result_row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $list .= "<td>".$result_row['PJCODE']."</td>";
                $list .= "<td>".$result_row['PJNAME']."</td>";                
            }
            $list .= "<td>".$_SESSION['pjend']['day'.$code5[$i]]."</td>";
            $list .= "<td>".$genin[$i]."</td>";
            $list .= "</tr>";
            $counter++;
        }
        $list .= "</table>";
        $list .= "</div>";
    }
    
    //���펞�͑I������PJ�̏���\������
    if($judge)
    {
        $counter = 1;
        $list .= "<div class='list_scroll' style='margin-top: 10px;'>";
        $list .= "<table>";
        $list .= "<tr><th>No</th><th>�v���W�F�N�g�R�[�h</th><th>�v���W�F�N�g��</th><th>�I�����t</th></tr>";
        $pjid = $_SESSION['pjend']['checkbox'];
        for($i = 0; $i < count($pjid); $i++)
        {
            $list .= "<tr>";
            $list .= "<td>".$counter."</td>";
            
            //�v���W�F�N�g�R�[�h�ƃv���W�F�N�g��
            $sql = "SELECT 5CODE,KOKYAKUID,KOKYAKUNAME,TEAMID,TEAMNAME,ANKENID,EDABAN,5STARTDATE,substring(KOKYAKUID,1,2) AS PERIOD,CONCAT(KOKYAKUID,'-',TEAMID,'-',ANKENID,'-',EDABAN) AS PJCODE,PJNAME,CHAEGE,date_format(URIAGEMONTH, '%Y-%m') AS URIAGEMONTH FROM projectinfo AS projectinfo ";
            $sql .= "LEFT JOIN kokyakuinfo AS kokyakuinfo ON projectinfo.12CODE = kokyakuinfo.12CODE ";
            $sql .= "LEFT JOIN teaminfo AS teaminfo ON projectinfo.13CODE = teaminfo.13CODE WHERE 5CODE = '".$pjid[$i]."' ;";
            $result = $con->query($sql);
            while($result_row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $list .= "<td>".$result_row['PJCODE']."</td>";
                $list .= "<td>".$result_row['PJNAME']."</td>";                
            }
            $list .= "<td>".$_SESSION['pjend']['day'.$pjid[$i]]."</td>";
            $list .= "</tr>";
            $counter++;
        }
        $list .= "</table>";
        $list .= "</div>";
    }
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title>PJ�I�������`�F�b�N</title>
        <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <script>
            window.onload = function(){
                var judge = '<?php echo $judge ?>';
                if(judge)
                {
                    if(confirm("�������e����m�F�B\n�v���W�F�N�g���I�����܂�����낵���ł����H"))
                    {
                        location.href = "./pjendComp.php";
                    }
                }
        }
        </script>
    </head>
    <body>
        <div class="title">PJ�I�������`�F�b�N</div>
        <div class="body_area">
            <form action="listJump.php" method="post">
                <?php echo $list; ?>
                <input type="submit" name = "pjend_5_button" value = "�߂�" class="list_button" style="margin-top: 10px;">
            </form>      
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>