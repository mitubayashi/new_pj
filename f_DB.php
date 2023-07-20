<?php
/***************************************************************************
function dbconect()


引数            なし

戻り値         $con                             mysql接続済みobjectT
***************************************************************************/
function dbconect(){
    
    //iniファイル読み取り準備
    $db_ini_array = parse_ini_file("./ini/DB.ini",true);
    
    //iniファイル内情報取得処理
    $host = $db_ini_array["database"]["host"];																			// DBサーバーホスト
    $user = $db_ini_array["database"]["user"];																			// DBサーバーユーザー
    $password = $db_ini_array["database"]["userpass"];																	// DBサーバーパスワード
    $database = $db_ini_array["database"]["database"];
    
    //DBアクセス処理
    $con = new mysqli($host,$user,$password, $database,"3306") or die('1'.$con->error);			// DB接続
    $con->set_charset("cp932") or die('2'.$con->error);												// cp932を使用する
    return ($con);
}

/************************************************************************************************************
function limit_date()


引数            なし

戻り値         $result                              有効期限結果
************************************************************************************************************/
function limit_date(){
    
    //初期設定
    require_once("f_DB.php");
    
    //定数
    $date = date_create("NOW");
    $date = date_format($date, "Y-m-d");
    $Loginsql = "select * from systeminfo;";
    
    //変数
    $limit_result = 0;																								// 有効期限判断
    $rownums = 0;																									// 検索結果件数
    $startdate = "";
    $enddate = "";
    $befor_month = "";
    $message = "";
    $result_limit = array();
    
    //ログイン検索処理
    $con = dbconect();
    $result = $con->query($Loginsql) or die($con-> error);
    $rownums = $result->num_rows;
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $startdate = $result_row['STARTDATE'];
    }
    
    //ログイン判断処理
    $enddate = date_create($startdate);
    $enddate = date_add($enddate, date_interval_create_from_date_string('1 year'));
    $enddate = date_sub($enddate, date_interval_create_from_date_string('1 days'));
    $enddate = date_format($enddate, 'Y-m-d');
    $befor_month = date_create($enddate);
    $befor_month = date_format($befor_month, 'Y-m-01');
    $befor_month = date_create($befor_month);
    $befor_month = date_sub($befor_month, date_interval_create_from_date_string('1 month'));
    $befor_month = date_format($befor_month, 'Y-m-d');
    if($enddate >= $date)
    {
        $limit_result = 1;
        if($befor_month <= $date)
        {
            $enddate2 = date_create($enddate);
            $date2 = date_create($date);
            $limit_result = 2;
            $interval = date_diff($date2, $enddate2);
            $message = $interval->format('%a');
        }
    }
    else
    {
            $limit_result = 0;
    }
    $result_limit[0] = $limit_result;
    $result_limit[1] = $message;
    return ($result_limit);
}

/************************************************************************************************************
function login($userName,$usserPass)


引数1           $userName				ユーザー名
引数2           $userPass				ユーザーパスワード

戻り値          $result					ログイン結果
************************************************************************************************************/
function login($userName,$userPass){
    
    //初期設定
    require_once("f_DB.php");
    
    //定数
    $Loginsql = "select * from syaininfo where LUSERNAME = '".$userName."' AND LUSERPASS = '".$userPass."' ;";		// ログインSQL文
	
    //変数
    $log_result = false;
    $rownums = 0;
    
    //ログイン検索処理
    $con = dbconect();
    $result = $con->query($Loginsql);																					// クエリ発行
    $rownums = $result->num_rows;																						// 検索結果件数取得
    $result_row = $result->fetch_array(MYSQLI_ASSOC);
       
    //ログイン判断処理
    if($rownums == 1)
    {
        $log_result = true;
        $_SESSION['user']['4CODE'] = $result_row['4CODE'];
        $_SESSION['user']['STAFFID'] = $result_row['STAFFID'];
        $_SESSION['user']['STAFFNAME'] = $result_row['STAFFNAME'];
    }
    return ($log_result);
}

/************************************************************************************************************
function makeList_item($sql,$post)

引数1           $sql                                      検索SQL

戻り値          $list_html                              リストhtml
************************************************************************************************************/
function makeList_item($sql,$post){
    	
    //初期設定
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    $system_ini = parse_ini_file('./ini/system.ini', true);
    require_once ("f_Form.php");
    require_once ("f_DB.php");
    require_once ("f_SQL.php");

    //定数
    $filename = $_SESSION['filename'];
    $filename_array = explode('_',$filename);
    $filename_edit = $filename_array[0].'_3';
    $columns_array = explode(',',$SQL_ini[$filename]['listcolumns']);
    $columnname_array = explode(',',$SQL_ini[$filename]['clumnname']);
    $main_code = $form_ini[$filename]['main_code'];
            
    //変数
    $list_html = "";
    $judge = false;
    $counter = 1;
    
    //処理
    $con = dbconect();    
    
    //表示件数表示
    $list_html .= '<div style="margin-top: 5px; margin-bottom: 5px;">';
    $result = $con->query($sql[1]);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        //表示範囲設定
        if($filename == 'rireki_2')
        {
            if(isset($post['pagenum']))
            {
                $pagenum = $post['pagenum'];   
                $startcount = $pagenum * $system_ini['list_item_count']['limit'] + 1;
                $endcount = $startcount + $system_ini['list_item_count']['limit'] -1;
            }
            else
            {
                $pagenum = 0;
                $startcount = '1';
                $endcount = $system_ini['list_item_count']['limit'];
            }
            //SQL追記
            $sql[0] .= " LIMIT ".($pagenum * $system_ini['list_item_count']['limit']).",".$system_ini['list_item_count']['limit'];
        }
        else
        {
            $startcount = '1';
            $endcount = $result_row['COUNT(*)'];
        }       
        $totalcount = $result_row['COUNT(*)'];
        $list_html .= $result_row['COUNT(*)']."件中 ".$startcount."件~".$endcount."件 表示中";
    }
    
    //一覧表ボタン作成処理
    $list_html .= makeList_button();
    $list_html .= '</div>';
    
    //一覧表作成
    $list_html .= "<div class='list_scroll'>";
    $list_html .= "<table>";
   
    //項目名作成処理
    $list_html .= "<thead><tr>";
    $list_html .= "<th><a>No</a></th>";
    for($i = 0; $i < count($columnname_array); $i++)
    {
        $list_html .= "<th><a>".$columnname_array[$i]."</a></th>";
    }
    if($filename != 'PJLIST_2' && $filename != 'ENDPJLIST_2' && $filename != 'MONTHLIST_2' && $filename != 'rireki_2')
    {
        $list_html .= "<th><a>編集</a></th>";
    }
    $list_html .= "</tr></thead>";
    
    //一覧表内容作成処理
    $list_html .= "<tbody>";
    $result = $con->query($sql[0]) or ($judge = true);																		// クエリ発行
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        if(($counter%2) == 1)
        {
            $list_html .= "<tr>";
        }
        else
        {
            $list_html .= "<tr class='list_stripe'>";
        }
        $list_html .= "<td>".$counter."</td>";
        for($i = 0; $i < count($columns_array); $i++)
        {
            $list_html .= "<td>".$result_row[$columns_array[$i]]."</td>";
        }
        if($filename != 'PJLIST_2' && $filename != 'ENDPJLIST_2' && $filename != 'MONTHLIST_2' && $filename != 'rireki_2')
        {
            $list_html .= "<td><input type='submit' name='".$filename_edit."_button_".$result_row[$form_ini[$main_code]['column']]."' value='編集'></td>";
        }
        $list_html .= "</tr>";
        $counter++;
    }
    $list_html .= "</tbody>";
    
    $list_html .= "</table>";
    $list_html .= "</div>";
    
    if($filename == 'rireki_2')
    {
        $lastpage_count = $system_ini['list_item_count']['limit'] - ($totalcount % $system_ini['list_item_count']['limit']);
        $totalcount = ($totalcount + $lastpage_count) / $system_ini['list_item_count']['limit'] - 1;
        $list_html .= "<div>";
        if(($pagenum - 1) >= 0)
        {
            $list_html .= "<button name='pagenum' value='0' class='pagenum_button'>一番最初に戻る</button>";
            $list_html .= "<button name='pagenum' value='".($pagenum - 1)."' class='pagenum_button'>戻る</button>";
        }
        else
        {
            $list_html .= "<button name='pagenum' value='0' class='pagenum_button' disabled>一番最初に戻る</button>";
            $list_html .= "<button name='pagenum' value='".($pagenum - 1)."' class='pagenum_button' disabled>戻る</button>";
        }
        if($pagenum < $totalcount)
        {
            $list_html .= "<button name='pagenum' value='".($pagenum + 1)."' class='pagenum_button'>進む</button>";
            $list_html .= "<button name='pagenum' value='".$totalcount."' class='pagenum_button'>一番最後に進む</button>";
        }
        else
        {
            $list_html .= "<button name='pagenum' value='".($pagenum + 1)."' class='pagenum_button' disabled>進む</button>";
            $list_html .= "<button name='pagenum' value='".$totalcount."' class='pagenum_button' disabled>一番最後に進む</button>";
        }
        
        $list_html .= "</div>";
    }
    return $list_html;
}

/************************************************************************************************************
function makeList_count($sql)

引数1           $sql                                      検索SQL

戻り値          $list_count                            リストhtml
************************************************************************************************************/
function makeList_count($sql){
    
    //初期設定
    require_once ("f_DB.php");
    
    //変数
    $list_count = "";
    $judge = false;
    
    //処理
    $con = dbconect();
    $result = $con->query($sql) or ($judge = true);
    if($judge)
    {
        error_log($con->error,0);
        $judge = false;
    }
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $totalcount = $result_row['COUNT(*)'];
        $list_count .= $totalcount."件中 "."1件~".$totalcount."件 表示中";
    }
    return $list_count;
}

/************************************************************************************************************
function progress_fileinsert_check($post)

引数		$post						登録情報

戻り値		なし
************************************************************************************************************/
function progress_fileinsert_check($post){
    
    //変数
    $errorinfo = array();
    $teizicheck = array();
    $counter = 0;
    
    $file = fopen("temp/tempfileinsert.txt", "r");
    if($file)
    {
        while ($line = fgets($file)) 
        {
            //処理
            $con = dbconect();
            $strsub = explode(",", $line);
            $errorinfo[$counter]['STAFFID'] = str_pad($strsub[0], 3, "0", STR_PAD_LEFT);
            $errorinfo[$counter]['SAGYOUDATE'] = $strsub[1];
            $errorinfo[$counter]['PJCODE'] = $strsub[2];
            $errorinfo[$counter]['KOUTEIID'] = str_pad($strsub[3], 3, "0", STR_PAD_LEFT);	
            $errorinfo[$counter]['TEIZITIME'] = $strsub[4];
            $errorinfo[$counter]['ZANGYOUTIME'] = $strsub[5];
            $errorinfo[$counter]['errormsg'] = "正常";
            if(isset($teizicheck[$strsub[0]][$strsub[1]]))
            {
                $teizicheck[$strsub[0]][$strsub[1]] += $strsub[4];
            }
            else
            {
                $teizicheck[$strsub[0]][$strsub[1]] = $strsub[4];
            }
            $counter++;
        }
        
        for($i = 0; $i < count($errorinfo); $i++)
        {
            //月次処理チェック
            $date = explode('/',$errorinfo[$i]['SAGYOUDATE']);
            $sql = "SELECT *FROM endmonthinfo WHERE YEAR = '".$date[0]."' AND MONTH = '".$date[1]."';";
            $result = $con->query($sql);
            if($result->num_rows > 0)
            {
                $errorinfo[$i]['errormsg'] = '月次処理終了済み期間のため、登録できません。'; 
            }
            
            //社員番号チェック
            $sql = "SELECT *FROM syaininfo WHERE STAFFID = '".$errorinfo[$i]['STAFFID']."';";
            $result = $con->query($sql);
            if($result->num_rows == 0)
            {
                $errorinfo[$i]['errormsg'] = '社員番号が登録されていません。'; 
            }
            
            //工程番号チェック
            $sql = "SELECT *FROM kouteiinfo WHERE KOUTEIID = '".$errorinfo[$i]['KOUTEIID']."';";
            $result = $con->query($sql);
            if($result->num_rows == 0)
            {
                $errorinfo[$i]['errormsg'] = '工程番号が登録されていません。'; 
            }
            
            //PJコードチェック
            $sql = "SELECT 6CODE,5PJSTAT FROM projectditealinfo ";
            $sql .= "LEFT JOIN projectinfo USING(5CODE) LEFT JOIN kokyakuinfo USING(12CODE) ";
            $sql .= "LEFT JOIN teaminfo USING(13CODE) LEFT JOIN syaininfo USING(4CODE)";
            $sql .= "WHERE STAFFID = '".$errorinfo[$i]['STAFFID']."' AND CONCAT(KOKYAKUID,TEAMID,ANKENID,EDABAN) = '".$errorinfo[$i]['PJCODE']."';";
            $result = $con->query($sql);
            if($result->num_rows == 0)
            {
                $errorinfo[$i]['errormsg'] = '該当プロジェクトが登録されていません。'; 
            }
            else
            {
                //PJ終了チェック
                while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    if($result_row['5PJSTAT'] == "1")
                    {
                        $errorinfo[$i]['errormsg'] = '既に終了したプロジェクトのため登録できません。'; 
                    }
                }
            }
        }
        
        //定時チェック
        $keyarray = array_keys($teizicheck);
        foreach($keyarray as $key)
        {
            $keyarray2 = array_keys($teizicheck[$key]);
            foreach($keyarray2 as $key2)
            {
                if($teizicheck[$key][$key2] > 7.75)
                {
                    for($i = 0; $i < count($errorinfo); $i++)
                    {
                        if($errorinfo[$i]['STAFFID'] == $key && $errorinfo[$i]['SAGYOUDATE'] == $key2)
                        {
                            $errorinfo[$i]['errormsg'] = '既定の定時時間を越えています。';
                        }
                    }
                }
            }
        }
    }
    return $errorinfo;
}

/************************************************************************************************************
function progress_fileinsert($post)

引数		$post						登録情報

戻り値		なし
************************************************************************************************************/
function progress_fileinsert($post){
    
    $file = fopen("temp/tempfileinsert.txt", "r");
    if($file)
    {
        while ($line = fgets($file)) 
        {
            $strsub = explode(",", $line);
            
            //工程情報取得
            $con = dbconect();
            $sql = "SELECT 3CODE FROM kouteiinfo WHERE KOUTEIID = '".$strsub[3]."';";
            $result = $con->query($sql);
            while($result_row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $code3 = $result_row['3CODE'];
            }
            
            //PJ情報取得
            $sql = "SELECT 6CODE FROM projectditealinfo AS projectditealinfo ";
            $sql .= "LEFT JOIN projectinfo AS projectinfo ON projectditealinfo.5CODE = projectinfo.5CODE ";
            $sql .= "LEFT JOIN kokyakuinfo AS kokyakuinfo ON kokyakuinfo.12CODE = projectinfo.5CODE ";
            $sql .= "LEFT JOIN teaminfo AS teaminfo ON teaminfo.13CODE = projectinfo.13CODE ";
            $sql .= "LEFT JOIN syaininfo AS syaininfo ON syaininfo.4CODE = projectditealinfo.4CODE ";
            $sql .= "WHERE syaininfo.STAFFID = '".$strsub[0]."' AND CONCAT(substring(KOKYAKUID,TEAMID,ANKENID,EDABAN) = '".$strsub[2]."';";
            $result = $con->query($sql);
            while($result_row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $code6 = $result_row['6CODE'];
            }
            
            //登録処理
            $sql = "INSERT INTO progressinfo (3CODE,6CODE,SAGYOUDATE,TEIZITIME,ZANGYOUTIME) VALUES ('".$code3."','".$code6."','".$strsub[1]."',".$strsub[4].",".$strsub[5].");";
            $result = $con->query($sql);
        }
    }
}

/************************************************************************************************************
function progress_delete($post)

引数		$post						登録情報

戻り値		なし
************************************************************************************************************/
function progress_delete($post){
    
    //初期設定
    require_once ("f_DB.php");
    
    //変数
    $sql = "";
    
    //処理
    $con = dbconect();
    $sql = "SELECT 7CODE FROM progressinfo AS progressinfo ";
    $sql .= "LEFT JOIN projectditealinfo AS projectditealinfo ON progressinfo.6CODE = projectditealinfo.6CODE ";
    $sql .= "WHERE projectditealinfo.4CODE = '".$post['401']."' AND SAGYOUDATE = '".$post['704']."';";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $sql = "DELETE FROM progressinfo WHERE 7CODE = '".$result_row['7CODE']."';";
        $delete_result = $con->query($sql);
    }
    
}

/************************************************************************************************************
function delete($post)

引数		$post						登録情報

戻り値		なし
************************************************************************************************************/
function delete($post){
        
    //初期設定
    require_once ("f_Form.php");
    require_once ("f_DB.php");																							// DB関数呼び出し準備
    require_once ("f_SQL.php");	
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $table_name = $form_ini[$form_ini[$filename]['main_table']]['table_name'];
    $main_code = $form_ini[$form_ini[$filename]['main_code']]['column'];
    
    //変数
    $sql = "";
    $judge = false;
    
    //処理
    $con = dbconect();
    $sql = "DELETE FROM ".$table_name." WHERE ".$main_code." = ".$post['edit_id'].";";
    $result = $con->query($sql) or ($judge = true);
    
    //付属する情報を削除する
    if($filename == "PJTOUROKU_3")
    {
        $counter = 0;
        $code6_list = array();
        $sql = "SELECT *FROM projectditealinfo WHERE 5CODE = '".$post['edit_id']."';";
        $result = $con->query($sql) or ($judge = true);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $code6_list[$counter] = $result_row['6CODE'];
            $counter++;
        }    
        for($i = 0; $i < count($code6_list); $i++)
        {
            $sql = "DELETE FROM progressinfo WHERE 6CODE = '".$code6_list[$i]."';";
            $result = $con->query($sql) or ($judge = true);
        }
        $sql = "DELETE FROM projectditealinfo WHERE ".$main_code." = '".$post['edit_id']."';";
        $result = $con->query($sql) or ($judge = true);
        $sql = "";
    }
}

/************************************************************************************************************
function insert($post)

引数		$post						登録情報

戻り値		なし
************************************************************************************************************/
function insert($post){
    
    //初期設定
    require_once ("f_Form.php");
    require_once ("f_DB.php");																							// DB関数呼び出し準備
    require_once ("f_SQL.php");	
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $sql = "";
    $judge = false;
    
    //処理
    $con = dbconect();
    $sql = InsertSQL($post);
    $result = $con->query($sql) or ($judge = true);
}

/************************************************************************************************************
function kokyakuteam_insert($post)

引数		$post						登録情報

戻り値		なし
************************************************************************************************************/
function kokyakuteam_insert($post){
    
    //初期設定
    require_once ("f_Form.php");
    require_once ("f_DB.php");																							// DB関数呼び出し準備
    require_once ("f_SQL.php");	
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $sql = "";
    $judge = false;
    $member = "";
    
    //処理
    $con = dbconect();
    
    //登録のない顧客コードが入力されていた場合、新規登録を行う
    $sql = "SELECT *FROM kokyakuinfo WHERE KOKYAKUID = '".$post['1202']."';";
    $result = $con->query($sql);
    if($result->num_rows == 0)
    {
        $sql = "INSERT INTO kokyakuinfo (KOKYAKUID,KOKYAKUNAME) VALUES('".$post['1202']."','".$post['1203']."');";
        $result = $con->query($sql);
    }
    
    //顧客IDを取得する
    $sql = "SELECT 12CODE FROM kokyakuinfo WHERE KOKYAKUID = '".$post['1202']."';";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $kokyakuid = $result_row['12CODE'];
    }
    
    //メンバー情報作成
    if(isset($post['all_check']))
    {
        $member = '0';
    }
    else
    {
        for($i = 0; $i < count($post['checkbox']); $i++)
        {
            $member .= $post['checkbox'][$i].',';
        }
        $member = substr($member,0,-1);
    }
        
    //チーム情報を登録する
    $sql = "INSERT INTO teaminfo (12CODE,TEAMID,TEAMNAME,MEMBER) VALUES('".$kokyakuid."','".$post['1303']."','".$post['1304']."','".$member."');";
    $result = $con->query($sql);
}

/************************************************************************************************************
function progress_insert($post)

引数		$post						登録情報

戻り値		なし
************************************************************************************************************/
function progress_insert($post){
    
    //初期設定
    require_once ("f_Form.php");
    require_once ("f_DB.php");																							// DB関数呼び出し準備
    require_once ("f_SQL.php");	
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $sql = "";
    $judge = false;
    
    //処理
    $con = dbconect();
    for($i = 0; $i < 10; $i++)
    {
        if($post['601_'.$i] != "")
        {
            $sql = "INSERT INTO progressinfo (3CODE,6CODE,SAGYOUDATE,TEIZITIME,ZANGYOUTIME) VALUES('".$post['301_'.$i]."','".$post['601_'.$i]."','".$post['704']."','".$post['705_'.$i]."','".$post['706_'.$i]."');";
            $result = $con->query($sql); 
        }
    }
}

