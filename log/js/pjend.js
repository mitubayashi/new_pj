function checkbox_select()
{
    const checkbox1 = document.getElementsByName("checkbox[]");
    var pjend_table = document.getElementById("checkboxlist");
    var select_pj_table = document.getElementById("select_pj_table");
    var enddate_array = new Array();    
    
    //終了日付入力内容保持
    for(var i = 0; i < checkbox1.length; i++)
    {
        if(checkbox1[i].checked)
        {
            if(document.getElementById(('day' + checkbox1[i].value)) != null)
            {
                enddate_array[i] = document.getElementById(('day' + checkbox1[i].value)).value;
            }
            else
            {
                var dt = new Date();
                var date = new Date(dt.getFullYear(),dt.getMonth(),0);
                enddate_array[i] = date.getFullYear() + '-' +  ('00' + (date.getMonth()+1)).slice(-2) + '-' + ('00' + date.getDate()).slice(-2);
            }            
        }
    }
    
    //選択内容リセット
    var rowLen = select_pj_table.rows.length;
    for (var i = rowLen-1; i > 0; i--) {
        select_pj_table.deleteRow(-1);            
    }
    
    //選択PJ一覧表作成
    var select_pj_table = document.getElementById("select_pj_table");
    var counter = 1;
    
    for(var i = 0; i < checkbox1.length; i++) 
    {
        if(checkbox1[i].checked)
        {
            //追加の処理
            var rows = select_pj_table.insertRow(-1);
            
            // -1で列末尾に追加。インデックスで指定の位置に追加も可能
            var cell1 = rows.insertCell(-1);
            var cell2 = rows.insertCell(-1);
            var cell3 = rows.insertCell(-1);
            var cell4 = rows.insertCell(-1);

            cell1.innerHTML = counter;
            cell2.innerHTML = pjend_table.rows[(i + 1)].cells[1].textContent;
            cell3.innerHTML = pjend_table.rows[(i + 1)].cells[2].textContent;
            
            //終了日付入力欄作成
            var id = 'day' + checkbox1[i].value;
            cell4.innerHTML = "<input type='date' class='form_text' id='" + id + "' name='" + id + "'>";
            document.getElementById(id).value = enddate_array[i];
            
            //背景色変更(偶数行の背景色を水色にする)
            if(counter%2 == 0)
            {
                rows.classList.add('list_stripe')
            }
            counter++;        
        }
    }
}

function pjend_check()
{
    const checkbox1 = document.getElementsByName("checkbox[]");
    var judge = false;
    var date_judge = true;
    for(i = 0; i < checkbox1.length; i++) 
    {
        if(checkbox1[i].checked)
        {
            judge = true;
            
            //日付チェック
            var id = 'day' + checkbox1[i].value;
            if(document.getElementById(id).value == "")
            {
                date_judge = false;
                input_style(id,false);
            }
            else
            {
                input_style(id,true);
            }
        }
    }

    if(judge == false)
    {
        alert("プロジェクトが未選択です。\nプロジェクトを選択してください。");
    }
    
    if(date_judge == false)
    {
        judge = false;
        alert("終了日付が未入力です。\n終了日付を入力してください。");
    }
    
    return judge;
}

function date_disabled_change(){    
    let pjstat = document.getElementsByName('PJSTAT');
    
    //終了日付入力欄切り替え
    if(pjstat.item(0).checked)
    {
        document.getElementById('5010_startdate').value = "";
        document.getElementById('5010_enddate').value = "";
        document.getElementById('5010_startdate').classList.add('disabled');
        document.getElementById('5010_enddate').classList.add('disabled');
    }
    else
    {
        document.getElementById('5010_startdate').classList.remove('disabled');
        document.getElementById('5010_enddate').classList.remove('disabled');
    }
}

    function pjsyousai_open(code5)
    {
        var w = screen.availWidth;
        var h = screen.availHeight;
        w = (w * 0.8);
        h = (h * 0.8);
        url = 'pj_syousai.php?code='+code5+'';
        n = window.open(
                url,
                this,
                "width =" + w + ",height=" + h + ",resizable=yes,maximize=yes"
        );	        
    }
    