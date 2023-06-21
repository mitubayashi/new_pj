<?php
/************************************************************************************************************
function makeModalHtml()

����          $post                                        �������

�߂�l	$modal_html                             ���[�_��HTML
************************************************************************************************************/
function makeModalHtml($post){
    
    //�����ݒ�
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $input_datalist_ini = parse_ini_file('./ini/input_datalist.ini', true);
    $system_ini = parse_ini_file('./ini/system.ini', true);
    //�萔
    $filename = $_SESSION['filename'];
    $sech_form_num = explode(',',$form_ini[$filename]['sech_form_num']);
    $orderby_columns = explode(',',$form_ini[$filename]['orderby_columns']);
    
    //�ϐ�
    $modal_html = "";
    
    //����
    $con = dbconect();
    $modal_html .= '<div class="kensaku_title">��������</div>';
    $modal_html .= '<table>';
    
    //���������ǋL
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
                            $modal_html .= '<option value="'.$j.'" '.$selected.'>'.$j.'��</option>';
                        }
                    }
                    else
                    {
                        $select = explode(',',$input_datalist_ini[$sech_form_num[$i]]['sech']);
                        $select_value = explode(',',$input_datalist_ini[$sech_form_num[$i]]['sech_value']);
                        $modal_html .= '<option value="">�w��Ȃ�</option>';
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
                        $modal_html .= '<input type="button" class="icon_button" value="����" onclick="input_startdate('.$sech_form_num[$i].');" style="width: 35px; margin-left: 2px;">';
                    }
                    $modal_html .= '�@~�@';
                    $modal_html .= '<input type="date" name="'.$sech_form_num[$i].'_enddate" id="'.$sech_form_num[$i].'_enddate" class="form_text" value="'.$enddate.'">';
                    break;
                case '5':
                    $select = explode(',',$input_datalist_ini[$sech_form_num[$i]]['sech']);
                    $select_value = explode(',',$input_datalist_ini[$sech_form_num[$i]]['sech_value']);
                    if($filename != 'pjend_5')
                    {
                        $select = explode(',',"�w��Ȃ�,���I��,�I���ς�");
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
                            $modal_html .= '<input type="radio" name="'.$sech_form_num[$i].'" value="'.$select_value[$j].'" checked onchange="'.$onchange.'">'.$select[$j].'�@';
                        }
                        else
                        {
                            if(!isset($post[$sech_form_num[$i]]) && $j == 0)
                            {
                                $modal_html .= '<input type="radio" name="'.$sech_form_num[$i].'" value="'.$select_value[$j].'" checked onchange="'.$onchange.'">'.$select[$j].'�@';
                            }
                            else
                            {
                                $modal_html .= '<input type="radio" name="'.$sech_form_num[$i].'" value="'.$select_value[$j].'" onchange="'.$onchange.'">'.$select[$j].'�@';
                            }
                        }
                    }
                    break;
            }
            $modal_html .= '</td>';
            $modal_html .= '</tr>';
        }
    }
    //�\�[�g�����ǋL
    if($form_ini[$filename]['orderby_columns'] != "")
    {
        for($i = 1; $i <= 2; $i++)
        {
            $modal_html .= '<tr>';
            $modal_html .= '<td>�\�[�g����'.$i.'</td>';
            $modal_html .= '<td>';
            $modal_html .= '<select name="sort'.$i.'" class="form_text">';
            $modal_html .= '<option value="">�w��Ȃ�</option>';
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
                $modal_html .= '<label><input name="radiobutton'.$i.'" type="radio" value="ASC">����</label>�@';
                $modal_html .= '<label><input name="radiobutton'.$i.'" type="radio" value="DESC" checked>�~��</label>�@';
            }
            else
            {
                $modal_html .= '<label><input name="radiobutton'.$i.'" type="radio" value="ASC" checked>����</label>�@';
                $modal_html .= '<label><input name="radiobutton'.$i.'" type="radio" value="DESC">�~��</label>�@';
            }
            $modal_html .= '</td>';
            $modal_html .= '</tr>';
        }
    }
    $modal_html .= '</table>';
    $modal_html .= '<input type="submit" name="serch" value="����" class="modal_button">';
    $modal_html .= '<input type="button" value="�L�����Z��" class="modal_button" onclick="modal_close();">';
    return $modal_html;
}

