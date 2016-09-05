<?php
// Created by: CyberGuard
// Contact: CyberGuard@hotmail.com
// Keybase: https://keybase.io/CyberGuard
// Twitter: @RealCyberGuard

$host   = 'localhost'; // Keep this at localhost if you don't know what you're doing.
$dbuser = 'username'; // Database username
$dbpass = 'password'; // Database password
$db     = 'databasename'; // Database you ran install.sql on

$timelimit    = 10; // amount of seconds to look at
$requestlimit = 15; // amount of requests to allow in the time limit before blocking the ip
$secondblock  = 3600; // amount of seconds to block the IP address for


if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) { // if cloudflare is used, grab the real ip address
  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}


$dbx= new PDO('mysql:host={host};dbname={$database}', $dbuser, $dbpass);

    $stmt  = $dbx->prepare("SELECT `type` FROM `ipblacklist` WHERE `ip`=:ip");
    $stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
    $stmt->execute();
    $type = $stmt->fetchColumn(0); // Quick check if the IP is blocked or not.
    if(!empty($type)) {
    
      switch($type) { // defines the type of block you want to give the user.
        case 1:
          header('HTTP/1.0 404 Not Found');
          echo "Access Denied";
          exit();
        break;
        case 2:
          header('HTTP/1.0 404 Not Found');
          echo "Not Found";
          exit();
        break;
        case 3:
          header('HTTP/1.0 403 Forbidden');
          echo "You triggered our firewall - You were blocked out for an hour";
          exit();
        break;
      }
    }
  $time=time();
  $stmt=$dbx->prepare("DELETE FROM `ipblacklist` WHERE expire<{$time}");
  $stmt->execute();


$time=time()-$timelimit;
$stmt  = $dbx->prepare("SELECT count(ip) FROM `visits` WHERE `ip`=:ip and `time`>{$time}");
    $stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
    $stmt->execute();
    $count = $stmt->fetchColumn(0);
    if($count>$requestlimit) {
      $expire=time()+$secondsblock;
      $stmt  = $dbx->prepare("INSERT INTO `ipblacklist` VALUES(:ip,:expire,3)");
      $stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
      $stmt->bindParam(":expire", $expire, PDO::PARAM_STR);
      $stmt->execute();
    }

$time=time();
if($_SERVER['REMOTE_ADDR'] != "1.1.1.1") { // optional whitelist for a specific IP
$stmt  = $dbx->prepare("INSERT INTO `visits` values(:ip,{$time})");
    $stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
    $stmt->execute();
}

unset($time); // prevent messing with other scripts
unset($stmt);
unset($dbx);
unset($dbuser);
unset($dbpass);
unset($timelimit);
unset($requestlimit);
unset($secondblock);
