<?php
/************************************************************************************************************
function makeModalHtml()

引数          $post                                        検索情報

戻り値	$modal_html                             モーダルHTML
************************************************************************************************************/
function makeModalHtml($post){
    
    //初期設定
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $input_datalist_ini = parse_ini_file('./ini/input_datalist.ini', true);
    $system_ini = parse_ini_file('./ini/system.ini', true);
    //定数
    $filename = $_SESSION['filename'];
    $sech_form_num = explode(',',$form_ini[$filename]['sech_form_num']);
    $orderby_columns = explode(',',$form_ini[$filename]['orderby_columns']);
    
    //変数
    $modal_html = "";
    
    //処理
    $con = dbconect();
    $modal_html .= '<div class="kensaku_title">検索条件</div>';
    $modal_html .= '<table>';
    
    //検索条件追記
    for($i = 0; $i < count($sech_form_num); $i++)
    {
        if($form_ini[$sech_form_num[$i]]['field_type']== "4")
        {
            $modal_html .= '<tr>';
            $modal_html .= '<td></td>';
            $modal_html .= '<td>';
            if(isset($post[$sech_form_num[$i].'01']))
            {
                $value = $post[$sech_form_num[$i].'01'];
            }
            else
            {
                $value = "";
            }
            $modal_html .= '<input type="hidden" name="'.$sech_form_num[$i].'01" id="'.$sech_form_num[$i].'01" value="'.$value.'">'; 
            $modal_html .= '<input type="button" value="'.$form_ini[$sech_form_num[$i]]['button_name'].'" onclick="popup_modal('.$sech_form_num[$i].');">';
            $modal_html .= '</td>';
            $modal_html .= '</tr>';
            $modal_array = explode(',',$form_ini[$sech_form_num[$i]]['result_num']);
            for($j = 0; $j < count($modal_array); $j++)
            {
                if(isset($post[$modal_array[$j]]))
                {
                    $value = $post[$modal_array[$j]];
                }
                else
                {
                    $value = "";
                }
                $modal_html .= '<tr>';
                $modal_html .= '<td>'.$form_ini[$modal_array[$j]]['item_name'].'</td>';
                $modal_html .= '<td><input type="text" size="'.$form_ini[$modal_array[$j]]['form_size'].'" name="'.$modal_array[$j].'" id="'.$modal_array[$j].'" class="form_text disabled" value="'.$value.'"></td>';
                $modal_html .= '</tr>';
            }
        }
        else
        {
            $modal_html .= '<tr>';
            $modal_html .= '<td>'.$form_ini[$sech_form_num[$i]]['item_name'].'</td>';
            $modal_html .= '<td>';
            switch($form_ini[$sech_form_num[$i]]['field_type'])
            {                
                case '1':
                    if(isset($post[$sech_form_num[$i]]))
                    {
                        $value = $post[$sech_form_num[$i]];
                    }
                    else
                    {
                        $value = "";
                    }
                    $modal_html .= '<input type="text" name="'.$sech_form_num[$i].'" id="'.$sech_form_num[$i].'" size="'.$form_ini[$sech_form_num[$i]]['form_size'].'" value="'.$value.'" class="form_text" autocomplete="off" list="'.$sech_form_num[$i].'_datalist">';
                    if(isset($input_datalist_ini[$sech_form_num[$i]]['sech']))
                    {
                        $modal_html .= '<datalist id="'.$sech_form_num[$i].'_datalist">';
                        $sql = $input_datalist_ini[$sech_form_num[$i]]['sech'];
                        $result = $con->query($sql);
                        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                        {
                            $modal_html .= '<option value="'.$result_row[$form_ini[$sech_form_num[$i]]['column']].'"></option>';
                        }
                        $modal_html .= '</datalist>';
                    }
                    break;
                case '2':
                    $modal_html .= '<select name="'.$sech_form_num[$i].'" id="'.$sech_form_num[$i].'" class="form_text">';
                    if($sech_form_num[$i] == 'period' || $sech_form_num[$i] == '904')
                    {
                        $period = date_format(date_create('NOW'), "Y") - $system_ini['period']['startyear'];
                        if($system_ini['period']['startmonth'] <= date_format(date_create('NOW'), "n"))
                        {
                            $period = $period + 1;
                        }
                        if($filename == "PJTOUROKU_2")
                        {
                            $period = $period + 1;
                        }
                        for($j = 9 ;$j <= $period ; $j++)
                        {
                            if(isset($post[$sech_form_num[$i]]))
                            {
                                if($post[$sech_form_num[$i]] == $j)
                                {
                                    $selected = "selected";
                                }
                                else
                                {
                                    $selected = "";
                                }
                            }
                            else
                            {
                                if($filename == "PJTOUROKU_2" && $j == ($period - 1))
                                {
                                    $selected = "selected";
                                }
                                elseif($filename != "PJTOUROKU_2" && $j == $period)
                                {
                                    $selected = "selected";
                                }
                                else
                                {
                                    $selected = "";
                                }
                            }
                            $modal_html .= '<option value="'.$j.'" '.$selected.'>'.$j.'期</option>';
                        }
                    }
                    else
                    {
                        $select = explode(',',$input_datalist_ini[$sech_form_num[$i]]['sech']);
                        $select_value = explode(',',$input_datalist_ini[$sech_form_num[$i]]['sech_value']);
                        $modal_html .= '<option value="">指定なし</option>';
                        for($j = 0; $j < count($select); $j++)
                        {
                            if(isset($post[$sech_form_num[$i]]) && $select_value[$j] == $post[$sech_form_num[$i]])
                            {
                                $modal_html .= '<option value="'.$select_value[$j].'" selected>'.$select[$j].'</option>';
                            }
                            else
                            {
                                $modal_html .= '<option value="'.$select_value[$j].'">'.$select[$j].'</option>';
                            }
                        }
                    }
                    $modal_html .= '</select>';
                    break;
                case '3':
                    if(isset($post[$sech_form_num[$i].'_startdate']) && isset($post[$sech_form_num[$i].'_enddate']))
                    {
                        $startdate = $post[$sech_form_num[$i].'_startdate'];
                        $enddate = $post[$sech_form_num[$i].'_enddate'];
                    }
                    else
                    {
                        $startdate = "";
                        $enddate = "";
                    }
                    $modal_html .= '<input type="date" name="'.$sech_form_num[$i].'_startdate" id="'.$sech_form_num[$i].'_startdate" class="form_text" value="'.$startdate.'">';
                    if($filename == 'PJLIST_2' || $filename == 'ENDPJLIST_2')
                    {
                        $modal_html .= '<input type="button" class="icon_button" value="期首" onclick="input_startdate('.$sech_form_num[$i].');" style="width: 35px; margin-left: 2px;">';
                    }
                    $modal_html .= '　~　';
                    $modal_html .= '<input type="date" name="'.$sech_form_num[$i].'_enddate" id="'.$sech_form_num[$i].'_enddate" class="form_text" value="'.$enddate.'">';
                    break;
                case '5':
                    $select = explode(',',$input_datalist_ini[$sech_form_num[$i]]['sech']);
                    $select_value = explode(',',$input_datalist_ini[$sech_form_num[$i]]['sech_value']);
                    if($filename != 'pjend_5')
                    {
                        $select = explode(',',"指定なし,未終了,終了済み");
                        $select_value = explode(',',",0,1");
                    }
                    if($filename == "pjend_5" && $sech_form_num[$i] == "PJSTAT")
                    {
                        $onchange = "date_disabled_change();";
                    }
                    else
                    {
                        $onchange = "";
                    }
                    for($j = 0; $j < count($select); $j++)
                    {
                        if((isset($post[$sech_form_num[$i]])) && $post[$sech_form_num[$i]] == $select_value[$j])
                        {
                            $modal_html .= '<input type="radio" name="'.$sech_form_num[$i].'" value="'.$select_value[$j].'" checked onchange="'.$onchange.'">'.$select[$j].'　';
                        }
                        else
                        {
                            if(!isset($post[$sech_form_num[$i]]) && $j == 0)
                            {
                                $modal_html .= '<input type="radio" name="'.$sech_form_num[$i].'" value="'.$select_value[$j].'" checked onchange="'.$onchange.'">'.$select[$j].'　';
                            }
                            else
                            {
                                $modal_html .= '<input type="radio" name="'.$sech_form_num[$i].'" value="'.$select_value[$j].'" onchange="'.$onchange.'">'.$select[$j].'　';
                            }
                        }
                    }
                    break;
            }
            $modal_html .= '</td>';
            $modal_html .= '</tr>';
        }
    }
    //ソート条件追記
    if($form_ini[$filename]['orderby_columns'] != "")
    {
        for($i = 1; $i <= 2; $i++)
        {
            $modal_html .= '<tr>';
            $modal_html .= '<td>ソート条件'.$i.'</td>';
            $modal_html .= '<td>';
            $modal_html .= '<select name="sort'.$i.'" class="form_text">';
            $modal_html .= '<option value="">指定なし</option>';
            for($j = 0; $j < count($orderby_columns); $j++)
            {
                if((isset($post['sort'.$i])) && ($post['sort'.$i] == $orderby_columns[$j]))
                {
                    $modal_html .= '<option value="'.$orderby_columns[$j].'" selected>';
                }
                else
                {
                    $modal_html .= '<option value="'.$orderby_columns[$j].'">';
                }
                $modal_html .= $form_ini[$orderby_columns[$j]]['item_name'];
                $modal_html .= '</oprion>';
            }
            $modal_html .= '</select>';
            if((isset($post['radiobutton'.$i])) && ($post['radiobutton'.$i] == "DESC"))
            {
                $modal_html .= '<label><input name="radiobutton'.$i.'" type="radio" value="ASC">昇順</label>　';
                $modal_html .= '<label><input name="radiobutton'.$i.'" type="radio" value="DESC" checked>降順</label>　';
            }
            else
            {
                $modal_html .= '<label><input name="radiobutton'.$i.'" type="radio" value="ASC" checked>昇順</label>　';
                $modal_html .= '<label><input name="radiobutton'.$i.'" type="radio" value="DESC">降順</label>　';
            }
            $modal_html .= '</td>';
            $modal_html .= '</tr>';
        }
    }
    $modal_html .= '</table>';
    $modal_html .= '<input type="submit" name="serch" value="検索" class="modal_button">';
    $modal_html .= '<input type="button" value="キャンセル" class="modal_button" onclick="modal_close();">';
    return $modal_html;
}