/************************************************************************************************************
function    makeformInsert_set()

����          $post                                        �o�^���

�߂�l	$insert_html                             ���[�_��HTML
************************************************************************************************************/
function makeformInsert_set($post){
    
    //�����ݒ�
    require_once ("f_Form.php");
    require_once ("f_SQL.php");
    require_once ("f_DB.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $input_datalist_ini = parse_ini_file('./ini/input_datalist.ini', true);
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
    $insert_form_num = explode(',',$form_ini[$filename]['insert_form_num']);
    
    //�ϐ�
    $insert_html = "";
    
    //����
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
        
        //���͎w���쐬
        if($form_ini[$insert_form_num[$i]]['field_type'] == '1')
        {
            $placeholder = "";
            switch($form_format)
            {
                case '3':
                    $placeholder .= "���p�p����";
                    break;
                case '4':
                    $placeholder .= "���p����";
                    break;
            }
            if($isJust == '1')
            {
                $placeholder .= $max_length."����";
            }
            else
            {
                $placeholder .= $max_length."�����ȓ�";
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
                                $insert_html .= '<option value="'.$j.'" selected>'.$j.'��</option>';
                            }
                            elseif($value == "" && $period == $j)
                            {
                                $insert_html .= '<option value="'.$j.'" selected>'.$j.'��</option>';
                            }
                            else
                            {
                                $insert_html .= '<option value="'.$j.'">'.$j.'��</option>';
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
                        $insert_html .= '<input type="radio" name="'.$insert_form_num[$i].'" value="'.$select_value[$j].'" checked>'.$select[$j].'�@';
                    }
                    else
                    {
                        if(!isset($post[$insert_form_num[$i]]) && $j == 0)
                        {
                            $insert_html .= '<input type="radio" name="'.$insert_form_num[$i].'" value="'.$select_value[$j].'" checked>'.$select[$j].'�@';
                        }
                        else
                        {
                            $insert_html .= '<input type="radio" name="'.$insert_form_num[$i].'" value="'.$select_value[$j].'">'.$select[$j].'�@';
                        }
                    }
                }
                break;
        }
        $insert_html .= '<a id="'.$insert_form_num[$i].'_errormsg" class="errormsg"></a>';
        $insert_html .= '</td>';
        $insert_html .= '</tr>';
    }
    //�Ј��ʋ��z���͗�
    if($filename == "PJTOUROKU_1")
    {
        $insert_html .= makeDetailCharge($post);
    }
    $insert_html .= '</table>';
    
    return $insert_html;
}

/************************************************************************************************************
function    makeformEdit_set()

����          $post                                        �o�^���

�߂�l	$edit_html                             ���[�_��HTML
************************************************************************************************************/
function makeformEdit_set($post){
    
    //�����ݒ�
    require_once ("f_Form.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $input_datalist_ini = parse_ini_file('./ini/input_datalist.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
    $edit_form_num = explode(',',$form_ini[$filename]['edit_form_num']);
    $edit_disabled = explode(',',$form_ini[$filename]['edit_disabled']);
    
    //�ϐ�
    $edit_html = "";
    
    //����
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
        
        //���͎w���쐬
        if($form_ini[$edit_form_num[$i]]['field_type'] == '1')
        {
            $placeholder = "";
            switch($form_format)
            {
                case '3':
                    $placeholder .= "���p�p����";
                    break;
                case '4':
                    $placeholder .= "���p����";
                    break;
            }
            if($isJust == '1')
            {
                $placeholder .= $max_length."����";
            }
            else
            {
                $placeholder .= $max_length."�����ȓ�";
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
                    $edit_html .= '<option value="'.$value.'">'.$value.'��</option>';
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
                        $edit_html .= '<input type="radio" name="'.$edit_form_num[$i].'" value="'.$select_value[$j].'" checked>'.$select[$j].'�@';
                    }
                    else
                    {
                        if(!isset($post[$edit_form_num[$i]]) && $j == 0)
                        {
                            $edit_html .= '<input type="radio" name="'.$edit_form_num[$i].'" value="'.$select_value[$j].'" checked>'.$select[$j].'�@';
                        }
                        else
                        {
                            $edit_html .= '<input type="radio" name="'.$edit_form_num[$i].'" value="'.$select_value[$j].'">'.$select[$j].'�@';
                        }
                    }
                }
                break;
        }
        $edit_html .= '<a id="'.$edit_form_num[$i].'_errormsg" class="errormsg"></a>';
        $edit_html .= '</td>';
        $edit_html .= '</tr>';
    }
    //�Ј��ʋ��z���͗�
    if($filename == "PJTOUROKU_3")
    {
        $edit_html .= makeDetailCharge($post);
    }
    $edit_html .= '</table>';
    
    return $edit_html;
}

