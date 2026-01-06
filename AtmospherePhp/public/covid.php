<?php
global $dates, $casConfirmes, $deces, $hospitalises, $reanimation, $gueris;
require_once __DIR__ . "/../config/bootstrap.php";
require_once __DIR__ . "/../action/GetCovidInfo.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord COVID-19 (Mars 2020 - 2022)</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f4f9; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        h1 { text-align: center; color: #333; margin-bottom: 40px; }
        .chart-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Évolution COVID-19 (Mars 2020)</h1>

    <div class="chart-box">
        <canvas id="chartHospital"></canvas>
    </div>

    <div class="chart-box">
        <canvas id="chartGlobal"></canvas>
    </div>
</div>

<script>
    // Récupération des données PHP vers JS
    const labels = <?php echo json_encode($dates); ?>;
    const dataCas = <?php echo json_encode($casConfirmes); ?>;
    const dataDeces = <?php echo json_encode($deces); ?>;
    const dataHosp = <?php echo json_encode($hospitalises); ?>;
    const dataRea = <?php echo json_encode($reanimation); ?>;
    const dataGueris = <?php echo json_encode($gueris); ?>;

    // --- Configuration Graphique 1 : Situation Hospitalière ---
    const ctxHosp = document.getElementById('chartHospital').getContext('2d');
    new Chart(ctxHosp, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Hospitalisations en cours',
                    data: dataHosp,
                    borderColor: 'rgb(54, 162, 235)', // Bleu
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'En Réanimation',
                    data: dataRea,
                    borderColor: 'rgb(255, 159, 64)', // Orange
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Guéris (Cumul)',
                    data: dataGueris,
                    borderColor: 'rgb(75, 192, 192)', // Vert
                    borderDash: [5, 5], // Ligne pointillée
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Pression Hospitalière et Guérisons' },
                tooltip: { mode: 'index', intersect: false }
            },
            interaction: { mode: 'nearest', axis: 'x', intersect: false }
        }
    });

    // --- Configuration Graphique 2 : Vue Globale (Cas vs Décès) ---
    const ctxGlobal = document.getElementById('chartGlobal').getContext('2d');
    new Chart(ctxGlobal, {
        type: 'bar', // On mélange Bar et Line
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'line',
                    label: 'Cas Confirmés (Cumul)',
                    data: dataCas,
                    borderColor: 'rgb(153, 102, 255)', // Violet
                    borderWidth: 2,
                    yAxisID: 'y'
                },
                {
                    type: 'bar',
                    label: 'Décès (Cumul)',
                    data: dataDeces,
                    backgroundColor: 'rgb(255, 99, 132)', // Rouge
                    yAxisID: 'y1' // Axe Y séparé car l'échelle est différente
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Propagation du virus et Mortalité' }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Cas Confirmés' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Décès' },
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
</script>

</body>
</html>
