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
    <table id="table-interets" class="table-centered">
      <thead>
        <tr>
          <th>ID Prêt</th>
          <th>ID Client</th>
          <th>Montant</th>
          <th>Durée (mois)</th>
          <th>Date demande</th>
          <th>Taux (%)</th>
          <th>Intérêt gagné</th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
        <tr>
          <th colspan="5">Total</th>
          <th id="total-taux"></th>
          <th id="total-interet"></th>
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
  ajax("GET", `/interets?mois_debut=${md}&annee_debut=${ad}&mois_fin=${mf}&annee_fin=${af}`, null, function(data) {
    const tbody = document.querySelector('#table-interets tbody');
    let totalTaux = 0;
    let totalInteret = 0;
    tbody.innerHTML = '';
    data.forEach(row => {
      tbody.innerHTML += `<tr>
        <td>${row.id_pret}</td>
        <td>${row.id_client}</td>
        <td>${parseFloat(row.montant).toLocaleString('fr-FR', {minimumFractionDigits:2})}</td>
        <td>${row.duree}</td>
        <td>${row.date_demande}</td>
        <td>${parseFloat(row.taux_annuel).toFixed(2)}</td>
        <td>${parseFloat(row.interet_gagne).toLocaleString('fr-FR', {minimumFractionDigits:2})}</td>
      </tr>`;
      totalTaux += parseFloat(row.taux_annuel);
      totalInteret += parseFloat(row.interet_gagne);
    });
    document.getElementById('total-taux').textContent = totalTaux.toFixed(2);
    document.getElementById('total-interet').textContent = totalInteret.toLocaleString('fr-FR', {minimumFractionDigits:2});
    // Afficher le graphique professionnel en courbe
    const ctx = document.getElementById('chart-interets').getContext('2d');
    if(window.interetChart) window.interetChart.destroy();
    // Dégradé pour la courbe
    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(54,162,235,0.5)');
    gradient.addColorStop(1, 'rgba(54,162,235,0.05)');
    window.interetChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.map(r => `Prêt #${r.id_pret}`),
        datasets: [{
          label: 'Intérêts gagnés',
          data: data.map(r => r.interet_gagne),
          fill: true,
          backgroundColor: gradient,
          borderColor: '#2a4d69',
          pointBackgroundColor: '#4b86b4',
          pointBorderColor: '#fff',
          pointRadius: 5,
          pointHoverRadius: 8,
          tension: 0.35,
          borderWidth: 3,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: true,
            labels: { color: '#2a4d69', font: { size: 15, weight: 'bold' } }
          },
          title: {
            display: true,
            text: 'Évolution des intérêts gagnés par prêt',
            color: '#2a4d69',
            font: { size: 20, weight: 'bold' },
            padding: { top: 10, bottom: 20 }
          },
          tooltip: {
            enabled: true,
            callbacks: {
              label: function(context) {
                return 'Intérêt: ' + parseFloat(context.parsed.y).toLocaleString('fr-FR', {minimumFractionDigits:2}) + ' DZD';
              }
            },
            backgroundColor: '#2a4d69',
            titleColor: '#fff',
            bodyColor: '#fff',
            borderColor: '#4b86b4',
            borderWidth: 1
          }
        },
        scales: {
          x: {
            title: { display: true, text: 'Prêt', color: '#2a4d69', font: { size: 15 } },
            ticks: { color: '#2a4d69', font: { size: 13 } }
          },
          y: {
            title: { display: true, text: 'Intérêt gagné (DZD)', color: '#2a4d69', font: { size: 15 } },
            ticks: { color: '#2a4d69', font: { size: 13 } },
            beginAtZero: true
          }
        }
      }
    });
  });
};
  </script>
</body>
</html>