/************************************************************************************************************
function    makeformInsert_set()

引数          $post                                        登録情報

戻り値	$insert_html                             モーダルHTML
************************************************************************************************************/
function makeformInsert_set($post){
    
    //初期設定
    require_once ("f_Form.php");
    require_once ("f_SQL.php");
    require_once ("f_DB.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $input_datalist_ini = parse_ini_file('./ini/input_datalist.ini', true);
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $insert_form_num = explode(',',$form_ini[$filename]['insert_form_num']);
    
    //変数
    $insert_html = "";
    
    //処理
    $con = dbconect();
    $insert_html .= '<table>';
    for($i = 0; $i < count($insert_form_num); $i++)
    {
        $max_length = $form_ini[$insert_form_num[$i]]['max_length'];
        $form_format = $form_ini[$insert_form_num[$i]]['form_format'];
        $isnotnull = $form_ini[$insert_form_num[$i]]['isnotnull'];
        $isJust = $form_ini[$insert_form_num[$i]]['isJust'];
        if($insert_form_num[$i] == "1202")
        {
            $onchange = 'inputcheck(this.id,'.$max_length.','.$form_format.','.$isnotnull.','.$isJust.'); kokyakumei_set();';           
        }
        elseif($insert_form_num[$i] == "1303" && $filename == "PJTOUROKU_1")
        {
            $onchange = 'inputcheck(this.id,'.$max_length.','.$form_format.','.$isnotnull.','.$isJust.'); teammei_set();';     
        }
        else
        {
            $onchange = 'inputcheck(this.id,'.$max_length.','.$form_format.','.$isnotnull.','.$isJust.');';                   
        }
        $insert_html .= '<tr>';
        $insert_html .= '<td>'.$form_ini[$insert_form_num[$i]]['item_name'].'</td>';
        $insert_html .= '<td>';
        if(isset($post[$insert_form_num[$i]]))
        {
            $value = $post[$insert_form_num[$i]];
        }
        else
        {
            $value = "";
        }
        
        //入力指示作成
        if($form_ini[$insert_form_num[$i]]['field_type'] == '1')
        {
            $placeholder = "";
            switch($form_format)
            {
                case '3':
                    $placeholder .= "半角英数字";
                    break;
                case '4':
                    $placeholder .= "半角数字";
                    break;
            }
            if($isJust == '1')
            {
                $placeholder .= $max_length."文字";
            }
            else
            {
                $placeholder .= $max_length."文字以内";
            }
        }
        else
        {
            $placeholder = "";
        }
        switch($form_ini[$insert_form_num[$i]]['field_type'])
        {                
            case '1':
                $insert_html .= '<input type="text" placeholder="'.$placeholder.'" name="'.$insert_form_num[$i].'" id="'.$insert_form_num[$i].'" size="'.$form_ini[$insert_form_num[$i]]['form_size'].'" value="'.$value.'" class="form_text" onchange="'.$onchange.'" autocomplete="off" list="'.$insert_form_num[$i].'_datalist">';
                if((isset($input_datalist_ini[$insert_form_num[$i]]['insert']) && $filename != "KOKYAKUTEAM_1") || $insert_form_num[$i] == "1202")
                {
                    $insert_html .= '<datalist id="'.$insert_form_num[$i].'_datalist">';
                    $sql = $input_datalist_ini[$insert_form_num[$i]]['insert'];
                    $result = $con->query($sql);
                    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                    {
                        $insert_html .= '<option value="'.$result_row[$form_ini[$insert_form_num[$i]]['column']].'"></option>';
                    }
                    $insert_html .= '</datalist>';
                }
                break;
            case '2':
                if($filename == "PJTOUROKU_1" && $insert_form_num[$i] == "period")
                {
                    $insert_html .= '<select name="'.$insert_form_num[$i].'" id="'.$insert_form_num[$i].'" class="form_text" onchange="period_select(); reset_kokyakuteam();">';
                }
                else
                {
                    $insert_html .= '<select name="'.$insert_form_num[$i].'" id="'.$insert_form_num[$i].'" class="form_text">';
                }
                if($insert_form_num[$i] == "period")
                {
                    $period = date_format(date_create('NOW'), "Y") - $system_ini['period']['startyear'];
                    if($system_ini['period']['startmonth'] <= date_format(date_create('NOW'), "n"))
                    {
                        $period = $period + 1;
                    }
                    $con = dbconect();
                    $sql = "SELECT *FROM endperiodinfo;";
                    $result = $con->query($sql);
                    $nenzi_list = array();
                    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
                    {
                        $nenzi_list[$result_row['PERIOD']] = $result_row['PERIOD'];
                    }
                    for($j = 9; $j <= ($period + 1); $j++)
                    {
                        if(!isset($nenzi_list[$j]))
                        {
                            if($value == $j)
                            {
                                $insert_html .= '<option value="'.$j.'" selected>'.$j.'期</option>';
                            }
                            elseif($value == "" && $period == $j)
                            {
                                $insert_html .= '<option value="'.$j.'" selected>'.$j.'期</option>';
                            }
                            else
                            {
                                $insert_html .= '<option value="'.$j.'">'.$j.'期</option>';
                            }
                        }
                    }
                }
                $insert_html .= '</select>';
                break;
            case '3':
                $insert_html .= '<input type="date" name="'.$insert_form_num[$i].'" id="'.$insert_form_num[$i].'" value="'.$value.'" class="form_text" onchange="'.$onchange.'">';                
                break;
            case '4':
                $insert_html .= '<input type="month" name="'.$insert_form_num[$i].'" id="'.$insert_form_num[$i].'" value="'.$value.'" class="form_text" onchange="'.$onchange.'">';
                break;
            case '5':
                $select = explode(',',$input_datalist_ini[$insert_form_num[$i]]['insert']);
                $select_value = explode(',',$input_datalist_ini[$insert_form_num[$i]]['insert_value']);
                for($j = 0; $j < count($select); $j++)
                {
                    if((isset($post[$insert_form_num[$i]])) && $post[$insert_form_num[$i]] == $select_value[$j])
                    {
                        $insert_html .= '<input type="radio" name="'.$insert_form_num[$i].'" value="'.$select_value[$j].'" checked>'.$select[$j].'　';
                    }
                    else
                    {
                        if(!isset($post[$insert_form_num[$i]]) && $j == 0)
                        {
                            $insert_html .= '<input type="radio" name="'.$insert_form_num[$i].'" value="'.$select_value[$j].'" checked>'.$select[$j].'　';
                        }
                        else
                        {
                            $insert_html .= '<input type="radio" name="'.$insert_form_num[$i].'" value="'.$select_value[$j].'">'.$select[$j].'　';
                        }
                    }
                }
                break;
        }
        $insert_html .= '<a id="'.$insert_form_num[$i].'_errormsg" class="errormsg"></a>';
        $insert_html .= '</td>';
        $insert_html .= '</tr>';
    }
    //社員別金額入力欄
    if($filename == "PJTOUROKU_1")
    {
        $insert_html .= makeDetailCharge($post);
    }
    $insert_html .= '</table>';
    
    return $insert_html;
}

/************************************************************************************************************
function    makeformEdit_set()

引数          $post                                        登録情報

戻り値	$edit_html                             モーダルHTML
************************************************************************************************************/
function makeformEdit_set($post){
    
    //初期設定
    require_once ("f_Form.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $input_datalist_ini = parse_ini_file('./ini/input_datalist.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $edit_form_num = explode(',',$form_ini[$filename]['edit_form_num']);
    $edit_disabled = explode(',',$form_ini[$filename]['edit_disabled']);
    
    //変数
    $edit_html = "";
    
    //処理
    $edit_html .= '<table>';
    for($i = 0; $i < count($edit_form_num); $i++)
    {
        $max_length = $form_ini[$edit_form_num[$i]]['max_length'];
        $form_format = $form_ini[$edit_form_num[$i]]['form_format'];
        $isnotnull = $form_ini[$edit_form_num[$i]]['isnotnull'];
        $isJust = $form_ini[$edit_form_num[$i]]['isJust'];
        if($edit_disabled[$i] == "1")
        {
            $disabled = " disabled";
        }
        else
        {
            $disabled = "";
        }
        $onchange = 'inputcheck(this.id,'.$max_length.','.$form_format.','.$isnotnull.','.$isJust.');';    
        $edit_html .= '<tr>';
        $edit_html .= '<td>'.$form_ini[$edit_form_num[$i]]['item_name'].'</td>';
        $edit_html .= '<td>';
        $value = $post[$edit_form_num[$i]];
        
        //入力指示作成
        if($form_ini[$edit_form_num[$i]]['field_type'] == '1')
        {
            $placeholder = "";
            switch($form_format)
            {
                case '3':
                    $placeholder .= "半角英数字";
                    break;
                case '4':
                    $placeholder .= "半角数字";
                    break;
            }
            if($isJust == '1')
            {
                $placeholder .= $max_length."文字";
            }
            else
            {
                $placeholder .= $max_length."文字以内";
            }
        }
        else
        {
            $placeholder = "";
        }
        switch($form_ini[$edit_form_num[$i]]['field_type'])
        {
            case '1':
                $edit_html .= '<input type="text" placeholder="'.$placeholder.'" name="'.$edit_form_num[$i].'" id="'.$edit_form_num[$i].'" size="'.$form_ini[$edit_form_num[$i]]['form_size'].'" value="'.$value.'" class="form_text '.$disabled.'" onchange="'.$onchange.'" autocomplete="off">';
                break;
            case '2':
                $edit_html .= '<select name="'.$edit_form_num[$i].'" id="'.$edit_form_num[$i].'" class="form_text '.$disabled.'">';
                if($filename == 'PJTOUROKU_3')
                {
                    $edit_html .= '<option value="'.$value.'">'.$value.'期</option>';
                }
                $edit_html .= '</select>';
                break;
            case '3':
                $edit_html .= '<input type="date" name="'.$edit_form_num[$i].'" id="'.$edit_form_num[$i].'" value="'.$value.'" class="form_text '.$disabled.'" onchange="'.$onchange.'">';                
                break;
            case '4':
                $edit_html .= '<input type="month" name="'.$edit_form_num[$i].'" id="'.$edit_form_num[$i].'" value="'.$value.'" class="form_text '.$disabled.'" onchange="'.$onchange.'">';
                break;
            case '5':
                $select = explode(',',$input_datalist_ini[$edit_form_num[$i]]['edit']);
                $select_value = explode(',',$input_datalist_ini[$edit_form_num[$i]]['edit_value']);
                for($j = 0; $j < count($select); $j++)
                {
                    if((isset($post[$edit_form_num[$i]])) && $post[$edit_form_num[$i]] == $select_value[$j])
                    {
                        $edit_html .= '<input type="radio" name="'.$edit_form_num[$i].'" value="'.$select_value[$j].'" checked>'.$select[$j].'　';
                    }
                    else
                    {
                        if(!isset($post[$edit_form_num[$i]]) && $j == 0)
                        {
                            $edit_html .= '<input type="radio" name="'.$edit_form_num[$i].'" value="'.$select_value[$j].'" checked>'.$select[$j].'　';
                        }
                        else
                        {
                            $edit_html .= '<input type="radio" name="'.$edit_form_num[$i].'" value="'.$select_value[$j].'">'.$select[$j].'　';
                        }
                    }
                }
                break;
        }
        $edit_html .= '<a id="'.$edit_form_num[$i].'_errormsg" class="errormsg"></a>';
        $edit_html .= '</td>';
        $edit_html .= '</tr>';
    }
    //社員別金額入力欄
    if($filename == "PJTOUROKU_3")
    {
        $edit_html .= makeDetailCharge($post);
    }
    $edit_html .= '</table>';
    
    return $edit_html;
}

/************************************************************************************************************
function makeDetailCharge($post)

引数          $post                                                 登録情報

戻り値	$detail_charge_html                             社員別金額入力欄HTML
************************************************************************************************************/
function makeDetailCharge($post){
    
    //初期設定
    require_once ("f_DB.php");
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $detail_charge_html = '';
    $detail_charge_list = array();
    $syain_list = array();
    $counter = 0;
    
    //処理
    $detail_charge_html .= '<tr>';
    $detail_charge_html .= '<td>合計金額</td>';
    $detail_charge_html .= '<td><input type="text" size="30" name="goukei" id="goukei" class="form_text disabled" value="0"></td>';
    $detail_charge_html .= '</tr>';
    $detail_charge_html .= '<tr>';
    $detail_charge_html .= '<td colspan="2">';
        
    //表示対象社員検索
    $con = dbconect();
    $sql = "SELECT *FROM syaininfo WHERE LUSERNAME IS NOT NULL AND LUSERPASS IS NOT NULL ORDER BY STAFFID ASC;";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $syain_list[$counter]['4CODE'] = $result_row['4CODE'];
        $syain_list[$counter]['STAFFID'] = $result_row['STAFFID'];
        $syain_list[$counter]['STAFFNAME'] = $result_row['STAFFNAME'];
        $counter++;
    }
    
    $detail_charge_html .= '<input type="hidden" name="total_row" id="total_row" value="'.$result->num_rows.'">';
    //登録済み社員別金額取得
    if($filename == 'PJTOUROKU_3')
    {
        $sql = "SELECT *FROM projectditealinfo WHERE 5CODE = '".$_SESSION['edit_id']."';";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $detail_charge_list[$result_row['4CODE']] = $result_row['DETALECHARGE'];
        }
    }
    
    //社員リスト作成
    $detail_charge_html .= '<div class="list_scroll" style="max-height: 360px;">';
    $detail_charge_html .= '<table>';
    $detail_charge_html .= '<tr>';
    $detail_charge_html .= '<th>No</th>';
    $detail_charge_html .= '<th>社員番号</th>';
    $detail_charge_html .= '<th>社員名</th>';
    $detail_charge_html .= '<th>社員別金額</th>';
    $detail_charge_html .= '</tr>';
    for($i = 0; $i < count($syain_list); $i++)
    {
        $detail_charge_html .= '<tr>';
        $detail_charge_html .= '<td>'.($i + 1).'</td>';
        $detail_charge_html .= '<td>'.$syain_list[$i]['STAFFID'].'</td>';
        $detail_charge_html .= '<td>'.$syain_list[$i]['STAFFNAME'].'</td>';
        $detail_charge_html .= '<td>';
        if(isset($post['kingaku_'.$syain_list[$i]['4CODE']]))
        {
            $detail_charge_html .= '<input type="text" class="form_text" placeholder="半角数字7字以内" name="kingaku_'.$syain_list[$i]['4CODE'].'" id="kingaku_'.$i.'" value="'.$post['kingaku_'.$syain_list[$i]['4CODE']].'" onchange="kingaku_check(this.id); kingaku_goukei();">';
        }
        elseif(isset($detail_charge_list[$syain_list[$i]['4CODE']]))
        {
            $detail_charge_html .= '<input type="text" class="form_text" placeholder="半角数字7字以内" name="kingaku_'.$syain_list[$i]['4CODE'].'" id="kingaku_'.$i.'" value="'.$detail_charge_list[$syain_list[$i]['4CODE']].'" onchange="kingaku_check(this.id); kingaku_goukei();">';
        }
        else
        {
            $detail_charge_html .= '<input type="text" class="form_text" placeholder="半角数字7字以内" name="kingaku_'.$syain_list[$i]['4CODE'].'" id="kingaku_'.$i.'" value="" onchange="kingaku_check(this.id); kingaku_goukei();">';
        }
        $detail_charge_html .= '</td>';
        $detail_charge_html .= '</tr>';
    }
    $detail_charge_html .= '</table>';
    $detail_charge_html .= '</div>';
    $detail_charge_html .= '</td>';
    $detail_charge_html .= '</tr>';
    return $detail_charge_html;
}

