<?php
/////////////////
/// For Debug ///
/////////////////
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



header('Content-Type: text/html; charset=utf-8');

$myfile = fopen("ArisUpdateProductsFile.txt", "w") or die("Unable to open file!");
    
    //login
    $curlLogin = curl_init();

    curl_setopt_array($curlLogin, [
      CURLOPT_URL => "http://s1novac.oncloud.gr/s1services",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "{\n\t\"service\": \"login\", \n\t\"username\": \"eshop1\", \n\t\"password\": \"novaceshop\", \n\t\"appId\": \"1050\"\n}\n",
      CURLOPT_HTTPHEADER => [
        "content-type: application/json"
      ],
    ]);


    $responseLogin = curl_exec($curlLogin);
    $err = curl_error($curlLogin);

    $responseLogin=utf8_encode($responseLogin);
    $jsonLogin = json_decode($responseLogin);



    curl_close($curlLogin);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $responseLogin;
    }

    fwrite($myfile, "2 responseLogin->clientID\n");
    fwrite($myfile, $jsonLogin->clientID);
    fwrite($myfile, "\n");
    
    $COMPANY = $jsonLogin->objs[0]->COMPANY;
    $BRANCH = $jsonLogin->objs[0]->BRANCH;
    $MODULE = $jsonLogin->objs[0]->MODULE;
    $REFID = $jsonLogin->objs[0]->REFID;
    $USERID = $jsonLogin->objs[0]->USERID;


    //auth__________________________________________________________________________________________________________________________________________________________________________________________________________
    $curlAuth = curl_init();

    curl_setopt_array($curlAuth, [
      CURLOPT_URL => "http://s1novac.oncloud.gr/s1services",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "{\n\t\"service\": \"authenticate\", \n\t\"clientID\": \"$jsonLogin->clientID\", \n\t\"COMPANY\": \"$COMPANY\", \n\t\"BRANCH\": \"$BRANCH\", \n\t\"MODULE\": \"$MODULE\", \n\t\"REFID\": \"$REFID\", \n\t\"USERID\": \"$USERID\"}\n}\n",
      CURLOPT_HTTPHEADER => [
        "content-type: application/json"
      ],
    ]);

    $responseAuth = curl_exec($curlAuth);
    $err = curl_error($curlAuth);

    curl_close($curlAuth);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $responseAuth;
    }

	$newResponseAuth = mb_convert_encoding($responseAuth, 'UTF-8', mb_detect_encoding($responseAuth, 'UTF-8, ISO-8859-7', true));

	//$newResponseAuth = iconv("UTF-8", "windows-1253", $responseAuth);

	fwrite($myfile, "3 responseAuth\n");
    fwrite($myfile, $newResponseAuth);
    fwrite($myfile, "\n");
    
    //$newResponseAuth=utf8_encode($newResponseAuth);
    $jsonAuth = json_decode($newResponseAuth);

	fwrite($myfile, "3.3 testjson\n");
    fwrite($myfile, $jsonAuth->companyinfo);
    fwrite($myfile, "\n");
    

    fwrite($myfile, "EDW?");
    $authClientID = $jsonAuth->clientID;
    fwrite($myfile, "EDW1111?");
    fwrite($myfile, $jsonAuth->clientID);
    fwrite($myfile, "\n");

$curlProd = curl_init();

curl_setopt_array($curlProd, [
  CURLOPT_URL => "http://s1novac.oncloud.gr/s1services",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "{\n\n    \"service\": \"sqlData\",\n\n    \"clientID\": \"$authClientID\",\n\n    \"appId\": \"1050\",\n\n\t\t\"SqlName\": \"GETITEMS\"\n}",
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json"
  ],
]);

fwrite($myfile, "4 tring\n");

$getItemsResponse = curl_exec($curlProd);
$err = curl_error($curlProd);

curl_close($curlProd);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $getItemsResponse;
}

$newResponseGetItems = mb_convert_encoding($getItemsResponse, 'UTF-8', mb_detect_encoding($getItemsResponse, 'UTF-8, ISO-8859-7', true));
$dec = json_decode($newResponseGetItems);

$a=0;

$products = array();
if (! empty($dec->rows)) {
    foreach ($dec->rows as $row) {
        $user['ESHOPID'] = $row->ESHOPID;
        $user['BALANCE'] = $row->BALANCE;
        $products[] = $row;
        fwrite($myfile, date("Y/m/d")."wooId= '".$row->ESHOPID."', Balance='".$row->BALANCE."'");

        $wooReadcurl = curl_init();
        curl_setopt_array($wooReadcurl, [
            CURLOPT_URL => "https://novac.gr/wp-json/wc/v3/products/\"$row->ESHOPID\"",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => [
            "Authorization: Basic Y2tfZTllNDAwN2M1YWYyM2QzNDkzZjc4NGQ4YzkxYjQ1Y2FhMGFkYWU5NTpjc18xMzUyMzcwM2ZjNDkwZDJhNjNlYmNhZmI5OTQ0MzM0M2EwYjljZWIy",
            "Content-Type: application/json"
        ],    
        ]);
        
        $responsePeadProd = curl_exec($wooReadcurl);
        if (!empty($responsePeadProd))
        {
            $err = curl_error($wooReadcurl);
            $newResponseReadWooProd = mb_convert_encoding($responsePeadProd, 'UTF-8', mb_detect_encoding($responsePeadProd, 'UTF-8, ISO-8859-7', true));
            curl_close($wooReadcurl);
            $jsonReadProd = json_decode($newResponseReadWooProd);
            fwrite($myfile, "1->".$jsonReadProd->sku." : ".$jsonReadProd->name."'\n");

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {     
                $wooBalance = $jsonReadProd->stock_quantity;
                echo $wooBalance."<br>";
                fwrite($myfile, ",wooBalance= '".$wooBalance." ");

                if ($wooBalance <> $row->BALANCE) {
                    echo("<br>Πρέπει να γίνει ενημέρωση<br>");
                    fwrite($myfile, "Πρέπει να γίνει ενημέρωση wooBalance='".$wooBalance."' soft1Balance='".$row->BALANCE."'"." ");

                    $curlUpdateWoo = curl_init();
                    curl_setopt_array($curlUpdateWoo, [
                        CURLOPT_URL => "https://novac.gr/wp-json/wc/v3/products/23272",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "PUT",
                        CURLOPT_POSTFIELDS => "{\n\t\"manage_stock\":\"true\",\n\t\"stock_quantity\":\"0\"\n}",
                        CURLOPT_HTTPHEADER => [
                            "Authorization: Basic Y2tfZTllNDAwN2M1YWYyM2QzNDkzZjc4NGQ4YzkxYjQ1Y2FhMGFkYWU5NTpjc18xMzUyMzcwM2ZjNDkwZDJhNjNlYmNhZmI5OTQ0MzM0M2EwYjljZWIy",
                            "Content-Type: application/json"
                        ],
                    ]);
                    $response = curl_exec($curlUpdateWoo);
                    $err = curl_error($curlUpdateWoo);

                    curl_close($curlUpdateWoo);

                    if ($err) {
                        echo "cURL Error #:" . $err;
                    } else {
                    echo $response;
                    fwrite($myfile, " Έγινε η ενημέρωση\n");
                }
            }
        } 
    }         
}


    fwrite($myfile, "4.1 read products\n");
    fwrite($myfile, $newResponseGetItems->companyinfo);
    fwrite($myfile, "\n");
}

fclose($myfile);

?>