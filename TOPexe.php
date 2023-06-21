<?php
        session_start();
        require_once("f_Construct.php");
	require_once("f_DB.php");
        
	$_SESSION['pre_post'] = $_SESSION['post'];
        
        $keyarray = array_keys($_POST);
	$url = 'retry';
	foreach($keyarray as $key)
        {
            if($key == 'copydate')
            {
                // ���Ԃ̊J�n��
                $begin = new DateTimeImmutable($_POST['pasteStart']);
                // ���Ԃ̏I����
                $end = new DateTimeImmutable($_POST['pasteEnd']);
                $interval = new DateInterval('P1D');

                $daterange = new DatePeriod( $begin, $interval ,$end );
                foreach( $daterange as $date )
                {
                    $dates[] = $date->format('Y-m-d').PHP_EOL;;
                }
                $dates[] = $_POST['pasteEnd'];
                $a = 0;
                
                // db�ڑ��֐����s
                $con = dbconect();
                $selecrSQL = "SELECT * FROM progressinfo LEFT JOIN projectditealinfo using (6CODE) "
                        . "WHERE SAGYOUDATE = '".$_POST['copydate']."' AND 4CODE = '".$_SESSION['user']['4CODE']."';";
                
                // SQL���s
                $result = $con->query($selecrSQL);																	// �N�G�����s
                if(!$result)
                {
                        error_log($con->error,0);
                        exit();
                }        

                while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $copyDate[$a]['3CODE'] = $result_row['3CODE'];
                    $copyDate[$a]['6CODE'] = $result_row['6CODE'];
                    $copyDate[$a]['TEIZITIME'] = $result_row['TEIZITIME'];
                    $copyDate[$a]['ZANGYOUTIME'] = $result_row['ZANGYOUTIME'];
                    $copyDate[$a]['7PJSTAT'] = $result_row['7PJSTAT'];
                    $a++;
                }

                //�폜����H��������
                $delete_selectsql = "SELECT * FROM progressinfo LEFT JOIN projectditealinfo using (6CODE) "
                        . "WHERE SAGYOUDATE BETWEEN '".$_POST['pasteStart']."' AND '".$_POST['pasteEnd']."' AND 4CODE = '".$_SESSION['user']['4CODE']."';";
                
                $result = $con->query($delete_selectsql);																	// �N�G�����s
                if(!$result)
                {
                        error_log($con->error,0);
                        exit();
                }
                
                $a = 0;
                while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $deletecode[$a] = $result_row['7CODE'];
                    $a++;
                }
                
                //�H���폜
                for($i = 0; $i < count($deletecode); $i++)
                {
                    $deletesql = "DELETE FROM progressinfo WHERE 7CODE = '".$deletecode[$i]."';";
                    $result = $con->query($deletesql);																	// �N�G�����s
                    if(!$result)
                    {
                            error_log($con->error,0);
                            exit();
                    }
                }
                
                //�H���ǉ�
                $insertSQL = "INSERT INTO progressinfo (3CODE,6CODE,TEIZITIME,ZANGYOUTIME,7PJSTAT,SAGYOUDATE) VALUES ";
                for($i = 0; $i  < count($dates); $i ++)
                {
                    for($j = 0; $j < count($copyDate); $j++)
                    {
                        $insertSQL .= '("'.$copyDate[$j]['3CODE'].'",';
                        $insertSQL .= '"'.$copyDate[$j]['6CODE'].'",';
                        $insertSQL .= '"'.$copyDate[$j]['TEIZITIME'].'",';
                        $insertSQL .= '"'.$copyDate[$j]['ZANGYOUTIME'].'",';
                        $insertSQL .= '"'.$copyDate[$j]['7PJSTAT'].'",';
                        $insertSQL .= '"'.$dates[$i].'"),';
                    }
                }
                $insertSQL = substr($insertSQL,0,-1);	
                $insertSQL .= ";";

                // SQL���s
                $result2 = $con->query($insertSQL);																	// �N�G�����s
                if(!$result2)
                {
                        error_log($con->error,0);
                        exit();
                }
                insert_sousarireki('3', $_POST);
            }
            else if($key == 'prev')
            {
                $_SESSION['TOP_2'] = $_POST['prev_month'];
                $url = "TOP";
            }
            else if($key == 'next')
            {
                $_SESSION['TOP_2'] = $_POST['next_month'];
                $url = "TOP";
            }
            else if($key == 'TOP_6_button')
            {
                $filename = $_SESSION['filename'];
                $filename_array = explode('_',$filename);
                $_SESSION['filename'] = $filename_array[0]."_6";
                $url = "Fileinsert";
            }
            else if($key == 'TOP_1_button')
            {
                $filename = $_SESSION['filename'];
                $filename_array = explode('_',$filename);
                $_SESSION['filename'] = $filename_array[0]."_1";
                $url = "insert";
                $_SESSION['insert']['704'] = $_POST['TOP_1_button'];
            }
            else if($key == 'TOP_3_button')
            {
                $filename = $_SESSION['filename'];
                $filename_array = explode('_',$filename);
                $_SESSION['filename'] = $filename_array[0]."_3";
                $url = "edit";
                $_SESSION['edit']['704'] = $_POST['TOP_3_button'];
            }
            
        }
        
        //���C�����j���[��TOP�{�^���������ꂽ�ꍇ
        if(isset($_GET['mainmenu']))
        {
            unset($_GET['mainmenu']);
            unset($_SESSION['TOP_2']);
            $url = "TOP";
            $_SESSION['filename'] = "TOP_2";
        }
        
        header("location:".(empty($_SERVER['HTTPS'])? "http://" : "https://")
					.$_SERVER['HTTP_HOST'].dirname($_SERVER["REQUEST_URI"])."/".$url.".php");
?>
<html>
    <head>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
        <script language="JavaScript">
            history.forward();
        </script>
    </head>
    <body>
    </body>
</html>