/************************************************************************************************************
function    get_inputcheck_data($form_num)

引数          $form                                   

戻り値	$inputcheck_data                             入力チェックデータ
************************************************************************************************************/
function get_inputcheck_data($form){
    
    //初期設定
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $form_num = explode(',',$form_ini[$filename][$form]);
    
    //変数
    $inputcheck_data = array();
    $counter = 0;
    
    //処理
    for($i = 0; $i < count($form_num); $i++)
    {
        if($form_ini[$form_num[$i]]['field_type'] != "5")
        {
            $inputcheck_data[$counter]['name'] = $form_num[$i];
            $inputcheck_data[$counter]['max_length'] = $form_ini[$form_num[$i]]['max_length'];
            $inputcheck_data[$counter]['form_format'] = $form_ini[$form_num[$i]]['form_format'];
            $inputcheck_data[$counter]['isnotnull'] = $form_ini[$form_num[$i]]['isnotnull'];
            $inputcheck_data[$counter]['field_type'] = $form_ini[$form_num[$i]]['field_type'];
            $inputcheck_data[$counter]['isJust'] = $form_ini[$form_num[$i]]['isJust'];
            $counter++;
        }
    }
    
    return $inputcheck_data;
}

/************************************************************************************************************
function make_listUser_modal()

引数          $post                                        検索情報

戻り値	$modal_html                             モーダルHTML
************************************************************************************************************/
function make_listUser_modal($post){
    
    //初期設定
    $form_ini = parse_ini_file('./ini/form.ini', true);
	
    //定数
    $filename = $_SESSION['filename'];
    $orderby_columns = explode(',',$form_ini[$filename]['orderby_columns']);
    
    //変数
    $modal_html = "";
    $id = "";
    $staff_id = "";
    $staff_name = "";
    
    //処理
    if(isset($post['401']))
    {
        $id = $post['401'];
        $staff_id = $post['402'];
        $staff_name = $post['403'];
    }
    $modal_html .= '<div class="kensaku_title">検索条件</div>';
    $modal_html .= '<table>';
    $modal_html .= '<tr><td></td><td><input type="button" value="社員選択" onclick="popup_modal(4);"><input type="hidden" name="401" id="401" value="'.$id.'"></td></tr>';
    $modal_html .= '<tr><td>社員番号</td>';
    $modal_html .= '<td><input type="text" value="'.$staff_id.'" size="30" id="402" name="402" class="form_text disabled"></td></tr>';
    $modal_html .= '<tr><td>社員名</td>';
    $modal_html .= '<td><input type="text" value="'.$staff_name.'" size="60" id="403" name="403" class="form_text disabled"></td></tr>';  
    $modal_html .= '<tr>';
    $modal_html .= '<td>ソート条件1</td>';
    $modal_html .= '<td>';
    $modal_html .= '<select name="sort1" class="form_text">';
    $modal_html .= '<option value="">指定なし</option>';
    for($j = 0; $j < count($orderby_columns); $j++)
    {
        if((isset($post['sort1'])) && ($post['sort1'] == $orderby_columns[$j]))
        {
            $modal_html .= '<option value="'.$orderby_columns[$j].'" selected>';
        }
        else
        {
            $modal_html .= '<option value="'.$orderby_columns[$j].'">';
        }
        $modal_html .= $form_ini[$orderby_columns[$j]]['item_name'];
        $modal_html .= '</oprion>';
    }
    $modal_html .= '</select>';
    if((isset($post['radiobutton1'])) && ($post['radiobutton1'] == "DESC"))
    {
        $modal_html .= '<label><input name="radiobutton1" type="radio" value="ASC">昇順</label>　';
        $modal_html .= '<label><input name="radiobutton1" type="radio" value="DESC" checked>降順</label>　';
    }
    else
    {
        $modal_html .= '<label><input name="radiobutton1" type="radio" value="ASC" checked>昇順</label>　';
        $modal_html .= '<label><input name="radiobutton1" type="radio" value="DESC">降順</label>　';
    }
    $modal_html .= '</td>';
    $modal_html .= '</tr>';
    $modal_html .= '</table>';
    $modal_html .= '<input type="submit" name="serch" value="検索" class="modal_button">';
    $modal_html .= '<input type="button" value="キャンセル" class="modal_button" onclick="modal_close();">';
    return $modal_html;
}

