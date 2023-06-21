function open_sech_modal()
{
    //検索条件画面を開く
    var modal = document.getElementById('dialog');
    modal.showModal();
}

function modal_close()
{
    //検索条件画面を閉じる
    document.getElementById('dialog').close();
}

function popup_modal(GET)
{
    var w = screen.availWidth;
    var h = screen.availHeight;
    w = (w * 0.9);
    h = (h * 0.9);
    url = 'Modal.php?tablenum='+GET+'&form=edit';
    n = window.open(
        url,
        this,
        "width =" + w + ",height=" + h + ",resizable=yes,maximize=yes"
    );	
}
function select_value(value,name)
{
    value = value.split("#$");
    name = name.split(",");
    for(var i = 0; i < value.length; i++)
    {
        document.getElementById(name[i] + '_select').value = value[i];
    }
}
function toMainWin(tablenum)
{
    switch(Number(tablenum))
    {
        case 3:
            var columnnum = "301,302,303";
            break;
        case 4:
            var columnnum = "401,402,403";
            break;
        case 5:
            var columnnum = "501,PJCODE,506";
            break;
        case 6:
            var columnnum = "601,PJCODE,506";
            break;
    }
        
    var opener = window.opener;
    var opener_form = opener.document;
    var column = columnnum.split(",");
    for(var i = 0; i < column.length; i++)
    {
        var value = document.getElementById(column[i] + '_select').value;
        var form_number = document.getElementById('form_number').value;
        if(form_number == "")
        {
            opener_form.getElementById(column[i]).value = value;
            opener_form.getElementById(column[i]).style.backgroundColor = '';
            opener_form.getElementById(column[i]).style.boxShadow = '';
        }
        else
        {
            opener_form.getElementById(column[i] + '_' + form_number).value = value;
            opener_form.getElementById(column[i] + '_' + form_number).style.backgroundColor = '';
            opener_form.getElementById(column[i] + '_' + form_number).style.boxShadow = '';
        }
    }
    close();
}

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
