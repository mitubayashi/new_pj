<?php
    session_start();
    header('Content-type: text/html; charset=Shift_JIS');
?>
<?php

    //�����ݒ�
    require_once("f_Construct.php");
    require_once ("f_Form.php");
    require_once ("f_Button.php");
    require_once ("f_SQL.php");
    require_once ("f_DB.php");
    $form_ini = parse_ini_file('./ini/form.ini', true);
    
    //�萔
    $filename = $_SESSION['filename'];
    
    //�ϐ�
    $sql = array();
    $list = "";
    $select_list = "";
    
    //�u���E�U�o�b�N�A�����[�h�΍�
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //�ꗗ�\SQL�쐬����
    if(isset($_SESSION['list']) == false)
    {
        $_SESSION['list']['limitstart'] = 0;
    }
    $sql = itemListSQL($_SESSION['list']);
    $sql = SQLsetOrderby($_SESSION['list'],$sql);
    
    //�\�������\��
    $list .= '<div style="margin-top: 5px; margin-bottom: 5px;">';
    $list .= makeList_count($sql[1]);
    
    //�ꗗ�\�{�^���쐬����
    $list .= makeList_button();
    $list .= '</div>';
    
    //�ꗗ�\�쐬����
    $list .= make_pjagain_list($sql,$_SESSION['list']);
    
    //����������ʍ쐬����
    if($form_ini[$filename]['sech_form_num'] != "")
    {
        $sech_modal_html = makeModalHtml($_SESSION['list']);
    }
    
    //�I��PJ�ꗗ�쐬����
    $select_list .= "<table>";
    $select_list .= "<tr><td>�v���W�F�N�g�R�[�h</td><td><input type='text' size='30' id='PJCODE' name='PJCODE' value='' class='form_text disabled'></td></tr>";
    $select_list .= "<tr><td>�v���W�F�N�g��</td><td><input type='text' size='60' id='PJNAME' name='PJNAME' value='' class='form_text disabled'></td></tr>";
    $select_list .= "</table>";
    $select_list .= "<input type='submit' value='PJ�I���L�����Z��' name='pjagain' onclick='return pjagain_check();'>";
    $select_list .= "<input type='hidden' value='' id='5CODE' name='5CODE'>";
?>
<html>
    <head>
        <title>PJ�I���L�����Z������</title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./js/pjagain.js'></script>
        <script src='./jquery/jquery-1.8.3.min.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
            
            //���W�I�{�^���A�`�F�b�N�{�b�N�X�̂���Z���ɃJ�[�\�������킹�����̓���
            function mouseMove(row)
            {
                var tabledata = document.getElementById("radiolist");
                for(var i = 0; i < tabledata.rows[row].cells.length; i++)
                {
                    tabledata.rows[row].cells[i].style.backgroundColor = "#f7ca79";
                }
            }

            //���W�I�{�^���A�`�F�b�N�{�b�N�X�̂���Z������J�[�\�������ꂽ���̓���
            function mouseOut(row)
            {
                var tabledata = document.getElementById("radiolist");
                for(var i = 0; i < tabledata.rows[row].cells.length; i++)
                {
                    tabledata.rows[row].cells[i].style.backgroundColor = "";
                }
            }
        </script>
    </head>
    <body>
        <div class="title">PJ�I���L�����Z������</div>
        <div class="body_area">
            <form action="listJump.php" method="post">
                <?php echo $list; ?>
                <dialog id="dialog" class="modal_body"> 
                    <?php echo $sech_modal_html; ?>
                </dialog>
                <?php echo $select_list; ?>
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>