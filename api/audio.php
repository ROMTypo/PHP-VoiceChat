<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if($_SERVER['REQUEST_METHOD']==="POST"){
  $active=file_get_contents("vc.txt");
  $active=$_POST['data'];
  file_put_contents("vc.txt",$active);
  exit("saved");
}else{
  exit(file_get_contents("vc.txt"));
}