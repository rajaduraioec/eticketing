<?php
$servername = "localhost";
$username = "mmttechm_demo";
$password = "LU;tY!VGksgF";
$database = "mmttechm_demo";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

if(! $conn ) {
            die('Could not connect: ' . mysql_error());
         }
        //  echo 'Connected successfully';
         mysql_close($conn);
?>
