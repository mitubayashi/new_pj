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
    $error = array();
    $endmonth = array();
    $judge = true;
    $monthjudge = false;
    
    //�u���E�U�o�b�N�A�����[�h�΍�
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //�N�������ς݃`�F�b�N
    $con = dbconect();
    $sql = "SELECT *FROM endperiodinfo WHERE PERIOD = '".$_SESSION['nenzi']['period']."';";
    $result = $con->query($sql);
    if($result->num_rows > 0)
    {
        $judge = false;
        $list .= $_SESSION['nenzi']['period']."���͊��ɔN�������ς݂ł��B<br>";
    }
    
    //�����`�F�b�N
    if($judge)
    {
        $arrayMonth = explode(',',"6,7,8,9,10,11,12,1,2,3,4,5");
        $error_month = "";
        $count = 0;
        $sql = "SELECT *FROM endmonthinfo WHERE PERIOD = '".$_SESSION['nenzi']['period']."';";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $endmonth[$count] = $result_row['MONTH'];
            $count++;
        }
        for($i = 0; $i < 12; $i++)
        {
            for($j = 0; $j < count($endmonth); $j++)
            {
                if($arrayMonth[$i] == $endmonth[$j])
                {
                    $monthjudge = true;
                }
            }
            if(!$monthjudge)
            {
                //�������s���Ă��Ȃ������W�v
                $error_month .= $arrayMonth[$i].',';
                $judge = false;
            }
            $monthjudge = false;
        }
        $error_month = rtrim($error_month,',');
        if($error_month != "")
        {
            $list .= $error_month."���̌����������������Ă��܂���B<br>";
        }
    }
    //PJ�`�F�b�N
    if($judge)
    {
        $error = nenjiCheck($_SESSION['nenzi']['period']);
    }
    
    //PJ�G���[�\��
    if(count($error) > 0)
    {
        $judge = false;
        $counter = 1;
        $list .= "�ȉ��̃v���W�F�N�g�̏I���������s���Ă��܂���B";
        $list .= "<div class='list_scroll' style='margin-top:10px;'>";
        $list .= "<table>";
        $list .= "<tr><th>No</th><th>�v���W�F�N�g�R�[�h</th><th>�v���W�F�N�g��</th></tr>";
        for($i = 0; $i < count($error); $i++)
        {
            $list .= "<tr>";
            $list .= "<td>".$counter."</td>";
            $list .= "<td>".$error[$i]['PJCODE']."</td>";
            $list .= "<td>".$error[$i]['PJNAME']."</td>";
            $list .= "</tr>";
            $counter++;
        }
        $list .= "</table>";
        $list .= "</div>";
    }
?>
<html>
    <head>
        <title>�N�������`�F�b�N</title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./jquery/jquery-1.8.3.min.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
            window.onload = function(){
                var judge = '<?php echo $judge ?>';
                if(judge)
                {
                    if(confirm("�������e����m�F�B\�N���������s���܂�����낵���ł����H"))
                    {
                        location.href = "./nenziComp.php";
                    }
                }
        }
        </script>
    </head>
    <body>
        <div class="title">�N�������`�F�b�N</div>
         <div class="body_area">
            <form action="listJump.php" method="post">
                <table>
                    <tr><td>�N�����s��</td><td><?php echo $_SESSION['nenzi']['period']; ?>��</td></tr>
                </table>
                <?php echo $list; ?>
                <input type="submit" name="nenzi_5_button" value="�߂�" class="list_button" style="margin-top: 10px;">
            </form>
         </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>