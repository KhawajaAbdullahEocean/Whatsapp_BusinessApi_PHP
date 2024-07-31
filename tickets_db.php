<?php

$query="select * from msgs where query=1";
$tickets=mysqli_query($connection,$query);
$rowcount=mysqli_num_rows($tickets);
// echo "Total tickets are $rowcount";

?>