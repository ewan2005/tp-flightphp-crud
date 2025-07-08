<?php
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit();
}
require_once 'ws/controllers/DashboardController.php';
$stats = DashboardController::getStats();
$nbClients = $stats['nbClients'];
$nbPrets = $stats['nbPrets'];
$montantTotal = $stats['montantTotal'];
$interets = $stats['interets'];
?>
<!DOCTYPE html>
<html lang="en" class="">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/main.css?v=1628755089081">
</head>

<body>
  <?php include('sidebar.php'); ?>
  <section class="section dashboard-section" style="margin-left: 260px;width:80%;">
    <h2 class="dashboard-title">Tableau de bord</h2>
    <div class="dashboard-cards">
      <div class="card">
        <div class="card-content">
          <div class="flex items-center justify-between">
            <div class="widget-label">
              <h3>Clients</h3>
              <h1><?= $nbClients ?></h1>
            </div>
            <span class="icon widget-icon text-green-500"><i class="mdi mdi-account-multiple mdi-48px"></i></span>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-content">
          <div class="flex items-center justify-between">
            <div class="widget-label">
              <h3>Prêts</h3>
              <h1><?= $nbPrets ?></h1>
            </div>
            <span class="icon widget-icon text-blue-500"><i class="mdi mdi-cash-multiple mdi-48px"></i></span>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-content">
          <div class="flex items-center justify-between">
            <div class="widget-label">
              <h3>Montant prêté</h3>
              <h1><?= number_format($montantTotal, 2, ',', ' ') ?> Ar</h1>
            </div>
            <span class="icon widget-icon text-red-500"><i class="mdi mdi-finance mdi-48px"></i></span>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-content">
          <div class="flex items-center justify-between">
            <div class="widget-label">
              <h3>Intérêts générés</h3>
              <h1><?= number_format($interets, 2, ',', ' ') ?> Ar</h1>
            </div>
            <span class="icon widget-icon text-yellow-500"><i class="mdi mdi-percent mdi-48px"></i></span>
          </div>
        </div>
      </div>
    </div>
    <div class="card mb-6">
      <header class="card-header">
        <p class="card-header-title">
          <span class="icon"><i class="mdi mdi-finance"></i></span>
          Performance
        </p>
        <a href="#" class="card-header-icon">
          <span class="icon"><i class="mdi mdi-reload"></i></span>
        </a>
      </header>
      <div class="card-content">
        <div class="chart-area" style="max-width:600px;margin:0 auto;">
          <canvas id="big-line-chart" height="220" style="max-width:100%;"></canvas>
        </div>
      </div>
    </div>
  </section>
  <!-- Scripts below are for demo only -->
  <script type="text/javascript" src="js/main.min.js?v=1628755089081"></script>
  <script type="text/javascript" src="js/chart.js"></script>
  <script>
    // Exemple de données dynamiques pour le dashboard
    const ctx = document.getElementById('big-line-chart').getContext('2d');
    const chartData = {
      labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
      datasets: [
        {
          label: 'Clients',
          data: [12, 19, 15, 22, 30, 25, 28, 32, 35, 40, 45, 50],
          borderColor: '#1de9b6',
          backgroundColor: 'rgba(29,233,182,0.1)',
          fill: false,
          tension: 0.3
        },
        {
          label: 'Prêts',
          data: [8, 14, 10, 18, 22, 20, 24, 27, 29, 33, 36, 38],
          borderColor: '#2196f3',
          backgroundColor: 'rgba(33,150,243,0.1)',
          fill: false,
          tension: 0.3
        },
        {
          label: 'Montant prêté',
          data: [1000, 2000, 1500, 3000, 3500, 3200, 4000, 4200, 4500, 4800, 5000, 5200],
          borderColor: '#f44336',
          backgroundColor: 'rgba(244,67,54,0.1)',
          fill: false,
          tension: 0.3
        }
      ]
    };
    new Chart(ctx, {
      type: 'line',
      data: chartData,
      options: {
        responsive: true,
        plugins: {
          legend: { display: true },
          title: { display: true, text: 'Évolution annuelle', color: '#2a4d69', font: { size: 18 } }
        },
        scales: {
          x: { title: { display: true, text: 'Mois', color: '#2a4d69' } },
          y: { title: { display: true, text: 'Valeur', color: '#2a4d69' }, beginAtZero: true }
        }
      }
    });
  </script>
</body>

</html>


<script>
  const apiBase = "http://localhost:8888/tp-flightphp-crud/ws";

  function ajax(method, url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, apiBase + url, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = () => {
      if (xhr.readyState === 4 && xhr.status === 200) {
        callback(JSON.parse(xhr.responseText));
      }
    };
    xhr.send(data);
  }
</script>