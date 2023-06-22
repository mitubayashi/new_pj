var row_copy = { CODE6:'', PJCODE:'',  PJNAME:'', CODE3:'', KOUTEIID:'', KOUTEINAME:'', TEIZI:'', ZANGYOU:'0' };//コピー用
var row_init = { CODE6:'', PJNUM:'', EDABAN:'', PJNAME:'', STAFFID:'', STAFFNAME:'', CODE3:'', KOUTEIID:'', KOUTEINAME:'', TEIZI:'', ZANGYOU:'0' };//クリア用
function close_dailog()
{
    close();
}

function showdialog(date)
{
        sessionStorage.setItem('date',date);
        setdate();
        document.getElementById('dgl').showModal();
}

function setdate() 
{        
        //カレンダーの初期値を今日の日付にする
        var today = new Date();
        today.setDate(today.getDate());
        var yyyy = today.getFullYear();
        var mm = ("0"+(today.getMonth()+1)).slice(-2);
        var dd = ("0"+today.getDate()).slice(-2);

        //未来の日付と締め処理済の月は選択できないようにする
        document.getElementById("startdate").value = yyyy+'-'+mm+'-'+dd;
        document.getElementById("startdate").max = yyyy+'-'+mm+'-'+dd;
        document.getElementById("enddate").value = yyyy+'-'+mm+'-'+dd;
        document.getElementById("enddate").max = yyyy+'-'+mm+'-'+dd;
}

function copyrow(pos)
{
    row_copy.CODE6 = $('#601_'+pos).val();	
    row_copy.PJCODE = $('#PJCODE_'+pos).val();	
    row_copy.PJNAME = $('#506_'+pos).val();		
    row_copy.CODE3 = $('#301_'+pos).val();	
    row_copy.KOUTEIID = $('#302_'+pos).val();	
    row_copy.KOUTEINAME = $('#303_'+pos).val();	
    row_copy.TEIZI = $('#705_'+pos).val();	
    row_copy.ZANGYOU = $('#706_'+pos).val();
}

function pasterow(pos)
{
    $('#601_'+pos).val(row_copy.CODE6);	
    $('#PJCODE_'+pos).val(row_copy.PJCODE);	
    $('#506_'+pos).val(row_copy.PJNAME);		
    $('#301_'+pos).val(row_copy.CODE3);	
    $('#302_'+pos).val(row_copy.KOUTEIID);	
    $('#303_'+pos).val(row_copy.KOUTEINAME);	
    $('#705_'+pos).val(row_copy.TEIZI);	
    $('#706_'+pos).val(row_copy.ZANGYOU);    
}

function deleterow(pos)
{
    $('#601_'+pos).val('');	
    $('#PJCODE_'+pos).val('');
    $('#506_'+pos).val('');	
    $('#301_'+pos).val('');
    $('#302_'+pos).val('');
    $('#303_'+pos).val('');
    $('#705_'+pos).val('');
    $('#706_'+pos).val('0');
}

function time_total()
{
    //変数
    var teizi_goukei = 0;
    var zangyou_goukei = 0;
    var judge = true;
    
    //入力内容チェック
    for(var i = 0; i < 10; i++)
    {
        var teizi = document.getElementById('705_' + i).value * 100;
        var zangyou = document.getElementById('706_' + i).value * 100;
        
        //エラーリセット
        input_style('705_' + i,true);
        input_style('706_' + i,true);
        
        //入力文字チェック
        if(document.getElementById('705_' + i).value.match(/[^0-9\.]+/) || teizi % 25 != 0)
        {
            judge = false;
            input_style('705_' + i,false);
        }   
        if(document.getElementById('706_' + i).value.match(/[^0-9\.]+/) || zangyou % 25 != 0)
        {
            judge = false;
            input_style('706_' + i,false);                
        }
    }
    
    //定時時間と残業時間の合計を計算する
    if(judge)
    {
        for(var i = 0; i < 10; i++)
        {
            var teizi = document.getElementById('705_' + i).value * 100;
            var zangyou = document.getElementById('706_' + i).value * 100;
            teizi_goukei = teizi_goukei + teizi;
            zangyou_goukei = zangyou_goukei + zangyou;
        }
    }
    
    //合計時間をセットする
    document.getElementById('TEIZI_GOUKEI').value = (teizi_goukei / 100);
    document.getElementById('ZANGYOU_GOUKEI').value = (zangyou_goukei / 100);
    
    //合計時間チェック
    if(judge)
    {
        if(document.getElementById('TEIZI_GOUKEI').value > 7.75)
        {
            judge = false;
            input_style('TEIZI_GOUKEI',false);           
        }
        if(((teizi_goukei / 100) + (zangyou_goukei / 100)) > 24.00)
        {
            judge = false;
            input_style('ZANGYOU_GOUKEI',false);           
        }
    }
    
    return judge;
}

function date_check()
{
    var judge = true;
    input_style('704',true);   
    //作業日付チェック
    if(document.getElementById('704').value == "")
    {
        judge = false;
        input_style('704',false);    
    }
    return judge;
}

