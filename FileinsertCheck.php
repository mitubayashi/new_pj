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
    require_once ("f_Button.php");
    require_once ("f_DB.php");
    require_once ("f_Form.php");
    require_once ("f_SQL.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
        
    //�ϐ�
    $judge = true;
    $error_list = "";
    
    //�u���E�U�o�b�N�A�����[�h�΍�
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;

    //�t�@�C���`�F�b�N
    $error_data = progress_fileinsert_check($_SESSION['fileinsert']);
    $error_list .= "<div class='list_scroll' style='margin-top: 10px; margin-bottom: 10px;'>";
    $error_list .= "<table>";
    $error_list .= "<tr><th>�Ј��ԍ�</th><th>��Ɠ�</th><th>PJ�R�[�h</th><th>�H���ԍ�</th><th>�莞����</th><th>�c�Ǝ���</th><th>����</th></tr>";
    for($i = 0; $i < count($error_data); $i++)
    {
        $error_list .= "<tr>";
        $error_list .= "<td>".$error_data[$i]['STAFFID']."</td>";
        $error_list .= "<td>".$error_data[$i]['SAGYOUDATE']."</td>";
        $error_list .= "<td>".$error_data[$i]['PJCODE']."</td>";
        $error_list .= "<td>".$error_data[$i]['KOUTEIID']."</td>";
        $error_list .= "<td>".$error_data[$i]['TEIZITIME']."</td>";
        $error_list .= "<td>".$error_data[$i]['ZANGYOUTIME']."</td>";
        $error_list .= "<td>".$error_data[$i]['errormsg']."</td>";
        $error_list .= "</tr>";
        if($error_data[$i]['errormsg'] != '����')
        {
            $judge = false;
        }
    }
    $error_list .= "</table>";
    $error_list .= "</div>";
    
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <title><?php echo $form_ini[$filename]['title']; ?></title>
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./js/progress.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
            var judge = '<?php echo $judge ?>';
            if(judge)
            {
                if(confirm("���͓��e����m�F�B\n���o�^���܂�����낵���ł����H" +
                    "\n�ēx�m�F����ꍇ�́u�L�����Z���v�{�^���������Ă��������B"))
                {
                    location.href = "./FileinsertComp.php";
                }
            }
        </script>
    </head>
    <body>
        <div class="title"><?php echo $form_ini[$filename]['title']; ?></div>
        <div class="body_area">
            <form action="listJump.php" method="post" enctype="multipart/form-data">
                <?php
                    echo $error_list;
                    echo "<input type ='submit' value = '�߂�' name = 'back' class = 'list_button' onClick ='isCancel = true;'>";
                    echo "<input type ='submit' value = '�捞��ʂɖ߂�' name = 'PROGRESSINFO_6_button' class = 'list_button' onClick ='isCancel = true;' style='width: 120px;'>";
                ?>
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>