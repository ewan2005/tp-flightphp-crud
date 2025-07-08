<?php
session_start(); if (!isset($_SESSION['user'])) { header('Location: login.php'); exit(); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Solde mensuel de l'établissement</title>
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
<?php include('sidebar.php'); ?>
<section class="main-section">
  <h2>Solde mensuel de l'établissement financier</h2>
  <form id="filtre-solde" style="display:flex;flex-wrap:wrap;gap:1rem;align-items:end;margin-bottom:1.5rem;">
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
      <input type="number" min="1900" max="2100" id="annee_debut" required placeholder="Année de début" style="width: 180px;">
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
      <input type="number" min="1900" max="2100" id="annee_fin" required placeholder="Année de fin" style="width: 180px;">
    </div>
    <div style="min-width:180px;flex:1 1 180px;">
      <button type="submit">Afficher</button>
    </div>
  </form>
  <div id="message" style="margin-bottom:1rem;"></div>
  <h3 style="margin-top:2.5rem;">Solde disponible par mois (période choisie)</h3>
  <table id="table-solde" class="table-centered" style="margin-bottom:2rem;">
    <thead>
      <tr>
        <th>Mois</th>
        <th>Année</th>
        <th>Solde disponible (Ar)</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</section>
<script>
    
const apiBase = "http://localhost/tp-flightphp-crud/ws";

function ajax(method, url, data, callback) {
  const xhr = new XMLHttpRequest();
  xhr.open(method, apiBase + url, true);
  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4) {
      let res = {};
      try { res = JSON.parse(xhr.responseText); } catch {}
      callback(res, xhr.status);
    }
  };
  xhr.send(data);
}

document.getElementById('filtre-solde').onsubmit = function(e) {
  e.preventDefault();
  const md = document.getElementById('mois_debut').value;
  const ad = document.getElementById('annee_debut').value;
  const mf = document.getElementById('mois_fin').value;
  const af = document.getElementById('annee_fin').value;
  ajax("GET", `/solde-mensuel?mois_debut=${md}&annee_debut=${ad}&mois_fin=${mf}&annee_fin=${af}`, null, function(data) {
    const tbody = document.querySelector('#table-solde tbody');
    tbody.innerHTML = '';
    if (!data || !Array.isArray(data) || data.length === 0) {
      document.getElementById('message').textContent = "Aucune donnée.";
      return;
    }
    document.getElementById('message').textContent = "";
    data.forEach(row => {
      tbody.innerHTML += `<tr>
        <td>${row.mois}</td>
        <td>${row.annee}</td>
        <td>${parseFloat(row.solde).toLocaleString('fr-FR', {minimumFractionDigits:2})}</td>
      </tr>`;
    });
  });
};
</script>
</body>
</html>