/************************************************************************************************************
function makePROGRESSlist($post)

引数          $post                                        登録情報

戻り値	$progress_html                          入力欄HTML
************************************************************************************************************/
function makePROGRESSlist($post,$user){
    
    //定数
    $filename = $_SESSION['filename'];
    
    //変数
    $progress_html = "";
    $disabled = "";
    
    //月次処理済の月以前は入力不可とする
    $min = lastEndMonth();
    
    //処理
    $progress_html .= "<input type='hidden' id='mindate' value='".$min."'>";
    $progress_html .= "<table style='width: 100%;'>";
    $progress_html .= "<tr>";
    $progress_html .= "<td>作業日</td>";
    
    //TOP工数登録、編集時は作業日付、編集不可
    if($filename == "TOP_1" || $filename == "TOP_3")
    {
        $disabled = " disabled ";
    }
    
    if(isset($post['704']))
    {
        $progress_html .= "<td><input type='date' class='form_text ".$disabled." ' name='704' id='704' value='".$post['704']."' onchange='date_check();'></td>";
        $progress_html .= "</tr>";
    }
    else
    {
        $progress_html .= "<td><input type='date' class='form_text ".$disabled." ' name='704' id='704' value='".date_format(date_create('NOW'), "Y-m-d")."' onchange='date_check();'></td>";
        $progress_html .= "</tr>";
    }
    if(isset($post['401']))
    {
        $progress_html .= "<tr>";
        $progress_html .= "<td>社員番号<input type='hidden' name='401' value='".$post['401']."'></td>";
        $progress_html .= "<td><input type='text' size='30' name='402' class='form_text disabled' value='".$post['402']."'></td>";
        $progress_html .= "</tr>";
        $progress_html .= "<tr>";
        $progress_html .= "<td>社員名</td>";
        $progress_html .= "<td><input type='text' size='60' name='403' class='form_text disabled' value='".$post['403']."'></td>";
        $progress_html .= "</tr>";        
    }
    else
    {
        $progress_html .= "<tr>";
        $progress_html .= "<td>社員番号<input type='hidden' name='401' value='".$user['4CODE']."'></td>";
        $progress_html .= "<td><input type='text' size='30' name='402' class='form_text disabled' value='".$user['STAFFID']."'></td>";
        $progress_html .= "</tr>";
        $progress_html .= "<tr>";
        $progress_html .= "<td>社員名</td>";
        $progress_html .= "<td><input type='text' size='60' name='403' class='form_text disabled' value='".$user['STAFFNAME']."'></td>";
        $progress_html .= "</tr>";
    }

    
    //入力欄作成処理
    $progress_html .= "<tr><td colspan='2'>";
    $progress_html .= "<div class='list_scroll'>";
    $progress_html .= "<table>";
    
    //項目名作成
    $progress_html .= "<tr>";
    $progress_html .= "<th>No</th>";
    $progress_html .= "<th></th>";
    $progress_html .= "<th>プロジェクトコード</th>";
    $progress_html .= "<th>プロジェクト名</th>";
    $progress_html .= "<th></th>";
    $progress_html .= "<th>工程番号</th>";
    $progress_html .= "<th>工程名</th>";
    $progress_html .= "<th>定時時間</th>";
    $progress_html .= "<th>残業時間</th>";
    $progress_html .= "<th>編集</th>";
    $progress_html .= "</tr>";
    
    //入力欄作成
    for($i = 0; $i <= 9; $i++)
    {
        if(($i % 2) == 1)
        {
            $progress_html .= "<tr class='list_stripe'>";
        }
        else
        {
            $progress_html .= "<tr>";
        }
        $progress_html .= "<td>".($i + 1)."</td>";
        if(isset($post['601_'.$i]))
        {
            $progress_html .= "<td><button class='icon_button' type='button' onclick='popup_modal(\"6_$i\");'><i class='fas fa-chalkboard-teacher' title='PJ詳細選択'></i></button><input type='hidden' id='601_".$i."' name='601_".$i."' value='".$post['601_'.$i]."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 145px;' id='PJCODE_".$i."' name='PJCODE_".$i."' value='".$post['PJCODE_'.$i]."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 290px;' id='506_".$i."' name='506_".$i."' value='".$post['506_'.$i]."'></td>";
            $progress_html .= "<td><button class='icon_button' type='button' onclick='popup_modal(\"3_$i\");'><i class='fas fa-tasks' title='工程選択'></i></button><input type='hidden' id='301_".$i."' name='301_".$i."' value='".$post['301_'.$i]."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 100px;' id='302_".$i."' name='302_".$i."' value='".$post['302_'.$i]."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 290px;' id='303_".$i."' name='303_".$i."' value='".$post['303_'.$i]."'></td>";
            $progress_html .= "<td><input type='text' class='form_text' id='705_".$i."' name='705_".$i."' style='width: 90px;' value='".$post['705_'.$i]."' onchange='time_total();'></td>";
            $progress_html .= "<td><input type='text' class='form_text' id='706_".$i."' name='706_".$i."' style='width: 90px;' value='".$post['706_'.$i]."' onchange='time_total();'></td>";
        }
        else
        {
            $progress_html .= "<td><button class='icon_button' type='button' onclick='popup_modal(\"6_$i\");'><i class='fas fa-chalkboard-teacher' title='PJ詳細選択'></i></button><input type='hidden' id='601_".$i."' name='601_".$i."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 145px;' id='PJCODE_".$i."' name='PJCODE_".$i."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 290px;' id='506_".$i."' name='506_".$i."'></td>";
            $progress_html .= "<td><button class='icon_button' type='button' onclick='popup_modal(\"3_$i\");'><i class='fas fa-tasks' title='工程選択'></i></button><input type='hidden' id='301_".$i."' name='301_".$i."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 100px;' id='302_".$i."' name='302_".$i."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 290px;' id='303_".$i."' name='303_".$i."'></td>";
            $progress_html .= "<td><input type='text' class='form_text' id='705_".$i."' name='705_".$i."' style='width: 90px;' onchange='time_total();'></td>";
            $progress_html .= "<td><input type='text' class='form_text' id='706_".$i."' name='706_".$i."' style='width: 90px;' value='0' onchange='time_total();'></td>";            
        }
        $progress_html .= "<td>";
        $progress_html .= "<button class='icon_button' type='button' onclick='copyrow(".$i.");'><i class='far fa-copy' title='行をコピー'></i></button>";
        $progress_html .= "<button class='icon_button' type='button'><i class='fas fa-paint-brush' title='貼り付け' onclick='pasterow(".$i.");'></i></button>";
        $progress_html .= "<button class='icon_button' type='button'><i class='far fa-minus-square'  title='削除' onclick='deleterow(".$i.");'></i></button>";
        $progress_html .= "</td>";
        $progress_html .= "</tr>";
    }
    
    $progress_html .= "</table>";
    $progress_html .= "</td></tr>";
    $progress_html .= "</table>";
    
    return $progress_html;
}

