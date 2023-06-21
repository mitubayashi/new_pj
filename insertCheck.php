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
    require_once("f_Button.php");        
    require_once ("f_DB.php");
    require_once ("f_Form.php");
    require_once ("f_SQL.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
    $main_table = $form_ini[$filename]['main_table'];
    
    //�ϐ�
    $judge = true;
    $error_type = "";
    $error_data = array();
    $inputcheck_data = array();
    $form = "";
    
    //�u���E�U�o�b�N�A�����[�h�΍�
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //���o�^�`�F�b�N  
    $con = dbconect();
    if($filename == "KOKYAKUTEAM_1")
    {
        //�ڋq�V�K�o�^�`�F�b�N       
        $sql = "SELECT *FROM kokyakuinfo WHERE KOKYAKUID = '".$_SESSION['insert']['1202']."';";
        $result = $con->query($sql);
        if($result->num_rows == 0)
        {
            $judge = 2;
        }
        
        //���o�^�`�F�b�N
        $sql = "SELECT *FROM teaminfo AS a LEFT JOIN kokyakuinfo AS b ON a.12CODE = b.12CODE ";
        $sql .= "WHERE KOKYAKUID = '".$_SESSION['insert']['1202']."' AND TEAMID = '".$_SESSION['insert']['1303']."';";
        $result = $con->query($sql);
        if($result->num_rows > 0)
        {
            $judge = false;
            $errorinfo = array();
            $errorinfo[0] = "1303";
            $error_type = 1;
        }
    }
    elseif($filename == "PROGRESSINFO_1" || $filename == "TOP_1")
    {
        
    }
    elseif($filename == "PJTOUROKU_1")
    {
        $sql = "SELECT a.12CODE,a.13CODE FROM teaminfo as a LEFT JOIN kokyakuinfo as b ON a.12CODE = b.12CODE WHERE KOKYAKUID = '".$_SESSION['insert']['1202']."' AND TEAMID = '".$_SESSION['insert']['1303']."';";
        $result = $con->query($sql);
        if($result->num_rows == 0)
        {
            $judge = 3;
        }
        else
        {
            while($result_row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $code12 = $result_row['12CODE'];
                $code13 = $result_row['13CODE'];
            }
            $sql = "SELECT *FROM projectinfo WHERE 12CODE = '".$code12."' AND 13CODE = '".$code13."' AND ANKENID = '".$_SESSION['insert']['504']."' AND EDABAN = '".$_SESSION['insert']['505']."';";
            $result = $con->query($sql);
            if($result->num_rows != 0)
            {
                $judge = false;
                $errorinfo[0] = "504,505";
                $error_type = 1;
            }
        }
    }
    else
    {
        $errorinfo = existCheck($_SESSION['insert'],$main_table,1);	
        if($errorinfo[0] != "")
        {
            $judge = false;
            $error_type = 1;
        }
    }
    //���ꕶ���`�F�b�N
    if($judge == true && $filename != "PROGRESSINFO_1" && $filename != "TOP_1")
    {
        $errorinfo = array();
        $errorinfo[0] = "";
        $insert_form_num = explode(",",$form_ini[$filename]['insert_form_num']);
        for($i = 0; $i < count($insert_form_num); $i++)
        {
            if($form_ini[$insert_form_num[$i]]['field_type'] == "1")
            {
                $max_length = $form_ini[$insert_form_num[$i]]["max_length"];
                $count = mb_strlen($_SESSION['insert'][$insert_form_num[$i]], 'SJIS');
                if($count > $max_length)
                {
                    $errorinfo[0] .= $insert_form_num[$i].",";
                    $error_type = 2;
                    $judge = false;                
                }
            }
        }
    }
    
    //�G���[���b�Z�[�W�쐬
    if($judge == false)
    {
        $error_form_num = explode(",",$errorinfo[0]);
        for($i = 0; $i < count($error_form_num); $i++)
        {
            if($error_form_num[$i] != "")
            {
                $error_data[$i]['name'] = $error_form_num[$i];
                $error_data[$i]['error_type'] = $error_type;
            }
        }
    }
    
    //���͗��쐬
    if($filename == "PROGRESSINFO_1" || $filename == "TOP_1")
    {
        $form = makePROGRESSlist($_SESSION['insert'],$_SESSION['user']);
    }
    else
    {
        $form = makeformInsert_set($_SESSION['insert']);
        $inputcheck_data = get_inputcheck_data("insert_form_num");
    }
    //���擾
    $kokyaku = get_kokyaku();
    $team = get_team();
    $syain = get_syain();
    $koutei = get_koutei();
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
        <script src='./jquery/jquery-1.8.3.min.js'></script>
        <script>
            var inputcheck_data = JSON.parse('<?php echo json_encode($inputcheck_data); ?>');
            var error_data = JSON.parse('<?php echo json_encode($error_data); ?>');
            var filename = '<?php echo $filename; ?>';
        </script>
        <script>
            window.onload = function(){
                var judge = '<?php echo $judge ?>';
                error_data_set();
                if(filename == 'PJTOUROKU_1')
                {
                    kingaku_goukei();
                    period_select();
                    if(document.getElementById('goukei').value != document.getElementById('507').value)
                    {
                        judge = false;
                        if(confirm("���͓��e����m�F�B\n�v���W�F�N�g���z�ƍ��v���z���قȂ�܂��B\n���v���z�Ńv���W�F�N�g���z��ύX���܂�����낵���ł����H\n�ēx�m�F����ꍇ�́u�L�����Z���v�{�^���������Ă��������B"))
                        {
                            location.href = "./insertComp.php";
                        }
                    }
                }
                if(judge == 2)
                {
                    if(confirm("�ڋq��V�K�o�^���A�`�[������o�^���܂��B\n��낵���ł����H" +
                        "\n�ēx�m�F����ꍇ�́u�L�����Z���v�{�^���������Ă��������B"))
                    {
                        location.href = "./insertComp.php";
                    }
                }
                else if(judge == 3)
                {
                    if(confirm("�ڋq�`�[������V�K�o�^���A�v���W�F�N�g��o�^���܂��B\n��낵���ł����H" +
                        "\n�ēx�m�F����ꍇ�́u�L�����Z���v�{�^���������Ă��������B"))
                    {
                        location.href = "./insertComp.php";
                    }
                }
                else if(judge == true)
                {
                    if(confirm("���͓��e����m�F�B\n���o�^���܂�����낵���ł����H" +
                        "\n�ēx�m�F����ꍇ�́u�L�����Z���v�{�^���������Ă��������B"))
                    {
                        location.href = "./insertComp.php";
                    }
                }
            }
        </script>
    </head>
    <body>
        <div class="title"><?php echo $form_ini[$filename]['title']; ?></div>
        <div class="body_area">
            <form name="insert" action="listJump.php" method="post">
                <?php echo $form; ?>
                <?php
                    if($filename == "PROGRESSINFO_1" || $filename == "TOP_1")
                    {
                        echo '<div style="width: 1200px;">';
                        echo '<input type="submit" name = "insert" value = "�o�^" class="list_button" onclick="return progress_check();">';
                        echo '<input type="submit" name = "back" value = "�߂�" class="list_button" onClick ="isCancel = true;">';   
                        echo '<input type="text" id="TEIZI_GOUKEI" name="TEIZI_GOUKEI" value="'.$_SESSION['insert']['TEIZI_GOUKEI'].'" class="form_text disabled" style="width:90px; margin-left: 730px;">';
                        echo '<input type="text" id="ZANGYOU_GOUKEI" name="ZANGYOU_GOUKEI" value="'.$_SESSION['insert']['ZANGYOU_GOUKEI'].'" class="form_text disabled" style="width:90px; margin-left: 2px;">';
                        echo '</div>';
                    }
                    else
                    {
                        echo '<div style="width: 1200px;">';
                        echo '<input type="submit" name = "insert" value = "�o�^" class="list_button" onclick="return check();">';
                        echo '<input type="submit" name = "cancel" value = "�N���A" class="list_button" onClick ="isCancel = true;">';
                        echo '<input type="submit" name = "back" value = "�߂�" class="list_button" onClick ="isCancel = true;">';             
                        echo '</div>';
                    }
                ?>
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
        <input type='hidden' id='kokyakulist' value='<?php echo $kokyaku; ?>'>
        <input type='hidden' id='teamlist' value='<?php echo $team; ?>'>
        <input type='hidden' id='syainlist' value='<?php echo $syain; ?>'>
        <input type='hidden' id='kouteilist' value='<?php echo $koutei; ?>'>
    </body>
</html>