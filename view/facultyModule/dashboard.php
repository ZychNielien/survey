<?php

include "components/navBar.php";

?>

<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../public/css/style.css">
    <script src="../../public/js/chart.js"></script>
</head>

<section class="contentContainer ">
    <div class="contentHeader d-flex justify-content-center my-3">
        <h3 class="fw-bold text-danger">Faculty Dashboard</h3>
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