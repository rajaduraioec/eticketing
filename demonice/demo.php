<?php

include 'connection.php';



$sql = "SELECT depot_name,config_status,active FROM depots ";
$result = mysqli_query($conn,$sql);

if (mysqli_num_rows($result)>0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
         echo "Depot: " . $row["depot_name"]. " Routed Bus: " . $row["config_status"]. " Amount (CFA): " . $row["active"]. "<br>";
    }
} else {
    echo "0 results";
}
mysqli_close($conn);

?>
