<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="css/main.css">
  <title>Intérêts gagnés par prêt</title>
  <script src="js/chart.js"></script>
  <!-- Le CSS est maintenant dans main.css -->
</head>
<body>
  <?php include('sidebar.php'); ?>
  <div class="main-section">
    <h2>Détail des intérêts gagnés par prêt</h2>
    <form id="filtre-interets">
      <div>
        <label for="mois_debut">Mois début:</label>
        <select id="mois_debut" required>
          <option value="1">Janvier</option>
          <option value="2">Février</option>
          <option value="3">Mars</option>
          <option value="4">Avril</option>
          <option value="5">Mai</option>
          <option value="6">Juin</option>
          <option value="7">Juillet</option>
          <option value="8">Août</option>
          <option value="9">Septembre</option>
          <option value="10">Octobre</option>
          <option value="11">Novembre</option>
          <option value="12">Décembre</option>
        </select>
      </div>
      <div>
        <label for="annee_debut">Année début:</label>
        <input type="number" min="2000" max="2100" id="annee_debut" required>
      </div>
      <div>
        <label for="mois_fin">Mois fin:</label>
        <select id="mois_fin" required>
          <option value="1">Janvier</option>
          <option value="2">Février</option>
          <option value="3">Mars</option>
          <option value="4">Avril</option>
          <option value="5">Mai</option>
          <option value="6">Juin</option>
          <option value="7">Juillet</option>
          <option value="8">Août</option>
          <option value="9">Septembre</option>
          <option value="10">Octobre</option>
          <option value="11">Novembre</option>
          <option value="12">Décembre</option>
        </select>
      </div>
      <div>
        <label for="annee_fin">Année fin:</label>
        <input type="number" min="2000" max="2100" id="annee_fin" required>
      </div>
      <div style="min-width:180px;flex:1 1 180px;">
        <button type="submit">Filtrer</button>
      </div>
    </form>
    <h3 style="margin-top:2.5rem;">Intérêts gagnés par mois (période choisie)</h3>
    <table id="table-interets" class="table-centered">
      <thead>
        <tr>
          <th>ID Prêt</th>
          <th>ID Client</th>
          <th>Montant</th>
          <th>Durée (mois)</th>
          <th>Date demande</th>
          <th>Taux (%)</th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
        <tr>
          <th colspan="5">Total</th>
          <th id="total-taux"></th>
        </tr>
      </tfoot>
    </table>
        <table id="table-interets-mois" class="table-centered" style="margin-bottom:2rem;">
      <thead>
        <tr>
          <th>Mois (période)</th>
          <th>Année</th>
          <th>Intérêt gagné</th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
        <tr>
          <th colspan="2">Total</th>
          <th id="total-interet-mois"></th>
        </tr>
      </tfoot>
    </table>
    <canvas id="chart-interets" height="300"></canvas>
  </div>
  <script>
const apiBase = "ws";
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

document.getElementById('filtre-interets').onsubmit = function(e) {
  e.preventDefault();
  const md = document.getElementById('mois_debut').value;
  const ad = document.getElementById('annee_debut').value;
  const mf = document.getElementById('mois_fin').value;
  const af = document.getElementById('annee_fin').value;
  // Charger les intérêts par mois
  ajax("GET", `/interets/mois?mois_debut=${md}&annee_debut=${ad}&mois_fin=${mf}&annee_fin=${af}`, null, function(data) {
    const tbody = document.querySelector('#table-interets-mois tbody');
    let total = 0;
    tbody.innerHTML = '';
    // Préparer les données pour la courbe
    const labels = [];
    const dataPoints = [];
    data.forEach(row => {
      tbody.innerHTML += `<tr>
        <td>${row.mois_periode}</td>
        <td>${row.annee}</td>
        <td>${parseFloat(row.interet_gagne).toLocaleString('fr-FR', {minimumFractionDigits:2})}</td>
      </tr>`;
      total += parseFloat(row.interet_gagne);
      labels.push(`${row.mois_periode} ${row.annee}`);
      dataPoints.push(parseFloat(row.interet_gagne));
    });
    document.getElementById('total-interet-mois').textContent = total.toLocaleString('fr-FR', {minimumFractionDigits:2});
    // Affichage de la courbe Chart.js
    if(window.interetsChart) window.interetsChart.destroy();
    const ctx = document.getElementById('chart-interets').getContext('2d');
    window.interetsChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Intérêts gagnés',
          data: dataPoints,
          borderColor: '#1de9b6',
          backgroundColor: 'rgba(29,233,182,0.1)',
          fill: true,
          tension: 0.3
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: true },
          title: { display: true, text: 'Intérêts gagnés par mois', color: '#2a4d69', font: { size: 18 } }
        },
        scales: {
          x: { title: { display: true, text: 'Mois', color: '#2a4d69' } },
          y: { title: { display: true, text: 'Intérêt', color: '#2a4d69' }, beginAtZero: true }
        }
      }
    });
  });
  ajax("GET", `/interets?mois_debut=${md}&annee_debut=${ad}&mois_fin=${mf}&annee_fin=${af}`, null, function(data) {
    const tbody = document.querySelector('#table-interets tbody');
    let totalTaux = 0;
    tbody.innerHTML = '';
    data.forEach(row => {
      tbody.innerHTML += `<tr>
        <td>${row.id_pret}</td>
        <td>${row.id_client}</td>
        <td>${parseFloat(row.montant).toLocaleString('fr-FR', {minimumFractionDigits:2})}</td>
        <td>${row.duree}</td>
        <td>${row.date_demande}</td>
        <td>${parseFloat(row.taux_annuel).toFixed(2)}</td>
      </tr>`;
      totalTaux += parseFloat(row.taux_annuel);
    });
    document.getElementById('total-taux').textContent = totalTaux.toFixed(2);
    // Le graphique peut être adapté ou supprimé si non pertinent
  });
};
  </script>
</body>
</html>
