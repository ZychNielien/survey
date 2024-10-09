<?php

include "components/navBar.php";

?>

<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .header-content {
            background-color: #d0112b;
            border-radius: 10px;
            color: #fff;
            letter-spacing: 2px;
        }

        .contentContainer {
            position: relative;
            height: 100px;
            margin: 0 20px;
            margin-left: 310px;
            height: calc(100% / 3 - 2px);
            transition: all 0.5s ease;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .sidebar.close~.contentContainer {
            left: 105px;
            width: 93%;
            transition: all 0.5s ease-out;
            margin-left: 10px;
        }
    </style>
</head>
<section class="contentContainer ">
    <div class="contentHeader d-flex justify-content-center my-3">
        <h3 class="fw-bold text-danger">ADMIN Dashboard</h3>
    </div>
    <div class="row mx-5 d-flex justify-content-between">
        <div class="col-md-6">
            <h4>Bar Chart 1</h4>
            <canvas id="barChart1"></canvas>
        </div>
        <div class="col-md-6">
            <h4>Bar Chart 2</h4>
            <canvas id="barChart2"></canvas>
        </div>
    </div>


</section>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Bar Chart 1
    const ctxBar1 = document.getElementById('barChart1').getContext('2d');
    const barChart1 = new Chart(ctxBar1, {
        type: 'bar',
        data: {
            labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
            datasets: [{
                label: 'Votes for Chart 1',
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Bar Chart 2
    const ctxBar2 = document.getElementById('barChart2').getContext('2d');
    const barChart2 = new Chart(ctxBar2, {
        type: 'bar',
        data: {
            labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
            datasets: [{
                label: 'Votes for Chart 2',
                data: [5, 10, 15, 20, 25, 30],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>