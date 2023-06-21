//function    inputcheck()
//
//引数            name                      入力欄id
//                  max_length             文字数
//                  form_format            入力制限
//                  isnotnull                  NULLチェック(1：NOT NULL 1以外：NULL許可)
//                  isJust                      文字数固定(1：固定　1以外：固定しない)
//                  
//戻り値         judge
function inputcheck(name,max_length,form_format,isnotnull,isJust)
{
    //変数
    var judge = true;
    var str = document.getElementById(name).value;
  
    //エラーメッセージリセット
    document.getElementById(name + "_errormsg").textContent = "";
    
    //入力内容チェック
    switch(String(form_format))
    {
        case '1':
            //日付入力チェック
            if(document.getElementById(name).validity.valid == false)
            {
                judge = false;
                document.getElementById(name + "_errormsg").textContent = "日付に誤りがあります";
            }
            break;
        case '3':
            //半角英数チェック
            if(str.match(/[^0-9A-Za-z]+/)) 
            {
                judge = false;
                document.getElementById(name + "_errormsg").textContent = "半角英数字で入力してください";
            }
            break;
        case '4':
            //半角数字チェック
            if(str.match(/[^0-9]+/)) 
            {
                judge = false;
                document.getElementById(name + "_errormsg").textContent = "半角数字で入力してください";
            }
            break;        
    }
    
    //入力文字数チェック
    if(judge)
    {
        if(max_length != 0)
        {
            if(isJust == 1)
            {
                if(max_length != strlen(str))
                {
                    document.getElementById(name + "_errormsg").textContent = max_length + "字で入力してください";
                    judge = false;                       
                }
            }
            else
            {
                if(max_length < strlen(str))
                {
                    document.getElementById(name + "_errormsg").textContent = max_length + "字以内で入力してください";
                    judge = false;     
                }            
            }
        }
    }
    
    //未入力チェック
    if(judge)
    {
            if(isnotnull == 1)
        {
            if(str == '')
            {
                judge = false;
                document.getElementById(name + "_errormsg").textContent = "必須入力項目です";
            }
        }
    }
    
    //既登録チェック
    if(judge)
    {
        if(exist_check(name,str) === false)
        {
            judge = false;
        }
    }
    
    //入力欄のデザイン
    input_style(name,judge);
    
    return judge;
}
              
function exist_check(name,str)
{
    var judge = true;
    var counter = 0;
    if(name == '302')
    {
        var koutei = document.getElementById('kouteilist').value;
        var kouteilist = koutei.split('#$');
        while(counter < kouteilist.length - 1)
        {
            if(kouteilist[counter] == str && str != document.getElementById(name).defaultValue)
            {
                judge = false;
                document.getElementById(name + "_errormsg").textContent = "この工程番号は登録済みです。";
            }
            counter = counter + 2;
        }
    }
    if(name == '303')
    {
        var koutei = document.getElementById('kouteilist').value;
        var kouteilist = koutei.split('#$');
        while(counter < kouteilist.length - 1)
        {
            if(kouteilist[counter + 1] == str && str != document.getElementById(name).defaultValue)
            {
                judge = false;
                document.getElementById(name + "_errormsg").textContent = "この工程名は登録済みです。";
            }
            counter = counter + 2;
        }        
    }
    if(name == '402')
    {
        var syain = document.getElementById('syainlist').value;
        var syainlist = syain.split('#$');
        while(counter < syainlist.length - 1)
        {
            if(syainlist[counter] == str && str != document.getElementById(name).defaultValue)
            {
                judge = false;
                document.getElementById(name + "_errormsg").textContent = "この社員番号は登録済みです。";
            }
            counter = counter + 2;
        }        
    }
    return judge;
}

//function    check()
//
//引数            なし
//
//戻り値         judge
function check()
{
    //変数
    var data = inputcheck_data;
    var judge = true;
    for(var i = 0; i < data.length; i++)
    {
        if(inputcheck(data[i]['name'],data[i]['max_length'],data[i]['form_format'],data[i]['isnotnull'],data[i]['isJust']) === false)
        {
            judge = false;
        }
    }
    
    if(filename == 'PJTOUROKU_3')
    {
        var total_row = document.getElementById('total_row').value;
        for(var i = 0; i < total_row; i++)
        {
            if(!kingaku_check('kingaku_' + i))
            {
                judge = false;
            }
        }
        kingaku_goukei();
    }
    return judge;
}

