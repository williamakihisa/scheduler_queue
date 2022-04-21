<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

$worker1 = intval(trim(file_get_contents('worker1.stats')));
$worker2 = intval(trim(file_get_contents('worker2.stats')));
$json = json_decode(file_get_contents('quelist.json'), true);
$logs = file_get_contents('logger.txt');
$proceed = 0;
$urltorun = '';

if ($worker2 == 0){
   if ($worker1 == 0 && count($json) > 0){
      file_put_contents('worker2.stats', 1);
      $proceed = $worker2 = 1;
   }else{
     file_put_contents('worker2.stats', 0);
     $proceed = $worker2 = 0;
   }
}else{
  if (count($json) > 0){
    $proceed = 1;
  }else{
    file_put_contents('worker2.stats', 0);
  }
}

if ($worker2 == 1 && $proceed == 1){
  $urltorun = $json[0];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $urltorun);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $output = curl_exec($ch);
  curl_close($ch);

  if ($output){
    echo $output;
    unset($json[0]);
    $json = array_values($json);
    file_put_contents('quelist.json', json_encode($json));
    file_put_contents('logger.txt', trim(date('Y-m-d H:i:s').' : '.$output).PHP_EOL, FILE_APPEND);
  }else{
    echo 'failed to fetch, retry again later';
  }
}else{
  echo 'no worker2 available!';
}