/************************************************************************************************************
function pjtouroku_insert($post)

引数		$post						登録情報

戻り値		なし
************************************************************************************************************/
function pjtouroku_insert($post){
        
    //初期設定
    require_once ("f_Form.php");
    require_once ("f_DB.php");																							// DB関数呼び出し準備
    require_once ("f_SQL.php");	
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $sql = "";
    $judge = false;

    //顧客コードとチームコードを取得
    $con = dbconect();
    $sql = "SELECT a.12CODE,a.13CODE FROM teaminfo AS a LEFT JOIN kokyakuinfo AS b ON a.12CODE = b.12CODE WHERE KOKYAKUID = '".$post['1202']."' AND TEAMID = '".$post['1303']."';";
    $result = $con->query($sql) or ($judge = true);
    if($result->num_rows == 0)
    {
        $sql = "SELECT *FROM kokyakuinfo WHERE KOKYAKUID = '".$post['1202']."';";
        $result = $con->query($sql) or ($judge = true);
        if($result->num_rows == 0)
        {
            $sql = "INSERT INTO kokyakuinfo (KOKYAKUID,KOKYAKUNAME) VALUES('".$post['1202']."','".$post['1303']."');";
            $result = $con->query($sql) or ($judge = true);
            $sql = "SELECT MAX(12CODE) FROM kokyakuinfo;";
            $result = $con->query($sql) or ($judge = true);
            while($result_row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $kokyakuid = $result_row['MAX(12CODE)'];
            }
        }
        else
        {
            while($result_row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $kokyakuid = $result_row['12CODE'];
            }
        }
        
        $sql = "INSERT INTO teaminfo (12CODE,TEAMID,TEAMNAME) VALUES('".$kokyakuid."','".$post['1303']."','".$post['1304']."');";
        $result = $con->query($sql) or ($judge = true);
        $sql = "SELECT MAX(13CODE) FROM teaminfo;";
        $result = $con->query($sql) or ($judge = true);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $teamid = $result_row['MAX(13CODE)'];
        }
    }
    else
    {
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $kokyakuid = $result_row['12CODE'];
            $teamid = $result_row['13CODE'];
        }
    }
    
    //プロジェクト登録処理
    if($post['508'] == "")
    {
        $post['508'] = "NULL";
    }
    else
    {
        $post['508'] = "'".$post['508']."-01'";
    }
    $sql = "INSERT INTO projectinfo (12CODE,13CODE,ANKENID,EDABAN,PJNAME,CHAEGE,URIAGEMONTH,5STARTDATE,5PJSTAT) VALUES ('".$kokyakuid."','".$teamid."','".$post['504']."','".$post['505']."','".$post['506']."',".$post['507'].",".$post['508'].",'".$post['509']."','0');";
    $result = $con->query($sql) or ($judge = true);

    //5OCDE取得処理
    $sql = "SELECT MAX(5CODE) FROM projectinfo;";
    $result = $con->query($sql) or ($judge = true);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $code5 = $result_row['MAX(5CODE)'];
    }
    
    //社員別金額登録
    $keyarray = array_keys($post);
    foreach($keyarray as $key)
    {
        if(strstr($key, 'kingaku_'))
        {
            if($post[$key] != "")
            {
                $name_arrsy = explode('_',$key);
                $sql = "INSERT INTO projectditealinfo (4CODE,5CODE,DETALECHARGE) VALUES('".$name_arrsy[1]."','".$code5."',".$post[$key].");";
                $result = $con->query($sql);
            }
        }
    }
}

/************************************************************************************************************
function get_edit_value($edit_id)

引数		$id						編集コード

戻り値	$edit_value                                編集情報
************************************************************************************************************/
function get_edit_value($edit_id){
    
    //初期設定
    require_once ("f_DB.php");																							// DB関数呼び出し準備
    require_once ("f_SQL.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $filename_array = explode('_',$filename);
    $filename_list = $filename_array[0]."_2";
    $edit_form_num = explode(',',$form_ini[$filename]['edit_form_num']);
    
    //変数
    $edit_value = array();
    $edit_sql = "";
    $judge = false;
    
    //処理
    $con = dbconect();
    $edit_sql .= $SQL_ini[$filename_list]['select_sql'];
    $main_code = $form_ini[$form_ini[$filename]['main_code']]['column'];
    $edit_sql .= " AND ".$main_code." = '".$edit_id."';";
    $result = $con->query($edit_sql) or ($judge = true);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        for($i = 0; $i < count($edit_form_num); $i++)
       {
           $edit_value[$edit_form_num[$i]] = $result_row[$form_ini[$edit_form_num[$i]]['column']];
       }       
    }
    return $edit_value;
}

/************************************************************************************************************
function update($post)

引数		$post						登録情報

戻り値		なし
************************************************************************************************************/
function update($post){
    
    //初期設定
    require_once ("f_Form.php");
    require_once ("f_DB.php");																							// DB関数呼び出し準備
    require_once ("f_SQL.php");	
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $sql = "";
    $judge = false;
    
    //処理
    $con = dbconect();
    $sql = UpdateSQL($post);
    $result = $con->query($sql) or ($judge = true); 
}

/************************************************************************************************************
function progress_update($post)

引数		$post						登録情報

戻り値		なし
************************************************************************************************************/
function progress_update($post){
    
    //初期設定
    require_once ("f_Form.php");
    require_once ("f_DB.php");																							// DB関数呼び出し準備
    require_once ("f_SQL.php");	
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $sql = "";
    $judge = false;
    $progress_data = array("","","","","","","","","","");
    $counter = 0;
    
    //処理
    $con = dbconect();
    
    //登録済みデータ取得
    $sql = "SELECT 7CODE FROM progressinfo AS progressinfo ";
    $sql .= "LEFT JOIN projectditealinfo AS projectditealinfo ON progressinfo.6CODE = projectditealinfo.6CODE ";
    $sql .= "WHERE projectditealinfo.4CODE = '".$post['401']."' AND SAGYOUDATE = '".$post['704']."';";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $progress_data[$counter] = $result_row['7CODE'];
        $counter++;
    }
    
    for($i = 0; $i < 10; $i++)
    {
        if($i < $counter)
        {
            if($post['601_'.$i] != "")
            {
                $sql = "UPDATE progressinfo SET 6CODE = '".$post['601_'.$i]."',3CODE = '".$post['301_'.$i]."',SAGYOUDATE = '".$post['704']."',TEIZITIME = '".$post['705_'.$i]."',ZANGYOUTIME = '".$post['706_'.$i]."' WHERE 7CODE = '".$progress_data[$i]."';";
            }
            else
            {
                $sql = "DELETE FROM progressinfo WHERE 7CODE = '".$progress_data[$i]."';";
            }
        }
        else
        {
            if($post['601_'.$i] != "")
            {
                $sql = "INSERT INTO progressinfo (6CODE,3CODE,SAGYOUDATE,TEIZITIME,ZANGYOUTIME) VALUES('".$post['601_'.$i]."','".$post['301_'.$i]."','".$post['704']."','".$post['705_'.$i]."','".$post['706_'.$i]."');";
            }
        }
        if($sql != "")
        {
            $result = $con->query($sql);
        }
        $sql = "";
    }
}

/************************************************************************************************************
function pjtouroku_update($post)

引数		$post						登録情報

戻り値		なし
************************************************************************************************************/
function pjtouroku_update($post){
    
    //初期設定
    require_once ("f_Form.php");
    require_once ("f_DB.php");																							// DB関数呼び出し準備
    require_once ("f_SQL.php");	
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $sql = "";
    $judge = false;
    $detail_charge_list =array();
    
    //プロジェクト更新処理
    if($post['508'] == "")
    {
        $post['508'] = "NULL";
    }
    else
    {
        $post['508'] = "'".$post['508']."-01'";
    }
    $con = dbconect();
    $sql = "UPDATE projectinfo SET ANKENID = '".$post['504']."',EDABAN = '".$post['505']."',PJNAME='".$post['506']."',CHAEGE='".$post['goukei']."',5STARTDATE='".$post['509']."',URIAGEMONTH=".$post['508']." WHERE 5CODE = '".$post['edit_id']."';";
    $result = $con->query($sql) or ($judge = true);
    
    //登録済み社員別金額取得
    $sql = "SELECT *FROM projectditealinfo WHERE 5CODE = '".$post['edit_id']."';";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $detail_charge_list[$result_row['4CODE']] = $result_row['DETALECHARGE'];
    }
    
    //社員別金額更新
    $keyarray = array_keys($post);
    foreach($keyarray as $key)
    {
        if(strstr($key, 'kingaku_'))
        {
            if($post[$key] != "")
            {
                $name_arrsy = explode('_',$key);
                if(isset($detail_charge_list[$name_arrsy[1]]))
                {
                    $sql = "UPDATE projectditealinfo SET DETALECHARGE = ".$post[$key]." WHERE 5CODE = '".$post['edit_id']."' AND 4CODE = '".$name_arrsy[1]."';";
                    $result = $con->query($sql);
                }
                else
                {
                    $sql = "INSERT INTO projectditealinfo (4CODE,5CODE,DETALECHARGE) VALUES('".$name_arrsy[1]."','".$post['edit_id']."',".$post[$key].");";
                    $result = $con->query($sql);
                }
            }
        }
    }
}

/************************************************************************************************************
function kokyakuteam_update($post)

引数		$post						登録情報

戻り値		なし
************************************************************************************************************/
function kokyakuteam_update($post){
    
    //初期設定
    require_once ("f_Form.php");
    require_once ("f_DB.php");																							// DB関数呼び出し準備
    require_once ("f_SQL.php");	
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $sql = "";
    $before_data = array();
    $judge = false;
    $member = "";
    
    //処理
    $con = dbconect();
    
    //更新前顧客情報取得
    $sql = "SELECT b.12CODE,KOKYAKUNAME FROM teaminfo AS a LEFT JOIN kokyakuinfo AS b ON a.12CODE = b.12CODE WHERE 13CODE = '".$post['edit_id']."';";
    $result = $con->query($sql) or ($judge = true);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $before_data['12CODE'] = $result_row['12CODE'];
        $before_data['KOKYAKUNAME'] = $result_row['KOKYAKUNAME'];
    }
    
    //顧客名が編集されている場合、更新処理を行う
    if($before_data['KOKYAKUNAME'] != $post['1203'])
    {
        $sql = "UPDATE kokyakuinfo SET KOKYAKUNAME = '".$post['1203']."' WHERE 12CODE = '".$before_data['12CODE']."';";
        $result = $con->query($sql) or ($judge = true);
    }
    
    //メンバー情報作成
    if(isset($post['all_check']))
    {
        $member = '0';
    }
    else
    {
        for($i = 0; $i < count($post['checkbox']); $i++)
        {
            $member .= $post['checkbox'][$i].',';
        }
        $member = substr($member,0,-1);
    }
    
    //チーム情報を更新
    $sql = "UPDATE teaminfo SET TEAMNAME = '".$post['1304']."',MEMBER = '".$member."' WHERE 13CODE = '".$post['edit_id']."';";
    $result = $con->query($sql) or ($judge = true);    
}

/************************************************************************************************************
function existCheck($post,$tablenum,$type)

引数1		$post							登録フォーム入力値
引数2		$tablenum						テーブル番号
引数3		$type							1:insert 2:edit 3:delete

戻り値       $errorinfo						既登録確認結果
************************************************************************************************************/
function existCheck($post,$tablenum,$type){
	
    //初期設定
    $form_ini = parse_ini_file('./ini/form.ini', true);
    require_once ("f_Form.php");							
    require_once ("f_SQL.php");
    
    //定数
    $filename = $_SESSION['filename'];
    $uniquecheck = explode(',',$form_ini[$filename]['uniquecheck']);
    
    //変数
    $errorinfo = array();
    $errorinfo[0] = "";
    $sql = "";
    $judge = false;
    
    //処理
    $con = dbconect();
    if($type == 1)
    {
        for($i = 0; $i < count($uniquecheck); $i++)
        {
            if($uniquecheck[$i] == "")
            {
                break;
            }
            $sql = uniqeSelectSQL($post,$tablenum,$uniquecheck[$i]);
            if($sql != '')
            {
                $result = $con->query($sql) or ($judge = true);
                if($judge)
                {
                    error_log($con->error,0);
                    $judge = false;
                }
                if(isset($result->num_rows) && $result->num_rows != 0 )
                {
                    $errorinfo[0] .= $uniquecheck[$i].",";
                }
            }
        }
    }
    elseif($type == 2)
    {
        for($i = 0; $i < count($uniquecheck); $i++)
        {
            if($uniquecheck[$i] == "")
            {
                break;
            }
            $sql = uniqeSelectSQL($post,$tablenum,$uniquecheck[$i]);
            if($sql != '')
            {
                $main_code = $form_ini[$form_ini[$filename]['main_code']]['column'];
                $sql .= "AND ".$main_code." != ".$post['edit_id']." ";
                $result = $con->query($sql) or ($judge = true);
                if($judge)
                {
                    error_log($con->error,0);
                    $judge = false;
                }
                if(isset($result->num_rows) && $result->num_rows != 0 )
                {
                    $errorinfo[0] .= $uniquecheck[$i].",";
                }
            }
        }        
    }
    return $errorinfo;
}

/************************************************************************************************************
function makeList_keihi($sql,$post)

引数1           $sql                                      検索SQL

戻り値          $list_html                              リストhtml
************************************************************************************************************/
function makeList_keihi($sql,$post){
    
    //初期設定
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    require_once ("f_Form.php");
    require_once ("f_DB.php");
    require_once ("f_SQL.php");
    
    //定数
    $filename = $_SESSION['filename'];
    $columns_array = explode(',',$SQL_ini[$filename]['listcolumns']);
    $columnname_array = explode(',',$SQL_ini[$filename]['clumnname']);
    $main_code = $form_ini[$filename]['main_code'];
    
    //変数
    $list_html = "";
    $judge = false;
    $counter = 1;
    
    //処理
    $con = dbconect();
    $list_html .= "<div class='list_scroll'>";
    $list_html .= "<table>";
   
    //項目名作成処理
    $list_html .= "<thead><tr>";
    $list_html .= "<th><a>No</a></th>";
    for($i = 0; $i < count($columnname_array); $i++)
    {
        $list_html .= "<th><a>".$columnname_array[$i]."</a></th>";
    }
    $list_html .= "<th><a>編集</a></th>";
    $list_html .= "</tr></thead>";
    
    //経費情報取得
    $keihi_data = array();
    $keihi_sql = "SELECT 5CODE,kubun,SUM(charge) AS charge FROM keihiinfo GROUP BY 5CODE,kubun;";
    $result = $con->query($keihi_sql) or ($judge = true);																		// クエリ発行
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $keihi_data[$result_row['5CODE']][$result_row['kubun']] = $result_row['charge'];
    }   
    
    //一覧表内容作成処理
    $list_html .= "<tbody>";
    $result = $con->query($sql[0]) or ($judge = true);																		// クエリ発行
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        if(($counter%2) == 1)
        {
            $list_html .= "<tr>";
        }
        else
        {
            $list_html .= "<tr class='list_stripe'>";
        }
        $list_html .= "<td>".$counter."</td>";
        for($i = 0; $i < count($columns_array); $i++)
        {
            $list_html .= "<td>".$result_row[$columns_array[$i]]."</td>";            
        }
        
        //交通費表示
        if(isset($keihi_data[$result_row['5CODE']]['0']))
        {
            $koutuhi = $keihi_data[$result_row['5CODE']]['0'];
        }
        else
        {
            $koutuhi = 0;
        }
        $list_html .= "<td align='right'>".$koutuhi."</td>";
        
        //その他表示
        if(isset($keihi_data[$result_row['5CODE']]['1']))
        {
            $sonota = $keihi_data[$result_row['5CODE']]['1'];
        }
        else
        {
            $sonota = 0;
        }
        $list_html .= "<td align='right'>".$sonota."</td>";
        
        //経費合計表示
        $list_html .= "<td align='right'>".($koutuhi + $sonota)."</td>";        
        
        $list_html .= "<td><input type='submit' name='keihinyuryoku_5_button_".$result_row[$form_ini[$main_code]['column']]."' value='編集'></td>";
        $list_html .= "</tr>";
        $counter++;
    }
    $list_html .= "</tbody>";
    
    $list_html .= "</table>";
    $list_html .= "</div>";
    return $list_html;
}

/************************************************************************************************************
function make_listUser($edit_id)

引数1           $edit_id                                      更新ID

戻り値          $list_html                              リストhtml
************************************************************************************************************/
function makekeihi_form($edit_id)
{
    //初期設定
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    require_once ("f_Form.php");
    require_once ("f_DB.php");
    require_once ("f_SQL.php");

    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $list_html = "";
    $judge = false;
    $counter = 1;
    $pulldown_html = "";
    $keihi_array = array();
    
    //プロジェクトコード、プロジェクト名取得
    $con = dbconect();
    $sql = "SELECT 5CODE,substring(KOKYAKUID,1,2) AS PERIOD,CONCAT(KOKYAKUID,'-',TEAMID,'-',ANKENID,'-',EDABAN) AS PJCODE,PJNAME FROM projectinfo AS projectinfo ";
    $sql .= "LEFT JOIN kokyakuinfo AS kokyakuinfo ON projectinfo.12CODE = kokyakuinfo.12CODE ";
    $sql .= "LEFT JOIN teaminfo AS teaminfo ON projectinfo.13CODE = teaminfo.13CODE WHERE 5CODE = '".$edit_id."';";
    $result = $con->query($sql) or ($judge = true);																		// クエリ発行    
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        //プロジェクトコード、プロジェクト名表示
        $list_html .= "<input type='hidden' name='edit_id' value='".$edit_id."'>";
        $list_html .= "<table>";
        $list_html .= "<tr>";
        $list_html .= "<td>プロジェクトコード</td>";
        $list_html .= "<td><input type='text' value='".$result_row['PJCODE']."' name='PJCODE' class='form_text disabled' size='30'></td>";
        $list_html .= "</tr>";
        $list_html .= "<tr>";
        $list_html .= "<td>プロジェクト名</td>";
        $list_html .= "<td><input type='text' value='".$result_row['PJNAME']."' name='PJNAME' class='form_text disabled' size='60'></td>";       
        $list_html .= "</tr>";
        $list_html .= "</table>";
    }
    
    //項目名作成
    $list_html .= '<div style="margin-top: 5px; margin-bottom: 5px;">';
    $list_html .= "<div class='list_scroll' style='height: 350px;'>";
    $list_html .= "<table>";
    $list_html .= "<tr>";
    $list_html .= "<th>No</th>";
    $list_html .= "<th>社員選択</th>";
    $list_html .= "<th>区分</th>";
    $list_html .= "<th>金額</th>";
    $list_html .= "<th>年月</th>";
    $list_html .= "</tr>";

    //経費情報取得
    $counter = 0;
    $sql = "SELECT 4CODE,kubun,charge,date_format(month, '%Y-%m') AS month FROM keihiinfo WHERE 5CODE = '".$edit_id."';";
    $result = $con->query($sql) or ($judge = true);																		// クエリ発行    
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $keihi_array[$counter]['syain'] = $result_row['4CODE'];
        $keihi_array[$counter]['kubun'] = $result_row['kubun'];
        $keihi_array[$counter]['charge'] = $result_row['charge'];
        $keihi_array[$counter]['month'] = $result_row['month'];
        $counter++;
    }    
    
    //入力欄作成
    for($i = 0; $i < 15; $i++)
    {
        $list_html .= "<tr>";
        
        //値
        if(isset($keihi_array[$i]))
        {
            $syain = $keihi_array[$i]['syain'];
            $kubun = $keihi_array[$i]['kubun'];
            $charge = $keihi_array[$i]['charge'];
            $month = $keihi_array[$i]['month'];
        }
        else
        {
            $syain = "";
            $kubun = "";
            $charge = "";
            $month = "";
        }
        
        //No
        $list_html .= "<td>".($i + 1)."</td>";
        
        //社員選択プルダウン
        $sql = "SELECT *FROM syaininfo WHERE LUSERNAME IS NOT NULL AND LUSERPASS IS NOT NULL ORDER BY STAFFID ASC;";
        $result = $con->query($sql) or ($judge = true);																		// クエリ発行    
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            if($syain == $result_row['4CODE'])
            {
                $pulldown_html .= "<option value='".$result_row['4CODE']."' selected>".$result_row['STAFFNAME']."</option>";
            }
            else
            {
                $pulldown_html .= "<option value='".$result_row['4CODE']."'>".$result_row['STAFFNAME']."</option>";
            }
        }
        $list_html .= "<td>";
        $list_html .= "<select id='syain_".$i."' name='syain_".$i."' class='form_text' onchange='input_style(this.id,true);'>";
        $list_html .= "<option value=''>指定なし</option>";
        $list_html .= $pulldown_html;
        $pulldown_html = "";
        $list_html .= "</select>";
        $list_html .= "</td>";
        
        //区分
        $list_html .= "<td>";
        $list_html .= "<select id='kubun_".$i."' name='kubun_".$i."' class='form_text' onchange='input_style(this.id,true);'>";
        
        if($kubun == '')
        {
            $list_html .= "<option value='' selected>指定なし</option>";
            $list_html .= "<option value='0'>交通費</option>";
            $list_html .= "<option value='1'>その他</option>";        
        }
        elseif($kubun == '0')
        {
            $list_html .= "<option value=''>指定なし</option>";
            $list_html .= "<option value='0' selected>交通費</option>";
            $list_html .= "<option value='1'>その他</option>";                    
        }        
        else
        {
            $list_html .= "<option value=''>指定なし</option>";
            $list_html .= "<option value='0'>交通費</option>";
            $list_html .= "<option value='1' selected>その他</option>";                       
        }
        $list_html .= "</select>";
        $list_html .= "</td>";
        
        //金額
        $list_html .= "<td>";
        $list_html .= "<input type='text' id='charge_".$i."' name='charge_".$i."' value='".$charge."' class='form_text' onchange='kingaku_check(this.id);' placeholder='半角数字7字以内'>";
        $list_html .= "</td>";
        
        //年月
        $list_html .= "<td>";
        $list_html .= "<input type='month' id='month_".$i."' name='month_".$i."' value='".$month."' class='form_text' onchange='input_style(this.id,true);'>";
        $list_html .= "</td>";
        
        $list_html .= "</tr>";
    }
    $list_html .= "</table>";
    $list_html .= "</div>";
    $list_html .= "</div>";
    return $list_html;
}

/************************************************************************************************************
function make_listUser($sql,$post)

引数1           $sql                                      検索SQL

戻り値          $list_html                              リストhtml
************************************************************************************************************/
function make_listUser($sql,$post){
    
    //初期設定
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    require_once ("f_Form.php");
    require_once ("f_DB.php");
    require_once ("f_SQL.php");
    
    //定数
    $filename = $_SESSION['filename'];
    $columns_array = explode(',',$SQL_ini[$filename]['listcolumns']);
    $columnname_array = explode(',',$SQL_ini[$filename]['clumnname']);
    $main_code = $form_ini[$filename]['main_code'];
    
    //変数
    $list_html = "";
    $judge = false;
    $counter = 1;
    
    //処理
    $con = dbconect();
    $list_html .= "<div class='list_scroll'>";
    $list_html .= "<table>";
   
    //項目名作成処理
    $list_html .= "<thead><tr>";
    $list_html .= "<th><a>No</a></th>";
    for($i = 0; $i < count($columnname_array); $i++)
    {
        $list_html .= "<th><a>".$columnname_array[$i]."</a></th>";
    }
    $list_html .= "<th><a>編集</a></th>";
    $list_html .= "</tr></thead>";
    
    //一覧表内容作成処理
    $list_html .= "<tbody>";
    $result = $con->query($sql[0]) or ($judge = true);																		// クエリ発行
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        if(($counter%2) == 1)
        {
            $list_html .= "<tr>";
        }
        else
        {
            $list_html .= "<tr class='list_stripe'>";
        }
        $list_html .= "<td>".$counter."</td>";
        for($i = 0; $i < count($columns_array); $i++)
        {
            $list_html .= "<td>".$result_row[$columns_array[$i]]."</td>";
        }
        $list_html .= "<td><input type='submit' name='editUser_5_button_".$result_row[$form_ini[$main_code]['column']]."' value='編集'></td>";
        $list_html .= "</tr>";
        $counter++;
    }
    $list_html .= "</tbody>";
    
    $list_html .= "</table>";
    $list_html .= "</div>";
    return $list_html;
}

