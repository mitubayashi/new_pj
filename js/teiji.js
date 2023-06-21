const checkbox1 = document.getElementsByName("checkbox[]");

function checkAll() {
    for(i = 0; i < checkbox1.length; i++) {
      checkbox1[i].checked = true;
    }
}

function clearAll() {
    for(i = 0; i < checkbox1.length; i++) 
    {
      checkbox1[i].checked = false;
    }
}

function teiji_check(){
    var judge = false;
    for(i = 0; i < checkbox1.length; i++) 
    {
        if(checkbox1[i].checked)
        {
            judge = true;
        }
    }

    if(judge == false)
    {
        alert("社員が未選択です。\n社員を選択してください。")
    }
    return judge;
}

function check_checkbox()
{
    for(var i = 0; i < checkbox.length; i++)
    {
        for(var j = 0; j < checkbox1.length; j++)
        {
            if(checkbox1[j].value == checkbox[i])
            {
                checkbox1[j].checked = true;
            }
        }
    }
}