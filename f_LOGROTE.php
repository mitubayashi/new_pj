<?php
/***************************************************************************
function loglotation()


引数            なし

戻り値         なし
***************************************************************************/
function loglotaton(){
    
    //ログファイルローテンション処理
    $strTargetFileName = "./log/error.log";
    
    //ファイル読み取り準備
    $fp = fopen($strTargetFileName, "r+");
    $date = date_create('NOW');
    $date = date_format($date,'YmdHis');
    
    // ファイルサイズを確認する。
    // ファイルサイズが5Mを超えているようであればローテーションを行う。
    if(filesize($strTargetFileName) >= 5120000)
    {
        //書き込み先ハンドラ
        $fpw = fopen("./log/".$date."_error.log","w+");
        
        //ファイルを排他ロック
        if(flock($fp,LOCK_EX))
        {//ファイルロック成功
            //ファイル内容をすべて取得し、本日日付ファイルに書き込み
            while(!feof($fp))
            {
                $buffer = fgets($fp);
                fwrite($fpw,$buffer);
            }

            //読み出し元ファイルの中身をすべて消す
            ftruncate($fp,0);
            //ロック解除
            flock($fp,LOCK_UN);
            //ファイルクローズ
            fclose($fpw);
            fclose($fp);
            // ログフォルダ以下のファイル名を取得し4ファイル以上あれば過去ファイルを削除する。
            $ListArray = glob("./log/*_error.log");
            
            //過去順に並び替える
            arsort($ListArray);
            $i = 0;
            foreach($ListArray as $filedate)
            {
                if($i >= 3)
                {
                    unlink($filedate);
                }
                $i++;
            }
        }
        else
        {
            //ファイルロック失敗
            //次回ログイン時に再処理できれば問題ないため
            //何もせずに見送る
            //念のためクローズ
            fclose($fp);
        }
    }
    else
    {
        //超えていなければ処理を抜ける
        //念のためクローズ
        fclose($fp);
    }
}
?>