/************************************************************************************************************
function makeGenka_item($sql)

引数1           $sql                                      検索SQL

戻り値          $list_html                              リストhtml
************************************************************************************************************/
function makeGenka_item($sql){
    
    //初期設定
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    require_once ("f_Form.php");
    require_once ("f_DB.php");
    require_once ("f_SQL.php");

    //定数
    $filename = $_SESSION['filename'];
    $columns_array = explode(',',$SQL_ini[$filename]['listcolumns']);
    $columnname_array = explode(',',$SQL_ini[$filename]['clumnname']);
    
    //変数
    $list_html = "";
    $judge = false;
    $counter = 1;
    
    //処理
    $con = dbconect();
    $list_html .= "<div class='list_scroll'>";
    $list_html .= "<table>";

    //項目名作成処理
    $list_html .= "<thead><tr>";
    $list_html .= "<th><a>No</a></th>";
    for($i = 0; $i < count($columnname_array); $i++)
    {
        $list_html .= "<th><a>".$columnname_array[$i]."</a></th>";
    }
    $list_html .= "</tr></thead>";
    
    //一覧表内容作成処理
    $list_html .= "<tbody>";
    $result = $con->query($sql[0]) or ($judge = true);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        if(($counter%2) == 1)
        {
            $list_html .= "<tr>";
        }
        else
        {
            $list_html .= "<tr class='list_stripe'>";
        }
        $list_html .= "<td>".$counter."</td>";
        $list_html .= "<td>".$result_row['STAFFID']."</td>";
        $list_html .= "<td>".$result_row['STAFFNAME']."</td>";
        $list_html .= "<td><input type='text' name='1402_".$result_row['4CODE']."' size='20' id='1402_".$result_row['4CODE']."' value='".$result_row['GENKA']."' class='form_text' onchange='kingaku_check(this.id);' placeholder='半角数字7字以内'></td>";
        $list_html .= "<td><input type='text' name='1403_".$result_row['4CODE']."' size='20' id='1403_".$result_row['4CODE']."' value='".$result_row['ZANGYOTANKA']."' class='form_text' onchange='kingaku_check(this.id);' placeholder='半角数字7字以内'></td>";
        $list_html .= "</tr>";
        $counter++;
    }
    $list_html .= "</tbody>";
    $list_html .= "</table>";
    $list_html .= "</div>";
    return $list_html;
}

function get_kokyaku(){
    
    //初期設定
    require_once ("f_DB.php");

    //変数
    $kokyaku = "";
    $sql = "";

    //処理
    $con = dbconect();
    $sql = "SELECT *FROM kokyakuinfo;";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $kokyaku .= $result_row['KOKYAKUID'].'#$'.$result_row['KOKYAKUNAME'].'#$';
    }
    
    return $kokyaku; 
}

function get_syain(){
    
    //初期設定
    require_once ("f_DB.php");

    //変数
    $syain = "";
    $sql = "";

    //処理
    $con = dbconect();
    $sql = "SELECT *FROM syaininfo;";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $syain .= $result_row['STAFFID'].'#$'.$result_row['STAFFNAME'].'#$';
    }
    
    return $syain; 
}

function get_koutei(){
    
    //初期設定
    require_once ("f_DB.php");

    //変数
    $koutei = "";
    $sql = "";

    //処理
    $con = dbconect();
    $sql = "SELECT *FROM kouteiinfo;";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $koutei .= $result_row['KOUTEIID'].'#$'.$result_row['KOUTEINAME'].'#$';
    }
    
    return $koutei; 
}

function get_team(){
    
    //初期設定
    require_once ("f_DB.php");

    //変数
    $team = "";
    $sql = "";

    //処理
    $con = dbconect();
    $sql = "SELECT *FROM teaminfo AS a LEFT JOIN kokyakuinfo AS b ON a.12CODE = b.12CODE;";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $team .= $result_row['KOKYAKUID'].'#$'.$result_row['TEAMID'].'#$'.$result_row['TEAMNAME'].'#$';
    }
    return $team; 
}

function get_member(){
    
    //初期設定
    require_once ("f_DB.php");

    //変数
    $member = "";
    $sql = "";

    //処理
    $con = dbconect();
    $sql = "SELECT *FROM teaminfo AS a LEFT JOIN kokyakuinfo AS b ON a.12CODE = b.12CODE;";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $member .= $result_row['KOKYAKUID'].'#$'.$result_row['TEAMID'].'#$'.$result_row['MEMBER'].'#$';
    }
    return $member;
}
/************************************************************************************************************
function get_progress_data($edit_id)

引数1           $edit_id                                 編集データ

戻り値          $progress_data                      工数情報
************************************************************************************************************/
function get_progress_data($edit_id){
    
    //初期設定
    require_once ("f_DB.php");
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    
    //変数
    $progress_data = array();
    $counter = 0;
    
    //日付と社員IDを取得する
    $con = dbconect();
    $sql = "SELECT syaininfo.4CODE,STAFFID,STAFFNAME,SAGYOUDATE FROM progressinfo AS progressinfo ";
    $sql .= "LEFT JOIN projectditealinfo AS projectditealinfo ON progressinfo.6CODE = projectditealinfo.6CODE ";
    $sql .= "LEFT JOIN syaininfo AS syaininfo ON projectditealinfo.4CODE = syaininfo.4CODE ";
    $sql .= "WHERE 7CODE = '".$edit_id."';"; 
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $progress_data['401'] = $result_row['4CODE'];
        $progress_data['402'] = $result_row['STAFFID'];
        $progress_data['403'] = $result_row['STAFFNAME'];
        $progress_data['704'] = $result_row['SAGYOUDATE'];
    }
    
    //工数情報を取得する
    $progress_data['pjstat'] = "0";
    $sql = "SELECT 5PJSTAT,TEIZITIME,ZANGYOUTIME,kouteiinfo.3CODE,KOUTEIID,KOUTEINAME,progressinfo.6CODE,PJNAME,CONCAT(KOKYAKUID,'-',TEAMID,'-',ANKENID,'-',EDABAN) AS PJCODE FROM progressinfo AS progressinfo ";
    $sql .= "LEFT JOIN projectditealinfo AS projectditealinfo ON progressinfo.6CODE = projectditealinfo.6CODE ";
    $sql .= "LEFT JOIN projectinfo AS projectinfo ON projectinfo.5CODE = projectditealinfo.5CODE ";
    $sql .= "LEFT JOIN kokyakuinfo AS kokyakuinfo ON kokyakuinfo.12CODE = projectinfo.12CODE ";
    $sql .= "LEFT JOIN teaminfo AS teaminfo ON teaminfo.13CODE = projectinfo.13CODE ";
    $sql .= "LEFT JOIN syaininfo AS syaininfo ON projectditealinfo.4CODE = syaininfo.4CODE ";
    $sql .= "LEFT JOIN kouteiinfo AS kouteiinfo ON progressinfo.3CODE = kouteiinfo.3CODE ";
    $sql .= "WHERE syaininfo.4CODE = '".$progress_data['401']."' AND SAGYOUDATE = '".$progress_data['704']."';";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $progress_data['301_'.$counter] = $result_row['3CODE'];
        $progress_data['302_'.$counter] = $result_row['KOUTEIID'];
        $progress_data['303_'.$counter] = $result_row['KOUTEINAME'];
        $progress_data['601_'.$counter] = $result_row['6CODE'];
        $progress_data['PJCODE_'.$counter] = $result_row['PJCODE'];
        $progress_data['506_'.$counter] = $result_row['PJNAME'];
        $progress_data['705_'.$counter] = $result_row['TEIZITIME'];
        $progress_data['706_'.$counter] = $result_row['ZANGYOUTIME'];
        if($result_row['5PJSTAT'] == "1")
        {
            $progress_data['pjstat'] = "1";
        }
        $counter++;
    }
    return $progress_data;
}

/************************************************************************************************************
function makeCalendarHtml()

引数1           なし

戻り値          $calendar_html              カレンダーHTML
************************************************************************************************************/
function makeCalendarHtml(){
    
    //変数
    $calendar_html = "";
    
    //処理
    if(isset($_SESSION['TOP_2']))
    {
        $ym = $_SESSION['TOP_2'];
    }
    else 
    {
        $ym = date('Y-m');
    }
    
    //タイトル作成
    $year = date('Y', strtotime($ym));
    $month = date('m', strtotime($ym));
    $calendar_html .= '<button type="submit" class="icon_button" type="button" name="prev"><i class="fas fa-chevron-left"></i></button>';       
    $calendar_html .= "　".$year."年".$month."月　";
    $calendar_html .= '<button type="submit" class="icon_button" type="button" name="next" style="margin-right: 2px;"><i class="fas fa-chevron-right"></i></button>';     
    $calendar_html .= "<input type='submit' value='ファイル取込' class='list_button' name = 'TOP_6_button'>";
    if(str_pad(($month - 1), 2, 0, STR_PAD_LEFT) == '0')
    {
        $prev_value = ($year - 1).'-'.'12';
    }
    else
    {
        $prev_value = $year.'-'.str_pad(($month - 1), 2, 0, STR_PAD_LEFT);
    }
    if(str_pad(($month + 1), 2, 0, STR_PAD_LEFT) == '13')
    {
        $next_value = ($year + 1).'-'.'01';
    }
    else
    {
        $next_value = $year.'-'.str_pad(($month + 1), 2, 0, STR_PAD_LEFT);
    }
    $calendar_html .= "<input type='hidden' value='".$prev_value."' name='prev_month'>";
    $calendar_html .= "<input type='hidden' value='".$year.'-'.str_pad($month, 2, 0, STR_PAD_LEFT)."' name='now_month'>";
    $calendar_html .= "<input type='hidden' value='".$next_value."' name='next_month'>";
    //カレンダー作成
    $calendar_html .= "<table class='calendar_table' border='1' cellspacing='0'>";
    $calendar_html .= "<tr><th style='color: red;'>日</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th style='color: blue;'>土</th></tr>";
    $calendar = makeCalendar($month,$year);
    $td_cnt = 0;
    
    //工数情報取得
    $data = get_calendar_data($ym);
    $min = lastEndMonth();
    $min = new DateTime($min);
     for($i = 0; $i < count($calendar); $i++)
    {
        $today = new DateTime('today');
        switch ($td_cnt)
        {
            case '0':
                $calendar_html .= '<tr>';
                $date_color = 'red';
                break;
            case '6':
                $date_color = 'blue';
                break;
            default :
                $date_color = 'black';
                break;
        }
        
        $calendar_html .= '<td>';
        $dayCcount = str_pad($calendar[$i]['day'], 2, 0, STR_PAD_LEFT);
        $time = $ym . '-' . $dayCcount;
        $date = new DateTime($ym . '-' . $dayCcount);
        if($today < $date)
        {
            $calendar_html .= '<a style="color: '.$date_color.';" class="hiduke">'.$calendar[$i]['day'].'</a>';
        }
        elseif($min > $date)
        {
            $calendar_html .= '<a style="color: '.$date_color.';" class="hiduke">'.$calendar[$i]['day'].'</a>';
        }
        elseif($calendar[$i]['day'] != "")
        {
            if(isset($data[$time]))
            {
                $name = "TOP_3_button";
            }
            else
            {
                $name = "TOP_1_button";
            }
            $calendar_html .= '<button type="submit" value="'.$time.'" class="hiduke_button" name="'.$name.'" style="color: '.$date_color.';">'.$calendar[$i]['day'].'</button>';
        }

        if(isset($data[$time]))
        {
            $teizi = $data[$time]['teizi'];
            $zangyou = $data[$time]['zangyou'];
        }
        else
        {
            $teizi = '';
            $zangyou = '';

        }
        if($calendar[$i]['day'] != "" && $teizi == '7.75' && ($teizi + $zangyou) <= '24.00')
        {
            $calendar_html .= '<button class="icon_button" type="button" title="コピー" onclick="showdialog('."'$time'".')" style="float: right; margin-right: 3px;"><i class="far fa-copy faa-tada animated-hover"></i></button>';
        }
        
        if($teizi != '7.75')
        {
            $color = ' color: red; ';
        }
        else
        {
            $color = "";
        }
        if($calendar[$i]['day'] == "1")
        {
            $calendar_html .= '<div class="teizitime">[定]<a style="float: right; margin-right: 3px; '.$color.'">'.$teizi.'</a></div>';
            $calendar_html .= '<div class="zangyoutime">[残]<a style="float: right; margin-right: 3px;">'.$zangyou.'</a></div>';
        }
        elseif($calendar[$i]['day'] != "")
        {
            $calendar_html .= '<div class="teizitime">　<a style="float: right; margin-right: 3px; '.$color.'">'.$teizi.'</a></div>';
            $calendar_html .= '<div class="zangyoutime">　<a style="float: right; margin-right: 3px;">'.$zangyou.'</a></div>';
        }        
        $calendar_html .= '</td>';
        
        if($td_cnt == 6)
        {
            $calendar_html .= '</tr>';
            $td_cnt = 0;
        }
        else
        {
            $td_cnt++;
        }
        
    }
    
    $calendar_html .= "</table>";
    
    return $calendar_html;
}

/************************************************************************************************************
function makeCalendar()


引数1	$month                       月情報
引数2   $year                        年情報

戻り値	$calendar                    カレンダーデータ作成
************************************************************************************************************/
	
function makeCalendar($month,$year){    

    // 月末日を取得
    $last_day = date('j', mktime(0, 0, 0, $month + 1, 0, $year));
    $calendar = array();
    $j = 0;

    // 月末日までループ
    for ($i = 1; $i < $last_day + 1; $i++) 
    {
        // 曜日を取得
        $week = date('w', mktime(0, 0, 0, $month, $i, $year));

        // 1日の場合
        if ($i == 1) 
        {
            // 1日目の曜日までをループ
            for ($s = 1; $s <= $week; $s++) 
            {
                // 前半に空文字をセット
                $calendar[$j]['day'] = '';
                $j++;
            }
        }

        // 配列に日付をセット
        $calendar[$j]['day'] = $i;
        $j++;

        // 月末日の場合
        if ($i == $last_day) 
        {
            // 月末日から残りをループ
            for ($e = 1; $e <= 6 - $week; $e++) 
            {
                // 後半に空文字をセット
                $calendar[$j]['day'] = '';
                $j++;
            }
        }
    }    
    return $calendar;
}

/************************************************************************************************************
function get_calendar_data($calendar)


引数1       $calendar                      カレンダー情報

戻り値	$calendar_data                    カレンダーデータ作成
************************************************************************************************************/
	
function get_calendar_data($ym){
    
    //初期設定
    require_once ("f_DB.php");
    
    //変数
    $calendar_data = array();
    
    //作業時間計算処理
    $con = dbconect();
    $sql = "SELECT SAGYOUDATE,TEIZITIME,ZANGYOUTIME,5PJSTAT FROM progressinfo AS progressinfo ";
    $sql .= "LEFT JOIN projectditealinfo AS projectditealinfo ON progressinfo.6CODE = projectditealinfo.6CODE ";
    $sql .= "LEFT JOIN projectinfo AS projectinfo ON projectditealinfo.5CODE = projectinfo.5CODE ";
    $sql .= "WHERE 4CODE = '".$_SESSION['user']['4CODE']."' AND SAGYOUDATE LIKE '%".$ym."%';";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        if(!isset($calendar_data[$result_row['SAGYOUDATE']]))
        {
            $calendar_data[$result_row['SAGYOUDATE']]['teizi'] = 0;
            $calendar_data[$result_row['SAGYOUDATE']]['zangyou'] = 0;
            $calendar_data[$result_row['SAGYOUDATE']]['pjstat'] = '0';
        }
        $calendar_data[$result_row['SAGYOUDATE']]['teizi'] += $result_row['TEIZITIME'];
        $calendar_data[$result_row['SAGYOUDATE']]['zangyou'] += $result_row['ZANGYOUTIME'];
        //終了フラグデータを格納する
        if($result_row['5PJSTAT'] == '1')
        {
            $calendar_data[$result_row['SAGYOUDATE']]['pjstat'] = $result_row['5PJSTAT'];
        }
    }
    
    return $calendar_data;
}

/************************************************************************************************************
最後の月次処理された月取得
function lastEndMonth()

引数1		

戻り値		$lastEndmonth　　締め処理済の最終月のよく月の一日
************************************************************************************************************/
function lastEndMonth()
{
    //------------------------//
    //          処理          //
    //------------------------//
    $con = dbconect();	
    $sql = "SELECT * FROM endmonthinfo ORDER BY 10code DESC LIMIT 1;";
    $result = $con->query($sql);
    if($result->num_rows == '0')
    {
        $system_ini = parse_ini_file('./ini/system.ini', true);
        $lastEndmonth = $system_ini['period']['startyear']."-";
        $m =(int)$system_ini['period']['startmonth'];
    }
    else
    {
        $result_row = $result->fetch_array(MYSQLI_ASSOC);
        $lastEndmonth = $result_row['YEAR']."-";
        $m =(int)$result_row['MONTH'];        
    }
    //締め処理が終わった次の月の一日
    if($m == 12)
    {
        $lastEndmonth .= "01-01";
    }
    else if($m == 10 || $m == 11)
    {
        $m++;
        $lastEndmonth .= $m."-01"; 
    }
    else
    {
        $m++;
        $lastEndmonth .= "0".$m."-01"; 
    }
    return ($lastEndmonth);
}

/************************************************************************************************************
function make_teiji_list()


引数1       なし

戻り値	$teiji_list             定時チェックリスト
************************************************************************************************************/
	
function make_teiji_list(){
    
    //初期設定
    require_once ("f_DB.php");
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    
    //変数
    $teiji_list = "";
    $counter = 0;
    
    //処理
    $con = dbconect();
    $sql = $SQL_ini['teiji_5']['select_sql'];
    $result = $con->query($sql);
    
    $teiji_list .= "<div class='list_scroll' style='max-height: 385px;'>";
    $teiji_list .= "<table id='checkboxlist'>";
    $teiji_list .= "<tr><th>選択</th><th>社員番号</th><th>社員名</th></tr>";
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        if(($counter % 2) == 1)
        {
            $teiji_list .= "<tr class='list_stripe'>";
        }
        else
        {
            $teiji_list .= "<tr>";
        }
        $teiji_list .= "<td onmousemove='mouseMove(this.parentNode.rowIndex,checkboxlist);' onmouseout='mouseOut(this.parentNode.rowIndex,checkboxlist);'><label for='check".$counter."' style='display:block;width:100%;height:100%;'><input type='checkbox' name='checkbox[]' id='check".$counter."' value='".$result_row['4CODE']."' class='checkbox_style'></label></td>";
        $teiji_list .= "<td>".$result_row['STAFFID']."</td>";
        $teiji_list .= "<td>".$result_row['STAFFNAME']."</td>";
        $teiji_list .= "</tr>";
        $counter++;
    }
    $teiji_list .= "</table>";
    $teiji_list .= "</div>";
    
    return $teiji_list;
}

/************************************************************************************************************
function teijicheck()


引数1       $post               情報

戻り値	$error              エラーデータ
************************************************************************************************************/
function teijicheck($post){
    
    //初期設定
    $system_ini = parse_ini_file('./ini/system.ini', true);
  
    //定数
    $teijitime = (float)$system_ini['settime']['teijitime'];
    $syainArray = $post['checkbox'];
    $start = $post['startdate'];
    $end = $post['enddate'];
    
    //変数
    $error = array();
    $sql = "";
    $judge = false;
    $errorcnt = 0;
    $kikan = "";
    $errorflg = false;
    $_SESSION['teijicheck']['syain'] = array();
            
    //チェック処理
    $con = dbconect();
    
    //開始日付と終了日付が同じ場合は全期間を検索する
    if($start != $end)
    {
        $kikan = "progressinfo.SAGYOUDATE BETWEEN '".$start."' AND '".$end."' AND";
    }
    
    for($i = 0; $i < count($syainArray); $i++)
    {             
        //データのない社員も名前を取得するため
        $sql="SELECT STAFFNAME FROM syaininfo WHERE 4CODE = ".$syainArray[$i]."";
        $result = $con->query($sql) or ($judge = true);
        if($judge)
        {
            error_log($con->error,0);
            $judge = false;
        }
        $result_row = $result->fetch_array(MYSQLI_ASSOC);
        $_SESSION['teijicheck']['syain'][$i] = $result_row['STAFFNAME'];

        $sql2 = "SELECT SAGYOUDATE,sum(TEIZITIME)as TEIZITIME,sum(TEIZITIME+ZANGYOUTIME)as SAGYOUTIME,STAFFID,STAFFNAME FROM progressinfo LEFT JOIN projectditealinfo USING(6CODE) 
            LEFT JOIN syaininfo USING(4CODE) WHERE ".$kikan." syaininfo.4CODE = ".$syainArray[$i]." GROUP BY SAGYOUDATE";

        $result2 = $con->query($sql2) or ($judge = true);																		// クエリ発行
        if($judge)
        {
            error_log($con->error,0);
            $judge = false;
        }

        while($result2_row = $result2->fetch_array(MYSQLI_ASSOC))
        {
            $errorflg = false;
            //確認1：一日の定時時間が7.75になっているか
            if($result2_row['TEIZITIME'] != $teijitime)
            {
                    $error[$errorcnt]['STAFFID'] = $result2_row['STAFFID'];
                    $error[$errorcnt]['STAFFNAME'] = $result2_row['STAFFNAME'];
                    $error[$errorcnt]['SAGYOUDATE'] = $result2_row['SAGYOUDATE'];
                    $error[$errorcnt]['TEIZITIME'] = $result2_row['TEIZITIME'];
                    $error[$errorcnt]['SAGYOUTIME'] = $result2_row['SAGYOUTIME'];
                    $error[$errorcnt]['GENIN'] = "1";
                    $errorcnt++;
                    $errorflg = true;
            }

            //確認2：一日の作業時間が24時間を超えていないか
            if($result2_row['SAGYOUTIME'] > 24)
            {
                if($errorflg == false)
                {
                    $error[$errorcnt]['STAFFID'] = $result2_row['STAFFID'];
                    $error[$errorcnt]['STAFFNAME'] = $result2_row['STAFFNAME'];
                    $error[$errorcnt]['SAGYOUDATE'] = $result2_row['SAGYOUDATE'];
                    $error[$errorcnt]['TEIZITIME'] = $result2_row['TEIZITIME'];
                    $error[$errorcnt]['SAGYOUTIME'] = $result2_row['SAGYOUTIME'];
                    $error[$errorcnt]['GENIN'] = "2";
                    $errorcnt++;
                }
                else if($errorflg == true)
                {
                    $error[$errorcnt-1]['GENIN'] .= ",2";
                }
            }
        }
    }
    return($error);
}

