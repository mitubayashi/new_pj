function syain_sort()
{
    //���z���͗��̍s��
    var total_row = document.getElementById("total_row").value;
    
    //�ڋq�`�[���ɓo�^����Ă��郁���o�[���擾����
    var kokyakuid = document.getElementById('1202').value;
    var teamid = document.getElementById('1303').value;
    var memberlist = document.getElementById('memberlist').value.split('#$');
    var counter = 0;
    var member = "";
    while(counter < memberlist.length - 1)
    {
        if(memberlist[counter] == kokyakuid && memberlist[counter + 1] == teamid)
        {
            member = memberlist[counter + 2];
            break;
        }
        counter = counter + 3;
    }
    
    //�f�t�H���g�̎Ј����X�g�쐬
    var kingaku_list = document.getElementById("kingaku_list");
    var default_syainlist = new Array();
    for(var i = 0; i < total_row; i++)
    {
        default_syainlist[i] = new Array();
        default_syainlist[i]['syaincode'] = document.getElementById("syaincode_" + i).value;      
        default_syainlist[i]['syainid'] = document.getElementById("syainid_" + i).value;    
        default_syainlist[i]['syainname'] = document.getElementById("syainname_" + i).value;   
        default_syainlist[i]['kingaku'] = document.getElementById("kingaku_" + i).value;
    }
    
    //�S�s�폜����    
    while(kingaku_list.rows[1]) kingaku_list.deleteRow(1);
    
    //�����o�[���o�^�܂��͑S�Ј��o�^�̏ꍇ�A�Ј��ԍ����ɕ��ёւ���
    if(member == "0" || member == "")
    {
        for(var i = 0; i < total_row; i++)
        {
            //�s�̒ǉ�
            var row = kingaku_list.insertRow(-1);
            
            //No�Z���ǉ�
            var cell = row.insertCell(-1);
            cell.innerHTML = (i + 1) + "<input type='hidden' id='syaincode_" + i + "' value='" + default_syainlist[i]['syaincode'] + "'>";
            
            //�Ј��ԍ��Z���ǉ�
            var cell = row.insertCell(-1);            
            cell.innerHTML = default_syainlist[i]['syainid'] + "<input type='hidden' id='syainid_" + i + "' value='" + default_syainlist[i]['syainid'] + "'>";
            
            //�Ј����Z���ǉ�
            var cell = row.insertCell(-1);
            cell.innerHTML = default_syainlist[i]['syainname'] + "<input type='hidden' id='syainname_" + i + "' value='" + default_syainlist[i]['syainname'] + "'>";
            
            //�Ј��ʋ��z�Z���ǉ�
            var cell = row.insertCell(-1);
            cell.innerHTML = "<input type='text' class='form_text' name='kingaku_" + default_syainlist[i]['syaincode'] + "' id='kingaku_" + i + "' value='" + default_syainlist[i]['kingaku'] + "' onchange='kingaku_check(this.id); kingaku_goukei();'>";
        }
    }
    else
    {
        //�����o�[�odefault_syainlist�^����Ă���Ј�����i�֕\��
        var counter = 1;
        member = member.split(',');
        for(i = 0; i < member.length; i++)
        {
            var number = "";
            //id�ԍ��̎擾
            for(var j = 0; j < default_syainlist.length; j++)
            {
                if(default_syainlist[j]['syaincode'] == member[i])
                {                                
                    //�s�̒ǉ�
                    var row = kingaku_list.insertRow(-1);

                    //No�Z���ǉ�
                    var cell = row.insertCell(-1);
                    cell.innerHTML = counter + "<input type='hidden' id='syaincode_" + j + "' value='" + default_syainlist[j]['syaincode'] + "'>" ;

                    //�Ј��ԍ��Z���ǉ�
                    var cell = row.insertCell(-1);            
                    cell.innerHTML = default_syainlist[j]['syainid'] + "<input type='hidden' id='syainid_" + j + "' value='" + default_syainlist[j]['syainid'] + "'>" ;

                    //�Ј����Z���ǉ�
                    var cell = row.insertCell(-1);
                    cell.innerHTML = default_syainlist[j]['syainname'] + "<input type='hidden' id='syainname_" + j + "' value='" + default_syainlist[j]['syainname'] + "'>" ;

                    //�Ј��ʋ��z�Z���ǉ�
                    var cell = row.insertCell(-1);
                    cell.innerHTML = "<input type='text' class='form_text' name='kingaku_" + default_syainlist[j]['syaincode'] + "' id='kingaku_" + j + "' value='" + default_syainlist[j]['kingaku'] + "' onchange='kingaku_check(this.id); kingaku_goukei();'>";
                    break;
                }
            } 
            counter++;
        }
        
        //�����o�[�o�^����Ă��Ȃ��Ј������i�ɕ\��
        for(var i = 0; i < default_syainlist.length; i++)
        {
            if(!member.includes(default_syainlist[i]['syaincode']))
            {
                //�s�̒ǉ�
                var row = kingaku_list.insertRow(-1);

                //No�Z���ǉ�
                var cell = row.insertCell(-1);
                cell.innerHTML = counter + "<input type='hidden' id='syaincode_" + i + "' value='" + default_syainlist[i]['syaincode'] + "'>" ;

                //�Ј��ԍ��Z���ǉ�
                var cell = row.insertCell(-1);            
                cell.innerHTML = default_syainlist[i]['syainid'] + "<input type='hidden' id='syainid_" + i + "' value='" + default_syainlist[i]['syainid'] + "'>" ;

                //�Ј����Z���ǉ�
                var cell = row.insertCell(-1);
                cell.innerHTML = default_syainlist[i]['syainname'] + "<input type='hidden' id='syainname_" + i + "' value='" + default_syainlist[i]['syainname'] + "'>" ;

                //�Ј��ʋ��z�Z���ǉ�
                var cell = row.insertCell(-1);
                cell.innerHTML = "<input type='text' class='form_text' name='kingaku_" + default_syainlist[i]['syaincode'] + "' id='kingaku_" + i + "' value='" + default_syainlist[i]['kingaku'] + "' onchange='kingaku_check(this.id); kingaku_goukei();'>";
                counter++;
            }
        }
    }    
}

function all_checked()
{
    const checkbox1 = document.getElementsByName("checkbox[]");
    const checkbox2 = document.getElementById("all_check");
    if(checkbox2.checked)
    {
        for(i = 0; i < checkbox1.length; i++) {
            checkbox1[i].checked = true;
        }
    }
    else
    {
        for(i = 0; i < checkbox1.length; i++) {
            if(checkbox1[i].classList.contains("check_box_none") == false)
            {
                checkbox1[i].checked = false;
            }            
        }
    }
}

function check_change()
{
    const checkbox1 = document.getElementsByName("checkbox[]");
    const checkbox2 = document.getElementById("all_check");
    var all_check_flag = true;
    
    for(i = 0; i < checkbox1.length; i++) {
        if(checkbox1[i].checked == false)
        {
            checkbox2.checked = false;
            all_check_flag = false;
        }
    }
    
    if(all_check_flag)
    {
        checkbox2.checked = true;
    }
}

