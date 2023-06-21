<?php
/************************************************************************************************************
function itemListSQL($post)

ˆø”	$post

–ß‚è’l	‚È‚µ
************************************************************************************************************/
function itemListSQL($post){
    
    //‰ŠúÝ’è
    require_once ("f_Form.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
	
    //’è”
    $filename = $_SESSION['filename'];
    $sech_form_num = explode(',',$form_ini[$filename]['sech_form_num']);
    $sech_type = explode(',',$form_ini[$filename]['sech_type']);
    
    //•Ï”
    $select_sql = $SQL_ini[$filename]['select_sql'];
    $count_sql = $SQL_ini[$filename]['count_sql'];
    $where = "";
    $sql = array();    

    //ŒŸõðŒ’Ç‹L
    for($i = 0; $i < count($sech_form_num); $i++)
    {
        if(((isset($post[$sech_form_num[$i]])) && ($post[$sech_form_num[$i]] != "")))
        {
            switch($sech_type[$i])
            {
                case "0":
                    $where .= " AND ".$form_ini[$sech_form_num[$i]]['column']." = '".$post[$sech_form_num[$i]]."' ";
                    break;
                case "1":
                    $where .= " AND ".$form_ini[$sech_form_num[$i]]['column']." LIKE '%".$post[$sech_form_num[$i]]."%' ";
                    break;
                case "2":
                    if($sech_form_num[$i] == 'period')
                    {
                        if($post[$sech_form_num[$i]] < 10)
                        {
                            $where .= " AND KOKYAKUID LIKE '%0".$post[$sech_form_num[$i]]."%' ";
                        }
                        else
                        {
                            $where .= " AND KOKYAKUID LIKE '%".$post[$sech_form_num[$i]]."%' ";
                        }
                    }
                    else
                    {
                        $where .= " AND ".$form_ini[$sech_form_num[$i]]['column']." = '".$post[$sech_form_num[$i]]."' ";
                    }
                    break;
            }
        }
        elseif(isset($post[$sech_form_num[$i].'_startdate']) && isset($post[$sech_form_num[$i].'_enddate']))
        {
            if($filename == "rireki_2")
            {
                if($post[$sech_form_num[$i].'_startdate'] != "" && $post[$sech_form_num[$i].'_enddate'] != "")
                {
                    $where .= " AND ".$form_ini[$sech_form_num[$i]]['column']." BETWEEN '".$post[$sech_form_num[$i].'_startdate']." 00:00:00' AND '".$post[$sech_form_num[$i].'_enddate']." 23:59:59' ";
                }
                elseif($post[$sech_form_num[$i].'_startdate'] != "" && $post[$sech_form_num[$i].'_enddate'] == "")
                {
                    $where .= " AND ".$form_ini[$sech_form_num[$i]]['column']." >= '".$post[$sech_form_num[$i].'_startdate']." 00:00:00' ";
                }
                elseif($post[$sech_form_num[$i].'_startdate'] == "" && $post[$sech_form_num[$i].'_enddate'] != "")
                {
                    $where .= " AND ".$form_ini[$sech_form_num[$i]]['column']." <= '".$post[$sech_form_num[$i].'_enddate']." 23:59:59' ";
                }                
            }
            else
            {
                if($post[$sech_form_num[$i].'_startdate'] != "" && $post[$sech_form_num[$i].'_enddate'] != "")
                {
                    $where .= " AND ".$form_ini[$sech_form_num[$i]]['column']." BETWEEN '".$post[$sech_form_num[$i].'_startdate']."' AND '".$post[$sech_form_num[$i].'_enddate']."' ";
                }
                elseif($post[$sech_form_num[$i].'_startdate'] != "" && $post[$sech_form_num[$i].'_enddate'] == "")
                {
                    $where .= " AND ".$form_ini[$sech_form_num[$i]]['column']." >= '".$post[$sech_form_num[$i].'_startdate']."'";
                }
                elseif($post[$sech_form_num[$i].'_startdate'] == "" && $post[$sech_form_num[$i].'_enddate'] != "")
                {
                    $where .= " AND ".$form_ini[$sech_form_num[$i]]['column']." <= '".$post[$sech_form_num[$i].'_enddate']."'";
                }
            }
        }
        elseif($form_ini[$sech_form_num[$i]]['field_type'] == "4")
        {
            if(((isset($post[$sech_form_num[$i].'01'])) && ($post[$sech_form_num[$i].'01'] != "")))
            {
                $table_name = $form_ini[$sech_form_num[$i]]['table_name'];
                $where .= " AND ".$table_name.".".$sech_form_num[$i]."CODE = '".$post[$sech_form_num[$i].'01']."' ";
            }
        }
        
        //PJó‘Ô‚ÌŒŸõðŒ
        if($sech_form_num[$i] == "PJSTAT" && isset($post[$sech_form_num[$i]]))
        {
            if($post[$sech_form_num[$i]] != "")
            {
                $where .= " AND ".$form_ini[$sech_form_num[$i]]['column']." = '".$post[$sech_form_num[$i]]."' ";
            }
        }
        elseif($sech_form_num[$i] == "PJSTAT")
        {
            if($filename == 'pjend_5')
            {
                $where .= " AND ".$form_ini[$sech_form_num[$i]]['column']." != '1' ";        
            }
        }
        
        //Šú‚ÌŒŸõðŒ
        if($sech_form_num[$i] == "period" && !isset($post[$sech_form_num[$i]]))
        {
            $today = explode('/',date("Y/m/d"));
            $period = getperiod($today[1],$today[0]);
            if($period < 10)
            {
                $where .= " AND KOKYAKUID LIKE '%0".$period."%' ";
            }
            else
            {
                $where .= " AND KOKYAKUID LIKE '%".$period."%' ";
            }
        }
        if($sech_form_num[$i] == "904" && !isset($post[$sech_form_num[$i]]))
        {
            $today = explode('/',date("Y/m/d"));
            $period = getperiod($today[1],$today[0]);
            $where .= " AND PERIOD = '".$period."' ";
        }
    }   

    if(isset($SQL_ini[$filename]['groupby']))
    {
        $groupby = $SQL_ini[$filename]['groupby'];
    }
    else
    {
        $groupby = "";
    }
    $sql[0] = $select_sql.$where.$groupby;
    $sql[1] = $count_sql.$where;
    return $sql;
}

/************************************************************************************************************
function SQLsetOrderby($post,$sql)

ˆø”            $post                   
                  $sql

–ß‚è’l	$sql
************************************************************************************************************/
function SQLsetOrderby($post,$sql){
    
    //‰ŠúÝ’è
    $form_ini = parse_ini_file('./ini/form.ini', true);	
    $SQL_ini = parse_ini_file('./ini/form.ini', true);
	
    //’è”
    $filename = $_SESSION['filename'];   
    $default_orderby = explode(',',$form_ini[$filename]['default_orderby']);
    $default_orderby_type = explode(',',$form_ini[$filename]['default_orderby_type']);
    $orderby_array = array(' DESC ',' ASC ');
    
    //•Ï”
    $orderby = " ORDER BY ";
    
    //ˆ—
    if((!isset($post['sort1']) && !isset($post['sort2'])) || ($post['sort1'] == "" && $post['sort2'] == ""))
    {
        for($i = 0; $i < count($default_orderby); $i++)
        {
            if($default_orderby[$i] != "")
            {
                $sql[0] .= $orderby." ".$form_ini[$default_orderby[$i]]['column']." ".$orderby_array[$default_orderby_type[$i]];
                $orderby = " , ";                
            }
        }
    }
    else
    {
        for($i = 1; $i <= 2; $i++)
        {
            if($post['sort'.$i] != "")
            {
                $sql[0] .= $orderby." ".$form_ini[$post['sort'.$i]]['column']." ".$post['radiobutton'.$i];
                $orderby = " , ";
            }
        }
    }
    
    return $sql;
}

/************************************************************************************************************
function InsertSQL($post)

ˆø”          $post                             “o˜^î•ñ

–ß‚è’l       $inset_sql                       V‹K“o˜^SQL
************************************************************************************************************/
function InsertSQL($post){
    
    //‰ŠúÝ’è
    require_once ("f_DB.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //’è”
    $filename = $_SESSION['filename'];
    $insert_form_num = explode(',',$form_ini[$filename]['insert_form_num']);
    $tableName = $form_ini[$form_ini[$filename]['main_table']]['table_name'];
    
    //•Ï”
    $insert_sql = "";
    
    //ˆ—
    $insert_sql .= "INSERT INTO ".$tableName." (";
    for($i = 0; $i < count($insert_form_num); $i++)
    {
        $insert_sql .= $form_ini[$insert_form_num[$i]]['column'].",";
    }
    $insert_sql = substr($insert_sql,0,-1);
    $insert_sql .= ")VALUES(";
    for($i = 0; $i < count($insert_form_num); $i++)
    {
        $insert_sql .= "'".$post[$insert_form_num[$i]]."',";
    }
    $insert_sql = substr($insert_sql,0,-1);
    $insert_sql .= ");";
    
    return $insert_sql;
}

/************************************************************************************************************
function UpdateSQL($post)

ˆø”          $post                             “o˜^î•ñ

–ß‚è’l       $update_sql                   V‹K“o˜^SQL
************************************************************************************************************/
function UpdateSQL($post){
    
    //‰ŠúÝ’è
    require_once ("f_DB.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //’è”
    $filename = $_SESSION['filename'];
    $edit_form_num = explode(',',$form_ini[$filename]['edit_form_num']);
    $tableName = $form_ini[$form_ini[$filename]['main_table']]['table_name'];
    $main_code = $form_ini[$form_ini[$filename]['main_code']]['column'];
    
    //•Ï”
    $update_sql = "";
    
    //ˆ—
    $update_sql = "UPDATE ".$tableName." SET";
    for($i = 0; $i < count($edit_form_num); $i++)
    {
        $update_sql .= " ".$form_ini[$edit_form_num[$i]]['column']." = ";
        $update_sql .= "'".$post[$edit_form_num[$i]]."',";
    }
    $update_sql = rtrim($update_sql,',');
    $update_sql .= " WHERE ".$main_code." = '".$post['edit_id']."';";
    return $update_sql;
}

/************************************************************************************************************
function uniqeSelectSQL($post,$tablenum,$columns)

ˆø”	$post

–ß‚è’l	‚È‚µ
************************************************************************************************************/
function uniqeSelectSQL($post,$tablenum,$columns){
    
    //‰ŠúÝ’è
    require_once ("f_DB.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //’è”
    $table_name = $form_ini[$tablenum]['table_name'];
    $column_name = $form_ini[$columns]['column'];
    //•Ï”
    $select_sql = "";
    
    //ˆ—
    $select_sql .= "SELECT * FROM ".$table_name." WHERE 1=1 ";
    $select_sql .= "AND ".$column_name." = '".$post[$columns]."' ";
    
    return $select_sql;
}

/************************************************************************************************************
function listUser_SQL($post)

ˆø”	$post                   ŒŸõî•ñ

–ß‚è’l	$sql             ŒŸõSQL
************************************************************************************************************/
function listUser_SQL($post){
    
    //‰ŠúÝ’è
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $SQL_ini = parse_ini_file('./ini/SQL.ini', true);
    
    //’è”
    $filename = $_SESSION['filename'];

    //•Ï”
    $select_sql = $SQL_ini[$filename]['select_sql'];
    $count_sql = $SQL_ini[$filename]['count_sql'];
    $where = "";
    $orderby = "";
    $sql = array();
    
    //ŒŸõðŒ’Ç‹L
    $where .= " AND LUSERNAME IS NOT NULL AND LUSERPASS IS NOT NULL ";
    if((isset($post['401'])) && ($post['401'] != ""))
    {
        $where .= " AND 4CODE = '".$post['401']."' ";
    }
    
    //ƒ\[ƒgðŒ’Ç‹L
    if((!isset($post['sort1'])) || ($post['sort1'] == ""))
    {
        $orderby .= " ORDER BY STAFFID ASC ";
    }
    else
    {
        $orderby .= " ORDER BY ".$form_ini[$post['sort1']]['column']." ".$post['radiobutton1'];
    }
    $sql[0] = $select_sql.$where.$orderby;
    $sql[1] = $count_sql.$where.$orderby;
    return $sql;
}
?>