/************************************************************************************************************
function make_teijicomplist($post)

引数	$post

戻り値	$list_html
************************************************************************************************************/
function make_teijicomplist($post){
    
    //初期設定
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);

    //定数
    $totalcount = count($post);
    $filename = $_SESSION['filename'];

    //変数
    $list_html = "";
    $counter = 1;

    //処理
    $list_html .= "<div>";
    $list_html .= $totalcount."件中 1件～".$totalcount."件 表示中";				// 件数表示作成
    $list_html .= "</div>";
    $list_html .= "<div class='list_scroll'>";
    $list_html .= "<table>";
    $list_html .= "<tr><th>No</th><th>社員番号</th><th>社員名</th><th>作業日</th><th>定時作業時間</th><th>総作業時間</th><th>エラー理由</th></tr>";
    for($i = 0 ; $i < $totalcount ; $i++)
    {
        if(($counter%2) == 1)
        {
            $list_html .= "<tr>";
        }
        else
        {
            $list_html .= "<tr class='list_stripe'>";
        }
        
        $list_html .= "<td>".$counter."</td>";
        $list_html .= "<td>".$post[$i]['STAFFID']."</td>";
        $list_html .= "<td>".$post[$i]['STAFFNAME']."</td>";
        $list_html .= "<td>".$post[$i]['SAGYOUDATE']."</td>";
        $list_html .= "<td>".$post[$i]['TEIZITIME']."</td>";
        $list_html .= "<td>".$post[$i]['SAGYOUTIME']."</td>";
        $list_html .= "<td>".$post[$i]['GENIN']."</td>";
        $list_html .= "</tr>";
        $counter++;
    }
    $list_html .= "</table>";
    $list_html .= "</div>";
    return ($list_html);
}

/************************************************************************************************************
function make_pjend_list($sql,$post)


引数1       $sql                       検索SQL
引数2       $post                     

戻り値	$list_html             定時チェックリスト
************************************************************************************************************/
function make_pjend_list($sql,$post){
    
    //初期設定
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    require_once ("f_Form.php");
    require_once ("f_DB.php");
    require_once ("f_SQL.php");

    //定数
    $filename = $_SESSION['filename'];
    $filename_array = explode('_',$filename);
    $filename_edit = $filename_array[0].'_3';
    $columns_array = explode(',',$SQL_ini[$filename]['listcolumns']);
    $columnname_array = explode(',',$SQL_ini[$filename]['clumnname']);
    $main_code = $form_ini[$filename]['main_code'];
    
    //変数
    $list_html = "";
    $counter = 0;
    
    //処理
    $con = dbconect();
    $list_html .= "<div class='list_scroll' style='max-height: 300px;'>";
    $list_html .= "<table id='checkboxlist'>";
    
    //項目名作成処理
    $list_html .= "<tr>";
    $list_html .= "<th>選択</th>";
    for($i = 0; $i < count($columnname_array); $i++)
    {
        $list_html .= "<th><a>".$columnname_array[$i]."</a></th>";
    }
    $list_html .= "<th>詳細</th>";
    $list_html .= "</tr>";
    
    //一覧表内容作成処理
    $result = $con->query($sql[0]);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        if(($counter%2) == 0)
        {
            $list_html .= "<tr>";
        }
        else
        {
            $list_html .= "<tr class='list_stripe'>";
        }
        if(isset($post['PJSTAT']) && $post['PJSTAT'] == "1")
        {
            $list_html .= "<td>済</td>";
        }
        else
        {
            $list_html .= "<td onmousemove='mouseMove(this.parentNode.rowIndex,checkboxlist);' onmouseout='mouseOut(this.parentNode.rowIndex,checkboxlist);'><label for='check".$counter."' style='display:block;width:100%;height:100%;'><input type='checkbox' name='checkbox[]' id='check".$counter."' value='".$result_row['5CODE']."' onclick='checkbox_select();' class='checkbox_style'></label></td>";
        }
        for($i = 0; $i < count($columns_array); $i++)
        {
            if(!isset($result_row[$columns_array[$i]]))
            {
                $list_html .= "<td></td>";
            }
            else
            {
                $list_html .= "<td>".$result_row[$columns_array[$i]]."</td>";
            }
        }
        $list_html .= "<td><input type='button' value='詳細' onclick='pjsyousai_open(".$result_row['5CODE'].");'></td>";
        $list_html .= "</tr>";
        $counter++;
    }
    $list_html .= "</table>";
    $list_html .= "</div>";
    
    return $list_html;
}

/************************************************************************************************************
function pjCheck($post)


引数1       $post                

戻り値	$error_data                 エラー情報
************************************************************************************************************/
function pjCheck($post){
    
    //初期設定
    require_once("f_DB.php");
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $nowdate = date_format(date_create("NOW"), 'Y-n-j');
    $teijitime = (float)$system_ini['settime']['teijitime'];
    if($filename == "getuzi_5")
    {
        $period = $post['period'];
        $month = $post['month'];
        $year = getyear($month,$period);
        $lastday = getlastday($month,$year);
        $Month = str_pad($month, 2, "0", STR_PAD_LEFT);
    }
    else if($filename == "pjend_5")
    {
        $pjid = $post['checkbox'];
    }
  
    //変数
    $judge = false;
    $syainArray = array();
    $error = array();
    
    //チェック処理
    $con = dbconect();
    $errorcnt = 0;
    $syaincnt = 0;
    if($filename == "getuzi_5")
    {
        //指定期間内に登録されている社員コード取得
        $sql = "SELECT DISTINCT(4CODE) FROM progressinfo LEFT JOIN projectditealinfo USING(6CODE) LEFT JOIN projectinfo USING(5CODE) ";
        $sql .= "LEFT JOIN syaininfo USING(4CODE) LEFT JOIN kouteiinfo USING(3CODE) ";
        $sql .= "WHERE progressinfo.SAGYOUDATE BETWEEN '".$year."-".$month."-01' AND '".$year."-".$month."-".$lastday."' ";
        $sql .= "ORDER BY syaininfo.4CODE;";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $syainArray[$syaincnt] = $result_row['4CODE'];
            $syaincnt++;
        }
        
        //社員ごとに定時チェック
        for($i = 0; $i < count($syainArray); $i++)
        {
            //社員が変わるごとにbeforeとteiziを初期化
            $before = "";
            $teizi = 0;
            
            //社員コードと日付を条件に作業日順で選択
            $sql = "SELECT * FROM progressinfo LEFT JOIN projectditealinfo USING(6CODE) LEFT JOIN projectinfo USING(5CODE) ";
            $sql .= "LEFT JOIN syaininfo USING(4CODE) LEFT JOIN kouteiinfo USING(3CODE) ";
            $sql .= "WHERE progressinfo.SAGYOUDATE BETWEEN '".$year."-".$month."-01' AND '".$year."-".$month."-".$lastday."' ";
            $sql .= "AND syaininfo.4CODE = '".$syainArray[$i]."' ";
            $sql .= "ORDER BY SAGYOUDATE;";
            $result = $con->query($sql);
            while($result_row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $after = $result_row['SAGYOUDATE'];
                if(!empty($before))
                {
                    if($before == $after)
                    {
                        $teizi += $result_row['TEIZITIME'];
                        if($teizi > $teijitime)
                        {
                            $checkflg = true;
                            //定時エラー
                            $error[$errorcnt]['STAFFNAME'] = $result_row['STAFFNAME'];
                            $error[$errorcnt]['SAGYOUDATE'] = $result_row['SAGYOUDATE'];
                            $error[$errorcnt]['KOUTEINAME'] = "";
                            $error[$errorcnt]['GENIN'] = "規定の定時時間を越えています。";
                            $errorcnt++;
                        }
                    }
                    else
                    {
                        //日付が変わるごとにteiziを初期化
                        $teizi = 0;
                        $teizi += $result_row['TEIZITIME'];
                        if($teizi > $teijitime)
                        {
                            $checkflg = true;
                            //定時エラー
                            $error[$errorcnt]['STAFFNAME'] = $result_row['STAFFNAME'];
                            $error[$errorcnt]['SAGYOUDATE'] = $result_row['SAGYOUDATE'];
                            $error[$errorcnt]['KOUTEINAME'] = "";
                            $error[$errorcnt]['GENIN'] = "規定の定時時間を越えています。";
                            $errorcnt++;
                        }
                    }
                }
                else
                {
                    $teizi += $result_row['TEIZITIME'];
                    if($teizi > $teijitime)
                    {
                        $checkflg = true;
                        //定時エラー
                        $error[$errorcnt]['STAFFNAME'] = $result_row['STAFFNAME'];
                        $error[$errorcnt]['SAGYOUDATE'] = $result_row['SAGYOUDATE'];
                        $error[$errorcnt]['KOUTEINAME'] = "";
                        $error[$errorcnt]['GENIN'] = "規定の定時時間を越えています。";
                        $errorcnt++;
                    }                   
                }
                $before = $result_row['SAGYOUDATE'];
            }
        }
    }
    else if($filename == "pjend_5")
    {
        for($i = 0; $i < count($pjid); $i++)
        {
            //工数情報の有無
            $sql = "SELECT * FROM progressinfo LEFT JOIN projectditealinfo USING(6CODE) ";
            $sql .= "LEFT JOIN projectinfo USING(5CODE) LEFT JOIN syaininfo USING(4CODE) ";
            $sql .= "LEFT JOIN kouteiinfo USING(3CODE) WHERE 5CODE = ".$pjid[$i]." order by SAGYOUDATE ;";            
            $result = $con->query($sql) or ($judge = true);																		// クエリ発行
            if(($result->num_rows) > 0)
            {
                //プロジェクトの開始日と終了日付取得
                $sql = "SELECT MIN(SAGYOUDATE),MAX(SAGYOUDATE) FROM progressinfo LEFT JOIN projectditealinfo USING(6CODE) ";
                $sql .= "LEFT JOIN projectinfo USING(5CODE) LEFT JOIN syaininfo USING(4CODE) ";
                $sql .= "LEFT JOIN kouteiinfo USING(3CODE) WHERE 5CODE = ".$pjid[$i]." order by SAGYOUDATE ;";
                $result = $con->query($sql) or ($judge = true);
                $result_row = $result->fetch_array(MYSQLI_ASSOC);
                $start = $result_row['MIN(SAGYOUDATE)'];
                $end =  $result_row['MAX(SAGYOUDATE)'];
                
                //プロジェクトの作業社員取得
                $sql = "SELECT DISTINCT(4CODE) FROM progressinfo LEFT JOIN projectditealinfo USING(6CODE) ";
                $sql .= "LEFT JOIN projectinfo USING(5CODE) LEFT JOIN syaininfo USING(4CODE) ";
                $sql .= "LEFT JOIN kouteiinfo USING(3CODE) WHERE 5CODE = ".$pjid[$i]." order by 4CODE ;";
                $result = $con->query($sql) or ($judge = true);
                while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $syainArray[$syaincnt] = $result_row['4CODE'];
                    $syaincnt++;
                }
                
                //社員ごとに定時チェック
                for($s = 0; $s < count($syainArray); $s++)
                {
                    //初期化
                    $before = "";
                    $teizi = 0;
                    
                    $sql = "SELECT * FROM progressinfo LEFT JOIN projectditealinfo USING(6CODE) LEFT JOIN projectinfo USING(5CODE) ";
                    $sql .= "LEFT JOIN syaininfo USING(4CODE) ";
                    $sql .= "LEFT JOIN kouteiinfo USING(3CODE) WHERE progressinfo.SAGYOUDATE BETWEEN '".$start."' AND '".$end."' AND syaininfo.4CODE = ".$syainArray[$s]." ORDER BY SAGYOUDATE;";
                    $result = $con->query($sql) or ($judge = true);
                    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                    {
                        $after = $result_row['SAGYOUDATE'];
                        if(!empty($before))
                        {
                            if($before == $after)
                            {
                                $teizi += $result_row['TEIZITIME'];
                                if($teizi > $teijitime)
                                {
                                    $checkflg = true;
                                    //定時エラー
                                    $error[$errorcnt]['STAFFNAME'] = $result_row['STAFFNAME'];
                                    $error[$errorcnt]['SAGYOUDATE'] = $result_row['SAGYOUDATE'];
                                    $error[$errorcnt]['KOUTEINAME'] = "";
                                    $error[$errorcnt]['GENIN'] = "規定の定時時間を越えています。";
                                    $errorcnt++;
                                }
                            }
                            else
                            {
                                //日付が変わるごとにteiziを初期化
                                $teizi = 0;
                                $teizi += $result_row['TEIZITIME'];
                                if($teizi > $teijitime)
                                {
                                    $error[$errorcnt]['STAFFNAME'] = $result_row['STAFFNAME'];
                                    $error[$errorcnt]['SAGYOUDATE'] = $result_row['SAGYOUDATE'];
                                    $error[$errorcnt]['KOUTEINAME'] = "";
                                    $error[$errorcnt]['GENIN'] = "規定の定時時間を越えています。";
                                    $errorcnt++;
                                }
                            }
                        }
                        else
                        {
                            $teizi += $result_row['TEIZITIME'];
                            if($teizi > $teijitime)
                            {
                                $checkflg = true;
                                $error[$errorcnt]['STAFFNAME'] = $result_row['STAFFNAME'];
                                $error[$errorcnt]['SAGYOUDATE'] = $result_row['SAGYOUDATE'];
                                $error[$errorcnt]['KOUTEINAME'] = "";
                                $error[$errorcnt]['GENIN'] = "規定の定時時間を越えています。";
                                $errorcnt++;
                            }
                        }
                        $before = $result_row['SAGYOUDATE'];
                    }
                }
            }
            else
            {
                $_SESSION['error_code5'][] = $pjid[$i];
                $_SESSION['error_GENIN'][] = "工数情報が登録されていません。";
            }
        }
    }
    return $error;
}

/************************************************************************************************************
PJ終了処理(プロジェクト管理システム)
function pjend($post)

引数1		$post						削除対象

戻り値		
************************************************************************************************************/
function pjend($post){
    
    //初期設定
    require_once("f_DB.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $nowdate = date_format(date_create("NOW"), 'Y-n-j');
    $teijitime = (float)$system_ini['settime']['teijitime'];
    if($filename == "pjend_5")
    {
        $pjid = $post['checkbox'];
    }

    //終了処理
    $con = dbconect();
    for($i = 0; $i < count($pjid); $i++)
    {           
        //変数
        $time = array();
        $upcode6 = "";
        
        //該当プロジェクト($pjid)を選択
        $sql = "SELECT * FROM progressinfo LEFT JOIN projectditealinfo USING(6CODE) LEFT JOIN projectinfo USING(5CODE) ";
        $sql .= "LEFT JOIN syaininfo USING(4CODE) LEFT JOIN kouteiinfo USING(3CODE) ";
        $sql .= "LEFT JOIN kokyakuinfo USING(12CODE)  LEFT JOIN teaminfo USING(13CODE) ";
        $sql .= "WHERE projectditealinfo.5CODE = ".$pjid[$i]." order by SAGYOUDATE ;";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            //社員別プロジェクトコード(6CODE)ごとに多次元配列に格納
            if(isset($time[$result_row['6CODE']]))
            {
                $time[$result_row['6CODE']][count($time[$result_row['6CODE']])] = $result_row;
            }
            else
            {
                $time[$result_row['6CODE']][0] = $result_row;
            }
        }
        $keyarray = array_keys($time);
        foreach($keyarray as $key)
        {
            //$key(=6CODE)が変わるごとに初期化
            $teizi = 0;
            $zangyou = 0;
            unset($before);
            //実績時間計算
            for($k = 0; $k < count($time[$key]); $k++)
            {
                $teizi += $time[$key][$k]['TEIZITIME'];
                $zangyou += $time[$key][$k]['ZANGYOUTIME'];
            }
            //終了PJ登録
            $pjcode = $time[$key][0]['KOKYAKUID'].$time[$key][0]['TEAMID'].$time[$key][0]['ANKENID'].$time[$key][0]['EDABAN'];
            $pjname = $time[$key][0]['PJNAME'];
            $charge = $time[$key][0]['DETALECHARGE'];
            $total = $teizi + $zangyou;
            $performance = round($charge/$total,3);
            $sql_end = "INSERT INTO endpjinfo (6CODE,TEIJITIME,ZANGYOTIME,TOTALTIME,PERFORMANCE,8ENDDATE,PJCODE,PJNAME) VALUES "
                        ."(".$key.",".$teizi.",".$zangyou.",".$total.",".$performance.","."'".$post['day'.$pjid[$i]]."'".","."'".$pjcode."'".","."'".$pjname."'".") ;";
            $result = $con->query($sql_end);
            if(!empty($upcode6))
            {
                $upcode6 .= $key.",";
            }
            else
            {
                $upcode6 = $key.",";
            }
        }
        //フラグを終了PJ(STAT=1)に更新
        $sql_update = "UPDATE projectinfo SET  5ENDDATE = '".$post['day'.$pjid[$i]]."' , 5PJSTAT = '1' WHERE 5CODE = ".$pjid[$i]." ;";
        $result = $con->query($sql_update);
        
        $upcode6 = substr($upcode6, 0, -1);
        $sql_update = "UPDATE projectditealinfo SET 6ENDDATE = '".$post['day'.$pjid[$i]]."' , 6PJSTAT = '1' WHERE 6CODE IN (".$upcode6.");";
        $result = $con->query($sql_update);
        
        $sql_update = "UPDATE progressinfo SET 7ENDDATE = '".$post['day'.$pjid[$i]]."' , 7PJSTAT = '1' WHERE 6CODE IN (".$upcode6.");";
        $result = $con->query($sql_update);																	// クエリ発行
    }
}

/************************************************************************************************************
function make_pjagain_list($sql,$post)


引数1       $sql                       検索SQL
引数2       $post                     

戻り値	$list_html             定時チェックリスト
************************************************************************************************************/
function make_pjagain_list($sql,$post){
    
    //初期設定
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    require_once ("f_Form.php");
    require_once ("f_DB.php");
    require_once ("f_SQL.php");

    //定数
    $filename = $_SESSION['filename'];
    $filename_array = explode('_',$filename);
    $filename_edit = $filename_array[0].'_3';
    $columns_array = explode(',',$SQL_ini[$filename]['listcolumns']);
    $columnname_array = explode(',',$SQL_ini[$filename]['clumnname']);
    $main_code = $form_ini[$filename]['main_code'];
    
    //変数
    $list_html = "";
    $counter = 0;
    
    //処理
    $con = dbconect();
    $list_html .= "<div class='list_scroll' style='max-height: 300px;'>";
    $list_html .= "<table id='radiolist'>";
    
    //項目名作成処理
    $list_html .= "<tr>";
    $list_html .= "<th>選択</th>";
    for($i = 0; $i < count($columnname_array); $i++)
    {
        $list_html .= "<th><a>".$columnname_array[$i]."</a></th>";
    }
    $list_html .= "</tr>";
    
    //一覧表内容作成処理
    $result = $con->query($sql[0]);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        if(($counter%2) == 0)
        {
            $list_html .= "<tr>";
        }
        else
        {
            $list_html .= "<tr class='list_stripe'>";
        }
        $list_html .= "<td onmousemove='mouseMove(this.parentNode.rowIndex,radiolist);' onmouseout='mouseOut(this.parentNode.rowIndex,radiolist);'><label for='radio".$counter."' style='display:block;width:100%;height:100%;'><input type='radio' name='radio' id='radio".$counter."' value='".$result_row['5CODE']."' onclick='radiobutton_select();' class='radio_style'></label></td>";
        for($i = 0; $i < count($columns_array); $i++)
        {
            if(!isset($result_row[$columns_array[$i]]))
            {
                $list_html .= "<td></td>";
            }
            else
            {
                $list_html .= "<td>".$result_row[$columns_array[$i]]."</td>";
            }
        }
        $list_html .= "</tr>";
        $counter++;
    }
    $list_html .= "</table>";
    $list_html .= "</div>";
    
    return $list_html;
}

/************************************************************************************************************
PJ終了キャンセル処理(プロジェクト管理システム)
function pjagain($post)

引数1		$post						削除対象

戻り値		
************************************************************************************************************/
function pjagain($post){
    
    //初期設定
    require_once("f_DB.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $pjid = $post['5CODE'];
    $nowdate = date_format(date_create("NOW"), 'Y-n-j');
    $teijitime = (float)$system_ini['settime']['teijitime'];
    
    //変数
    $code5 = 0;
    $code6 = 0;
    $code8 = 0;
            
    //処理
    $con = dbconect();
    $sql = "SELECT * FROM endpjinfo LEFT JOIN projectditealinfo USING (6CODE) LEFT JOIN syaininfo USING (4CODE) ";
    $sql .= " RIGHT JOIN projectinfo USING (5CODE) LEFT JOIN progressinfo USING (6CODE) ";
    $sql .= " WHERE projectinfo.5CODE = ".$pjid.";";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        if($code8 != $result_row['8CODE'])
        {
            $code8 = $result_row['8CODE'];
            //endpjinfoから削除
            $sql_delete =  "DELETE FROM endpjinfo WHERE 8CODE = ".$code8." ;";
            $result_delete = $con->query($sql_delete);																		// クエリ発行
        }
        if($code5 != $result_row['5CODE'])
        {
            $code5 = $result_row['5CODE'];
            //フラグを0（未処理）に変更
            $sql5 = "UPDATE projectinfo SET  5ENDDATE = NULL , 5PJSTAT = '0' WHERE 5CODE = ".$code5.";";
            $result5 = $con->query($sql5);																		// クエリ発行
        }
        if($code6 != $result_row['6CODE'])
        {
            $code6 = $result_row['6CODE'];
            //フラグを0（未処理）に変更
            $sql6 = "UPDATE projectditealinfo SET  6ENDDATE = NULL , 6PJSTAT = '0' WHERE 6CODE = ".$code6.";";
            $result6 = $con->query($sql6);																		// クエリ発行
            $sql7 = "UPDATE progressinfo SET  7ENDDATE = NULL , 7PJSTAT = '0' WHERE 6CODE = ".$code6.";";
            $result7 = $con->query($sql7);																		// クエリ発行
        }
    }
}

/************************************************************************************************************
期→年変換処理(プロジェクト管理システム)
function getyear($month,$period)

引数1		$month						月
引数2		$period 					期

戻り値	$year
************************************************************************************************************/
function getyear($month,$period){
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_File.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //定数
    $startyear = $system_ini['period']['startyear'];
    $startmonth = $system_ini['period']['startmonth'];
    
    //変数
    $year = 0;
    
    //処理
    $year = $period + $startyear - 1;
    if($startmonth > $month)
    {
        $year = $year + 1 ;
    }
    return $year;
}

