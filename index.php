<?php
include 'config.php';

function getDataRaw($spreadsheetId, $apiKey)
{
    $url = "https://sheets.googleapis.com/v4/spreadsheets/" . $spreadsheetId . "/values/Punktacja!B2:E";
    $url .= "?key=" . $apiKey;
    $curlSession = curl_init();
    curl_setopt($curlSession, CURLOPT_URL, $url);
    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
    $jsonData = json_decode(curl_exec($curlSession));
    curl_close($curlSession);
    return $jsonData;
}
$data = getDataRaw($spreadsheetId, $apiKey)->values;
include 'content.php';
