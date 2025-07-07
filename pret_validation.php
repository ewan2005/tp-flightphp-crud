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
    <h2>Validation des Prêts (Admin)</h2>
    <table border="1" id="table-prets-validation">
        <thead>
            <tr>
                <th>ID</th><th>Client</th><th>Type</th><th>Montant</th><th>Durée</th><th>Date</th><th>Statut</th><th>Agent</th><th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div id="result"></div>
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
  const id_admin = prompt("ID admin :");
  if (!id_admin) return;
  ajax("PUT", `/prets/${id}`, `id_statut=2&id_admin=${id_admin}`, (res) => {
    document.getElementById('result').textContent = res.message || 'Prêt validé';
    chargerPretsValidation();
  });
}

function rejeterPret(id) {
  const id_admin = prompt("ID admin :");
  const motif = prompt("Motif du rejet :");
  if (!id_admin || !motif) return;
  ajax("PUT", `/prets/${id}`, `id_statut=3&id_admin=${id_admin}&motif_rejet=${encodeURIComponent(motif)}`, (res) => {
    document.getElementById('result').textContent = res.message || 'Prêt rejeté';
    chargerPretsValidation();
  });
}

chargerPretsValidation();
    </script>
</body>
</html>
