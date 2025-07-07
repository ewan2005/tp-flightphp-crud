<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Prêts</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div style="background:#f2f2f2;padding:10px 20px;display:flex;justify-content:space-between;align-items:center;">
      <div>
        Connecté en tant que <b><?= htmlspecialchars($user['nom']) ?></b> (<?= htmlspecialchars($user['role']) ?>)
      </div>
      <a href="logout.php" style="color:#fff;background:#d9534f;padding:6px 14px;border-radius:4px;text-decoration:none;">Déconnexion</a>
    </div>
    <h2>Gestion des Prêts</h2>
    <form id="pretForm" onsubmit="event.preventDefault(); ajouterOuModifierPret();">
        <input type="hidden" id="id_pret">
        <label>Client :</label>
        <input type="number" id="id_client" required><br>
        <label for="type_pret">Type de prêt</label>
        <select id="type_pret" name="type_pret" required>
          <option value="">Sélectionner un type de prêt</option>
        </select>

        <label>Montant :</label>
        <input type="number" id="montant" step="0.01" min="0" required><br>
        <label>Durée (mois) :</label>
        <input type="number" id="duree" min="1" required><br>
        <label>Date de début :</label>
        <input type="date" id="date_demande" required><br>
        <label>Agent :</label>
        <input type="number" id="id_agent" required><br>
        <button type="submit">Créer / Modifier le prêt</button>
        <button type="button" onclick="resetFormPret()">Annuler</button>
    </form>
    <div id="result"></div>
    <h3>Liste des Prêts</h3>
    <table border="1" id="table-prets">
        <thead>
            <tr>
                <th>ID</th><th>Client</th><th>Type</th><th>Montant</th><th>Durée</th><th>Date</th><th>Statut</th><th>Agent</th><th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <script>
const apiBase = "http://localhost/tp-flightphp-crud/ws";

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

function chargerTypesPretSelect() {
  ajax("GET", "/typePret", null, (data) => {
    const select = document.getElementById("type_pret");
    select.innerHTML = '<option value="">Sélectionner un type de prêt</option>';
    data.forEach(t => {
      const option = document.createElement("option");
      option.value = t.id_type_pret;
      option.textContent = t.nom;
      select.appendChild(option);
    });
  });
}

// Appelle cette fonction au chargement de la page :
window.onload = chargerTypesPretSelect;

function chargerPrets() {
  ajax("GET", "/prets", null, (data) => {
    const tbody = document.querySelector("#table-prets tbody");
    tbody.innerHTML = "";
    data.forEach(p => {
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
          <button onclick='remplirFormPret(${JSON.stringify(p)})'>✏️</button>
          <button onclick='supprimerPret(${p.id_pret})'>🗑️</button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  });
}

function ajouterOuModifierPret() {
  const id = document.getElementById("id_pret").value;
  const id_client = document.getElementById("id_client").value;
  const id_type_pret = document.getElementById("id_type_pret").value;
  const montant = document.getElementById("montant").value;
  const duree = document.getElementById("duree").value;
  const date_demande = document.getElementById("date_demande").value;
  const id_agent = document.getElementById("id_agent").value;

  const data = `id_client=${id_client}&id_type_pret=${id_type_pret}&montant=${montant}&duree=${duree}&date_demande=${date_demande}&id_agent=${id_agent}`;

  if (id) {
    ajax("PUT", `/prets/${id}`, data, () => {
      resetFormPret();
      chargerPrets();
    });
  } else {
    ajax("POST", "/prets", data, () => {
      resetFormPret();
      chargerPrets();
    });
  }
}

function remplirFormPret(p) {
  document.getElementById("id_pret").value = p.id_pret;
  document.getElementById("id_client").value = p.id_client;
  document.getElementById("id_type_pret").value = p.id_type_pret;
  document.getElementById("montant").value = p.montant;
  document.getElementById("duree").value = p.duree;
  document.getElementById("date_demande").value = p.date_demande;
  document.getElementById("id_agent").value = p.id_agent;
}

function supprimerPret(id) {
  if (confirm("Supprimer ce prêt ?")) {
    ajax("DELETE", `/prets/${id}`, null, () => {
      chargerPrets();
    });
  }
}

function resetFormPret() {
  document.getElementById("id_pret").value = "";
  document.getElementById("id_client").value = "";
  document.getElementById("id_type_pret").value = "";
  document.getElementById("montant").value = "";
  document.getElementById("duree").value = "";
  document.getElementById("date_demande").value = "";
  document.getElementById("id_agent").value = "";
}

chargerPrets();
    </script>
</body>
</html>