function progress_check()
{
    //変数
    var judge = true;
    
    //エラーリセット
    input_style('TEIZI_GOUKEI',true);   
   
    //定時時間チェック
    if(!time_total())
    {
        judge = false;   
    }
     
    //作業日付チェック
    if(!date_check())
    {
        judge = false;
    }

    //空欄チェック
    var kuuran = 0;
    for(var i = 0; i < 10; i++)
    {
        input_style('302_' + i,true);
        input_style('303_' + i,true);
        input_style('PJCODE_' + i,true);
        input_style('506_' + i,true);   
        var code3 = document.getElementById('301_' + i).value;
        var code6 = document.getElementById('601_' + i).value;
        var teizi = document.getElementById('705_' + i).value;
        var zangyou = document.getElementById('706_' + i).value;
        if(code3 == ""  && code6 == "" && teizi == "" && zangyou == "0")
        {
            kuuran++;
            continue;
        }
        else
        {
            if(code3 == "")
            {
                input_style('302_' + i,false);
                input_style('303_' + i,false);
                judge = false;
            }
            if(code6 == "")
            {
                input_style('PJCODE_' + i,false);
                input_style('506_' + i,false);   
                judge = false;
            }
            if(teizi == "")
            {
                input_style('705_' + i,false);
                judge = false;
            }
            if(zangyou == "")
            {
                input_style('706_' + i,false);
                judge = false;
            }
        }
    }
    if(kuuran == 10)
    {
        judge = false;
        window.alert("項目を入力してください。");
    }
    
    //月次処理済日付チェック
   if(judge)
   {
       var min = document.getElementById("mindate").value;
       if(document.getElementById("704").value < min)
       {
           judge = false;
           input_style('704',false);
           window.alert("作業日付に月次処理済の日付が入力されています。");
       }
   }
    return judge;
}

function copy()
{
    var judge = true;
    var startdate = document.getElementById("startdate").value;
    var enddate = document.getElementById("enddate").value;
    var msg = "";
    input_style("startdate", true);
    input_style("enddate", true);
    
    //開始日付、終了日付の未入力チェック
    if(startdate == "")
    {
        judge = false;
        msg = "項目を入力してください。";
        input_style("startdate", false);
    }
    if(enddate == "")
    {
         judge = false;
        msg = "項目を入力してください。";
        input_style("enddate", false);       
    }
    
    //開始日付、終了日付の入力内容チェック
    if(judge)
    {
        //開始日付、終了日付が未来の日付でないかチェック
        var day = new Date();
        day.setDate(day.getDate());
        var yyyy = day.getFullYear();
        var mm = ("0"+(day.getMonth()+1)).slice(-2);
        var dd = ("0"+day.getDate()).slice(-2);
        var today = yyyy+'-'+mm+'-'+dd;

        if(startdate > today)
        {
            judge = false;
            msg = "未来の日付が入力されています。";
            input_style("startdate", false);    
        }
        if(enddate > today)
        {
            judge = false;
            msg = "未来の日付が入力されています。";
            input_style("enddate", false);                
        }

        //開始日付と終了日付が前後しているとき
        if(startdate > enddate)
        {
            judge = false;
            msg = "開始日付に終了日付より過去の日付が入力されています。";
            input_style("startdate", false);
            input_style("enddate", false);
        }

        //月次処理済みのチェック
        var mindate = document.getElementById("startdate").min;
        if(startdate < mindate)
        {
            judge = false;
            msg = "月次処理済みの日付が入力されています。";
            input_style("startdate", false);
        }
        if(enddate < mindate)
        {
            judge = false;
            msg = "月次処理済みの日付が入力されています。";
            input_style("enddate", false);
        }                
    }
    
    //メッセージ出力
    if(judge == false)
    {
        window.alert(msg);
    }
    
    //コピー元の日付に終了済みプロジェクトが登録されている
    if(judge)
    {
        var copydate = sessionStorage.getItem('date');
        var pjstat = progress_data[copydate]['pjstat'];
        
        if(pjstat == '1')
        {
            judge = false;
            window.alert("コピー元日付(" + copydate + ")に終了処理済の工数が登録されています。");
        }
    }
    
    //コピー先の日付に終了済みプロジェクトが登録されている
    if(judge)
    {
        var start = new Date(startdate);
        var end = new Date(enddate);
        for(var d = start; d <= end; d.setDate(d.getDate()+1))
        {
            var date = (d.getFullYear() + '-' + ('00' + (d.getMonth()+1)).slice(-2) + '-' + ('00' + d.getDate()).slice(-2));
            if(date in progress_data)
            {
                if(progress_data[date]['pjstat'] == '1')
                {
                    judge = false;
                }
            }
        }
        if(!judge)
        {
            window.alert("選択された期間(" + startdate + "~" + enddate + ")に終了処理済の工数が登録されています。");
        }
    }
    
    //工数登録済みかチェック
    if(judge)
    {
        var tourokucheck = false;
        var startDate = new Date(startdate);
        var endDate = new Date(enddate);
        var dateList = new Array();

        for(var d = startDate; d <= endDate; d.setDate(d.getDate()+1)) 
        {
            for(var i = 0; i < worklist.length ; i++)
            {
                var date = new Date(worklist[i]);
                if(d.getTime() == date.getTime())
                {
                    tourokucheck = true;
                }
            }
        }

        if(tourokucheck)
        {
            if(confirm("工数登録済みの日付が入力されています。\n" + "上書きしてもよろしいでしょうか？") ) 
            {
                judge = true;
            }
            else
            {
                judge = false;
            }
        }
    }

    //工数コピー処理
    if(judge)
    {            
        jQuery.ajax({
            type: 'post',
            url: 'TOPexe.php',
            data: {'copydate' : sessionStorage.getItem('date'),
                'pasteStart': document.getElementById("startdate").value,
                'pasteEnd': document.getElementById("enddate").value},
            success: function(){ 
                sessionStorage.removeItem('date');
                location.href = "./TOP.php";
            }
        });
    }
    
}