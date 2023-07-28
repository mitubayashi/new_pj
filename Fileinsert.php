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
        
    //�u���E�U�o�b�N�A�����[�h�΍�
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;

?>
<html>
    <head>
        <title><?php echo $form_ini[$filename]['title']; ?></title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./js/progress.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
            function check()
            {
                var judge = true;
                if(document.getElementsByName('inpath')[(document.getElementsByName('inpath').length-1)].value == "")
                {
                        judge = false;
                        alert('�t�@�C����I�����ĉ�����');
                }
                return judge;
            }
        </script>
    </head>
    <body>
        <div class="title"><?php echo $form_ini[$filename]['title']; ?></div>
        <div class="body_area">
            <form action="listJump.php" method="post" enctype="multipart/form-data">
                <?php
                    echo '<br><input type="file" name="inpath" size="300"><br><br>';
                    echo '<input type="submit" name = "fileinsert" value = "�捞" class="list_button" onclick="return check();">';
                    echo "<input type ='submit' value = '�߂�' name = 'back' class = 'list_button' onClick ='isCancel = true;'>";
                    echo "<br><br><br>";
                    echo "<FONT color='red'>���[�U�P�ʂ̍H����񂪎�荞�݂ł��܂��B��荞�ݏ���</FONT><br><br>";
                    echo "<FONT color='red'>�Ј��ԍ��A���t(yyyy/mm/dd)�APJ�R�[�h(12���n�C�t������)�A�H���ԍ��A�莞���ԁA�c�Ǝ���<br>��CSV�`���ō쐬���Ă��������B</FONT><br><br>";
                    echo "<FONT color='red'>������t�ɂ��łɓo�^�f�[�^�����݂���ꍇ�͑��݂��Ă���f�[�^�͔j�������荞�݃f�[�^���������f�[�^�Ƃ��ēo�^����܂��B</FONT><br><br>";
                ?>
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>