/************************************************************************************************************
function makeEndMonth()

引数1		なし

戻り値	$listhtml          月次処理テーブルHTML
************************************************************************************************************/
function makeEndMonth(){
    
    //初期設定
    $system_ini = parse_ini_file('./ini/system.ini', true);
    require_once ("f_Form.php");
    require_once ("f_DB.php");
    require_once ("f_SQL.php");

    //定数
    $nowyr = date_format(date_create('NOW'), "Y");
    $nowmn = date_format(date_create('NOW'), "n");
    $nowpd = getperiod($nowmn,$nowyr);
    $before = $system_ini['endmonth']['before_period'];
    $start = $nowpd - $before + 1 ;
    
    //変数
    $sql = "";
    $judge = false;
    $endmonth = array();
    $listhtml = "";
    
    //処理
    $con = dbconect();																									// db接続関数実行
    $sql = "SELECT * FROM endmonthinfo;";
    $result = $con->query($sql);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        if(isset($endmonth[$result_row['PERIOD']]))
        {
            $endmonth[$result_row['PERIOD']][count($endmonth[$result_row['PERIOD']])] = $result_row['MONTH'];
        }
        else
        {
            $endmonth[$result_row['PERIOD']][0] = $result_row['MONTH'];
        }
    }
    	$listhtml .= "<table><tr><td>";
	$listhtml .= "<table><td width='20' bgcolor='#f4a460'></td><td>･･･月次済</td></tr></table>";
	$listhtml .= "</td></tr><tr><td><div class='list_scroll'><table class ='list'><thead><tr>";
	$listhtml .= "<th><a class ='head'>期</a></th>";
	$listhtml .= "<th colspan='12'><a class ='head'>月</a></th></tr></thead>";
	$listhtml .= "<tbody>";	
	
	for($i = 0; $i < $before; $i++)
	{
            //期を作成
            $listhtml .= "<tr><td style='background-color: rgb(189,215,238);'>".($start+$i)."</td>";

            //12ヶ月表作成
            for($j = 0; $j < 12; $j++)
            {
                if($j < 7)
                {
                    $color = "";
                    if(!empty($endmonth[($start+$i)]))
                    {
                        for($g = 0; $g < count($endmonth[($start+$i)]); $g++)
                        {
                            $month = $endmonth[($start+$i)][$g];
                            if($month == ($j + 6))
                            {
                                $color = "#f4a460";
                                break;
                            }
                        }
                    }
                    $listhtml .= "<td width='25' bgcolor='".$color."'>".($j + 6)."</td>";
                }
                else
                {
                    if(!empty($endmonth[($start+$i)]))
                    {
                        $color = "";
                        for($g = 0; $g < count($endmonth[($start+$i)]); $g++)
                        {
                            $month = $endmonth[($start+$i)][$g];
                            if($month == ($j - 6))
                            {
                                $color = "#f4a460";
                                break;
                            }
                        }
                    }
                    $listhtml .= "<td width='25' bgcolor='".$color."'>".($j - 6)."</td>";
                }
            }
            $listhtml .= "</tr>";
        }
        $listhtml .= "</tbody></table></div></td></tr></table>";
 
        return ($listhtml);
}

