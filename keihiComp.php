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
    require_once ("f_DB.php");
    
    //�ϐ�
    $judge = false;
    $genkalist = array();
    $genka = array();
    $keihi = $_SESSION['keihi'];
    unset($_SESSION['keihi']);
    
    //�u���E�U�o�b�N�΍�
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //����
    $con = dbconect();
    
    //�o�^�ς݃f�[�^�擾
    $keihi_data = array("","","","","","","","","","","","","","","");
    $counter = 0;
    $sql = "SELECT *FROM keihiinfo WHERE 5CODE = '".$keihi['edit_id']."';";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $keihi_data[$counter] = $result_row['15CODE'];
        $counter++;
    }

    //�o��o�^����
    for($i = 0; $i < 15; $i++)
    {
        if($i < $counter)
        {
            if($keihi['syain_'.$i] != '' && $keihi['kubun_'.$i] != '' && $keihi['charge_'.$i])
            {
                $sql = "UPDATE keihiinfo SET 4CODE='".$keihi['syain_'.$i]."',kubun='".$keihi['kubun_'.$i]."',charge='".$keihi['charge_'.$i]."',month='".$keihi['month_'.$i]."-01' WHERE 15CODE = '".$keihi_data[$i]."';";
            }
            else
            {
                $sql = "DELETE FROM keihiinfo WHERE 15CODE = '".$keihi_data[$i]."'; ";
            }
        }
        else
        {
            if($keihi['syain_'.$i] != '' && $keihi['kubun_'.$i] != '' && $keihi['charge_'.$i])
            {
                $sql = "INSERT INTO keihiinfo (5CODE,4CODE,charge,kubun,month) VALUES ('".$keihi['edit_id']."','".$keihi['syain_'.$i]."','".$keihi['charge_'.$i]."','".$keihi['kubun_'.$i]."','".$keihi['month_'.$i]."-01') ;";
            }
        }
        if($sql != "")
        {
            $result = $con->query($sql);
        }
        $sql = "";
    }
    
    //���엚��
    insert_sousarireki('0', $keihi);
    
    //�y�[�W�ړ�����
    $_SESSION['pre_post'] = $_SESSION['post'];
    $_SESSION['post'] = null;
    unset($_SESSION['keihi']);
    $_SESSION['filename'] = 'keihi_5';
    header("location:".(empty($_SERVER['HTTPS'])? "http://" : "https://")
            .$_SERVER['HTTP_HOST'].dirname($_SERVER["REQUEST_URI"])."/keihi.php");
?>