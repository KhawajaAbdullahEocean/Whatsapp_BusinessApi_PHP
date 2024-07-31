<?php

require 'mysql_con.php';
require 'tickets_db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Whatsapp chat bot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">


</head>
<body>
<div class="container mt-4">
<h1 class="text-center mb-3">Support Tickets</h1>

<table class="table table-primary table-bordered table-responsive">
    <tr>
            <th>Contact</th>
            <th>Complain</th>
    </tr>
    <?php
    while($row=mysqli_fetch_array($tickets,MYSQLI_ASSOC)){
        echo "<tr>";
 echo '<td>'.$row['contact'].'</td>';
 echo '<td>'.substr($row['msg_content'],1).'</td>';
 echo "</tr>";
    }

    ?>
</table>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>