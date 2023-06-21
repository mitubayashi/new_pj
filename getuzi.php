<?php
    session_start();
    header('Expires:-1');
    header('Cache-Control:'); 
    header('Pragma:'); 
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
    $list = "";
    
    //�u���E�U�o�b�N�A�����[�h�΍�
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //�����������HTML�쐬
    $list .= makeEndMonth();
    
    //���݂̊��ƌ����擾
    $today = explode('/',date("Y/m/d"));
    if($today[1] == '6')
    {
        $post['period'] = getperiod($today[1],$today[0]) - 1;
    }
    else
    {
        $post['period'] = getperiod($today[1],$today[0]);
    }
    $post['month'] = $today[1]-1;
    
?>
<html>
    <head>
        <title>��������</title>
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" type="text/css" href="./css/list_css.css">
        <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet">
        <script src='./js/inputcheck.js'></script>
        <script src='./js/modal.js'></script>
        <script src='./jquery/jquery-1.8.3.min.js'></script>
        <script>
            var filename = '<?php echo $filename; ?>';
            function output_getuzicsv()
            {
                var period = document.getElementById('period').value;
                var month = document.getElementById('month').value;
                location.href='download_csv.php?filename=getuzi_5&period=' + period + '&month=' + month;
            }
        </script>
    </head>
    <body>
        <div class="title">��������</div>
        <div class="body_area">
            <form action="listJump.php" method="post">
                �O����{���F<?php echo getuzi_rireki(); ?>
                <table>
                    <tr>
                        <td>���������Ώۊ�</td>
                        <td><?php echo period_pulldown_set($post);  ?></td>
                    </tr>
                    <tr>
                        <td>���������Ώی�</td>
                        <td><?php echo month_pulldown_set($post); ?></td>
                    </tr>
                </table>
                <?php echo $list; ?>
                <input type='submit' name='getuzi' value = '��������' class='list_button' onclick='return check();'>
                <input type ='button' class='list_button' value = 'CSV�t�@�C������' style ='width:140px;' onclick='output_getuzicsv();'>
            </form>
        </div>
        <form action="pageJump.php" method="post">
            <?php echo makebutton(); ?>
	</form>
    </body>
</html>