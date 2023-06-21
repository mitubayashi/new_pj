<?php
    session_start(); 
    header('Expires:-1'); 
    header('Cache-Control:'); 
    header('Pragma:'); 
    header('Content-type: text/html; charset=Shift_JIS'); 	
?>
<?php
    //初期設定
    require_once("f_Construct.php");
    require_once ("f_DB.php");
    
    //変数
    $judge = false;
    $genkalist = array();
    $genka = array();
    
    //ブラウザバック対策
    start();    
    $_SESSION['post'] = $_SESSION['pre_post'];
    $_SESSION['pre_post'] = null;
    
    //処理
    $con = dbconect();
    
    //原価情報を整理する
    $keys = array_keys($_SESSION['genka']);
    for($i = 0; $i < count($keys); $i++)
    {
        if($keys[$i] != "genka")
        {
            $key = explode('_',$keys[$i]);
            $genka[$key[1]][$key[0]] = $_SESSION['genka'][$keys[$i]];
        }
    }
    
    //原価マスタに登録されている社員情報を取得
    $genka_sql = "SELECT 4CODE FROM genkainfo;";
    $result = $con->query($genka_sql) or ($judge = true);
    while($result_row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $genkalist[$result_row['4CODE']] = $result_row['4CODE'];
    }
    
    //原価マスタに登録がある場合はUPDATE、登録がない場合はINSERT
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
    
    //操作履歴
    insert_sousarireki('0', "");
    
    //ページ移動処理
    $_SESSION['pre_post'] = $_SESSION['post'];
    $_SESSION['post'] = null;
    unset($_SESSION['genka']);
    $_SESSION['filename'] = 'genka_5';
    header("location:".(empty($_SERVER['HTTPS'])? "http://" : "https://")
            .$_SERVER['HTTP_HOST'].dirname($_SERVER["REQUEST_URI"])."/genka.php");
?>