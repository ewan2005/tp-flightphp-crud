<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="css/main.css">
  <title>Intérêts gagnés par mois</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    table { border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; }
    th { background: #f2f2f2; }
  </style>
</head>
<body>
    <div class="main-section" style="max-width:1100px;width:95vw;min-height:70vh;">
      <?php include('sidebar.php'); ?>
      <h2>Intérêts gagnés par mois</h2>
      <form id="filtre-interets" style="width:100%;display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:2rem;align-items:end;max-width:900px;margin:0 auto 2rem auto;">
        <div>
          <label for="mois_debut">Mois début:</label>
          <select id="mois_debut" required style="width:100%;">
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
          <input type="number" min="2000" max="2100" id="annee_debut" required style="width:100%;">
        </div>
        <div>
          <label for="mois_fin">Mois fin:</label>
          <select id="mois_fin" required style="width:100%;">
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
          <input type="number" min="2000" max="2100" id="annee_fin" required style="width:100%;">
        </div>
        <div style="display:flex;align-items:end;height:100%;grid-column:span 4;">
          <button type="submit" style="width:100%;">Filtrer</button>
        </div>
      </form>
      <table id="table-interets" class="table-centered" style="margin-top:2rem;">
        <thead>
          <tr><th>Mois</th><th>Année</th><th>Intérêts gagnés</th></tr>
        </thead>
        <tbody></tbody>
      </table>
      <canvas id="chart-interets" width="600" height="300"></canvas>
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