/************************************************************************************************************
年→期変換処理(プロジェクト管理システム)
function getperiod($month,$year)

引数1		$month						月
引数2		$year 						年

戻り値		$form						モーダルに表示リストhtml
************************************************************************************************************/
function getperiod($month,$year){
    
    //初期設定
    require_once("f_DB.php");																							// DB関数呼び出し準備
    require_once("f_File.php");																							// DB関数呼び出し準備
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $system_ini = parse_ini_file('./ini/system.ini', true);

    //定数
    $startyear = $system_ini['period']['startyear'];
    $startmonth = $system_ini['period']['startmonth'];
	
    //変数
    $period = 0 ;
	
    //処理
    $period = $year - $startyear + 1;
    if($startmonth > $month)
    {
        $period = $period - 1 ;
    }

    return $period;
}

/****************************************************************************************
function rireki_change()


引数	なし

戻り値	なし
****************************************************************************************/
function rireki_change(){
    
    //初期設定
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $check_path = $system_ini[$filename]['file_path'];																				// 送信確認ファイル
    $date = date_format(date_create('NOW'), "Y-m-d");
    if($filename == 'getuzi_5')
    {
        $period = $_SESSION['getuzi']['period'];
        $month = $_SESSION['getuzi']['month'];
        $date = $period."期 ".$month."月 ( 実行日： ".$date." )";
    }
    if($filename == 'nenzi_5')
    {
        $period = $_SESSION['nenzi']['period'];
        $date = $period."期 ( 実行日 ：".$date." )";
    }
    
    //変数
    $buffer = "";
    
    //CSVファイルの追記処理
    if(!file_exists($check_path))
    {
        $fp = fopen($check_path, 'ab');																							// 送信確認ファイルを追記書き込みで開く
        fclose($fp);				
    }
    $fp = fopen($check_path, 'a+b');
    
    //ファイルが開けたか
    if ($fp)
    {
        //ファイルのロックができたか
        if (flock($fp, LOCK_EX))																								// ロック
        {
            ftruncate( $fp,0);
            //ログの書き込みを失敗したか
            if (fwrite($fp ,$date) === FALSE)																		// check_mail追記書き込み
            {
                //書き込み失敗時の処理
            }
            flock($fp, LOCK_UN);																								// ロックの解除
        }
        else
        {
            //ロック失敗時の処理
        }    
    }
    fclose($fp);
}