/************************************************************************************************************
function makeDetailCharge($post)

����          $post                                                 �o�^���

�߂�l	$detail_charge_html                             �Ј��ʋ��z���͗�HTML
************************************************************************************************************/
function makeDetailCharge($post){
    
    //�����ݒ�
    require_once ("f_DB.php");
    
    //�萔
    $filename = $_SESSION['filename'];
    
    //�ϐ�
    $detail_charge_html = '';
    $detail_charge_list = array();
    $syain_list = array();
    $counter = 0;
    
    //����
    $detail_charge_html .= '<tr>';
    $detail_charge_html .= '<td>���v���z</td>';
    $detail_charge_html .= '<td><input type="text" size="30" name="goukei" id="goukei" class="form_text disabled" value="0"></td>';
    $detail_charge_html .= '</tr>';
    $detail_charge_html .= '<tr>';
    $detail_charge_html .= '<td colspan="2">';
        
    //�\���ΏێЈ�����
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
    //�o�^�ςݎЈ��ʋ��z�擾
    if($filename == 'PJTOUROKU_3')
    {
        $sql = "SELECT *FROM projectditealinfo WHERE 5CODE = '".$_SESSION['edit_id']."';";
        $result = $con->query($sql);
        while($result_row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $detail_charge_list[$result_row['4CODE']] = $result_row['DETALECHARGE'];
        }
    }
    
    //�Ј����X�g�쐬
    $detail_charge_html .= '<div class="list_scroll" style="max-height: 360px;">';
    $detail_charge_html .= '<table>';
    $detail_charge_html .= '<tr>';
    $detail_charge_html .= '<th>No</th>';
    $detail_charge_html .= '<th>�Ј��ԍ�</th>';
    $detail_charge_html .= '<th>�Ј���</th>';
    $detail_charge_html .= '<th>�Ј��ʋ��z</th>';
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
            $detail_charge_html .= '<input type="text" class="form_text" placeholder="���p����7���ȓ�" name="kingaku_'.$syain_list[$i]['4CODE'].'" id="kingaku_'.$i.'" value="'.$post['kingaku_'.$syain_list[$i]['4CODE']].'" onchange="kingaku_check(this.id); kingaku_goukei();">';
        }
        elseif(isset($detail_charge_list[$syain_list[$i]['4CODE']]))
        {
            $detail_charge_html .= '<input type="text" class="form_text" placeholder="���p����7���ȓ�" name="kingaku_'.$syain_list[$i]['4CODE'].'" id="kingaku_'.$i.'" value="'.$detail_charge_list[$syain_list[$i]['4CODE']].'" onchange="kingaku_check(this.id); kingaku_goukei();">';
        }
        else
        {
            $detail_charge_html .= '<input type="text" class="form_text" placeholder="���p����7���ȓ�" name="kingaku_'.$syain_list[$i]['4CODE'].'" id="kingaku_'.$i.'" value="" onchange="kingaku_check(this.id); kingaku_goukei();">';
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

����          $form                                   

�߂�l	$inputcheck_data                             ���̓`�F�b�N�f�[�^
************************************************************************************************************/
function get_inputcheck_data($form){
    
    //�����ݒ�
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
    $form_num = explode(',',$form_ini[$filename][$form]);
    
    //�ϐ�
    $inputcheck_data = array();
    $counter = 0;
    
    //����
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

����          $post                                        �������

�߂�l	$modal_html                             ���[�_��HTML
************************************************************************************************************/
function make_listUser_modal($post){
    
    //�����ݒ�
    $form_ini = parse_ini_file('./ini/form.ini', true);
	
    //�萔
    $filename = $_SESSION['filename'];
    $orderby_columns = explode(',',$form_ini[$filename]['orderby_columns']);
    
    //�ϐ�
    $modal_html = "";
    $id = "";
    $staff_id = "";
    $staff_name = "";
    
    //����
    if(isset($post['401']))
    {
        $id = $post['401'];
        $staff_id = $post['402'];
        $staff_name = $post['403'];
    }
    $modal_html .= '<div class="kensaku_title">��������</div>';
    $modal_html .= '<table>';
    $modal_html .= '<tr><td></td><td><input type="button" value="�Ј��I��" onclick="popup_modal(4);"><input type="hidden" name="401" id="401" value="'.$id.'"></td></tr>';
    $modal_html .= '<tr><td>�Ј��ԍ�</td>';
    $modal_html .= '<td><input type="text" value="'.$staff_id.'" size="30" id="402" name="402" class="form_text disabled"></td></tr>';
    $modal_html .= '<tr><td>�Ј���</td>';
    $modal_html .= '<td><input type="text" value="'.$staff_name.'" size="60" id="403" name="403" class="form_text disabled"></td></tr>';  
    $modal_html .= '<tr>';
    $modal_html .= '<td>�\�[�g����1</td>';
    $modal_html .= '<td>';
    $modal_html .= '<select name="sort1" class="form_text">';
    $modal_html .= '<option value="">�w��Ȃ�</option>';
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
        $modal_html .= '<label><input name="radiobutton1" type="radio" value="ASC">����</label>�@';
        $modal_html .= '<label><input name="radiobutton1" type="radio" value="DESC" checked>�~��</label>�@';
    }
    else
    {
        $modal_html .= '<label><input name="radiobutton1" type="radio" value="ASC" checked>����</label>�@';
        $modal_html .= '<label><input name="radiobutton1" type="radio" value="DESC">�~��</label>�@';
    }
    $modal_html .= '</td>';
    $modal_html .= '</tr>';
    $modal_html .= '</table>';
    $modal_html .= '<input type="submit" name="serch" value="����" class="modal_button">';
    $modal_html .= '<input type="button" value="�L�����Z��" class="modal_button" onclick="modal_close();">';
    return $modal_html;
}

/************************************************************************************************************
function makePROGRESSlist($post)

����          $post                                        �o�^���

�߂�l	$progress_html                          ���͗�HTML
************************************************************************************************************/
function makePROGRESSlist($post,$user){
    
    //�萔
    $filename = $_SESSION['filename'];
    
    //�ϐ�
    $progress_html = "";
    $disabled = "";
    
    //���������ς̌��ȑO�͓��͕s�Ƃ���
    $min = lastEndMonth();
    
    //����
    $progress_html .= "<input type='hidden' id='mindate' value='".$min."'>";
    $progress_html .= "<table style='width: 100%;'>";
    $progress_html .= "<tr>";
    $progress_html .= "<td>��Ɠ�</td>";
    
    //TOP�H���o�^�A�ҏW���͍�Ɠ��t�A�ҏW�s��
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
        $progress_html .= "<td>�Ј��ԍ�<input type='hidden' name='401' value='".$post['401']."'></td>";
        $progress_html .= "<td><input type='text' size='30' name='402' class='form_text disabled' value='".$post['402']."'></td>";
        $progress_html .= "</tr>";
        $progress_html .= "<tr>";
        $progress_html .= "<td>�Ј���</td>";
        $progress_html .= "<td><input type='text' size='60' name='403' class='form_text disabled' value='".$post['403']."'></td>";
        $progress_html .= "</tr>";        
    }
    else
    {
        $progress_html .= "<tr>";
        $progress_html .= "<td>�Ј��ԍ�<input type='hidden' name='401' value='".$user['4CODE']."'></td>";
        $progress_html .= "<td><input type='text' size='30' name='402' class='form_text disabled' value='".$user['STAFFID']."'></td>";
        $progress_html .= "</tr>";
        $progress_html .= "<tr>";
        $progress_html .= "<td>�Ј���</td>";
        $progress_html .= "<td><input type='text' size='60' name='403' class='form_text disabled' value='".$user['STAFFNAME']."'></td>";
        $progress_html .= "</tr>";
    }

    
    //���͗��쐬����
    $progress_html .= "<tr><td colspan='2'>";
    $progress_html .= "<div class='list_scroll'>";
    $progress_html .= "<table>";
    
    //���ږ��쐬
    $progress_html .= "<tr>";
    $progress_html .= "<th>No</th>";
    $progress_html .= "<th></th>";
    $progress_html .= "<th>�v���W�F�N�g�R�[�h</th>";
    $progress_html .= "<th>�v���W�F�N�g��</th>";
    $progress_html .= "<th></th>";
    $progress_html .= "<th>�H���ԍ�</th>";
    $progress_html .= "<th>�H����</th>";
    $progress_html .= "<th>�莞����</th>";
    $progress_html .= "<th>�c�Ǝ���</th>";
    $progress_html .= "<th>�ҏW</th>";
    $progress_html .= "</tr>";
    
    //���͗��쐬
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
            $progress_html .= "<td><button class='icon_button' type='button' onclick='popup_modal(\"6_$i\");'><i class='fas fa-chalkboard-teacher' title='PJ�ڍבI��'></i></button><input type='hidden' id='601_".$i."' name='601_".$i."' value='".$post['601_'.$i]."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 145px;' id='PJCODE_".$i."' name='PJCODE_".$i."' value='".$post['PJCODE_'.$i]."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 290px;' id='506_".$i."' name='506_".$i."' value='".$post['506_'.$i]."'></td>";
            $progress_html .= "<td><button class='icon_button' type='button' onclick='popup_modal(\"3_$i\");'><i class='fas fa-tasks' title='�H���I��'></i></button><input type='hidden' id='301_".$i."' name='301_".$i."' value='".$post['301_'.$i]."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 100px;' id='302_".$i."' name='302_".$i."' value='".$post['302_'.$i]."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 290px;' id='303_".$i."' name='303_".$i."' value='".$post['303_'.$i]."'></td>";
            $progress_html .= "<td><input type='text' class='form_text' id='705_".$i."' name='705_".$i."' style='width: 90px;' value='".$post['705_'.$i]."' onchange='time_total();'></td>";
            $progress_html .= "<td><input type='text' class='form_text' id='706_".$i."' name='706_".$i."' style='width: 90px;' value='".$post['706_'.$i]."' onchange='time_total();'></td>";
        }
        else
        {
            $progress_html .= "<td><button class='icon_button' type='button' onclick='popup_modal(\"6_$i\");'><i class='fas fa-chalkboard-teacher' title='PJ�ڍבI��'></i></button><input type='hidden' id='601_".$i."' name='601_".$i."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 145px;' id='PJCODE_".$i."' name='PJCODE_".$i."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 290px;' id='506_".$i."' name='506_".$i."'></td>";
            $progress_html .= "<td><button class='icon_button' type='button' onclick='popup_modal(\"3_$i\");'><i class='fas fa-tasks' title='�H���I��'></i></button><input type='hidden' id='301_".$i."' name='301_".$i."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 100px;' id='302_".$i."' name='302_".$i."'></td>";
            $progress_html .= "<td><input type='text' class='form_text disabled' style='width: 290px;' id='303_".$i."' name='303_".$i."'></td>";
            $progress_html .= "<td><input type='text' class='form_text' id='705_".$i."' name='705_".$i."' style='width: 90px;' onchange='time_total();'></td>";
            $progress_html .= "<td><input type='text' class='form_text' id='706_".$i."' name='706_".$i."' style='width: 90px;' value='0' onchange='time_total();'></td>";            
        }
        $progress_html .= "<td>";
        $progress_html .= "<button class='icon_button' type='button' onclick='copyrow(".$i.");'><i class='far fa-copy' title='�s���R�s�['></i></button>";
        $progress_html .= "<button class='icon_button' type='button'><i class='fas fa-paint-brush' title='�\��t��' onclick='pasterow(".$i.");'></i></button>";
        $progress_html .= "<button class='icon_button' type='button'><i class='far fa-minus-square'  title='�폜' onclick='deleterow(".$i.");'></i></button>";
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

����1		�Ȃ�

�߂�l	$listhtml          ���������e�[�u��HTML
************************************************************************************************************/
function makeEndMonth(){
    
    //�����ݒ�
    $system_ini = parse_ini_file('./ini/system.ini', true);
    require_once ("f_Form.php");
    require_once ("f_DB.php");
    require_once ("f_SQL.php");

    //�萔
    $nowyr = date_format(date_create('NOW'), "Y");
    $nowmn = date_format(date_create('NOW'), "n");
    $nowpd = getperiod($nowmn,$nowyr);
    $before = $system_ini['endmonth']['before_period'];
    $start = $nowpd - $before + 1 ;
    
    //�ϐ�
    $sql = "";
    $judge = false;
    $endmonth = array();
    $listhtml = "";
    
    //����
    $con = dbconect();																									// db�ڑ��֐����s
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
	$listhtml .= "<table><td width='20' bgcolor='#f4a460'></td><td>���������</td></tr></table>";
	$listhtml .= "</td></tr><tr><td><div class='list_scroll'><table class ='list'><thead><tr>";
	$listhtml .= "<th><a class ='head'>��</a></th>";
	$listhtml .= "<th colspan='12'><a class ='head'>��</a></th></tr></thead>";
	$listhtml .= "<tbody>";	
	
	for($i = 0; $i < $before; $i++)
	{
            //�����쐬
            $listhtml .= "<tr><td style='background-color: rgb(189,215,238);'>".($start+$i)."</td>";

            //12�����\�쐬
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
�N�����ϊ�����(�v���W�F�N�g�Ǘ��V�X�e��)
function getperiod($month,$year)

����1		$month						��
����2		$year 						�N

�߂�l		$form						���[�_���ɕ\�����X�ghtml
************************************************************************************************************/
function getperiod($month,$year){
    
    //�����ݒ�
    require_once("f_DB.php");																							// DB�֐��Ăяo������
    require_once("f_File.php");																							// DB�֐��Ăяo������
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $system_ini = parse_ini_file('./ini/system.ini', true);

    //�萔
    $startyear = $system_ini['period']['startyear'];
    $startmonth = $system_ini['period']['startmonth'];
	
    //�ϐ�
    $period = 0 ;
	
    //����
    $period = $year - $startyear + 1;
    if($startmonth > $month)
    {
        $period = $period - 1 ;
    }

    return $period;
}

/****************************************************************************************
function rireki_change()


����	�Ȃ�

�߂�l	�Ȃ�
****************************************************************************************/
function rireki_change(){
    
    //�����ݒ�
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
    $check_path = $system_ini[$filename]['file_path'];																				// ���M�m�F�t�@�C��
    $date = date_format(date_create('NOW'), "Y-m-d");
    if($filename == 'getuzi_5')
    {
        $period = $_SESSION['getuzi']['period'];
        $month = $_SESSION['getuzi']['month'];
        $date = $period."�� ".$month."�� ( ���s���F ".$date." )";
    }
    if($filename == 'nenzi_5')
    {
        $period = $_SESSION['nenzi']['period'];
        $date = $period."�� ( ���s�� �F".$date." )";
    }
    
    //�ϐ�
    $buffer = "";
    
    //CSV�t�@�C���̒ǋL����
    if(!file_exists($check_path))
    {
        $fp = fopen($check_path, 'ab');																							// ���M�m�F�t�@�C����ǋL�������݂ŊJ��
        fclose($fp);				
    }
    $fp = fopen($check_path, 'a+b');
    
    //�t�@�C�����J������
    if ($fp)
    {
        //�t�@�C���̃��b�N���ł�����
        if (flock($fp, LOCK_EX))																								// ���b�N
        {
            ftruncate( $fp,0);
            //���O�̏������݂����s������
            if (fwrite($fp ,$date) === FALSE)																		// check_mail�ǋL��������
            {
                //�������ݎ��s���̏���
            }
            flock($fp, LOCK_UN);																								// ���b�N�̉���
        }
        else
        {
            //���b�N���s���̏���
        }    
    }
    fclose($fp);
}

/****************************************************************************************
function getuzi_rireki()


����	�Ȃ�

�߂�l	�Ȃ�
****************************************************************************************/
function getuzi_rireki(){
    
    //�����ݒ�
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
    $check_path = $system_ini[$filename]['file_path'];
    $date = date_format(date_create('NOW'), "Y-m-d");
    
    //�ϐ�
    $buffer = "";
    
    //CSV�t�@�C���̒ǋL����
    if(!file_exists($check_path))
    {
        $fp = fopen($check_path, 'ab');																							// ���M�m�F�t�@�C����ǋL�������݂ŊJ��
        fclose($fp);				
    }
    $fp = fopen($check_path, 'a+b');
    
    // �t�@�C�����J������
    if ($fp)
    {
        // �t�@�C���̃��b�N���ł����� //
        if (flock($fp, LOCK_EX))																								// ���b�N
        {
            $buffer = fgets($fp);
            flock($fp, LOCK_UN);																								// ���b�N�̉���
        }
        else
        {
            // ���b�N���s���̏���
        }
    }
    fclose($fp);																												// �t�@�C�������
    return($buffer);
}

/****************************************************************************************
function nenzi_rireki()


����	�Ȃ�

�߂�l	�Ȃ�
****************************************************************************************/
function nenzi_rireki(){
    
    //�����ݒ�
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
    $check_path = $system_ini[$filename]['file_path'];
    $date = date_format(date_create('NOW'), "Y-m-d");
    
    //�ϐ�
    $buffer = "";
    
    //CSV�t�@�C���̒ǋL����
    if(!file_exists($check_path))
    {
        $fp = fopen($check_path, 'ab');																							// ���M�m�F�t�@�C����ǋL�������݂ŊJ��
        fclose($fp);				
    }
    $fp = fopen($check_path, 'a+b');
    
    // �t�@�C�����J������
    if ($fp)
    {
        // �t�@�C���̃��b�N���ł����� //
        if (flock($fp, LOCK_EX))																								// ���b�N
        {
            $buffer = fgets($fp);
            flock($fp, LOCK_UN);																								// ���b�N�̉���
        }
        else
        {
            // ���b�N���s���̏���
        }
    }
    fclose($fp);																												// �t�@�C�������
    return($buffer);
}

/****************************************************************************************
function delete_rireki()

����	�Ȃ�

�߂�l	�Ȃ�
****************************************************************************************/
function delete_rireki(){
    
    //�����ݒ�
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
    $check_path = $system_ini[$filename]['file_path'];																				// ���M�m�F�t�@�C��
    $date = date_format(date_create('NOW'), "Y-m-d");
    
    //�ϐ�
    $buffer = "";
    
    //CSV�t�@�C���̒ǋL����
    if(!file_exists($check_path))
    {
        $fp = fopen($check_path, 'ab');																							// ���M�m�F�t�@�C����ǋL�������݂ŊJ��
        fclose($fp);				
    }
    $fp = fopen($check_path, 'a+b');
    
    //�t�@�C�����J������
    if ($fp)
    {
        // �t�@�C���̃��b�N���ł����� //
        if (flock($fp, LOCK_EX))																								// ���b�N
        {
            $buffer = fgets($fp);
            flock($fp, LOCK_UN);																								// ���b�N�̉���
        }
        else
        {
            // ���b�N���s���̏���
        }
    }
    fclose($fp);																												// �t�@�C�������
    return($buffer);
}

/************************************************************************************************************
function period_pulldown_set($post)

����	$post

�߂�l	�Ȃ�
************************************************************************************************************/
function period_pulldown_set($post){
    
    //�����ݒ�
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
    $year = date_format(date_create('NOW'), "Y");
    $month = date_format(date_create('NOW'), "n");
    $startyear = $system_ini['period']['startyear'];
    $startmonth = $system_ini['period']['startmonth'];
    $period = 0;
    
    //�ϐ�
    $pulldown_html = "";
    
    //����
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
            $pulldown_html.='<option value ="'.$i.'" selected>'.$i.'��</option>';
        }
        else
        {
            $pulldown_html.='<option value ="'.$i.'">'.$i.'��</option>';
        }
    }
    $pulldown_html .= "</select>";
    
    return $pulldown_html;
}

/************************************************************************************************************
function month_pulldown_set($post)

����	$post

�߂�l	�Ȃ�
************************************************************************************************************/
function month_pulldown_set($post){
        
    //�����ݒ�
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //�ϐ�
    $pulldown_html = "";
    
    //����
    $pulldown_html .= "<select name='month' id='month' class='form_text'>";
    for($i = 1; $i <= 12; $i++)
    {
        if($post['month'] == $i)
        {
            $pulldown_html .= "<option value='".$i."' selected>".$i."��</option>";
        }
        else
        {
            $pulldown_html .= "<option value='".$i."'>".$i."��</option>";
        }
    }
    $pulldown_html .= "</select>";
    return $pulldown_html;
}
?>