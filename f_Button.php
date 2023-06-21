<?php

/***************************************************************************
function makebutton()

����            �Ȃ�

�߂�l         $button_html                              �{�^���쐬HTML
***************************************************************************/
function makebutton(){
    
    //�����ݒ�
    $button_ini_array = parse_ini_file("./ini/button.ini",true);												// �{�^����{���i�[.ini�t�@�C��

    //�萔
    $mainbutton_num_array = explode(",",$button_ini_array['MENU_4']['set_button_center']);
	
    //�ϐ�
    $total_count = 0;
    $button_html = "";
	
    //�{�^���쐬����
    $button_html = '<nav><ul>';
        $button_html .= '<li class="has-child"><a href="TOPexe.php?mainmenu="><span class="line">TOP</span></a></li>';
        foreach($mainbutton_num_array as $mainbutton_num)
        {
            $mainbutton_value = $button_ini_array[$mainbutton_num]['value'];    //�{�^������
            $mainbutton_name = $button_ini_array[$mainbutton_num]['button_name'];   //�{�^������  
            $name = str_replace('_button', '', $mainbutton_name);
            $subbutton_num_array = explode(",",$button_ini_array[$name]['set_button_center']); //�T�u�{�^���i���o�[
            
            $button_html .= '<li class="has-child"><a><span class="line">'.$mainbutton_value.'</span></a>';
            $button_html .= '<ul>';
            foreach($subbutton_num_array as $subbutton_num)
            {
                $subbutton_value = $button_ini_array[$subbutton_num]['value'];    //�{�^������
                $subbutton_name = $button_ini_array[$subbutton_num]['button_name'];   //�{�^������
                if($subbutton_value != '���������e�i���X' || $_SESSION['user']['STAFFID'] == '808001')
                {
                    $button_html .= '<li>';
                    $button_html .= '<input type = "submit" class = "menu" name = "'.$subbutton_name.'" value = "'.$subbutton_value.'">';
                    $button_html .= '</li>';
                }
            }
            $button_html .= '</ul></li>';
        }
        $button_html .= '<li class="has-child"><a href="login.php"><span class="line">���O�A�E�g</span></a></li>';
        $button_html .= '</ul></nav>';
	return ($button_html);
}

/***************************************************************************
function makeList_button()

����            �Ȃ�

�߂�l         $button_html                              �{�^���쐬HTML
***************************************************************************/
function makeList_button(){
    
    //�����ݒ�
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
    $isCSV = $form_ini[$filename]['isCSV'];
    $filename_array = explode('_',$filename);
    $filename_insert = $filename_array[0]."_1";
    $filename_fileinsert = $filename_array[0]."_6";
    $sech_form_num = explode('_',$form_ini[$filename]['sech_form_num']);
            
    //�ϐ�
    $button_html = "";
    
    //�{�^���쐬����
    if($sech_form_num[0] != "")
    {
        $button_html .= "<input type='button' value='��������' class='list_button' onclick='open_sech_modal();'>";
    }
    if(isset($form_ini[$filename_insert]))
    {
        $button_html .= "<input type='submit' value='�V�K�쐬' class='list_button' name = '".$filename_insert."_button'>";
    }
    if($isCSV == "1")
    {
        $button_onclick = "location.href='download_csv.php?filename=".$filename."'";
        $button_html .= '<input type="button" value="CSV�o��" class="list_button" onclick="'.$button_onclick.'">';
    }
   if(isset($form_ini[$filename_fileinsert]))
   {
       $button_html .= "<input type='submit' value='�t�@�C���捞' class='list_button' name = '".$filename_fileinsert."_button'>";
   }
   if($filename == 'rireki_2')
   {
       $month = $system_ini['delete_rireki']['month'];
       $button_html .= "<input type='hidden' value='".$month."' id='delete_month' name='delete_month'>";
       $button_html .= "<input type='submit' value='�f�[�^�폜' class='list_button' name='rireki_delete' onclick='return rireki_delete_check();'>";
   }
    return $button_html;
}
?>