/****************************************************************************************
function getuzi_rireki()


引数	なし

戻り値	なし
****************************************************************************************/
function getuzi_rireki(){
    
    //初期設定
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $check_path = $system_ini[$filename]['file_path'];
    $date = date_format(date_create('NOW'), "Y-m-d");
    
    //変数
    $buffer = "";
    
    //CSVファイルの追記処理
    if(!file_exists($check_path))
    {
        $fp = fopen($check_path, 'ab');																							// 送信確認ファイルを追記書き込みで開く
        fclose($fp);				
    }
    $fp = fopen($check_path, 'a+b');
    
    // ファイルが開けたか
    if ($fp)
    {
        // ファイルのロックができたか //
        if (flock($fp, LOCK_EX))																								// ロック
        {
            $buffer = fgets($fp);
            flock($fp, LOCK_UN);																								// ロックの解除
        }
        else
        {
            // ロック失敗時の処理
        }
    }
    fclose($fp);																												// ファイルを閉じる
    return($buffer);
}

/****************************************************************************************
function nenzi_rireki()


引数	なし

戻り値	なし
****************************************************************************************/
function nenzi_rireki(){
    
    //初期設定
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $check_path = $system_ini[$filename]['file_path'];
    $date = date_format(date_create('NOW'), "Y-m-d");
    
    //変数
    $buffer = "";
    
    //CSVファイルの追記処理
    if(!file_exists($check_path))
    {
        $fp = fopen($check_path, 'ab');																							// 送信確認ファイルを追記書き込みで開く
        fclose($fp);				
    }
    $fp = fopen($check_path, 'a+b');
    
    // ファイルが開けたか
    if ($fp)
    {
        // ファイルのロックができたか //
        if (flock($fp, LOCK_EX))																								// ロック
        {
            $buffer = fgets($fp);
            flock($fp, LOCK_UN);																								// ロックの解除
        }
        else
        {
            // ロック失敗時の処理
        }
    }
    fclose($fp);																												// ファイルを閉じる
    return($buffer);
}