function input_style(name,judge)
{
    if(judge)
    {
        document.getElementById(name).style.backgroundColor = '';
        document.getElementById(name).style.boxShadow = '';
    }
    else
    {
        document.getElementById(name).style.backgroundColor = '#ffd6d6';
        document.getElementById(name).style.boxShadow = '0 0 0 1px red inset';
    }
}

function strlen(str) {
  var ret = 0;
  for (var i = 0; i < str.length; i++,ret++) {
    var upper = str.charCodeAt(i);
    var lower = str.length > (i + 1) ? str.charCodeAt(i + 1) : 0;
    if (isSurrogatePear(upper, lower)) {
      i++;
    }
  }
  return ret;
}

function isSurrogatePear(upper, lower) {
  return 0xD800 <= upper && upper <= 0xDBFF && 0xDC00 <= lower && lower <= 0xDFFF;
}

//function    error_data_set()
//
//引数            なし
//
//戻り値         なし
function error_data_set()
{
    var data = error_data;
    for(var i = 0; i < data.length; i++)
    {
        if(data[i]['error_type'] == 1)
        {
            document.getElementById(data[i]['name'] + "_errormsg").textContent = "この情報は登録されています。";
        }
        if(data[i]['error_type'] == 2)
        {
            document.getElementById(data[i]['name'] + "_errormsg").textContent = "登録できるデータ量を超えています。";
        }
        if(data[i]['error_type'] == 3)
        {
            document.getElementById(data[i]['name'] + "_errormsg").textContent = "このユーザーIDは登録済みです。";
        }
        input_style(data[i]['name'],false);
    }
}

function pass_check(type)
{
    var judge = true;
    
    if(type == 'edit')
    {
        var nowpass = document.getElementById("nowpass").value;
        var nowpass_check = document.getElementById("nowpass_check").value;
        if(nowpass != nowpass_check)
        {
            judge  = false;
            document.getElementById("nowpass_errormsg").textContent = "現在のパスワードと内容が異なっております。";
        }
        else
        {
            document.getElementById("nowpass_errormsg").textContent = "";
        }
    }
    if(judge)
    {
        if(type == 'insert')
        {
            if(document.getElementById("401").value == "")
            {
                judge = false;
                input_style('402',false);
                input_style('403',false);
            }
        }
        if(!inputcheck('luserid',60,3,1,0))
        {
            judge = false;
        }
        if(!passinput_check())
        {
            judge = false;
        }
    }
    return judge;
}

function passinput_check()
{
    var luserpass = document.getElementById("luserpass").value;
    var luserpass_check = document.getElementById("luserpass_check").value;
    var judge = true;
    input_style("luserpass", true);
    input_style("luserpass_check", true);
    
    if(!inputcheck('luserpass',60,3,1,0))
    {
        judge = false;
    }
    if(!inputcheck('luserpass_check',60,3,1,0))
    {
        judge = false;
    }

    if(luserpass != luserpass_check)
    {
        judge = false;
        document.getElementById("luserpass_check_errormsg").textContent = "パスワードと確認用パスワードの内容が一致していません。";
        input_style("luserpass_check", false);
    }

    return judge;
}

function kingaku_goukei(){
    
    //変数
    var goukei = 0;
    var total_row = document.getElementById("total_row").value;
    var judge = true;
    
    //合計金額を計算する
    for(var i = 0; i < total_row; i++)
    {
        goukei += Number(document.getElementById("kingaku_" + i).value);
    }
    
    //合計金額をセットする
    document.getElementById("goukei").value = goukei;
    
    return judge;
}

function kingaku_check(name)
{
    var judge = true;
    var str = document.getElementById(name).value;
    
    //半角数字チェック
    if(str.match(/[^0-9]+/)) 
    {
        judge = false;
    }
    
    //文字数チェック
    if(7 < strlen(str))
    {
        judge = false;
    }
    
    //未入力チェック
    if(filename == 'genka_5')
    {
        if(document.getElementById(name).value == '')
        {
            judge = false;
        }
    }
    //入力欄のデザイン
    input_style(name,judge);
    
    return judge;
}

function genka_setting_check()
{
    var judge = true;
    
    for(var i = 0; i < inputcheck_data.length; i++)
    {
        if(!kingaku_check(inputcheck_data[i]))
        {
            judge = false;
        }
    }
    return judge;
}

