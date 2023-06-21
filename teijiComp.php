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
    
    //�萔
    $filename = $_SESSION['filename'];
        
    //�ϐ�
    $errordata = array();
    $message = '';
    $message2 = '';
    $message3 = '';
    $list = '';
        
    //�u���E�U�o�b�N�΍�
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //�`�F�b�N����
    $errordata = teijicheck($_SESSION['teiji']);
    
    //�G���[���X�g�쐬
    if(!empty($errordata))
    {
        $message = "<br><a style='color:red;'>�C�����K�v�ȃf�[�^������܂�</a>";
        $message2 = "<a style='color:red;'>�ȉ��̃f�[�^���C�����Ă�������</a>";
        $message3 = "<table style='margin:auto;'><tr><td style='width:100px;;'>�G���[���R</td><td>1.�莞���Ԃ�7.75�łȂ�</td></tr>
                                  <tr><td></td><td>2.����Ǝ��Ԃ�24���Ԃ𒴂��Ă���</td></tr></table>";
        $list = make_teijicomplist($errordata);
    }
    else
    {
        $message = "<br><a>�C�����K�v�ȃf�[�^�͂���܂���ł���</a>";
    }
    
    //���t���
    if($_SESSION['teiji']['startdate'] == "")
    {
        $_SESSION['teiji']['startdate'] = date('Y-m-d');
    }
    if($_SESSION['teiji']['enddate'] == "")
    {
        $_SESSION['teiji']['enddate'] = date('Y-m-d');
    }
?>
<html>
    <head>
        <title>�莞�`�F�b�N����</title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./js/progress.js'></script>
        <script src='./jquery/jquery-1.8.3.min.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
        </script>
    </head>
    <body>
        <div class="title">�莞�`�F�b�N����</div>
        <CENTER>
        <div class="body_area">
            <?php echo $message; ?>
            <?php
                echo "<fieldset style='width:50%; margin:auto;'>";
                echo "<legend>�`�F�b�N����</legend>";
                echo "<table style='margin:auto;'><tbody>";
                if($_SESSION['teiji']['startdate'] == $_SESSION['teiji']['enddate'])
                {
                    echo "<tr><td style='width:80px'>�J�n���t<br>�I�����t</td>";
                    echo "<td>�S����</td></tr>";
                }
                else
                {
                    $startdate = explode('-',$_SESSION['teiji']['startdate']);
                    $enddate = explode('-',$_SESSION['teiji']['enddate']);
                    echo "<tr><td style='width:80px'>�J�n���t</td><td>".$startdate[0]."�N".$startdate[1]."��".$startdate[2]."��</td></tr>";
                    echo "<tr><td>�I�����t</td><td>".$enddate[0]."�N".$enddate[1]."��".$enddate[2]."��</td></tr>";
                }
                echo "<tr><td>�Ј�</td><td>".$_SESSION['teijicheck']['syain'][0]."</td></tr>";
                for($i = 1 ; $i < count($_SESSION['teijicheck']['syain']) ; $i++)
                {
                    echo "<tr><td></td><td>".$_SESSION['teijicheck']['syain'][$i]."</td></tr>";
                }
                echo "</table></tbody>";
                echo "</fieldset>";
            ?>
            <form name="insert" action="listJump.php" method="post">
                <?php echo $message2; ?>
                <?php echo $list; ?>
                <?php echo $message3; ?>
                <input type ='submit' name = 'teiji_5_button' class='list_button' value = '�߂�'>
                <input type='hidden' name='startdate' id='startdate' value='<?php echo $_SESSION['teiji']['startdate']; ?>'>
                <input type='hidden' name='enddate' id='enddate' value='<?php echo $_SESSION['teiji']['enddate']; ?>'>
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
        </CENTER>
    </body>
</html>