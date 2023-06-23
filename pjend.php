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
    $list .= make_pjend_list($sql,$_SESSION['list']);
    
    //����������ʍ쐬����
    if($form_ini[$filename]['sech_form_num'] != "")
    {
        $sech_modal_html = makeModalHtml($_SESSION['list']);
    }
    
    //�I��PJ�ꗗ�쐬����
    if(isset($_SESSION['list']['PJSTAT']) && $_SESSION['list']['PJSTAT'] == "1")
    {
        $select_list .= "<div style='margin-bottom: 3px;'><a id = 'select_pj_count'>0���I��</a><input type='submit' value='PJ�I��' name='pjend' disabled></div>";
    }
    else
    {
        $select_list .= "<div style='margin-bottom: 3px;'><a id = 'select_pj_count'>0���I��</a><input type='submit' value='PJ�I��' name='pjend' onclick='return pjend_check();'></div>";
    }
    $select_list .= "<div class='list_scroll' style='max-height: 300px;'>";
    $select_list .="<table id='select_pj_table'>";
    $select_list .= "<tr><th>No</th><th>�v���W�F�N�g�R�[�h</th><th>�v���W�F�N�g��</th><th>PJ�I�����t</th></tr>";
    $select_list .= "</table>";
    $select_list .= "</div>";
    
?>
<html>
    <head>
        <title>PJ�I������</title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./js/pjend.js'></script>
        <script src='./js/style_change.js'></script>
        <script src='./jquery/jquery-1.8.3.min.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
            window.onload = function(){
                //���t���͗��؂�ւ�
                date_disabled_change();
            }
        </script>
    </head>
    <body>
        <div class="title">PJ�I������</div>
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