/****************************************************************************************
function delete_rireki()

引数	なし

戻り値	なし
****************************************************************************************/
function delete_rireki(){
    
    //初期設定
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $check_path = $system_ini[$filename]['file_path'];																				// 送信確認ファイル
    $date = date_format(date_create('NOW'), "Y-m-d");
    
    //変数
    $buffer = "";
    
    //CSVファイルの追記処理
    if(!file_exists($check_path))
    {
        $fp = fopen($check_path, 'ab');																							// 送信確認ファイルを追記書き込みで開く
        fclose($fp);				
    }
    $fp = fopen($check_path, 'a+b');
    
    //ファイルが開けたか
    if ($fp)
    {
        // ファイルのロックができたか //
        if (flock($fp, LOCK_EX))																								// ロック
        {
            $buffer = fgets($fp);
            flock($fp, LOCK_UN);																								// ロックの解除
        }
        else
        {
            // ロック失敗時の処理
        }
    }
    fclose($fp);																												// ファイルを閉じる
    return($buffer);
}

/************************************************************************************************************
function period_pulldown_set($post)

引数	$post

戻り値	なし
************************************************************************************************************/
function period_pulldown_set($post){
    
    //初期設定
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $year = date_format(date_create('NOW'), "Y");
    $month = date_format(date_create('NOW'), "n");
    $startyear = $system_ini['period']['startyear'];
    $startmonth = $system_ini['period']['startmonth'];
    $period = 0;
    
    //変数
    $pulldown_html = "";
    
    //処理
    $period = $year - $startyear;
    if($startmonth <= $month)
    {
        $period = $period + 1;
    }
    $pulldown_html .= "<select name='period' id='period' class='form_text'>";
    for($i = 9; $i <= $period; $i++)
    {
        if($post['period'] == $i)
        {
            $pulldown_html.='<option value ="'.$i.'" selected>'.$i.'期</option>';
        }
        else
        {
            $pulldown_html.='<option value ="'.$i.'">'.$i.'期</option>';
        }
    }
    $pulldown_html .= "</select>";
    
    return $pulldown_html;
}

/************************************************************************************************************
function month_pulldown_set($post)

引数	$post

戻り値	なし
************************************************************************************************************/
function month_pulldown_set($post){
        
    //初期設定
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //変数
    $pulldown_html = "";
    
    //処理
    $pulldown_html .= "<select name='month' id='month' class='form_text'>";
    for($i = 1; $i <= 12; $i++)
    {
        if($post['month'] == $i)
        {
            $pulldown_html .= "<option value='".$i."' selected>".$i."月</option>";
        }
        else
        {
            $pulldown_html .= "<option value='".$i."'>".$i."月</option>";
        }
    }
    $pulldown_html .= "</select>";
    return $pulldown_html;
}
?>