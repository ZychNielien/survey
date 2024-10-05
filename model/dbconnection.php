<?php

    $con = new mysqli ("localhost", "root", "", "fep_bsu");

    if(!$con) {
        die(mysqli_error($con));
    }

?>