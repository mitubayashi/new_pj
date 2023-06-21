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
    
    //�����������`�F�b�N
    $con = dbconect();
    $sql = "SELECT *FROM endmonthinfo WHERE PERIOD = '".$_SESSION['getuzi']['period']."' AND MONTH = '".$_SESSION['getuzi']['month']."';";
    $result = $con->query($sql);
    if($result->num_rows > 0)
    {
        $judge = false;
    }
    
    //PJ�`�F�b�N
    if($judge)
    {
        $error = pjCheck($_SESSION['getuzi']);

        //�G���[���e�\��
        if(count($error) > 0)
        {
            $judge = false;
            $counter = 1;
            $list .= "<div class='list_scroll' style='margin-top:10px;'>";
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
    }
    
?>
<html>
    <head>
        <title>���������`�F�b�N</title>
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
                    if(confirm("�������e����m�F�B\�����������s���܂�����낵���ł����H"))
                    {
                        location.href = "./getuziComp.php";
                    }
                }
        }
        </script>
    </head>
    <body>
        <div class="title">���������`�F�b�N</div>
        <div class="body_area">
            <form action="listJump.php" method="post">
                <table>
                    <tr><td>�������s��</td><td><?php echo $_SESSION['getuzi']['period']; ?>���@<?php echo $_SESSION['getuzi']['month']; ?>��</td></tr>
                </table>
                <?php echo $list; ?>
                <input type="submit" name="getuzi_5_button" value="�߂�" class="list_button" style="margin-top: 10px;">
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>