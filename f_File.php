<?php
/****************************************************************************************
function csv_write($CSV)


引数1	$CSV				CSV

戻り値	なし
****************************************************************************************/
function csv_write($CSV) {
	
    //------------------------//
    //          定数          //
    //------------------------//
    $csv_path = "./temp/List_".session_id().".csv";



    //--------------------------//
    //  CSVファイルの追記処理  //
    //--------------------------//

//	$CSV = mb_convert_encoding($CSV,'sjis-win','utf-8');																		// 取得string文字コード変換

    $fp = fopen($csv_path, 'ab');																								// CSVファイルを追記書き込みで開く
    // ファイルが開けたか //
    if ($fp)
    {
            // ファイルのロックができたか //
            if (flock($fp, LOCK_EX))																								// ロック
            {
                    // ログの書き込みを失敗したか //
                    if (fwrite($fp , $CSV."\r\n") === FALSE)																			// CSV追記書き込み
                    {
                            // 書き込み失敗時の処理
                    }

                    flock($fp, LOCK_UN);																								// ロックの解除
            }
            else
            {
                    // ロック失敗時の処理
            }
    }
    fclose($fp);																												// ファイルを閉じる
    return($csv_path);
}	
?>