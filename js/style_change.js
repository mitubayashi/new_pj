//���W�I�{�^���A�`�F�b�N�{�b�N�X�̂���Z���ɃJ�[�\�������킹�����̓���
function mouseMove(row,obj)
{
    for(var i = 0; i < obj.rows[row].cells.length; i++)
    {
        obj.rows[row].cells[i].style.backgroundColor = "#f7ca79";
    }
}

//���W�I�{�^���A�`�F�b�N�{�b�N�X�̂���Z������J�[�\�������ꂽ���̓���
function mouseOut(row,obj)
{
    for(var i = 0; i < obj.rows[row].cells.length; i++)
    {
        obj.rows[row].cells[i].style.backgroundColor = "";
    }
}