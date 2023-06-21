<?php
    session_start();
?>
<?php
    //初期設定
    require_once("f_DB.php");
    require_once("f_SQL.php");
    require_once("f_File.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    
    //定数
    $filename = $_GET['filename'];
    
    //変数
    $csv = "";
    $header_csv = "";
    $sql = array();
    if($filename == 'getuzi_5')
    {
        $period = mb_convert_encoding($_GET['period'],'sjis-win','SJIS');
	$month = mb_convert_encoding($_GET['month'],'sjis-win','SJIS');
        $csv .= make_getujicsv($period,$month,$csv);
    }
    else if($filename == 'nenzi_5')
    {
        $period = mb_convert_encoding($_GET['period'],'sjis-win','SJIS');
	$csv .= make_nenzicsv($period,$csv);
    }
    else if($filename == 'SYUEKIHYO_7')
    {
        $csv .= make_syuekihyocsv();
    }
    else if($filename == 'SHIKAKARI_7')
    {
        $csv .= make_shikakaricsv();
    }
    else
    {
        $csv_form_num = explode(',', $form_ini[$filename]['csv_form_num']);
        //項目名作成
        for($i = 0; $i < count($csv_form_num); $i++)
        {
            if((isset($_SESSION['list'][$csv_form_num[$i]])) && ($_SESSION['list'][$csv_form_num[$i]] != ""))
            {
                $csv .= '"'.$form_ini[$csv_form_num[$i]]['item_name'].' = '.$_SESSION['list'][$csv_form_num[$i]].'",';
            }
            else
            {
                $csv .= '"'.$form_ini[$csv_form_num[$i]]['item_name'].' = ",';
            }
            $header_csv .= '"'.$form_ini[$csv_form_num[$i]]['item_name'].'",';
        }
        $csv .= "\r\n".$header_csv."\r\n";

        //データ作成
        $con = dbconect();
        $sql = itemListSQL($_SESSION['list']);
        $sql = SQLsetOrderby($_SESSION['list'],$sql);

        $result = $con->query($sql[0]);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            for($i = 0; $i < count($csv_form_num); $i++)
            {
                $csv .= '"'.$result_row[$form_ini[$csv_form_num[$i]]['column']].'",';
            }
            $csv .= "\r\n";
        }
    }
    
        //文字コードを変更する
//        $csv = mb_convert_encoding($csv, 'HTML-ENTITIES');
//        $csv = mb_convert_encoding($csv, 'sjis-win','HTML-ENTITIES');
        
        // ファイル出力
//        $filepath = './temp/template.csv';
//        $date = date_format(date_create("NOW"), "Ymd");
//        $file = "List_".$form_ini[$filename]['title']."_".$date.".csv";
//        header('Content-Type: application/octet-stream');
//        header('Content-Length: '.filesize($filepath));
//        header('Content-Disposition: attachment; filename='.$file.'');  
//        echo $csv;
//        readfile($filepath);
    
    //ファイル出力
    $path = csv_write($csv);
    $date = date_format(date_create("NOW"), "Ymd");
    $file_name = "List_".$form_ini[$filename]['title']."_".$date.".csv";
    header('Content-Type: application/octet-stream'); 
    header('Content-Disposition: attachment; filename="'.$file_name.'"'); 
    header('Content-Length: '.filesize($path));
    readfile($path);
    unlink($path);
?>