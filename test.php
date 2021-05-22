<?php

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
}

?>