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
    require_once ("f_DB.php");
    
    //�ϐ�
    $judge = false;
    $genkalist = array();
    $genka = array();
    
    //�u���E�U�o�b�N�΍�
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //����
    $con = dbconect();
    
    //�������𐮗�����
    $keys = array_keys($_SESSION['genka']);
    for($i = 0; $i < count($keys); $i++)
    {
        if($keys[$i] != "genka")
        {
            $key = explode('_',$keys[$i]);
            $genka[$key[1]][$key[0]] = $_SESSION['genka'][$keys[$i]];
        }
    }
    
    //�����}�X�^�ɓo�^����Ă���Ј������擾
    $genka_sql = "SELECT 4CODE FROM genkainfo;";
    $result = $con->query($genka_sql) or ($judge = true);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $genkalist[$result_row['4CODE']] = $result_row['4CODE'];
    }
    
    //�����}�X�^�ɓo�^������ꍇ��UPDATE�A�o�^���Ȃ��ꍇ��INSERT
    $keys = array_keys($genka);
    for($i = 0; $i < count($keys); $i++)
    {
        if($genkalist[$keys[$i]])
        {
            $sql = "UPDATE genkainfo SET GENKA = '".$genka[$keys[$i]]['1402']."',ZANGYOTANKA = '".$genka[$keys[$i]]['1403']."' WHERE 4CODE = '".$keys[$i]."';";
        }
        else
        {
            $sql = "INSERT INTO genkainfo (4CODE,GENKA,ZANGYOTANKA) VALUES('".$keys[$i]."','".$genka[$keys[$i]]['1402']."','".$genka[$keys[$i]]['1403']."');";
        }
        $result = $con->query($sql) or ($judge = true);
    }
    
    //���엚��
    insert_sousarireki('0', "");
    
    //�y�[�W�ړ�����
    $_SESSION['pre_post'] = $_SESSION['post'];
    $_SESSION['post'] = null;
    unset($_SESSION['genka']);
    $_SESSION['filename'] = 'genka_5';
    header("location:".(empty($_SERVER['HTTPS'])? "http://" : "https://")
            .$_SERVER['HTTP_HOST'].dirname($_SERVER["REQUEST_URI"])."/genka.php");
?>