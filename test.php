<html>
<body>
<p>
<?php
   $files = `ls -ltm1 videos/*.swf`;
   $foo = preg_split('/\s+/', $files);
   $complete = array();
   foreach ($foo as $toolong) {
      array_push($complete, substr($toolong, 7));
   }
   print_r($complete);
   $filelist = explode(" ", $files);
   //print_r($filelist);
   foreach ($filelist as $filename) {
      //echo $filename;
      $last = explode(".", $filename);
      //print_r($last);
      $ext = end($last);
      if ($ext == "swf") {
         //echo $filename;
      }
   }
   //print_r($filelist);
?>
</body>
</html>
