function radiobutton_select()
{
    const radio1 = document.getElementsByName("radio");
    var pjagain_table = document.getElementById("radiolist");
    
    //選択内容表示
    for(var i = 0; i < radio1.length; i++)
    {
        if (radio1[i].checked){
            document.getElementById("PJCODE").value = pjagain_table.rows[(i + 1)].cells[1].textContent;
            document.getElementById("PJNAME").value = pjagain_table.rows[(i + 1)].cells[2].textContent;
            document.getElementById("5CODE").value = radio1[i].value;
        }
    }
}

function pjagain_check()
{
    var judge = true;
    if(document.getElementById("5CODE").value == "")
    {
        alert("プロジェクトが未選択です。\nプロジェクトを選択してください。");
        judge = false;
    }
    return judge;
}