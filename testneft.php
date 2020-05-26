<?php

// set_time_limit(100);
set_time_limit(28800);


$db = new SQLite3('C:\sqlite\logs2.sq3');

$sqlCreateTable = "CREATE TABLE Visits (
    log_date VARCHAR(50),
    log_time VARCHAR(50),
    user_login VARCHAR(50),
    server_url VARCHAR(50),
    site_url VARCHAR(50),
    web_url VARCHAR(50),
    document_path VARCHAR(100))";
$signal = $db->query($sqlCreateTable);

$filesReadFrom = ["SRZWebAnaluticsByModules_2018-11-06.csv", "SRZWebAnaluticsByModules_2018-11-07.csv"];

foreach ($filesReadFrom as $filename) {
    $row = 1;
  if (($handle = fopen($filename, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      if ($row > 1) {
        $date = new DateTime($data[0]);
        $result = array();
        $result["date"] = $date->format('d.m.Y');
        $result["time"] = $date->format('H:m:s');
        $result["userLogin"] = substr($data[1], 7);
        $result["serverUrl"] = $data[2];
        $result["siteUrl"] = $data[3];
        $result["webUrl"] = $data[4];
        $result["documentPath"] = $data[5]; 
        
        insertTask($db, $result);
      }
      $row++;
    }
    fclose($handle);
  }
}


function insertTask($db, $result) {

    $stmt = $db->prepare('INSERT INTO Visits VALUES (:log_date, :log_time, :user_login, :server_url, :site_url, :web_url, :document_path);');

    $stmt->bindValue(':log_date', $result["date"]);
    $stmt->bindValue(':log_time', $result["time"]);
    $stmt->bindValue(':user_login', $result["userLogin"]);
    $stmt->bindValue(':server_url', $result["serverUrl"]);
    $stmt->bindValue(':site_url', $result["siteUrl"]);
    $stmt->bindValue(':web_url', $result["webUrl"]);
    $stmt->bindValue(':document_path', $result["documentPath"]);

    return $stmt->execute();
}


?>