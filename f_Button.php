<?php

/***************************************************************************
function makebutton()

引数            なし

戻り値         $button_html                              ボタン作成HTML
***************************************************************************/
function makebutton(){
    
    //初期設定
    $button_ini_array = parse_ini_file("./ini/button.ini",true);												// ボタン基本情報格納.iniファイル

    //定数
    $mainbutton_num_array = explode(",",$button_ini_array['MENU_4']['set_button_center']);
	
    //変数
    $total_count = 0;
    $button_html = "";
	
    //ボタン作成処理
    $button_html = '<nav><ul>';
        $button_html .= '<li class="has-child"><a href="TOPexe.php?mainmenu="><span class="line">TOP</span></a></li>';
        foreach($mainbutton_num_array as $mainbutton_num)
        {
            $mainbutton_value = $button_ini_array[$mainbutton_num]['value'];    //ボタン文字
            $mainbutton_name = $button_ini_array[$mainbutton_num]['button_name'];   //ボタン名称  
            $name = str_replace('_button', '', $mainbutton_name);
            $subbutton_num_array = explode(",",$button_ini_array[$name]['set_button_center']); //サブボタンナンバー
            
            $button_html .= '<li class="has-child"><a><span class="line">'.$mainbutton_value.'</span></a>';
            $button_html .= '<ul>';
            foreach($subbutton_num_array as $subbutton_num)
            {
                $subbutton_value = $button_ini_array[$subbutton_num]['value'];    //ボタン文字
                $subbutton_name = $button_ini_array[$subbutton_num]['button_name'];   //ボタン名称
                if($subbutton_value != '原価メンテナンス' || $_SESSION['user']['STAFFID'] == '808001')
                {
                    $button_html .= '<li>';
                    $button_html .= '<input type = "submit" class = "menu" name = "'.$subbutton_name.'" value = "'.$subbutton_value.'">';
                    $button_html .= '</li>';
                }
            }
            $button_html .= '</ul></li>';
        }
        $button_html .= '<li class="has-child"><a href="login.php"><span class="line">ログアウト</span></a></li>';
        $button_html .= '</ul></nav>';
	return ($button_html);
}

/***************************************************************************
function makeList_button()

引数            なし

戻り値         $button_html                              ボタン作成HTML
***************************************************************************/
function makeList_button(){
    
    //初期設定
    $form_ini = parse_ini_file('./ini/form.ini', true);
    $system_ini = parse_ini_file('./ini/system.ini', true);
    
    //定数
    $filename = $_SESSION['filename'];
    $isCSV = $form_ini[$filename]['isCSV'];
    $filename_array = explode('_',$filename);
    $filename_insert = $filename_array[0]."_1";
    $filename_fileinsert = $filename_array[0]."_6";
    $sech_form_num = explode('_',$form_ini[$filename]['sech_form_num']);
            
    //変数
    $button_html = "";
    
    //ボタン作成処理
    if($sech_form_num[0] != "")
    {
        $button_html .= "<input type='button' value='検索条件' class='list_button' onclick='open_sech_modal();'>";
    }
    if(isset($form_ini[$filename_insert]))
    {
        $button_html .= "<input type='submit' value='新規作成' class='list_button' name = '".$filename_insert."_button'>";
    }
    if($isCSV == "1")
    {
        $button_onclick = "location.href='download_csv.php?filename=".$filename."'";
        $button_html .= '<input type="button" value="CSV出力" class="list_button" onclick="'.$button_onclick.'">';
    }
   if(isset($form_ini[$filename_fileinsert]))
   {
       $button_html .= "<input type='submit' value='ファイル取込' class='list_button' name = '".$filename_fileinsert."_button'>";
   }
   if($filename == 'rireki_2')
   {
       $month = $system_ini['delete_rireki']['month'];
       $button_html .= "<input type='hidden' value='".$month."' id='delete_month' name='delete_month'>";
       $button_html .= "<input type='submit' value='データ削除' class='list_button' name='rireki_delete' onclick='return rireki_delete_check();'>";
   }
    return $button_html;
}
?>