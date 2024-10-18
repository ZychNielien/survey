<?php

$con = new mysqli("localhost", "root", "", "final-fep-bsu");

if (!$con) {
    die(mysqli_error($con));
}

?>