/************************************************************************************************************
月末日取得処理(プロジェクト管理システム)
function getlastday($month,$year)

引数1		$month						月
引数2        $year                                                 年

戻り値	$day
************************************************************************************************************/
function getlastday($month,$year){
    
    //変数
    $day = 0;
    
    //処理
    if($month == 1 || $month == 3 || $month == 5 || $month == 7 || $month == 8 || $month == 10 || $month == 12)
    {
        $day = 31;
    }
    else if($month == 2)
    {
        $day = 28;
        if($month%4 == 0)
        {
            $day = 29;
        }
    }
    else
    {
        $day = 30;
    }
    return $day;
}

/************************************************************************************************************
月次処理(プロジェクト管理システム)
function getuzi($month,$period)

引数1		$month						処理対象月
引数2		$period 					期

戻り値        なし
************************************************************************************************************/
function getuzi($month,$period){
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_File.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $teijitime = (float)$system_ini['settime']['teijitime'];
    
    //変数
    $year = getyear($month,$period);
    $lastday = getlastday($month,$year);
    $Month = str_pad($month, 2, "0", STR_PAD_LEFT);
    $insertArray = array();
    $time = array();
    $cnt = 0;
    
    //月次処理
    $con = dbconect();
    $sql = "SELECT * FROM progressinfo LEFT JOIN projectditealinfo USING(6CODE) LEFT JOIN projectinfo USING(5CODE) ";
    $sql .= "LEFT JOIN syaininfo USING(4CODE) LEFT JOIN kouteiinfo USING(3CODE) ";
    $sql .= "LEFT JOIN kokyakuinfo USING(12CODE)  LEFT JOIN teaminfo USING(13CODE) ";
    $sql .= "WHERE progressinfo.SAGYOUDATE BETWEEN '".$year."-".$Month."-01' AND '".$year."-".$Month."-".$lastday."' ";
    $sql .= "ORDER BY SAGYOUDATE;";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        //社員別プロジェクトコード(6CODE)ごとに多配列登録
        if(isset($time[$result_row['6CODE']]))
        {
            $time[$result_row['6CODE']][count($time[$result_row['6CODE']])] = $result_row;
        }
        else
        {
            $time[$result_row['6CODE']][0] = $result_row;
        }
    }
    $keyarray = array_keys($time);
    foreach($keyarray as $key)
    {
        //初期設定
        $teizi = 0;
        $zangyou = 0;
        unset($before);
        
        //登録データ格納
        $insertArray[$cnt]['4CODE'] = $time[$key][0]['4CODE'];
        $insertArray[$cnt]['5CODE'] = $time[$key][0]['5CODE'];
        $insertArray[$cnt]['PJCODE'] = $time[$key][0]['KOKYAKUID'].$time[$key][0]['TEAMID'].$time[$key][0]['ANKENID'].$time[$key][0]['EDABAN'];
        $insertArray[$cnt]['PJNAME'] = $time[$key][0]['PJNAME'];
        
        //社員別プロジェクトコードごとに実績計算
        for($i = 0 ; $i < count($time[$key]) ; $i++)
        {
            //一ヶ月分の実績データ作成
            $teizi += $time[$key][$i]['TEIZITIME'];
            $zangyou += $time[$key][$i]['ZANGYOUTIME'];
            $before = $time[$key][$i]['SAGYOUDATE'];
        }
        $insertArray[$cnt]['TEIZI'] = $teizi;
        $insertArray[$cnt]['ZANGYOU'] = $zangyou;
        $cnt++;
    }
    
    //月間実績登録
    for($i = 0; $i < count($insertArray); $i++)
    {
        $sql_month = "INSERT INTO monthdatainfo (4CODE,5CODE,PERIOD,MONTH,ITEM,VALUE,9ENDDATE,PJCODE,PJNAME) ";
        $sql_month .= "VALUES('".$insertArray[$i]['4CODE']."','".$insertArray[$i]['5CODE']."','".$period."','".$month."','定時時間','".$insertArray[$i]['TEIZI']."',NOW(),'".$insertArray[$i]['PJCODE']."','".$insertArray[$i]['PJNAME']."');";
        $result = $con->query($sql_month);
        $sql_month = "INSERT INTO monthdatainfo (4CODE,5CODE,PERIOD,MONTH,ITEM,VALUE,9ENDDATE,PJCODE,PJNAME) ";
        $sql_month .= "VALUES('".$insertArray[$i]['4CODE']."','".$insertArray[$i]['5CODE']."','".$period."','".$month."','残業時間','".$insertArray[$i]['ZANGYOU']."',NOW(),'".$insertArray[$i]['PJCODE']."','".$insertArray[$i]['PJNAME']."');";
        $result = $con->query($sql_month);
    }
    
    //月次済期間登録
    $year = getyear($month,$period);
    $sql = "INSERT INTO endmonthinfo (PERIOD,YEAR,MONTH) VALUE ('".$period."','".$year."','".$month."');";
    $result = $con->query($sql);
    rireki_change();
}

/************************************************************************************************************
function make_nenzicsv($period,$month)

引数1		$period                         期

戻り値        $csv 
************************************************************************************************************/
function make_nenzicsv($period,$csv){
    
    //初期設定
    require_once ("f_Form.php");
    require_once ("f_DB.php");
    require_once ("f_SQL.php");
    
    //定数
    $start = getyear("6",$period);
    $end = getyear("5",$period);
    
    //変数
    $syainArray = array();
    $syaincnt = 0;
    
    //処理
    $con = dbconect();
    $date = date_format(date_create("NOW"), "Y-m-d");
    $csv .= "作成日：".$date;
    $csv .= "\r\n";
    $csv .= "社員名,区分,合計,6月,7月,8月,9月,10月,11月,12月,1月,2月,3月,4月,5月";
    $csv .= "\r\n";
    
    //表示対象社員取得
    $sql = "SELECT DISTINCT(4CODE) FROM progressinfo LEFT JOIN projectditealinfo USING(6CODE) LEFT JOIN projectinfo USING(5CODE) "
            ."LEFT JOIN syaininfo USING(4CODE) LEFT JOIN kouteiinfo USING(3CODE) "
            ."WHERE progressinfo.SAGYOUDATE BETWEEN '".$start."-06-01' AND '".$end."-05-31' "
            ."ORDER BY syaininfo.4CODE;";																	// クエリ発行
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $syainArray[$syaincnt] = $result_row['4CODE'];
        $syaincnt++;
    }
    
    //合計生産性取得
    $teijiArray = array();
    $zangyouArray = array();
    for($s = 0; $s < count($syainArray); $s++)
    {       
        //作業時間計算
        $teijiArray[$syainArray[$s]] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
        $zangyouArray[$syainArray[$s]] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
        $sql = "SELECT *,date_format(SAGYOUDATE, '%m') AS MONTH FROM progressinfo LEFT JOIN projectditealinfo USING(6CODE) LEFT JOIN projectinfo USING(5CODE) "
                ."LEFT JOIN syaininfo USING(4CODE) LEFT JOIN kouteiinfo USING(3CODE) "
                ."LEFT JOIN kokyakuinfo USING(12CODE) LEFT JOIN teaminfo USING(13CODE) "
                ."WHERE KOKYAKUID NOT LIKE '%X%' AND KOKYAKUID NOT LIKE '%Y%' AND KOKYAKUID NOT LIKE '%Z%' AND progressinfo.SAGYOUDATE BETWEEN '"
                .$start."-06-01' AND '".$end."-05-31' AND syaininfo.4CODE = ".$syainArray[$s]." ORDER BY SAGYOUDATE;";
        $result = $con->query($sql);        
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $teijiArray[$result_row['4CODE']][$result_row['MONTH']] += $result_row['TEIZITIME'];
            $zangyouArray[$result_row['4CODE']][$result_row['MONTH']] += $result_row['ZANGYOUTIME'];
            $teijiArray[$result_row['4CODE']]['TOTAL'] += $result_row['TEIZITIME'];
            $zangyouArray[$result_row['4CODE']]['TOTAL'] += $result_row['ZANGYOUTIME'];
        }
        
        //売上額計算
        $uriageArray[$syainArray[$s]] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
        $sql = "SELECT *,date_format(URIAGEMONTH, '%m') AS MONTH FROM projectditealinfo LEFT JOIN projectinfo USING(5CODE) LEFT JOIN syaininfo USING(4CODE) "
                ."LEFT JOIN kokyakuinfo USING(12CODE) LEFT JOIN teaminfo USING(12CODE) "
                ."WHERE KOKYAKUID NOT LIKE '%X%' AND KOKYAKUID NOT LIKE '%Y%' AND KOKYAKUID NOT LIKE '%Z%' "
                ."AND syaininfo.4CODE = '".$syainArray[$s]."' AND KOKYAKUID LIKE '".$period."%';";
        $result = $con->query($sql);        
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            if(isset($result_row['MONTH']))
            {
                $uriageArray[$result_row['4CODE']][$result_row['MONTH']] += $result_row['DETALECHARGE'];
            }
            else
            {
                $uriageArray[$result_row['4CODE']]['05'] += $result_row['DETALECHARGE'];
            }
            $uriageArray[$result_row['4CODE']]['TOTAL'] += $result_row['DETALECHARGE'];
        }
        
        //生産性計算
        $seisanArray[$syainArray[$s]] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
        for($i = 1; $i <= 12 ;$i++)
        {
            if($i < 10)
            {
                $key = '0'.$i;
            }
            else
            {
                $key = $i;
            }
            if($uriageArray[$syainArray[$s]][$key] != 0 && ($teijiArray[$syainArray[$s]][$key] + $zangyouArray[$syainArray[$s]][$key]) != 0)
            {
                $seisanArray[$syainArray[$s]][$key] = $uriageArray[$syainArray[$s]][$key] / ($teijiArray[$syainArray[$s]][$key] + $zangyouArray[$syainArray[$s]][$key]);
            }
            elseif($uriageArray[$syainArray[$s]][$key] == 0 && ($teijiArray[$syainArray[$s]][$key] + $zangyouArray[$syainArray[$s]][$key]) == 0)
            {
                $uriageArray[$syainArray[$s]][$key] = "";
                $teijiArray[$syainArray[$s]][$key] = "";
                $zangyouArray[$syainArray[$s]][$key] = "";
                $seisanArray[$syainArray[$s]][$key] = "";
            }
        }
        if($uriageArray[$syainArray[$s]]['TOTAL'] != 0 && ($teijiArray[$syainArray[$s]]['TOTAL'] + $zangyouArray[$syainArray[$s]]['TOTAL']) != 0)
        {
            $seisanArray[$syainArray[$s]]['TOTAL'] = $uriageArray[$syainArray[$s]]['TOTAL'] / ($teijiArray[$syainArray[$s]]['TOTAL'] + $zangyouArray[$syainArray[$s]]['TOTAL']);
        }
    }
       
    //CSV出力
    for($s = 0; $s < count($syainArray); $s++)
    {
        $sql = "SELECT *FROM syaininfo WHERE 4CODE = '".$syainArray[$s]."';";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $csv .= $result_row['STAFFNAME'].',[売上],'.$uriageArray[$syainArray[$s]]['TOTAL'].','.$uriageArray[$syainArray[$s]]['06'].','.$uriageArray[$syainArray[$s]]['07'].','.$uriageArray[$syainArray[$s]]['08'].','.$uriageArray[$syainArray[$s]]['09'].','.$uriageArray[$syainArray[$s]]['10'].','.$uriageArray[$syainArray[$s]]['11'].','.$uriageArray[$syainArray[$s]]['12'].','.$uriageArray[$syainArray[$s]]['01'].','.$uriageArray[$syainArray[$s]]['02'].','.$uriageArray[$syainArray[$s]]['03'].','.$uriageArray[$syainArray[$s]]['04'].','.$uriageArray[$syainArray[$s]]['05'];                                                             
            $csv .= "\r\n";
            $csv .= $result_row['STAFFNAME'].',[生産性],'.$seisanArray[$syainArray[$s]]['TOTAL'].','.$seisanArray[$syainArray[$s]]['06'].','.$seisanArray[$syainArray[$s]]['07'].','.$seisanArray[$syainArray[$s]]['08'].','.$seisanArray[$syainArray[$s]]['09'].','.$seisanArray[$syainArray[$s]]['10'].','.$seisanArray[$syainArray[$s]]['11'].','.$seisanArray[$syainArray[$s]]['12'].','.$seisanArray[$syainArray[$s]]['01'].','.$seisanArray[$syainArray[$s]]['02'].','.$seisanArray[$syainArray[$s]]['03'].','.$seisanArray[$syainArray[$s]]['04'].','.$seisanArray[$syainArray[$s]]['05'];                                                             
            $csv .= "\r\n";
            $csv .= $result_row['STAFFNAME'].',[定時],'.$teijiArray[$syainArray[$s]]['TOTAL'].','.$teijiArray[$syainArray[$s]]['06'].','.$teijiArray[$syainArray[$s]]['07'].','.$teijiArray[$syainArray[$s]]['08'].','.$teijiArray[$syainArray[$s]]['09'].','.$teijiArray[$syainArray[$s]]['10'].','.$teijiArray[$syainArray[$s]]['11'].','.$teijiArray[$syainArray[$s]]['12'].','.$teijiArray[$syainArray[$s]]['01'].','.$teijiArray[$syainArray[$s]]['02'].','.$teijiArray[$syainArray[$s]]['03'].','.$teijiArray[$syainArray[$s]]['04'].','.$teijiArray[$syainArray[$s]]['05'];                                                             
            $csv .= "\r\n";
            $csv .= $result_row['STAFFNAME'].',[残業],'.$zangyouArray[$syainArray[$s]]['TOTAL'].','.$zangyouArray[$syainArray[$s]]['06'].','.$zangyouArray[$syainArray[$s]]['07'].','.$zangyouArray[$syainArray[$s]]['08'].','.$zangyouArray[$syainArray[$s]]['09'].','.$zangyouArray[$syainArray[$s]]['10'].','.$zangyouArray[$syainArray[$s]]['11'].','.$zangyouArray[$syainArray[$s]]['12'].','.$zangyouArray[$syainArray[$s]]['01'].','.$zangyouArray[$syainArray[$s]]['02'].','.$zangyouArray[$syainArray[$s]]['03'].','.$zangyouArray[$syainArray[$s]]['04'].','.$zangyouArray[$syainArray[$s]]['05'];                                                             
            $csv .= "\r\n";
        }
    }
    $csv .= "\r\n";
    
    //プロジェクト別生産性計算
    $csv .= "社員名,プロジェクト名,区分,合計,6月,7月,8月,9月,10月,11月,12月,1月,2月,3月,4月,5月";
    $csv .= "\r\n";
    for($s = 0; $s < count($syainArray); $s++)
    {
        //売上額計算
        $uriageArray[$syainArray[$s]] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
        $sql = "SELECT *,date_format(URIAGEMONTH, '%m') AS MONTH FROM projectditealinfo LEFT JOIN projectinfo USING(5CODE) LEFT JOIN syaininfo USING(4CODE) "
                ."LEFT JOIN kokyakuinfo USING(12CODE) LEFT JOIN teaminfo USING(12CODE) "
                ."WHERE KOKYAKUID NOT LIKE '%X%' AND KOKYAKUID NOT LIKE '%Y%' AND KOKYAKUID NOT LIKE '%Z%' "
                ."AND syaininfo.4CODE = '".$syainArray[$s]."' AND KOKYAKUID LIKE '".$period."%';";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            //作業時間計算
            $sql2 = "SELECT *,date_format(SAGYOUDATE, '%m') AS MONTH FROM progressinfo LEFT JOIN projectditealinfo USING(6CODE) LEFT JOIN projectinfo USING(5CODE) "
                ."LEFT JOIN syaininfo USING(4CODE) LEFT JOIN kouteiinfo USING(3CODE) "
                ."LEFT JOIN kokyakuinfo USING(12CODE) LEFT JOIN teaminfo USING(13CODE) "
                ."WHERE 6CODE = '".$result_row['6CODE']."' AND progressinfo.SAGYOUDATE BETWEEN '"
                .$start."-06-01' AND '".$end."-05-31' ORDER BY SAGYOUDATE;";
            $result2 = $con->query($sql2);
            $teijiArray = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
            $zangyouArray = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
            while($result_row2 = $result2->fetch_array(MYSQLI_ASSOC))
            {
                $teijiArray[$result_row2['MONTH']] += $result_row2['TEIZITIME'];
                $zangyouArray[$result_row2['MONTH']] += $result_row2['ZANGYOUTIME'];
                $teijiArray['TOTAL'] += $result_row2['TEIZITIME'];
                $zangyouArray['TOTAL'] += $result_row2['ZANGYOUTIME'];
            }
            
            //売上額計算
            $uriageArray = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
            if(isset($result_row['MONTH']))
            {
                $uriageArray[$result_row['MONTH']] += $result_row['DETALECHARGE'];
            }
            else
            {
                $uriageArray['05'] += $result_row['DETALECHARGE'];
            }
            $uriageArray['TOTAL'] += $result_row['DETALECHARGE'];
            
            //生産性計算
            $seisanArray = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
            for($i = 1; $i <= 12 ;$i++)
            {
                if($i < 10)
                {
                    $key = '0'.$i;
                }
                else
                {
                    $key = $i;
                }
                if($uriageArray[$key] != 0 && ($teijiArray[$key] + $zangyouArray[$key]) != 0)
                {
                    $seisanArray[$key] = $uriageArray[$key] / ($teijiArray[$key] + $zangyouArray[$key]);
                }
                elseif($uriageArray[$key] == 0 && ($teijiArray[$key] + $zangyouArray[$key]) == 0)
                {
                    $uriageArray[$key] = "";
                    $teijiArray[$key] = "";
                    $zangyouArray[$key] = "";
                    $seisanArray[$key] = "";
                }
            }
            if($uriageArray['TOTAL'] != 0 && ($teijiArray['TOTAL'] + $zangyouArray['TOTAL']) != 0)
            {
                $seisanArray['TOTAL'] = $uriageArray['TOTAL'] / ($teijiArray['TOTAL'] + $zangyouArray['TOTAL']);
            }
        
            //社員名を取得して、CSV追記
            $sql2 = "SELECT *FROM syaininfo WHERE 4CODE ='".$syainArray[$s]."';";
            $result2 = $con->query($sql2);
            while($result_row2 = $result2->fetch_array(MYSQLI_ASSOC))
            {
                $csv .= $result_row2['STAFFNAME'].','.$result_row['PJNAME'].',[売上],'.$uriageArray['TOTAL'].','.$uriageArray['06'].','.$uriageArray['07'].','.$uriageArray['08'].','.$uriageArray['09'].','.$uriageArray['10'].','.$uriageArray['11'].','.$uriageArray['12'].','.$uriageArray['01'].','.$uriageArray['02'].','.$uriageArray['03'].','.$uriageArray['04'].','.$uriageArray['05'];
                $csv .= "\r\n";
                $csv .= $result_row2['STAFFNAME'].','.$result_row['PJNAME'].',[生産性],'.$seisanArray['TOTAL'].','.$seisanArray['06'].','.$seisanArray['07'].','.$seisanArray['08'].','.$seisanArray['09'].','.$seisanArray['10'].','.$seisanArray['11'].','.$seisanArray['12'].','.$seisanArray['01'].','.$seisanArray['02'].','.$seisanArray['03'].','.$seisanArray['04'].','.$seisanArray['05'];
                $csv .= "\r\n";
                $csv .= $result_row2['STAFFNAME'].','.$result_row['PJNAME'].',[定時],'.$teijiArray['TOTAL'].','.$teijiArray['06'].','.$teijiArray['07'].','.$teijiArray['08'].','.$teijiArray['09'].','.$teijiArray['10'].','.$teijiArray['11'].','.$teijiArray['12'].','.$teijiArray['01'].','.$teijiArray['02'].','.$teijiArray['03'].','.$teijiArray['04'].','.$teijiArray['05'];
                $csv .= "\r\n";
                $csv .= $result_row2['STAFFNAME'].','.$result_row['PJNAME'].',[残業],'.$zangyouArray['TOTAL'].','.$zangyouArray['06'].','.$zangyouArray['07'].','.$zangyouArray['08'].','.$zangyouArray['09'].','.$zangyouArray['10'].','.$zangyouArray['11'].','.$zangyouArray['12'].','.$zangyouArray['01'].','.$zangyouArray['02'].','.$zangyouArray['03'].','.$zangyouArray['04'].','.$zangyouArray['05'];
                $csv .= "\r\n";
            }
        }
    }
    
    return $csv;
}

