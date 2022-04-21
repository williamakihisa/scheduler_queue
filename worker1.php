<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

$token = trim(file_get_contents('secret.key'));
$auth = 0;
$worker1 = intval(trim(file_get_contents('worker1.stats')));
$worker2 = intval(trim(file_get_contents('worker2.stats')));
$json = json_decode(file_get_contents('quelist.json'), true);

if (isset($_REQUEST['token']) && isset($_REQUEST['start'])){
   if ($token == trim($_REQUEST['token']) && $worker2 == 0){
      if ($worker1 == 0 && count($json) == 0){
         $worker1 = 1;
         file_put_contents('worker1.stats', 1);
      }else{
        die('worker already process!');
      }
   }else{
     die('invalid token');
   }
}

if (isset($_REQUEST['token']) && isset($_REQUEST['finish'])){
  if ($token == trim($_REQUEST['token']) && $worker2 == 0){
    $auth = 0;
    file_put_contents('worker1.stats', 0);
    $worker1 = 0;
    file_put_contents('log_que.txt', trim(date('Y-m-d H:i:s').' : '.implode(",",$json)).PHP_EOL, FILE_APPEND);
  }else{
    die('invalid token');
  }
}

if ($worker2 == 1) $worker1 = 0;

if ($worker1 == 1 && isset($_REQUEST['url'])){
  if (!in_array($_REQUEST['url'], $json)){
     $json[] = $_REQUEST['url'];
     file_put_contents('quelist.json', json_encode($json));
     echo 'url submitted!';
  }else{
    echo 'url already in queue!';
  }
}else{
  echo 'worker not started or url not sent';
}



 ?>
