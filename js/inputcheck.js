//function    inputcheck()
//
//����            name                      ���͗�id
//                  max_length             ������
//                  form_format            ���͐���
//                  isnotnull                  NULL�`�F�b�N(1�FNOT NULL 1�ȊO�FNULL����)
//                  isJust                      �������Œ�(1�F�Œ�@1�ȊO�F�Œ肵�Ȃ�)
//                  
//�߂�l         judge
function inputcheck(name,max_length,form_format,isnotnull,isJust)
{
    //�ϐ�
    var judge = true;
    var str = document.getElementById(name).value;
  
    //�G���[���b�Z�[�W���Z�b�g
    document.getElementById(name + "_errormsg").textContent = "";
    
    //���͓��e�`�F�b�N
    switch(String(form_format))
    {
        case '1':
            //���t���̓`�F�b�N
            if(document.getElementById(name).validity.valid == false)
            {
                judge = false;
                document.getElementById(name + "_errormsg").textContent = "���t�Ɍ�肪����܂�";
            }
            break;
        case '3':
            //���p�p���`�F�b�N
            if(str.match(/[^0-9A-Za-z]+/)) 
            {
                judge = false;
                document.getElementById(name + "_errormsg").textContent = "���p�p�����œ��͂��Ă�������";
            }
            break;
        case '4':
            //���p�����`�F�b�N
            if(str.match(/[^0-9]+/)) 
            {
                judge = false;
                document.getElementById(name + "_errormsg").textContent = "���p�����œ��͂��Ă�������";
            }
            break;        
    }
    
    //���͕������`�F�b�N
    if(judge)
    {
        if(max_length != 0)
        {
            if(isJust == 1)
            {
                if(max_length != strlen(str))
                {
                    document.getElementById(name + "_errormsg").textContent = max_length + "���œ��͂��Ă�������";
                    judge = false;                       
                }
            }
            else
            {
                if(max_length < strlen(str))
                {
                    document.getElementById(name + "_errormsg").textContent = max_length + "���ȓ��œ��͂��Ă�������";
                    judge = false;     
                }            
            }
        }
    }
    
    //�����̓`�F�b�N
    if(judge)
    {
            if(isnotnull == 1)
        {
            if(str == '')
            {
                judge = false;
                document.getElementById(name + "_errormsg").textContent = "�K�{���͍��ڂł�";
            }
        }
    }
    
    //���o�^�`�F�b�N
    if(judge)
    {
        if(exist_check(name,str) === false)
        {
            judge = false;
        }
    }
    
    //���͗��̃f�U�C��
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
                document.getElementById(name + "_errormsg").textContent = "���̍H���ԍ��͓o�^�ς݂ł��B";
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
                document.getElementById(name + "_errormsg").textContent = "���̍H�����͓o�^�ς݂ł��B";
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
                document.getElementById(name + "_errormsg").textContent = "���̎Ј��ԍ��͓o�^�ς݂ł��B";
            }
            counter = counter + 2;
        }        
    }
    return judge;
}

//function    check()
//
//����            �Ȃ�
//
//�߂�l         judge
function check()
{
    //�ϐ�
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
//����            �Ȃ�
//
//�߂�l         �Ȃ�
function error_data_set()
{
    var data = error_data;
    for(var i = 0; i < data.length; i++)
    {
        if(data[i]['error_type'] == 1)
        {
            document.getElementById(data[i]['name'] + "_errormsg").textContent = "���̏��͓o�^����Ă��܂��B";
        }
        if(data[i]['error_type'] == 2)
        {
            document.getElementById(data[i]['name'] + "_errormsg").textContent = "�o�^�ł���f�[�^�ʂ𒴂��Ă��܂��B";
        }
        if(data[i]['error_type'] == 3)
        {
            document.getElementById(data[i]['name'] + "_errormsg").textContent = "���̃��[�U�[ID�͓o�^�ς݂ł��B";
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
            document.getElementById("nowpass_errormsg").textContent = "���݂̃p�X���[�h�Ɠ��e���قȂ��Ă���܂��B";
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
        document.getElementById("luserpass_check_errormsg").textContent = "�p�X���[�h�Ɗm�F�p�p�X���[�h�̓��e����v���Ă��܂���B";
        input_style("luserpass_check", false);
    }

    return judge;
}

function kingaku_goukei(){
    
    //�ϐ�
    var goukei = 0;
    var total_row = document.getElementById("total_row").value;
    var judge = true;
    
    //���v���z���v�Z����
    for(var i = 0; i < total_row; i++)
    {
        goukei += Number(document.getElementById("kingaku_" + i).value);
    }
    
    //���v���z���Z�b�g����
    document.getElementById("goukei").value = goukei;
    
    return judge;
}

function kingaku_check(name)
{
    var judge = true;
    var str = document.getElementById(name).value;
    
    //���p�����`�F�b�N
    if(str.match(/[^0-9]+/)) 
    {
        judge = false;
    }
    
    //�������`�F�b�N
    if(7 < strlen(str))
    {
        judge = false;
    }
    
    //�����̓`�F�b�N
    if(filename == 'genka_5')
    {
        if(document.getElementById(name).value == '')
        {
            judge = false;
        }
    }
    //���͗��̃f�U�C��
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
    
    //�ڋq��������
    document.getElementById('1203').value = "";
    input_style("1203",true);
    document.getElementById("1203_errormsg").textContent = "";
    document.getElementById('1303').value = "";
    document.getElementById('1304').value = "";
    
    //�ڋq���Z�b�g
    while(counter < kokyakulist.length - 1)
    {
        if(kokyakulist[counter] == str)
        {
            
            document.getElementById('1203').value = kokyakulist[counter + 1];
            break;
        }
        counter = counter + 2;
    }
    
    //�`�[���R�[�h�A�`�[�������͌��i�荞��
    if(filename == 'PJTOUROKU_1')
    {
        var datalist1 = document.getElementById('1303_datalist');
        var datalist2 = document.getElementById('1304_datalist');
        var counter = 0;
        var kokyakuid = document.getElementById('1202').value;
        
        //�I����������
        while(datalist1.lastChild)
        {
            datalist1.removeChild(datalist1.lastChild);
        }
        while(datalist2.lastChild)
        {
            datalist2.removeChild(datalist2.lastChild);
        }
        
        //�I��������
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
    
    //�`�[����������
    document.getElementById('1304').value = "";
    input_style("1304",true);
    document.getElementById("1304_errormsg").textContent = "";
    
    //�`�[�����Z�b�g
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
    
    //�I����������
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
    //�I��������
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
    var msg  = delete_month + "�����ȏ�O�̑��엚�����폜���܂��B��낵���ł��傤���H";
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
    var msg = "�l�ʃv���W�F�N�g�����폜���܂��B��낵���ł����H\n�ēx�m�F����ꍇ�́u�L�����Z���v�{�^���������Ă��������B";
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