/************************************************************************************************************
function make_getujicsv($period,$month)

引数1		$period                         期
引数2         $month                        月

戻り値        $csv 
************************************************************************************************************/
function make_getujicsv($period,$month,$csv){
    
    //初期設定
    require_once ("f_Form.php");
    require_once ("f_DB.php");
    require_once ("f_SQL.php");
    
    //定数
    $year = getyear($month,$period);
    $lastday = getlastday($month,$year);
    
    //変数
    $syaincnt = 0;
    $syainArray = array();
    $pjArray = array();
    $pj = array();
    $getuji = array();
    $value_csv1 = "";
    $value_csv2 = "";
    
    //処理
    $con = dbconect();
    for($i = 0; $i <= $lastday; $i++)
    {
        if($i == 0)
        {
            $hedder1 = "社員名,区分,合計,";
            $hedder2 = "\r\n".$period."期　".$month."月\r\n社員名,製番・案件名,区分,合計,";
        }
        else
        {
            $hedder1 .= $i."日,";
            $hedder2 .= $i."日,";
        }
    }
    
    //期間内に工数データのある社員コードを取得
    $sql = "SELECT DISTINCT(4CODE) FROM progressinfo LEFT JOIN projectditealinfo USING(6CODE) LEFT JOIN projectinfo USING(5CODE) ";
    $sql .= "LEFT JOIN syaininfo USING(4CODE) LEFT JOIN kouteiinfo USING(3CODE) ";
    $sql .= "WHERE progressinfo.SAGYOUDATE BETWEEN '".$year."-".$month."-1' AND '".$year."-".$month."-".$lastday."' ";
    $sql .= "ORDER BY syaininfo.4CODE;";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $syainArray[$syaincnt] = $result_row['4CODE'];
        $syaincnt++;
    }
    
    //社員番号別作業時間計算
    for($s = 0; $s < count($syainArray); $s++)
    {
        //初期化
        $name = "";
        $before = "";
        $teizi = 0;
        $zangyou = 0;
        $pjcnt = 0;
        $pjArray = array();
        
        //社員コードと日付を条件に作業日順で選択
        $sql = "SELECT * FROM progressinfo LEFT JOIN projectditealinfo USING(6CODE) LEFT JOIN projectinfo USING(5CODE) ";
	$sql .= "LEFT JOIN syaininfo USING(4CODE) LEFT JOIN kouteiinfo USING(3CODE) ";
	$sql .= "WHERE progressinfo.SAGYOUDATE BETWEEN '";
	$sql .= $year."-".$month."-1' AND '".$year."-".$month."-".$lastday."' AND syaininfo.4CODE = ".$syainArray[$s]." ORDER BY SAGYOUDATE;";
	$result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $name  = $result_row['STAFFNAME'];
            //プロジェクトごとに格納
            if(isset($pjArray[$result_row['6CODE']]))
            {
                $pjArray[$result_row['6CODE']][count($pjArray[$result_row['6CODE']])] = $result_row;
            }
            else
            {
                $pjArray[$result_row['6CODE']][0] = $result_row;
            }
            $after = $result_row['SAGYOUDATE'];
            if(!empty($before))
            {
                if($before == $after)
                {
                    $teizi += $result_row['TEIZITIME'];
                    $zangyou += $result_row['ZANGYOUTIME'];
                }
                else
                {
                    //日付が変わるごとにteiziとzangyouを初期化
                    $date = explode('-',$before);
                    $day = $date[2];
                    if(substr($day,0,1) == "0")
                    {
                        $day = ltrim($day,"0");
                    }
                    $getuji[$syainArray[$s]]['name'] = $name;
                    $getuji[$syainArray[$s]][$day]['teizi'] = $teizi;
                    $getuji[$syainArray[$s]][$day]['zangyou'] = $zangyou;
                    $teizi = 0;
                    $zangyou = 0;
                    $teizi += $result_row['TEIZITIME'];
                    $zangyou += $result_row['ZANGYOUTIME'];
                }
            }
            else
            {
                $teizi += $result_row['TEIZITIME'];
                $zangyou += $result_row['ZANGYOUTIME'];
            }
            $before = $result_row['SAGYOUDATE'];
        }
        //最後のデータを格納
        $date = explode('-',$before);
        $day = $date[2];
        if(substr($day,0,1) == "0")
        {
            $day = ltrim($day,"0");
        }
        $getuji[$syainArray[$s]]['name'] = $name;
        $getuji[$syainArray[$s]][$day]['teizi'] = $teizi;
        $getuji[$syainArray[$s]][$day]['zangyou'] = $zangyou;

        //社員プロジェクト別作業時間計算
        $keyarray = array_keys($pjArray);
        foreach($keyarray as $key)
        {
            //初期化
            $pjbefore = "";
            $pjteizi = 0;
            $pjzangyou = 0;
            
            //プロジェクトが変わるごとに名前とプロジェクト名を格納
            for($i = 0 ; $i < count($pjArray[$key]) ; $i++)
            {
                $pjafter = $pjArray[$key][$i]['SAGYOUDATE'];
                if(!empty($pjbefore))
                {
                    if($pjbefore == $pjafter)
                    {
                        $pjteizi += $pjArray[$key][$i]['TEIZITIME'];
                        $pjzangyou += $pjArray[$key][$i]['ZANGYOUTIME'];
                    }
                    else
                    {
                        //日付が変わるごとにteiziとzangyouを初期化
                        $pjdate = explode('-',$pjbefore);
                        $pjday = $pjdate[2];
                        if(substr($pjday,0,1) == "0")
                        {
                            $pjday = ltrim($pjday,"0");
                        }
                        $pj[$key]['name'] = $pjArray[$key][$i]['STAFFNAME'];
                        $pj[$key]['pjname'] = $pjArray[$key][$i]['PJNAME'];
                        $pj[$key][$pjday]['teizi'] = $pjteizi;
                        $pj[$key][$pjday]['zangyou'] = $pjzangyou;
                        $pjteizi = 0;
                        $pjzangyou = 0;
                        $pjteizi += $pjArray[$key][$i]['TEIZITIME'];
                        $pjzangyou += $pjArray[$key][$i]['ZANGYOUTIME'];
                    }
                }
                else
                {
                    $pjteizi += $pjArray[$key][$i]['TEIZITIME'];
                    $pjzangyou += $pjArray[$key][$i]['ZANGYOUTIME'];
                }
                $pjbefore = $pjArray[$key][$i]['SAGYOUDATE'];
                //最後のデータを格納
                if($i == (count($pjArray[$key])-1))
                {
                    $pjdate = explode('-',$pjbefore);
                    $pjday = $pjdate[2];
                    if(substr($pjday,0,1) == "0")
                    {
                        $pjday = ltrim($pjday,"0");
                    }
                    $pj[$key]['name'] = $pjArray[$key][$i]['STAFFNAME'];
                    $pj[$key]['pjname'] = $pjArray[$key][$i]['PJNAME'];
                    $pj[$key][$pjday]['teizi'] = $pjteizi;
                    $pj[$key][$pjday]['zangyou'] = $pjzangyou;
                }
            }
        }
    }
    $keyarray = array_keys($getuji);
    //社員コード順にCSVデータ作成
    foreach($keyarray as $key)
    {
        $sum1 = 0;
        $sum2 = 0;
        $hteizi = "";
        $hzangyo = "";
        $teizi = "";
        $zangyo = "";
        for($i = 1; $i <= $lastday; $i++)
        {
            if($i == 1)
            {
                $hteizi = mb_convert_encoding($getuji[$key]['name'], "sjis-win", "cp932").",[定時],";
                $hzangyo = mb_convert_encoding($getuji[$key]['name'], "sjis-win", "cp932").",[残業],";
            }
            if(!empty($getuji[$key][$i]))
            {
                $value1 = $getuji[$key][$i]['teizi'];
                $value2 = $getuji[$key][$i]['zangyou'];
                $sum1 += $getuji[$key][$i]['teizi'];
                $sum2 += $getuji[$key][$i]['zangyou'];
                $value1 = mb_convert_encoding($value1, "sjis-win", "cp932");
                $value2 = mb_convert_encoding($value2, "sjis-win", "cp932");
                $teizi .= $value1.",";
                $zangyo .= $value2.",";
            }
            else
            {
                $teizi .= ",";
                $zangyo .= ",";
            }
        }
        $value_csv1 .= $hteizi.$sum1.",".$teizi."\r\n".$hzangyo.$sum2.",".$zangyo."\r\n";        
    }
    $keyarray = array_keys($pj);
    //社員別プロジェクトごとにCSVデータ作成
    foreach($keyarray as $key)
    {
        $sum1 = 0;
        $sum2 = 0;
        $hteizi = "";
        $hzangyo = "";
        $teizi = "";
        $zangyo = "";
        for($i = 1; $i <= $lastday; $i++)
        {
            if($i == 1)
            {
                $hteizi = mb_convert_encoding($pj[$key]['name'], "sjis-win", "cp932").",".mb_convert_encoding($pj[$key]['pjname'], "sjis-win", "cp932").",[定時],";
                $hzangyo = mb_convert_encoding($pj[$key]['name'], "sjis-win", "cp932").",".mb_convert_encoding($pj[$key]['pjname'], "sjis-win", "cp932").",[残業],";
            }
            if(!empty($pj[$key][$i]))
            {
                $value1 = $pj[$key][$i]['teizi'];
                $value2 = $pj[$key][$i]['zangyou'];
                $sum1 += $pj[$key][$i]['teizi'];
                $sum2 += $pj[$key][$i]['zangyou'];
                $value1 = mb_convert_encoding($value1, "sjis-win", "cp932");
                $value2 = mb_convert_encoding($value2, "sjis-win", "cp932");
                $teizi .= $value1.",";
                $zangyo .= $value2.",";
            }
            else
            {
                $teizi .= ",";
                $zangyo .= ",";
            }
        }
        $value_csv2 .= $hteizi.$sum1.",".$teizi."\r\n".$hzangyo.$sum2.",".$zangyo."\r\n";
    }
    $csv = $hedder1."\r\n".$value_csv1."\r\n\r\n".$hedder2."\r\n".$value_csv2;
    return $csv;
}

/************************************************************************************************************
年次処理(プロジェクト管理システム)
function nenjiCheck($period)

引数1		$period 					期

戻り値
************************************************************************************************************/
function nenjiCheck($period){
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_Form.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $teijitime = (float)$system_ini['settime']['teijitime'];
    $nowdate = date_format(date_create("NOW"), 'Y-n-j');
    
    //変数
    $error_pj = array();
    
    //PJチェック
    $count = 0;
    $con = dbconect();
    $start_year = getyear('6',$period);
    $end_year = $start_year + 1;
    $sql = "SELECT PJNAME,CONCAT(KOKYAKUID,'-',TEAMID,'-',ANKENID,'-',EDABAN) AS PJCODE FROM progressinfo ";
    $sql .= "LEFT JOIN projectditealinfo USING(6CODE) LEFT JOIN projectinfo USING(5CODE) LEFT JOIN syaininfo USING(4CODE) LEFT JOIN kouteiinfo USING(3CODE) ";
    $sql .= "LEFT JOIN kokyakuinfo USING(12CODE) LEFT JOIN teaminfo USING(13CODE) ";
    $sql .= "WHERE projectinfo.5PJSTAT = '0' AND progressinfo.SAGYOUDATE BETWEEN '".$start_year."-06-01' AND '".$end_year."-05-31' ";
    $sql .= "ORDER BY KOKYAKUID,TEAMID,ANKENID,EDABAN;";
    $result = $con->query($sql);
    if($result->num_rows > 0)
    {
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $error_pj[$count]['PJCODE'] = $result_row['PJCODE'];
            $error_pj[$count]['PJNAME'] = $result_row['PJNAME'];
            $count++;
        }        
    }
    return $error_pj;
}

/************************************************************************************************************
年次処理(プロジェクト管理システム)
function nenzi($period)

引数1		$period 					期

戻り値	なし
************************************************************************************************************/
function nenzi($period){
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_Form.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //定数
    $nowdate = date_format(date_create("NOW"), 'Y-n-j');
    
    //変数
    $judge = false;
    
    //処理
    $con = dbconect();
    $sql = "INSERT INTO endperiodinfo (PERIOD) VALUE ('".$period."');";
    $result = $con->query($sql);
    rireki_change();
}

/************************************************************************************************************
操作履歴登録処理
function insert_sousarireki()

引数1   $sousakubun     操作区分番号
引数2   $data   登録データ

戻り値		
***********************************************************************************************************/

function insert_sousarireki($sousakubun,$data)
{
    //初期設定
    $form_ini_array = parse_ini_file("./ini/form.ini",true);
    $system_ini_array = parse_ini_file("./ini/system.ini",true);
    $input_datalist = parse_ini_file("./ini/input_datalist.ini",true);
    
    //定数
    $filename = $_SESSION['filename'];
    $kubun_list = explode(',',$input_datalist['1405']['sech']);
    $sousa_gamen = "";
    
    //変数
    $naiyou = array();
    
    //操作内容作成
    $con = dbconect();
    switch($filename)
    {
        case 'PJTOUROKU_1':
            $sousa_gamen = "PJ登録";
            $naiyou[0] = "プロジェクトコード：".$data['1202'].$data['1303'].$data['504'].$data['505']."　プロジェクト名：".$data['506'];
            break;
        case 'PJTOUROKU_3':
            $sousa_gamen = "PJ編集";
            $naiyou[0] = "プロジェクトコード：".$data['1202'].$data['1303'].$data['504'].$data['505']."　プロジェクト名：".$data['506'];
            break;
        case 'PROGRESSINFO_6':
            $sousa_gamen = "PJ工数情報取込";
            $naiyou[0] = "CSV取り込み　PJ工数情報登録";
            break;
        case 'TOP_6':
            $sousa_gamen = "PJ工数情報取込";
            $naiyou[0] = "CSV取り込み　PJ工数情報登録";
            break;
        case 'TOP_2':
            $sousa_gamen = "TOP";
            $naiyou[0] = "開始日付：".$data["pasteStart"]."　終了日付：".$data["pasteEnd"]."　コピー元：".$data["copydate"];
            break;
        case 'TOP_1':
            $sousa_gamen = "PJ工数登録";
            $naiyou[0] = "作業日：".$data['704']."　社員名：".$data['403'];
            break;
        case 'TOP_3':
            $sousa_gamen = "PJ工数編集";
            $naiyou[0] = "作業日：".$data['704']."　社員名：".$data['403'];
            break;
        case 'PROGRESSINFO_1':
            $sousa_gamen = "PJ工数登録";
            $naiyou[0] = "作業日：".$data['704']."　社員名：".$data['403'];
            break;
        case 'PROGRESSINFO_2':
            $sousa_gamen = "PJ工数編集";
            $naiyou[0] = "作業日：".$data['704']."　社員名：".$data['403'];
            break;
        case 'getuzi_5':
            $sousa_gamen = "月次処理";
            $naiyou[0] = "月次処理対象期：".$data["period"]."期　月次処理対象月：".$data["month"]."月";
            break;
        case 'nenzi_5':
            $sousa_gamen = "年次処理";
            $naiyou[0] = "年次処理対象期：".$data["period"]."期";
            break;
        case 'genka_5':
            $sousa_gamen = "原価メンテナンス";
            $naiyou[0] = "原価情報設定";
            break;
        case 'rireki_2':
            $sousa_gamen = "操作履歴";
            $naiyou[0] = "操作履歴削除　削除対象：".$data."ヶ月以上前";
            break;
        case 'pjend_5':
            $sousa_gamen = "PJ終了処理";
            for($i = 0; $i < count($data['checkbox']); $i++)
            {
                $sql = "SELECT KOKYAKUID,TEAMID,ANKENID,EDABAN,PJNAME FROM projectinfo "
                            ."LEFT JOIN kokyakuinfo USING(12CODE) LEFT JOIN teaminfo USING(13CODE) "
                            ."WHERE 5CODE = '".$data['checkbox'][$i]."';";
                $result = $con->query($sql);
                while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                {
                    $naiyou[$i] = "プロジェクトコード：".$result_row['KOKYAKUID'].$result_row['TEAMID'].$result_row['ANKENID'].$result_row['EDABAN']."　プロジェクト名：".$result_row['PJNAME'];
                }
            }
            break;
        case 'pjagain_5':
            $sousa_gamen = "PJ終了キャンセル処理";
            $sql = "SELECT KOKYAKUID,TEAMID,ANKENID,EDABAN,PJNAME FROM projectinfo "
                            ."LEFT JOIN kokyakuinfo USING(12CODE) LEFT JOIN teaminfo USING(13CODE) "
                            ."WHERE 5CODE = '".$data['5CODE']."';";
            $result = $con->query($sql);
            while($result_row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $naiyou[0] = "プロジェクトコード：".$result_row['KOKYAKUID'].$result_row['TEAMID'].$result_row['ANKENID'].$result_row['EDABAN']."　プロジェクト名：".$result_row['PJNAME'];
            }
            break;
        case 'pjdelete_5':
            $sousa_gamen = "PJデータ削除";
            $naiyou[0] = "PJデータ削除　削除対象：5年以上前";
            break;
        case 'SYAINNINFO_1':
            $sousa_gamen = "社員メンテナンス";
            $naiyou[0] = "社員番号：".$data['402']."　社員名：".$data['403'];
            break;
        case 'SYAINNINFO_3':
            $sousa_gamen = "社員メンテナンス";
            $naiyou[0] = "社員番号：".$data['402']."　社員名：".$data['403'];
            break;
        case 'KOUTEIINFO_1':
            $sousa_gamen = "工程メンテナンス";
            $naiyou[0] = "工程番号：".$data['302']."　工程名：".$data['303'];
            break;
        case 'KOUTEIINFO_3':
            $sousa_gamen = "工程メンテナンス";
            $naiyou[0] = "工程番号：".$data['302']."　工程名：".$data['303'];
            break;
        case 'KOKYAKUTEAM_1':
            $sousa_gamen = "顧客チームメンテナンス";
            $naiyou[0] = "顧客コード：".$data['1202']."　顧客名：".$data['1203']."　チームコード：".$data['1303']."　チーム名：".$data['1304'];
            break;
        case 'KOKYAKUTEAM_3':
            $sousa_gamen = "顧客チームメンテナンス";
            $naiyou[0] = "顧客コード：".$data['1202']."　顧客名：".$data['1203']."　チームコード：".$data['1303']."　チーム名：".$data['1304'];
            break;
        case 'editUser_5':
            $sousa_gamen = "ユーザー管理";
            $naiyou[0] = "社員番号：".$data['staff_id']."　社員名：".$data['staff_name'];
            break;
        case 'insertUser_5':
            $sousa_gamen = "ユーザー管理";
            $naiyou[0] = "社員番号：".$data['402']."　社員名：".$data['403'];
            break;
        case 'keihinyuryoku_5':
            $sousa_gamen = "経費入力";
            $naiyou[0] = "プロジェクトコード：".str_replace('-', '', $data['PJCODE'])."　プロジェクト名：".$data['PJNAME'];
            break;
    }
    
    //操作履歴登録処理
    for($i = 0; $i < count($naiyou); $i++)
    {
        //操作履歴登録SQL
        $sql = "INSERT INTO rireki (4CODE,NUMBER,GAMEN,KUBUN,NAIYOU) VALUES('".$_SESSION["user"]["4CODE"]."','".$i."','".$sousa_gamen."','".$kubun_list[$sousakubun]."','".$naiyou[$i]."');";
        $result = $con->query($sql);																	// クエリ発行
    }   
}

/************************************************************************************************************
操作履歴削除処理
function rireki_delete($post)

引数1       $post

戻り値		
***********************************************************************************************************/
function rireki_delete($post){
    
    //初期設定
    require_once ("f_DB.php");
    
    //削除対象日付取得
    $delete_date = date("Y-m-d",strtotime("-".$post['delete_month']." month"))." 23:59:59";
    $sql = "DELETE FROM rireki WHERE DATE <= '".$delete_date."';";
    
    //履歴削除処理
    $con = dbconect();
    $result = $con->query($sql);	
    
    //操作履歴登録
    insert_sousarireki('2', $post['delete_month']);
}

/************************************************************************************************************
PJデータ削除処理
function pjdelete()

引数1       なし

戻り値		
***********************************************************************************************************/
function pjdelete(){
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_Form.php");
    
    //変数
    $date = date_sub(date_create("NOW"), date_interval_create_from_date_string('5 year'));
    $DATE = date_format($date, "Y-m-d");
    $judge = false;
    
    //処理
    $con = dbconect();
    $sql = "DELETE FROM projectinfo WHERE 5ENDDATE < '".$DATE."' ;";
    $result = $con->query($sql);
    
    $sql = "DELETE FROM projectditealinfo WHERE 6ENDDATE < '".$DATE."' ;";
    $result = $con->query($sql);
    
    $sql = "DELETE FROM progressinfo WHERE 7ENDDATE < '".$DATE."' ;";
    $result = $con->query($sql);
    
    $sql = "DELETE FROM endpjinfo WHERE 8ENDDATE < '".$DATE."' ;";
    $result = $con->query($sql);
    
    $sql = "DELETE FROM monthdatainfo WHERE 9ENDDATE < '".$DATE."' ;";
    $result = $con->query($sql);
    
    rireki_change();
}

/************************************************************************************************************
function get_startdate()

引数1       なし

戻り値		
***********************************************************************************************************/
function get_startdate(){

    //初期設定
    require_once("f_DB.php");
    $system_ini_array = parse_ini_file("./ini/system.ini",true);
    
    //現在の期を取得
    $today = explode('/',date("Y/m/d"));
    $period = getperiod($today[1],$today[0]);
    
    //年取得
    $year = getyear($today, $period);
    
    //開始月取得
    $month = $system_ini_array['period']['startmonth'];
    
    //期首日取得
    $startdate = $year.'-'.$month.'-01';
    
    return $startdate;
}

/************************************************************************************************************
function get_enddate()

引数1       なし

戻り値		
***********************************************************************************************************/
function get_enddate(){

    //初期設定
    require_once("f_DB.php");
    $system_ini_array = parse_ini_file("./ini/system.ini",true);
    
    //現在の期を取得
    $today = explode('/',date("Y/m/d"));
    $period = getperiod($today[1],$today[0]);
    
    //年取得
    $year = getyear($today, $period) + 1;
    
    //終了月取得
    $month = $system_ini_array['period']['startmonth'] -1 ;
    if($month == 0)
    {
        $month = 12;
    }
    //期末取得
    $startdate = $year.'-'.$month.'-31';
    
    return $startdate;
}

/************************************************************************************************************
function kobetu_delete()

引数1       なし

戻り値		
***********************************************************************************************************/
function kobetu_delete($delkey){
    
    //初期設定
    require_once("f_DB.php");
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $sql = "";
    $ssql = "";
    
    //処理
    $con = dbconect();
    $ssql = "SELECT DISTINCT(7CODE) FROM progressinfo WHERE 6CODE IN (SELECT 6CODE FROM projectditealinfo WHERE 5CODE = '".$delkey."' ) ;";
    $sresult = $con->query($ssql);
    while($result_row = $sresult->fetch_array(MYSQLI_ASSOC))
    {
        $sql = "DELETE FROM progressinfo WHERE 7CODE = '".$result_row['7CODE']."' ;";
        $result = $con->query($sql);																		// クエリ発
    }
    $sql = "DELETE FROM projectditealinfo WHERE 5CODE = '".$delkey."' ;";
    $result = $con->query($sql);
}

