<?php
    session_start();
    header('Expires:-1'); 
    header('Cache-Control:'); 
    header('Pragma:'); 
    header('Content-type: text/html; charset=Shift_JIS'); 
?>
<?php
    //�����ݒ�
    require_once("f_DB.php");																							// DB�֐��Ăяo������
    require_once("f_File.php");																							// File�֐��Ăяo������
    require_once("f_LOGROTE.php");	
    
    //�ϐ�
    $userName = "";
    $userPass = "";
    $login_result = false;
    $limit_result = false;
    $comment = "";
    $message = "";
    $error_style = "";
    
    //����
    $_SESSION = array();
    loglotaton();
    $result = limit_date();
    if($result[0] != 0)
    {
        if($result[0] == 2)
        {
            $message = "<a class = 'error'>���ƁA".$result[1]."���ŗL���������؂�܂��B</a>";		 
        }
        if(isset($_POST['userName']))
        {
            $userName = $_POST['userName'];
            $userPass = $_POST['userPass'];
            $login_result = login($userName,$userPass);
            if($login_result == true)
            {
                $_SESSION['pre_post'] = $_POST;
                $_SESSION['filename'] = 'TOP_2';
                echo '<script type="text/javascript">';
                echo "<!--\n";
                echo 'location.href = "./TOP.php";';
                echo '// -->';
                echo '</script>';
            }
            else
            {
                $comment = "<a class = 'error'>���O�C��ID�܂��̓p�X���[�h���Ԉ���Ă��܂��B</a>";			
                $error_style = " background-color: rgb(255, 229, 229); box-shadow: 0 0 0 1px red inset; ";
            }
        }
    }
    else
    {
        $message = "<a class = 'error'>�L���������؂�Ă܂��B</a>";
    }
?>

<html>
    <head>
        <title>���O�C��</title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <script src='./js/modal.js'></script>
        <script src='./js/inputcheck.js'></script>
        <script src='./js/progress.js'></script>
        <script src='./js/teiji.js'></script>
        <script src='./js/pjend.js'></script>
        <script src='./js/pjagain.js'></script>
    </head>
    <body>
        <div align="center" style="height: 100%; margin: auto;">            
            <div>
                <div style="height: 40px;"></div>
                <img src="./img/mlogo.png" style="width:370px">
                <div class="login_title">PJ�Ǘ��V�X�e��</div>
                <div style="height: 180px; color: red;">
                    <?php
                        //�L���������b�Z�[�W�\��
                        if($message != "")
                        {
                            echo $message."<br>";
                        }
                        //���O�C���G���[���b�Z�[�W�\��
                        if($comment != "")
                        {
                            echo $comment;
                        }
                    ?>
                </div>
                <form action="login.php" method="post">
                    <table style="height: 125px; margin-right: 20px;">
                        <tr>
                            <td>���[�U�[ID</td>      
                            <td><input type="text" value="<?php echo $userName; ?>" name="userName" class="form_text" style="width: 300px;<?php echo $error_style; ?>"></td>
                        </tr>
                        <tr>
                            <td>�p�X���[�h</td>
                            <td><input type="password"  name="userPass" class="form_text" style="width: 300px;<?php echo $error_style; ?>"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td align="center"><input type="submit" name="login" class="login_button" value="���O�C��"></td>
                        </tr>
                    </table>                
                </form>
            </div>
        </div>
    </body>
</html>