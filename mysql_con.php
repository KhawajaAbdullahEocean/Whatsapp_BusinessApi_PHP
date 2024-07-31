<?php

$connection = mysqli_connect("localhost", "root", "", "chatbot");
if (!$connection) {
    die("Connection failed".mysqli_connect_error());
} else {

    // echo "Connection established";
    //  $query="select * from msgs";
    // $stmt=mysqli_query($connection,$query);
    // $rowcount=mysqli_num_rows($stmt);
    //  echo "total employyes are: ".$rowcount;
    // echo "<br>"
    // while($row=mysqli_fetch_array($stmt,MYSQLI_ASSOC)){
    //     echo $row['id']." ";
    //     echo $row['name'];
    //     echo $row['salary'];
    //     echo "<br>";
    // }
}
?>