/************************************************************************************************************
function make_syuekihyocsv()

引数1		なし

戻り値        $csv 
************************************************************************************************************/
function make_syuekihyocsv(){
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_Form.php");
    
    //定数
    $stat_array = array('継続中','終了');
    $month_keys = array('06','07','08','09','10','11','12','01','02','03','04','05');
    
    //変数
    $csv = "";
    $genka_list = array();
    $zangyou_list = array();
    $pj_list = array();

    //社員原価、残業単価取得
    $con = dbconect();
    $sql = "SELECT syaininfo.4CODE,GENKA,ZANGYOTANKA FROM syaininfo LEFT JOIN genkainfo ON syaininfo.4CODE = genkainfo.4CODE;";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $genka_list[$result_row['4CODE']] = $result_row['GENKA'];
        $zangyou_list[$result_row['4CODE']] = $result_row['ZANGYOTANKA'];
    }
    
    //XX-XXXXXXXXまでが同じPJコードを取得
    $start = get_startdate();
    $end = date('Y-m-d',strtotime('last day of last month'));
    $sql = "SELECT DISTINCT CONCAT(KOKYAKUID,TEAMID,ANKENID) AS PJCODE FROM projectinfo "
                ."LEFT JOIN kokyakuinfo USING(12CODE) LEFT JOIN teaminfo USING(12CODE); ";
    $result = $con->query($sql);
    $counter = 0;
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $pj_list[$counter]['PJCODE'] = $result_row['PJCODE'];
        $counter++;
    }

    //定時時間格納配列を作成する
    $teizitime = array();
    $total_teizitime['TOTAL'] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
    for($i = 0; $i < count($pj_list); $i++)
    {
        $sql = "SELECT 5CODE,4CODE,DETALECHARGE,date_format(URIAGEMONTH, '%m') AS MONTH FROM projectditealinfo LEFT JOIN projectinfo USING(5CODE) "
                    ."LEFT JOIN kokyakuinfo USING(12CODE) LEFT JOIN teaminfo USING(12CODE); ";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $teizitime[$result_row['4CODE']][$result_row['5CODE']] = array('4CODE' => '','5CODE' => '','TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);            
            $teizitime[$result_row['4CODE']][$result_row['5CODE']]['4CODE'] = $result_row['4CODE'];
            $teizitime[$result_row['4CODE']][$result_row['5CODE']]['5CODE'] = $result_row['5CODE'];
            $total_teizitime[$result_row['4CODE']] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);                
        }
     }

     //定時時間を取得する
     $sql = "SELECT *,date_format(SAGYOUDATE, '%m') AS MONTH FROM progressinfo "
                ."LEFT JOIN projectditealinfo USING(6CODE) LEFT JOIN projectinfo USING(5CODE) "
                ."LEFT JOIN kokyakuinfo USING(12CODE) LEFT JOIN teaminfo USING(12CODE) ;";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $total_teizitime[$result_row['4CODE']][$result_row['MONTH']] += $result_row['TEIZITIME'];
        $total_teizitime[$result_row['4CODE']]['TOTAL'] += $result_row['TEIZITIME'];
        $total_teizitime['TOTAL'][$result_row['MONTH']] += $result_row['TEIZITIME'];
        $total_teizitime['TOTAL']['TOTAL'] += $result_row['TEIZITIME'];            
        $teizitime[$result_row['4CODE']][$result_row['5CODE']][$result_row['MONTH']] += $result_row['TEIZITIME'];
        $teizitime[$result_row['4CODE']][$result_row['5CODE']]['TOTAL'] += $result_row['TEIZITIME'];
    }       
    
    //係数を計算する
    $keisu = array();
    $key1 = array_keys($teizitime);
    for($i = 0; $i < count($teizitime); $i++)
    {
        $key2 = array_keys($teizitime[$key1[$i]]);
        $keisu_tmp = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);     
        for($s = 0; $s < count($teizitime[$key1[$i]]); $s++)
        {
            $code4 = $teizitime[$key1[$i]][$key2[$s]]['4CODE'];
            $code5 = $teizitime[$key1[$i]][$key2[$s]]['5CODE'];
            $keisu[$code4][$code5] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);    
            for($k = 0; $k < count($month_keys); $k++)
            {
                $key = $month_keys[$k];
                //係数計算式
                if($s < (count($teizitime[$key1[$i]]) - 1))
                {
                    $teizi = $teizitime[$code4][$code5][$key];
                    $total = $total_teizitime[$code4][$key];
                    if($teizi == 0 || $total == 0)
                    {
                        $keisu[$code4][$code5][$key] = 0;
                    }
                    else
                    {
                        $keisu[$code4][$code5][$key] = round($teizi / $total,2);
                    }
                    $keisu_tmp[$key] += $keisu[$code4][$code5][$key];
                }
                else
                {//最後のPJの場合、1から他の合計値を引いた数値を係数とする
                    $keisu[$code4][$code5][$key] = 1 - $keisu_tmp[$key];
                }
            }
        }
    }
    
    //収益表出力
    for($i = 0; $i < count($pj_list); $i++)
    {
        //初期化
        $total_uriage = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
        $total_genka = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
        $total_koutuhi = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
        $total_sonota = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
        
        //PJ情報取得
        $sql = "SELECT *,CONCAT(KOKYAKUID,'-',TEAMID,'-',ANKENID,'-',EDABAN) AS PJCODE FROM projectinfo "
                    ."LEFT JOIN kokyakuinfo ON projectinfo.12CODE = kokyakuinfo.12CODE LEFT JOIN teaminfo ON projectinfo.13CODE = teaminfo.13CODE "
                    ."WHERE (URIAGEMONTH BETWEEN '".$start."' AND '".$end."' OR KOKYAKUID LIKE '%X%' OR  KOKYAKUID LIKE '%Y%' OR KOKYAKUID LIKE '%Z%' "
                    ."OR (5PJSTAT = '1' AND CHAEGE)) "
                    ."AND CONCAT(KOKYAKUID,TEAMID,ANKENID) = '".$pj_list[$i]['PJCODE']."';";
        $result = $con->query($sql);
        
        //表示対象となるPJ情報が存在しない場合
        if($result->num_rows == 0)
        {
            continue;
        }
        
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            //プロジェクト名表示
            $csv .= '"'.$result_row['PJCODE'].'",,"'.$result_row['PJNAME'].'",,,,,,,,,,,'.$stat_array[$result_row['5PJSTAT']];
            $csv .=  "\r\n";
            
            //項目名表示
            $csv .= ',6月,7月,8月,9月,10月,11月,12月,1月,2月,3月,4月,5月,合計';
            $csv .=  "\r\n";
            
            //売上額取得,表示社員情報取得
            $uriage = array();
            $syain_array = array();
            $counter = 0;
            $uriage['TOTAL'] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
            $sql2 = "SELECT *,date_format(URIAGEMONTH, '%m') AS MONTH FROM projectditealinfo LEFT JOIN projectinfo USING(5CODE) LEFT JOIN syaininfo USING(4CODE) "
                    ."WHERE 5CODE = '".$result_row['5CODE']."';";
            $result2 = $con->query($sql2);
            while($result_row2 = $result2->fetch_array(MYSQLI_ASSOC))
            {
                if(!isset($uriage[$result_row2['4CODE']]))
                {
                    $uriage[$result_row2['4CODE']] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);    
                }
                if(!isset($result_row2['MONTH']))
                {
                    $result_row2['MONTH'] = '05';
                }
                $uriage[$result_row2['4CODE']][$result_row2['MONTH']] += $result_row2['DETALECHARGE'];
                $uriage[$result_row2['4CODE']]['TOTAL'] += $result_row2['DETALECHARGE'];
                $uriage['TOTAL'][$result_row2['MONTH']] += $result_row2['DETALECHARGE'];
                $uriage['TOTAL']['TOTAL'] += $result_row2['DETALECHARGE'];
                $total_uriage[$result_row2['MONTH']] += $result_row2['DETALECHARGE'];
                $total_uriage['TOTAL'] += $result_row2['DETALECHARGE'];
                $syain_array[$counter]['4CODE'] = $result_row2['4CODE'];
                $syain_array[$counter]['STAFFNAME'] = $result_row2['STAFFNAME'];
                $counter++;
            }
            
            //売上額表示
            $csv .= '"売上"';
            $flag = 0;
            for($k = 0; $k < count($month_keys); $k++)
            {
                $key = $month_keys[$k];
                $end_month = date('m',strtotime('last day of last month'));
                if($flag == 0)
                {
                    $csv .= ',"'.$uriage['TOTAL'][$key].'"';
                }
                else
                {
                    if($uriage['TOTAL'][$key] != 0)
                    {
                        $csv .= ',"'.$uriage['TOTAL'][$key].'"';
                    }
                    else
                    {
                        $csv .= ',';
                    }                    
                }
                if($key == $end_month)
                {
                    $flag = 1;
                }
            }
            $csv .= ',"'.$uriage['TOTAL']['TOTAL'].'"';
            $csv .=  "\r\n";
            
            //残業時間取得
            $zangyoutime = array();
            for($s = 0; $s < count($syain_array); $s++)
            {
                $sql3 = "SELECT *,date_format(SAGYOUDATE, '%m') AS MONTH FROM progressinfo "
                        ."LEFT JOIN projectditealinfo USING(6CODE) LEFT JOIN projectinfo USING(5CODE) "
                        ."LEFT JOIN kokyakuinfo USING(12CODE) LEFT JOIN teaminfo USING(12CODE) "
                        ."WHERE URIAGEMONTH BETWEEN '".$start."' AND '".$end."' "
                        ."AND 5CODE = '".$result_row['5CODE']."' AND 4CODE = '".$syain_array[$s]['4CODE']."';";
                $result3 = $con->query($sql3);
                while($result_row3 = $result3->fetch_array(MYSQLI_ASSOC))
                {
                    if(!isset($zangyoutime[$syain_array[$s]['4CODE']]))
                    {
                        $zangyoutime[$syain_array[$s]['4CODE']] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
                    }
                    $zangyoutime[$syain_array[$s]['4CODE']][$result_row3['MONTH']] += $result_row3['ZANGYOUTIME'];
                    $zangyoutime[$syain_array[$s]['4CODE']]['TOTAL'] += $result_row3['ZANGYOUTIME'];
                }
                if(!isset($zangyoutime[$syain_array[$s]['4CODE']]))
                {
                    $zangyoutime[$syain_array[$s]['4CODE']] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);    
                }
            }

            //経費取得
            $koutuhi = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
            $sql4 = "SELECT SUM(charge) AS charge,date_format(month, '%m') AS month FROM keihiinfo WHERE 5CODE = '".$result_row['5CODE']."' AND kubun = '0' GROUP BY month;";
            $result4 = $con->query($sql4);
            while($result_row4 = $result4->fetch_array(MYSQLI_ASSOC))
            {
                $koutuhi[$result_row4['month']] += $result_row4['charge'];
                $koutuhi['TOTAL'] += $result_row4['charge'];
                $total_koutuhi[$result_row4['month']] += $result_row4['charge'];
                $total_koutuhi['TOTAL'] += $result_row4['charge'];
            }
            $sonota = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
            $sql4 = "SELECT SUM(charge) AS charge,date_format(month, '%m') AS month FROM keihiinfo WHERE 5CODE = '".$result_row['5CODE']."' AND kubun = '1' GROUP BY month;";
            $result4 = $con->query($sql4);
            while($result_row4 = $result4->fetch_array(MYSQLI_ASSOC))
            {
                $sonota[$result_row4['month']] += $result_row4['charge'];
                $sonota['TOTAL'] += $result_row4['charge'];
                $total_sonota[$result_row4['month']] += $result_row4['charge'];
                $total_sonota['TOTAL'] += $result_row4['charge'];
            }
            
            //原価計算
            $genka = array();
            $genka['TOTAL'] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
            for($s = 0; $s < count($syain_array); $s++)
            {
                $genka[$syain_array[$s]['4CODE']] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
                $flag = 0;
                //原価の計算
                for($k = 0; $k < count($month_keys); $k++)
                {
                    $key = $month_keys[$k];
                    $end_month = date('m',strtotime('last day of last month'));
                    
                    //原価を求め、残業代を足す
                    if($flag == 0)
                    {
                        if(isset($genka_list[$syain_array[$s]['4CODE']]) && isset($keisu[$syain_array[$s]['4CODE']][$result_row['5CODE']][$key]))
                        {
                            $genka[$syain_array[$s]['4CODE']][$key] = $genka_list[$syain_array[$s]['4CODE']] * $keisu[$syain_array[$s]['4CODE']][$result_row['5CODE']][$key];
                            $genka[$syain_array[$s]['4CODE']]['TOTAL'] += $genka[$syain_array[$s]['4CODE']][$key];
                            $genka['TOTAL'][$key] += $genka[$syain_array[$s]['4CODE']][$key] + ($zangyoutime[$syain_array[$s]['4CODE']][$key] * $zangyou_list[$syain_array[$s]['4CODE']]);
                            $genka['TOTAL']['TOTAL'] += $genka[$syain_array[$s]['4CODE']][$key] + ($zangyoutime[$syain_array[$s]['4CODE']][$key] * $zangyou_list[$syain_array[$s]['4CODE']]);
                        }
                        else
                        {
                            $genka[$syain_array[$s]['4CODE']][$key] = 0;
                            $genka[$syain_array[$s]['4CODE']]['TOTAL'] += $genka[$syain_array[$s]['4CODE']][$key];
                            $genka['TOTAL'][$key] += $genka[$syain_array[$s]['4CODE']][$key] + ($zangyoutime[$syain_array[$s]['4CODE']][$key] * $zangyou_list[$syain_array[$s]['4CODE']]);
                            $genka['TOTAL']['TOTAL'] += $genka[$syain_array[$s]['4CODE']][$key] + ($zangyoutime[$syain_array[$s]['4CODE']][$key] * $zangyou_list[$syain_array[$s]['4CODE']]);                       
                        }

                        //XX-XXXXXXXXの合計原価
                        $total_genka[$key] += $genka[$syain_array[$s]['4CODE']][$key];
                        $total_genka['TOTAL'] += $genka[$syain_array[$s]['4CODE']][$key];
                    } 
                    
                    if($end_month == $key)
                    {
                        $flag = 1;
                    }
                }
            }
            
            //原価に経費を足す
            for($s = 0; $s < count($month_keys); $s++)
            {
                $genka['TOTAL'][$month_keys[$s]] += ($koutuhi[$month_keys[$s]] + $sonota[$month_keys[$s]]);
                $genka['TOTAL']['TOTAL'] += ($koutuhi[$month_keys[$s]] + $sonota[$month_keys[$s]]);
                $total_genka[$month_keys[$s]] += ($koutuhi[$month_keys[$s]] + $sonota[$month_keys[$s]]);
                $total_genka['TOTAL'] += ($koutuhi[$month_keys[$s]] + $sonota[$month_keys[$s]]);
            }
            
            //原価表示
            $csv .= '"原価"';
            $flag = 0;
            for($k = 0; $k < count($month_keys); $k++)
            {
                $key = $month_keys[$k];
                $end_month = date('m',strtotime('last day of last month'));
                if($flag == 0)
                {
                    $csv .= ',"'.round($genka['TOTAL'][$key]).'"';
                }
                else
                {
                    if($genka['TOTAL'][$key] != 0)
                    {
                        $csv .= ',"'.round($genka['TOTAL'][$key]).'"';
                    }
                    else
                    {
                        $csv .= ',';                        
                    }
                }
                if($key == $end_month)
                {
                    $flag = 1;
                }
            }
            $csv .= ',"'.round($genka['TOTAL']['TOTAL']).'"';
            $csv .=  "\r\n";
            
            //社員別原価表示
            for($s = 0; $s < count($syain_array); $s++)
            {
                $csv .= '"　'.$syain_array[$s]['STAFFNAME'].'"';
                $flag = 0;
                for($k = 0; $k < count($month_keys); $k++)
                {
                    $key = $month_keys[$k];
                    $end_month = date('m',strtotime('last day of last month'));
                    if($flag == 0)
                    {
                        $csv .= ',"'.round($genka[$syain_array[$s]['4CODE']][$key]).'"';
                    }
                    else
                    {
                        $csv .= ',';
                    }
                    if($key == $end_month)
                    {
                        $flag = 1;
                    }
                }
                $csv .= ',"'.round($genka[$syain_array[$s]['4CODE']]['TOTAL']).'"';
                $csv .=  "\r\n";
            }
            
            //残業時間表示
            for($s = 0; $s < count($syain_array); $s++)
            {
                $csv .= '"　'.$syain_array[$s]['STAFFNAME'].'残業"';
                $flag = 0;
                for($k = 0; $k < count($month_keys); $k++)
                {
                    $key = $month_keys[$k];
                    $end_month = date('m',strtotime('last day of last month'));
                    if($flag == 0)
                    {
                        $csv .= ',"'.($zangyoutime[$syain_array[$s]['4CODE']][$key] * $zangyou_list[$syain_array[$s]['4CODE']]).'"';
                    }
                    else
                    {
                        $csv .= ',';
                    }
                    if($key == $end_month)
                    {
                        $flag = 1;
                    }
                }
                $csv .= ',"'.($zangyoutime[$syain_array[$s]['4CODE']]['TOTAL'] * $zangyou_list[$syain_array[$s]['4CODE']]).'"';
                $csv .=  "\r\n";
            }           
            
            //経費
            $csv .= "経費";
            $flag = 0;
            for($k = 0; $k < count($month_keys); $k++)
            {
                $key = $month_keys[$k];
                $end_month = date('m',strtotime('last day of last month'));
                if($flag == 0)
                {
                    $csv .= ',"'.($koutuhi[$month_keys[$k]] + $sonota[$month_keys[$k]]).'"';
                }
                else
                {
                    if(($koutuhi[$month_keys[$k]] + $sonota[$month_keys[$k]]) != 0)
                    {
                        $csv .= ',"'.($koutuhi[$month_keys[$k]] + $sonota[$month_keys[$k]]).'"';
                    }
                    else
                    {
                        $csv .= ',';
                    }
                }
                if($key == $end_month)
                {
                    $flag = 1;
                }
            }            
            $csv .= ',"'.($koutuhi['TOTAL'] + $sonota['TOTAL']).'"';
            $csv .=  "\r\n";
            
            $csv .= "　交通費";
            $flag = 0;
            for($k = 0; $k < count($month_keys); $k++)
            {
                $key = $month_keys[$k];
                $end_month = date('m',strtotime('last day of last month'));
                if($flag == 0)
                {
                    $csv .= ',"'.$koutuhi[$month_keys[$k]].'"';
                }
                else
                {
                    if($koutuhi[$month_keys[$k]] != 0)
                    {
                        $csv .= ',"'.$koutuhi[$month_keys[$k]].'"';
                    }
                    else
                    {
                        $csv .= ',';
                    }
                }
                if($key == $end_month)
                {
                    $flag = 1;
                }
            }            
            $csv .= ',"'.$koutuhi['TOTAL'].'"';
            $csv .=  "\r\n";
            
            $csv .= "　その他";
            $flag = 0;
            for($k = 0; $k < count($month_keys); $k++)
            {
                $key = $month_keys[$k];
                $end_month = date('m',strtotime('last day of last month'));
                if($flag == 0)
                {
                    $csv .= ',"'.$sonota[$month_keys[$k]].'"';
                }
                else
                {
                    if($sonota[$month_keys[$k]] != 0)
                    {
                        $csv .= ',"'.$sonota[$month_keys[$k]].'"';
                    }
                    else
                    {
                        $csv .= ',';
                    }
                }
                if($key == $end_month)
                {
                    $flag = 1;
                }
            }
            $csv .= ',"'.$sonota['TOTAL'].'"';
            $csv .=  "\r\n";
            
            //粗利
            $csv .= "粗利";
            $flag = 0;
            for($k = 0; $k < count($month_keys); $k++)
            {
                $key = $month_keys[$k];
                $end_month = date('m',strtotime('last day of last month'));
                if($flag == 0)
                {
                    $csv .= ',"'.round($uriage['TOTAL'][$key] - $genka['TOTAL'][$key]).'"';
                }
                else
                {
                    if($uriage['TOTAL'][$key] != 0 || $genka['TOTAL'][$key] != 0)
                    {
                        $csv .= ',"'.round($uriage['TOTAL'][$key] - $genka['TOTAL'][$key]).'"';
                    }
                    else
                    {
                        $csv .= ',';
                    }
                }
                if($key == $end_month)
                {
                    $flag = 1;
                }
            }
            $csv .= ',"'.round($uriage['TOTAL']['TOTAL'] - $genka['TOTAL']['TOTAL']).'"';
            $csv .=  "\r\n";

            //倍率
            $csv .= "倍率";
            $flag = 0;
            for($k = 0; $k < count($month_keys); $k++)
            {
                $key = $month_keys[$k];
                $end_month = date('m',strtotime('last day of last month'));
                if($flag == 0)
                {
                    if($uriage['TOTAL'][$key] == 0 || $genka['TOTAL'][$key] == 0)
                    {
                        $csv .= ',"0"';
                    }
                    else
                    {
                        $csv .= ',"'.round($uriage['TOTAL'][$key] / $genka['TOTAL'][$key],2).'"';
                    }
                }
                else
                {
                    if($uriage['TOTAL'][$key] == 0 && $genka['TOTAL'][$key] == 0)
                    {
                        $csv .= ',';
                    }
                    else
                    {
                        if($uriage['TOTAL'][$key] == 0 || $genka['TOTAL'][$key] == 0)
                        {
                            $csv .= ',"0"';
                        }
                        else
                        {
                            $csv .= ',"'.round($uriage['TOTAL'][$key] / $genka['TOTAL'][$key],2).'"';
                        }
                    }
                }
                if($key == $end_month)
                {
                    $flag = 1;
                }
            }
            if($uriage['TOTAL']['TOTAL'] == 0 || $genka['TOTAL']['TOTAL'] == 0)
            {
                $csv .= ',"0"';
            }
            else
            {
                $csv .= ',"'.round($uriage['TOTAL']['TOTAL'] / $genka['TOTAL']['TOTAL'],2).'"';
            }
            $csv .=  "\r\n";
            
            $csv .=  "\r\n";
        }
        //XX-XXXXXXXXの合計値を出力
        $csv .= '"'.$pj_list[$i]['PJCODE'].'",,"合計"';
        $csv .=  "\r\n";
        
        //項目名表示
        $csv .= ',6月,7月,8月,9月,10月,11月,12月,1月,2月,3月,4月,5月,合計';
        $csv .=  "\r\n";
        
        //売上額表示
        $csv .= '"売上"';
        $flag = 0;
        for($k = 0; $k < count($month_keys); $k++)
        {
            $key = $month_keys[$k];
            $end_month = date('m',strtotime('last day of last month'));
            if($flag == 0)
            {
                $csv .= ',"'.$total_uriage[$key].'"';
            }
            else
            {
                if($total_uriage[$key] != 0)
                {
                    $csv .= ',"'.$total_uriage[$key].'"';
                }
                else
                {
                    $csv .= ',';
                }
            }
            if($key == $end_month)
            {
                $flag = 1;
            }
        }
        $csv .= ',"'.$total_uriage['TOTAL'].'"';
        $csv .=  "\r\n";
        
        //原価
        $csv .= '"原価"';
        $flag = 0;
        for($k = 0; $k < count($month_keys); $k++)
        {
            $key = $month_keys[$k];
            $end_month = date('m',strtotime('last day of last month'));
            if($flag == 0)
            {
                $csv .= ',"'.round($total_genka[$key]).'"';
            }
            else
            {
                if($total_genka[$key] != 0)
                {
                    $csv .= ',"'.round($total_genka[$key]).'"';
                }
                else
                {
                    $csv .= ',';
                }
            }
            if($key == $end_month)
            {
                $flag = 1;
            }
        }
        $csv .= ',"'.round($total_genka['TOTAL']).'"';
        $csv .=  "\r\n";
        
        //経費
        $csv .= "経費";
        $flag = 0;
        for($k = 0; $k < count($month_keys); $k++)
        {
            $key = $month_keys[$k];
            $end_month = date('m',strtotime('last day of last month'));
            if($flag == 0)
            {
                $csv .= ',"'.($total_koutuhi[$month_keys[$k]] + $total_sonota[$month_keys[$k]]).'"';
            }
            else
            {
                if(($total_koutuhi[$month_keys[$k]] + $total_sonota[$month_keys[$k]]) != 0)
                {
                    $csv .= ',"'.($total_koutuhi[$month_keys[$k]] + $total_sonota[$month_keys[$k]]).'"';
                }
                else
                {
                    $csv .= ',';
                }
            }
            if($key == $end_month)
            {
                $flag = 1;
            }
        }
        $csv .= ',"'.($total_koutuhi['TOTAL'] + $total_sonota['TOTAL']).'"';
        $csv .=  "\r\n";
        
        $csv .= "　交通費";
        $flag = 0;
        for($k = 0; $k < count($month_keys); $k++)
        {
            $key = $month_keys[$k];
            $end_month = date('m',strtotime('last day of last month'));
            if($flag == 0)
            {
                $csv .= ',"'.$total_koutuhi[$month_keys[$k]].'"';
            }
            else
            {
                if($total_koutuhi[$month_keys[$k]] != 0)
                {
                    $csv .= ',"'.$total_koutuhi[$month_keys[$k]].'"';
                }
                else
                {
                    $csv .= ',';
                }                
            }
            if($key == $end_month)
            {
                $flag = 1;
            }
        }
        $csv .= ',"'.$total_koutuhi['TOTAL'].'"';        
        $csv .=  "\r\n";
        
        $csv .= "　その他";
        $flag = 0;
        for($k = 0; $k < count($month_keys); $k++)
        {
            $key = $month_keys[$k];
            $end_month = date('m',strtotime('last day of last month'));
            if($flag == 0)
            {
                $csv .= ',"'.$total_sonota[$month_keys[$k]].'"';
            }
            else
            {
                if($total_sonota[$month_keys[$k]] != 0)
                {
                    $csv .= ',"'.$total_sonota[$month_keys[$k]].'"';
                }
                else
                {
                    $csv .= ',';
                }                
            }
            if($key == $end_month)
            {
                $flag = 1;
            }
        }
        $csv .= ',"'.$total_sonota['TOTAL'].'"';
        $csv .=  "\r\n";
        
        //粗利
        $csv .= "粗利";
        $flag = 0;
        for($k = 0; $k < count($month_keys); $k++)
        {
            $key = $month_keys[$k];
            $end_month = date('m',strtotime('last day of last month'));
            if($flag == 0)
            {
                $csv .= ',"'.round($total_uriage[$key] - $total_genka[$key]).'"';
            }
            else
            {
                if($total_uriage[$key] != 0 || $total_genka[$key] != 0)
                {
                    $csv .= ',"'.round($total_uriage[$key] - $total_genka[$key]).'"';
                }
                else
                {
                    $csv .= ',';
                }
            }
            if($key == $end_month)
            {
                $flag = 1;
            }
        }
        $csv .= ',"'.round($total_uriage['TOTAL'] - $total_genka['TOTAL']).'"';
        $csv .=  "\r\n";
        
        //倍率
        $csv .= "倍率";
        $flag = 0;
        for($k = 0; $k < count($month_keys); $k++)
        {
            $key = $month_keys[$k];
            $end_month = date('m',strtotime('last day of last month'));
            if($flag == 0)
            {
                if($total_uriage[$key] == 0 || $total_genka[$key] == 0)
                {
                    $csv .= ',"0"';
                }
                else
                {
                    $csv .= ',"'.round($total_uriage[$key] / $total_genka[$key],2).'"';
                }   
            }
            else
            {
                if($total_uriage[$key] == 0 && $total_genka[$key] == 0)
                {
                    $csv .= ',';
                }
                else
                {
                    if($total_uriage[$key] == 0 || $total_genka[$key] == 0)
                    {
                        $csv .= ',"0"';
                    }
                    else
                    {
                        $csv .= ',"'.round($total_uriage[$key] / $total_genka[$key],2).'"';
                    }
                }
            }
            if($key == $end_month)
            {
                $flag = 1;
            }         
        }
        if($total_uriage['TOTAL'] == 0 || $total_genka['TOTAL'] == 0)
        {
            $csv .= ',"0"';
        }
        else
        {
            $csv .= ',"'.round($total_uriage['TOTAL'] / $total_genka['TOTAL'],2).'"';
        }
        $csv .=  "\r\n";

        $csv .=  "\r\n";
    }
    return $csv;
}

