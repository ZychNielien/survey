<?php

$con = new mysqli("localhost", "root", "", "super-final-fep-bsu");

if (!$con) {
    die(mysqli_error($con));
}

?>