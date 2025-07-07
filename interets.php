<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Intérêts gagnés par mois</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    table { border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; }
    th { background: #f2f2f2; }
  </style>
</head>
<body>
  <h2>Intérêts gagnés par mois</h2>
  <form id="filtre-interets">
    Mois début: <input type="number" min="1" max="12" id="mois_debut" required>
    Année début: <input type="number" min="2000" max="2100" id="annee_debut" required>
    Mois fin: <input type="number" min="1" max="12" id="mois_fin" required>
    Année fin: <input type="number" min="2000" max="2100" id="annee_fin" required>
    <button type="submit">Filtrer</button>
  </form>
  <table id="table-interets">
    <thead>
      <tr><th>Mois</th><th>Année</th><th>Intérêts gagnés</th></tr>
    </thead>
    <tbody></tbody>
  </table>
  <canvas id="chart-interets" width="600" height="300"></canvas>
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
    // Remplir le tableau
    const tbody = document.querySelector('#table-interets tbody');
    tbody.innerHTML = '';
    data.forEach(row => {
      tbody.innerHTML += `<tr><td>${row.mois}</td><td>${row.annee}</td><td>${parseFloat(row.interet_gagne).toFixed(2)}</td></tr>`;
    });
    // Afficher le graphique
    const ctx = document.getElementById('chart-interets').getContext('2d');
    if(window.interetChart) window.interetChart.destroy();
    window.interetChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: data.map(r => `${r.mois}/${r.annee}`),
        datasets: [{
          label: 'Intérêts gagnés',
          data: data.map(r => r.interet_gagne),
          backgroundColor: 'rgba(54, 162, 235, 0.5)'
        }]
      }
    });
  });
};
  </script>
</body>
</html>