/************************************************************************************************************
function make_shikakaricsv

引数1		なし

戻り値        $csv 
************************************************************************************************************/
function make_shikakaricsv(){
    
    //初期設定
    require_once("f_DB.php");
    require_once("f_Form.php");
    
    //定数
    $stat_array = array('継続中','終了');
    $month_keys = array('06','07','08','09','10','11','12','01','02','03','04','05');
    
    //変数
    $csv = "";
    $genka_list = array();
    $zangyou_list = array();
    $pj_list = array();

    //社員原価、残業単価取得
    $con = dbconect();
    $sql = "SELECT *FROM genkainfo;";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $genka_list[$result_row['4CODE']] = $result_row['GENKA'];
        $zangyou_list[$result_row['4CODE']] = $result_row['ZANGYOTANKA'];
    }
    
    //XX-XXXXXXXXまでが同じPJコードを取得
    $start = date("Y-m-01");
    $end = get_enddate();
    $sql = "SELECT DISTINCT CONCAT(KOKYAKUID,TEAMID,ANKENID) AS PJCODE FROM projectinfo "
                ."LEFT JOIN kokyakuinfo USING(12CODE) LEFT JOIN teaminfo USING(12CODE); ";
    $result = $con->query($sql);
    $counter = 0;
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $pj_list[$counter]['PJCODE'] = $result_row['PJCODE'];
        $counter++;
    }

    //定時時間格納配列を作成する
    $teizitime = array();
    $total_teizitime['TOTAL'] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
    for($i = 0; $i < count($pj_list); $i++)
    {
        $sql = "SELECT 5CODE,4CODE,DETALECHARGE,date_format(URIAGEMONTH, '%m') AS MONTH FROM projectditealinfo LEFT JOIN projectinfo USING(5CODE) "
                    ."LEFT JOIN kokyakuinfo USING(12CODE) LEFT JOIN teaminfo USING(12CODE); ";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $teizitime[$result_row['4CODE']][$result_row['5CODE']] = array('4CODE' => '','5CODE' => '','TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);            
            $teizitime[$result_row['4CODE']][$result_row['5CODE']]['4CODE'] = $result_row['4CODE'];
            $teizitime[$result_row['4CODE']][$result_row['5CODE']]['5CODE'] = $result_row['5CODE'];
            $total_teizitime[$result_row['4CODE']] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);                
        }
     }

     //定時時間を取得する
     $sql = "SELECT *,date_format(SAGYOUDATE, '%m') AS MONTH FROM progressinfo "
                ."LEFT JOIN projectditealinfo USING(6CODE) LEFT JOIN projectinfo USING(5CODE) "
                ."LEFT JOIN kokyakuinfo USING(12CODE) LEFT JOIN teaminfo USING(12CODE) ;";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $total_teizitime[$result_row['4CODE']][$result_row['MONTH']] += $result_row['TEIZITIME'];
        $total_teizitime[$result_row['4CODE']]['TOTAL'] += $result_row['TEIZITIME'];
        $total_teizitime['TOTAL'][$result_row['MONTH']] += $result_row['TEIZITIME'];
        $total_teizitime['TOTAL']['TOTAL'] += $result_row['TEIZITIME'];            
        $teizitime[$result_row['4CODE']][$result_row['5CODE']][$result_row['MONTH']] += $result_row['TEIZITIME'];
        $teizitime[$result_row['4CODE']][$result_row['5CODE']]['TOTAL'] += $result_row['TEIZITIME'];
    }       
    
    //係数を計算する
    $keisu = array();
    $key1 = array_keys($teizitime);
    for($i = 0; $i < count($teizitime); $i++)
    {
        $key2 = array_keys($teizitime[$key1[$i]]);
        $keisu_tmp = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);     
        for($s = 0; $s < count($teizitime[$key1[$i]]); $s++)
        {
            $code4 = $teizitime[$key1[$i]][$key2[$s]]['4CODE'];
            $code5 = $teizitime[$key1[$i]][$key2[$s]]['5CODE'];
            $keisu[$code4][$code5] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);    
            for($k = 0; $k < count($month_keys); $k++)
            {
                $key = $month_keys[$k];
                //係数計算式
                if($s < (count($teizitime[$key1[$i]]) - 1))
                {
                    $teizi = $teizitime[$code4][$code5][$key];
                    $total = $total_teizitime[$code4][$key];
                    if($teizi == 0 || $total == 0)
                    {
                        $keisu[$code4][$code5][$key] = 0;
                    }
                    else
                    {
                        $keisu[$code4][$code5][$key] = round($teizi / $total,2);
                    }
                    $keisu_tmp[$key] += $keisu[$code4][$code5][$key];
                }
                else
                {//最後のPJの場合、1から他の合計値を引いた数値を係数とする
                    $keisu[$code4][$code5][$key] = 1 - $keisu_tmp[$key];
                }
            }
        }
    }
    
    //収益表出力
    for($i = 0; $i < count($pj_list); $i++)
    {
        //初期化
        $total_uriage = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
        $total_genka = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
        
        //PJ情報取得
        $sql = "SELECT *,CONCAT(KOKYAKUID,'-',TEAMID,'-',ANKENID,'-',EDABAN) AS PJCODE FROM projectinfo "
                    ."LEFT JOIN kokyakuinfo ON projectinfo.12CODE = kokyakuinfo.12CODE LEFT JOIN teaminfo ON projectinfo.13CODE = teaminfo.13CODE "
                    ."WHERE URIAGEMONTH BETWEEN '".$start."' AND '".$end."' "
                    ."AND (KOKYAKUID NOT LIKE '%X%' AND KOKYAKUID NOT LIKE '%Y%' AND KOKYAKUID NOT LIKE '%Z%') "
                    ."AND CHAEGE > 0 "
                    ."AND CONCAT(KOKYAKUID,TEAMID,ANKENID) = '".$pj_list[$i]['PJCODE']."';";
        $result = $con->query($sql);
        
        //表示対象となるPJ情報が存在しない場合
        if($result->num_rows == 0)
        {
            continue;
        }
        
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            //プロジェクト名表示
            $csv .= '"'.$result_row['PJCODE'].'",,"'.$result_row['PJNAME'].'",,,,,,,,,,,'.$stat_array[$result_row['5PJSTAT']];
            $csv .=  "\r\n";
            
            //項目名表示
            $csv .= ',6月,7月,8月,9月,10月,11月,12月,1月,2月,3月,4月,5月,合計';
            $csv .=  "\r\n";
            
            //売上額取得,表示社員情報取得
            $uriage = array();
            $syain_array = array();
            $counter = 0;
            $uriage['TOTAL'] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
            $sql2 = "SELECT *,date_format(URIAGEMONTH, '%m') AS MONTH FROM projectditealinfo LEFT JOIN projectinfo USING(5CODE) LEFT JOIN syaininfo USING(4CODE) "
                    ."WHERE 5CODE = '".$result_row['5CODE']."';";
            $result2 = $con->query($sql2);
            while($result_row2 = $result2->fetch_array(MYSQLI_ASSOC))
            {
                if(!isset($uriage[$result_row2['4CODE']]))
                {
                    $uriage[$result_row2['4CODE']] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);    
                }
                $uriage[$result_row2['4CODE']][$result_row2['MONTH']] += $result_row2['DETALECHARGE'];
                $uriage[$result_row2['4CODE']]['TOTAL'] += $result_row2['DETALECHARGE'];
                $uriage['TOTAL'][$result_row2['MONTH']] += $result_row2['DETALECHARGE'];
                $uriage['TOTAL']['TOTAL'] += $result_row2['DETALECHARGE'];
                $total_uriage[$result_row2['MONTH']] += $result_row2['DETALECHARGE'];
                $total_uriage['TOTAL'] += $result_row2['DETALECHARGE'];
                $syain_array[$counter]['4CODE'] = $result_row2['4CODE'];
                $syain_array[$counter]['STAFFNAME'] = $result_row2['STAFFNAME'];
                $counter++;
            }
            
            //開始日付と終了日付の配列番号取得
            $start_month_count = 0;
            $end_month_count = 12;
            $date = new DateTime($end);
            $end_month = $date->format('m');
            for($k = 0; $k < count($month_keys); $k++)
            {
                if(date("m") == $month_keys[$k])
                {
                    $start_month_count = $k;
                }
                if($end_month == $month_keys[$k])
                {
                    $end_month_count = $k;
                }
            }
            //売上額表示
            $csv .= '"売上"';
            for($k = 0; $k < count($month_keys); $k++)
            {
                $key = $month_keys[$k];
                if($k >= $start_month_count && $k <= $end_month_count)
                {
                    $csv .= ',"'.$uriage['TOTAL'][$key].'"';
                }
                else
                {
                    $csv .= ',';
                }
            }
            $csv .= ',"'.$uriage['TOTAL']['TOTAL'].'"';
            $csv .=  "\r\n";
            
            //残業時間取得
            $zangyoutime = array();
            for($s = 0; $s < count($syain_array); $s++)
            {
                $sql3 = "SELECT *,date_format(SAGYOUDATE, '%m') AS MONTH FROM progressinfo "
                        ."LEFT JOIN projectditealinfo USING(6CODE) LEFT JOIN projectinfo USING(5CODE) "
                        ."LEFT JOIN kokyakuinfo USING(12CODE) LEFT JOIN teaminfo USING(12CODE) "
                        ."WHERE URIAGEMONTH BETWEEN '".$start."' AND '".$end."' "
                        ."AND 5CODE = '".$result_row['5CODE']."' AND 4CODE = '".$syain_array[$s]['4CODE']."';";
                $result3 = $con->query($sql3);
                while($result_row3 = $result3->fetch_array(MYSQLI_ASSOC))
                {
                    if(!isset($zangyoutime[$syain_array[$s]['4CODE']]))
                    {
                        $zangyoutime[$syain_array[$s]['4CODE']] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
                    }
                    $zangyoutime[$syain_array[$s]['4CODE']][$result_row3['MONTH']] += $result_row3['ZANGYOUTIME'];
                    $zangyoutime[$syain_array[$s]['4CODE']]['TOTAL'] += $result_row3['ZANGYOUTIME'];
                }
                if(!isset($zangyoutime[$syain_array[$s]['4CODE']]))
                {
                    $zangyoutime[$syain_array[$s]['4CODE']] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);    
                }
            }
            
            //原価計算
            $genka = array();
            $genka['TOTAL'] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
            for($s = 0; $s < count($syain_array); $s++)
            {
                $genka[$syain_array[$s]['4CODE']] = array('TOTAL' => 0,'01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0);
                //原価の計算
                for($k = 0; $k < count($month_keys); $k++)
                {
                    $key = $month_keys[$k];
                    //社員別の原価,全体の原価
                    if($k >= $start_month_count && $k <= $end_month_count)
                    {
                        if(isset($genka_list[$syain_array[$s]['4CODE']]) && isset($keisu[$syain_array[$s]['4CODE']][$result_row['5CODE']][$key]))
                        {
                            $genka[$syain_array[$s]['4CODE']][$key] = $genka_list[$syain_array[$s]['4CODE']] * $keisu[$syain_array[$s]['4CODE']][$result_row['5CODE']][$key];
                            $genka[$syain_array[$s]['4CODE']]['TOTAL'] += $genka[$syain_array[$s]['4CODE']][$key];
                            $genka['TOTAL'][$key] += $genka[$syain_array[$s]['4CODE']][$key] + ($zangyoutime[$syain_array[$s]['4CODE']][$key] * $zangyou_list[$syain_array[$s]['4CODE']]);
                            $genka['TOTAL']['TOTAL'] += $genka[$syain_array[$s]['4CODE']][$key] + ($zangyoutime[$syain_array[$s]['4CODE']][$key] * $zangyou_list[$syain_array[$s]['4CODE']]);
                        }
                        else
                        {
                            $genka[$syain_array[$s]['4CODE']][$key] = 0;
                            $genka[$syain_array[$s]['4CODE']]['TOTAL'] += $genka[$syain_array[$s]['4CODE']][$key];
                            $genka['TOTAL'][$key] += $genka[$syain_array[$s]['4CODE']][$key] + ($zangyoutime[$syain_array[$s]['4CODE']][$key] * $zangyou_list[$syain_array[$s]['4CODE']]);
                            $genka['TOTAL']['TOTAL'] += $genka[$syain_array[$s]['4CODE']][$key] + ($zangyoutime[$syain_array[$s]['4CODE']][$key] * $zangyou_list[$syain_array[$s]['4CODE']]);                       
                        }

                        //XX-XXXXXXXXの合計原価
                        $total_genka[$key] += $genka[$syain_array[$s]['4CODE']][$key];
                        $total_genka['TOTAL'] += $genka[$syain_array[$s]['4CODE']][$key];
                    } 
                }
            }
            
            //原価表示
            $csv .= '"原価"';
            for($k = 0; $k < count($month_keys); $k++)
            {
                $key = $month_keys[$k];
                if($k >= $start_month_count && $k <= $end_month_count)
                {
                    $csv .= ',"'.$genka['TOTAL'][$key].'"';
                }
                else
                {
                    $csv .= ',';
                }
            }
            $csv .= ',"'.$genka['TOTAL']['TOTAL'].'"';
            $csv .=  "\r\n";
            
            //社員別原価表示
            for($s = 0; $s < count($syain_array); $s++)
            {
                $csv .= '"'.$syain_array[$s]['STAFFNAME'].'"';
                for($k = 0; $k < count($month_keys); $k++)
                {
                    $key = $month_keys[$k];
                    if($k >= $start_month_count && $k <= $end_month_count)
                    {
                        $csv .= ',"'.$genka[$syain_array[$s]['4CODE']][$key].'"';
                    }
                    else
                    {
                        $csv .= ',';
                    }
                }
                $csv .= ',"'.$genka[$syain_array[$s]['4CODE']]['TOTAL'].'"';
                $csv .=  "\r\n";
            }
            
            //残業時間表示
            for($s = 0; $s < count($syain_array); $s++)
            {
                $csv .= '"'.$syain_array[$s]['STAFFNAME'].'残業"';
                $flag = 0;
                for($k = 0; $k < count($month_keys); $k++)
                {
                    $key = $month_keys[$k];
                    if($k >= $start_month_count && $k <= $end_month_count)
                    {
                        $csv .= ',"'.($zangyoutime[$syain_array[$s]['4CODE']][$key] * $zangyou_list[$syain_array[$s]['4CODE']]).'"';
                    }
                    else
                    {
                        $csv .= ',';
                    }
                }
                $csv .= ',"'.($zangyoutime[$syain_array[$s]['4CODE']]['TOTAL'] * $zangyou_list[$syain_array[$s]['4CODE']]).'"';
                $csv .=  "\r\n";
            }            
            
            //粗利
            $csv .= "粗利";
            for($k = 0; $k < count($month_keys); $k++)
            {
                $key = $month_keys[$k];
                if($k >= $start_month_count && $k <= $end_month_count)
                {
                    $csv .= ',"'.($uriage['TOTAL'][$key] - $genka['TOTAL'][$key]).'"';
                }
                else
                {
                    $csv .= ',';
                }
            }
            $csv .= ',"'.($uriage['TOTAL']['TOTAL'] - $genka['TOTAL']['TOTAL']).'"';
            $csv .=  "\r\n";

            //倍率
            $csv .= "倍率";
            for($k = 0; $k < count($month_keys); $k++)
            {
                $key = $month_keys[$k];
                if($k >= $start_month_count && $k <= $end_month_count)
                {
                    if($uriage['TOTAL'][$key] == 0 || $genka['TOTAL'][$key] == 0)
                    {
                        $csv .= ',"0"';
                    }
                    else
                    {
                        $csv .= ',"'.($uriage['TOTAL'][$key] / $genka['TOTAL'][$key]).'"';
                    }
                }
                else
                {
                    $csv .= ',';
                }
            }
            if($uriage['TOTAL']['TOTAL'] == 0 || $genka['TOTAL']['TOTAL'] == 0)
            {
                $csv .= ',"0"';
            }
            else
            {
                $csv .= ',"'.($uriage['TOTAL']['TOTAL'] / $genka['TOTAL']['TOTAL']).'"';
            }
            $csv .=  "\r\n";
            
            $csv .=  "\r\n";
        }
        //XX-XXXXXXXXの合計値を出力
        $csv .= '"'.$pj_list[$i]['PJCODE'].'",,"合計"';
        $csv .=  "\r\n";
        
        //項目名表示
        $csv .= ',6月,7月,8月,9月,10月,11月,12月,1月,2月,3月,4月,5月,合計';
        $csv .=  "\r\n";
        
        //売上額表示
        $csv .= '"売上"';
        for($k = 0; $k < count($month_keys); $k++)
        {
            $key = $month_keys[$k];
            if($k >= $start_month_count && $k <= $end_month_count)
            {
                $csv .= ',"'.$total_uriage[$key].'"';
            }
            else
            {
                $csv .= ',';
            }
        }
        $csv .= ',"'.$total_uriage['TOTAL'].'"';
        $csv .=  "\r\n";
        
        //原価
        $csv .= '"売上"';
        for($k = 0; $k < count($month_keys); $k++)
        {
            $key = $month_keys[$k];
            if($k >= $start_month_count && $k <= $end_month_count)
            {
                $csv .= ',"'.$total_genka[$key].'"';
            }
            else
            {
                $csv .= ',';
            }
        }
        $csv .= ',"'.$total_genka['TOTAL'].'"';
        $csv .=  "\r\n";
        
        //粗利
        $csv .= "粗利";
        for($k = 0; $k < count($month_keys); $k++)
        {
            $key = $month_keys[$k];
            if($k >= $start_month_count && $k <= $end_month_count)
            {
                $csv .= ',"'.($total_uriage[$key] - $total_genka[$key]).'"';
            }
            else
            {
                $csv .= ',';
            }
        }
        $csv .= ',"'.($total_uriage['TOTAL'] - $total_genka['TOTAL']).'"';
        $csv .=  "\r\n";
        
        //倍率
        $csv .= "倍率";
        for($k = 0; $k < count($month_keys); $k++)
        {
            $key = $month_keys[$k];
            if($k >= $start_month_count && $k <= $end_month_count)
            {
                if($total_uriage[$key] == 0 || $total_genka[$key] == 0)
                {
                    $csv .= ',"0"';
                }
                else
                {
                    $csv .= ',"'.($total_uriage[$key] / $total_genka[$key]).'"';
                }   
            }
            else
            {
                $csv .= ',';
            }      
        }
        if($total_uriage['TOTAL'] == 0 || $total_genka['TOTAL'] == 0)
        {
            $csv .= ',"0"';
        }
        else
        {
            $csv .= ',"'.($total_uriage['TOTAL'] / $total_genka['TOTAL']).'"';
        }
        $csv .=  "\r\n";

        $csv .=  "\r\n";
    }
    return $csv;
}

?>