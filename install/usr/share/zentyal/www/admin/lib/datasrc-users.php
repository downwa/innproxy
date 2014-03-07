 <?php
 
  $datasrc_users="/var/lib/innproxy/users/users.txt";
  
  header("Content-language: en");
  header("Content-type: text/plain; charset=UTF-8");
  
  $users[0]=array();
  $users[0]['username']='test';
  $users[0]['password']='PASSWD';
  
  echo json_encode($users);
 
 ?>