function kokyakumei_set()
{
    var counter = 0;
    var str = document.getElementById('1202').value;
    var kokyaku = document.getElementById('kokyakulist').value;
    var kokyakulist = kokyaku.split('#$');
    var team = document.getElementById('teamlist').value;
    var teamlist = team.split('#$');
    
    //顧客名初期化
    document.getElementById('1203').value = "";
    input_style("1203",true);
    document.getElementById("1203_errormsg").textContent = "";
    document.getElementById('1303').value = "";
    document.getElementById('1304').value = "";
    
    //顧客名セット
    while(counter < kokyakulist.length - 1)
    {
        if(kokyakulist[counter] == str)
        {
            
            document.getElementById('1203').value = kokyakulist[counter + 1];
            break;
        }
        counter = counter + 2;
    }
    
    //チームコード、チーム名入力候補絞り込み
    if(filename == 'PJTOUROKU_1')
    {
        var datalist1 = document.getElementById('1303_datalist');
        var datalist2 = document.getElementById('1304_datalist');
        var counter = 0;
        var kokyakuid = document.getElementById('1202').value;
        
        //選択肢初期化
        while(datalist1.lastChild)
        {
            datalist1.removeChild(datalist1.lastChild);
        }
        while(datalist2.lastChild)
        {
            datalist2.removeChild(datalist2.lastChild);
        }
        
        //選択肢生成
        while(counter < teamlist.length - 1)
        {
            if(teamlist[counter] == kokyakuid)
            {
                var option1 = document.createElement('option');
                option1.value = teamlist[counter + 1];
                var option2 = document.createElement('option');
                option2.value = teamlist[counter + 2];
                datalist1.appendChild(option1);
                datalist2.appendChild(option2);
            }
            counter = counter + 3;
        }    
    }
}

function teammei_set()
{
    var counter = 0;
    var str = document.getElementById('1303').value;
    var kokyakuid = document.getElementById('1202').value;
    var team = document.getElementById('teamlist').value;
    var teamlist = team.split('#$');
    
    //チーム名初期化
    document.getElementById('1304').value = "";
    input_style("1304",true);
    document.getElementById("1304_errormsg").textContent = "";
    
    //チーム名セット
    while(counter < teamlist.length - 1)
    {
        if(teamlist[counter] == kokyakuid && teamlist[counter + 1] == str)
        {
            document.getElementById('1304').value = teamlist[counter + 2];
            break;
        }
        counter = counter + 3;
    }    
}

function period_select()
{
    var kokyaku = document.getElementById('kokyakulist').value;
    var kokyakulist = kokyaku.split('#$');  
    var team = document.getElementById('teamlist').value;
    var teamlist = team.split('#$');
    var datalist1 = document.getElementById('1202_datalist');
    var datalist2 = document.getElementById('1203_datalist');
    var datalist3 = document.getElementById('1303_datalist');
    var datalist4 = document.getElementById('1304_datalist');
    var period = document.getElementById('period').value;
    
    //選択肢初期化
    while(datalist1.lastChild)
    {
        datalist1.removeChild(datalist1.lastChild);
    }
    while(datalist2.lastChild)
    {
        datalist2.removeChild(datalist2.lastChild);
    }
    while(datalist3.lastChild)
    {
        datalist3.removeChild(datalist3.lastChild);
    }
    while(datalist4.lastChild)
    {
        datalist4.removeChild(datalist4.lastChild);
    }
    //選択肢生成
    var counter = 0;
    while(counter < kokyakulist.length - 1)
    {
        if(kokyakulist[counter].substr(0,2) == period)
        {
            var option1 = document.createElement('option');
            option1.value = kokyakulist[counter];
            var option2 = document.createElement('option');
            option2.value = kokyakulist[counter + 1];
            datalist1.appendChild(option1);
            datalist2.appendChild(option2);
        }
        counter = counter + 2;
    }
    counter = 0;   
}

function reset_kokyakuteam()
{
    document.getElementById('1202').value = "";
    document.getElementById('1203').value = "";
    document.getElementById('1303').value = "";
    document.getElementById('1304').value = "";
}
function rireki_delete_check()
{
    var delete_month = document.getElementById('delete_month').value;
    var msg  = delete_month + "ヶ月以上前の操作履歴を削除します。よろしいでしょうか？";
    if(confirm(msg))
    {
        var judge = true;
    }
    else
    {
        var judge = false;
    }
    return judge;
}

function input_startdate(name)
{
    document.getElementById(name + '_startdate').value = startdate;
}

function kobetu_delete_check()
{
    var msg = "個人別プロジェクト情報を削除します。よろしいですか？\n再度確認する場合は「キャンセル」ボタンを押してください。";
    if(confirm(msg))
    {
        var judge = true;
    }
    else
    {
        var judge = false;
    }
    return judge;
}