<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
if ($_SESSION['user']['role'] !== 'admin') {
    echo "<div style='color:red;text-align:center;margin-top:40px;'>Accès refusé : réservé à l'administrateur.</div>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation des Prêts</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
<section class="main-section" style="margin-left: 260px;width:80%;">
  <h2>Validation des Prêts (Admin)</h2>
  <?php include('sidebar.php'); ?>
  <table border="1" id="table-prets-validation" class="table-centered" style="margin-top:2rem;">
    <thead>
      <tr>
        <th>ID</th><th>Client</th><th>Type</th><th>Montant</th><th>Durée</th><th>Date</th><th>Statut</th><th>Agent</th><th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
  <div id="result"></div>
</section>
<script>
const apiBase = "http://localhost/tp-flightphp-crud/ws";

function ajax(method, url, data, callback, isJson = false) {
  const xhr = new XMLHttpRequest();
  xhr.open(method, apiBase + url, true);
  if (isJson) {
    xhr.setRequestHeader("Content-Type", "application/json");
  } else {
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  }
  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        callback(JSON.parse(xhr.responseText));
      } else {
        document.getElementById('result').innerHTML = '<div class="error">Erreur serveur: ' + xhr.status + '</div>';
      }
    }
  };
  xhr.send(data);
}

function chargerPretsValidation() {
  ajax("GET", "/prets", null, (data) => {
    const tbody = document.querySelector("#table-prets-validation tbody");
    tbody.innerHTML = "";
    data.filter(p => p.id_statut == 1).forEach(p => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${p.id_pret}</td>
        <td>${p.id_client}</td>
        <td>${p.id_type_pret}</td>
        <td>${p.montant}</td>
        <td>${p.duree}</td>
        <td>${p.date_demande}</td>
        <td>${p.id_statut}</td>
        <td>${p.id_agent}</td>
        <td>
          <button onclick='validerPret(${p.id_pret})'>Valider</button>
          <button onclick='rejeterPret(${p.id_pret})'>Rejeter</button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  });
}

function validerPret(id) {
  ajax("GET", `/prets/${id}`, null, (pret) => {
    pret.id_statut = 2;
    pret.id_agent = pret.id_agent || 1; // Remplacer par l'ID agent réel si besoin
    ajax("PUT", `/prets/${id}`, JSON.stringify(pret), (res) => {
      document.getElementById('result').innerHTML = `<div class="success">${res.message || 'Prêt validé'}</div>`;
      chargerPretsValidation();
    }, true);
  });
}

function rejeterPret(id) {
  const motif = prompt("Motif du rejet :");
  if (!motif) return;
  ajax("GET", `/prets/${id}`, null, (pret) => {
    pret.id_statut = 3;
    pret.motif_rejet = motif;
    pret.id_agent = pret.id_agent || 1; // Remplacer par l'ID agent réel si besoin
    ajax("PUT", `/prets/${id}`, JSON.stringify(pret), (res) => {
      document.getElementById('result').innerHTML = `<div class="error">${res.message || 'Prêt rejeté'}</div>`;
      chargerPretsValidation();
    }, true);
  });
}

chargerPretsValidation();
</script>
</body>
</html>
