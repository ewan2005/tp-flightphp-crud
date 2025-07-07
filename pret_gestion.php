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
        <?php include('sidebar.php'); ?>
        <h2>Gestion des Prêts</h2>
        <form id="pretForm" onsubmit="event.preventDefault(); ajouterOuModifierPret();" style="width:100%;display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;align-items:end;max-width:900px;margin:0 auto 2rem auto;">
          <input type="hidden" id="id_pret">
          <div>
            <label>Client :</label>
            <select id="id_client" required style="width:100%;"></select>
          </div>
          <div>
            <label for="type_pret">Type de prêt</label>
            <select id="type_pret" name="type_pret" required style="width:100%;">
              <option value="">Sélectionner un type de prêt</option>
            </select>
          </div>
          <div>
            <label>Montant :</label>
            <input type="number" id="montant" step="0.01" min="0" required style="width:100%;">
          </div>
          <div>
            <label>Durée (mois) :</label>
            <input type="number" id="duree" min="1" required style="width:100%;">
          </div>
          <div>
            <label>Date de début :</label>
            <input type="date" id="date_demande" required style="width:100%;">
          </div>
          <!-- <div>
            <label>Agent :</label>
            <input type="text" id="agent" style="width:100%;">
          </div> -->
          <div style="display:flex;align-items:end;height:100%;grid-column:span 3;">
            <button type="submit" style="width:100%;">Ajouter/Modifier</button>
          </div>
        </form>
        <div id="result"></div>
        <div id="echeancier" style="margin:20px 0;"></div>
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
  if (data) {
    xhr.setRequestHeader("Content-Type", "application/json");
  }
  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4 && xhr.status === 200) {
      callback(JSON.parse(xhr.responseText));
    } else if (xhr.readyState === 4 && xhr.status !== 200) {
      // Affiche l'erreur dans la console pour debug
      console.error(xhr.responseText);
    }
  };
  xhr.send(data ? JSON.stringify(data) : null);
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

function chargerClientsSelect() {
  const params = new URLSearchParams(window.location.search);
  const idClientURL = params.get("id_client");
  const select = document.getElementById("id_client");
  ajax("GET", "/clients", null, (data) => {
    select.innerHTML = '<option value="">Sélectionner un client</option>';
    data.forEach(e => {
      const option = document.createElement("option");
      option.value = e.id_client;
      option.textContent = e.nom + (e.prenom ? (' ' + e.prenom) : '');
      if (idClientURL && e.id_client == idClientURL) {
        option.selected = true;
      }
      select.appendChild(option);
    });
    // Si id_client dans l'URL, désactive le select et ajoute un champ caché pour l'envoi
    if (idClientURL) {
      select.disabled = true;
      let hidden = document.createElement("input");
      hidden.type = "hidden";
      hidden.name = "id_client";
      hidden.value = idClientURL;
      select.parentNode.appendChild(hidden);
    }
  });
}


function chargerAgentField() {
  const user = <?php echo json_encode($user); ?>;
  const agentField = document.getElementById("agentField");
  if (user.role === 'admin') {
    ajax("GET", "/agents", null, (data) => {
      let html = '<select id="id_agent" required><option value="">Sélectionner un agent</option>';
      data.forEach(a => {
        html += `<option value="${a.id_utilisateur}">${a.nom}</option>`;
      });
      html += '</select>';
      agentField.innerHTML = html;
    });
  } else {
    agentField.innerHTML = `<input type='text' id='id_agent' value='${user.id_utilisateur}' readonly style='background:#eee;' /> <b>${user.nom}</b>`;
  }
}

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
          <a href="ws/prets/${p.id_pret}/pdf" target="_blank" title="Télécharger PDF">📄 PDF</a>
        </td>
      `;
      tbody.appendChild(tr);
    });
  });
}

const user = <?php echo json_encode($user); ?>;

function ajouterOuModifierPret() {
  const id = document.getElementById("id_pret").value;
  const id_client = document.getElementById("id_client").value;
  const id_type_pret = document.getElementById("type_pret").value;
  const montant = document.getElementById("montant").value;
  const duree = document.getElementById("duree").value;
  const date_demande = document.getElementById("date_demande").value;
  const id_agent = user.id_utilisateur;
  const resultDiv = document.getElementById("result");
  const echeancierDiv = document.getElementById("echeancier");

  const data = {
    id_client,
    id_type_pret,
    montant,
    duree,
    date_demande,
    id_agent
  };

  function afficherEcheancier(echeancier) {
    if (!echeancier || echeancier.length === 0) { echeancierDiv.innerHTML = ""; return; }
    let html = '<h4>Échéancier prévisionnel</h4><table border="1"><tr><th>#</th><th>Date</th><th>Montant</th></tr>';
    echeancier.forEach(e => {
      html += `<tr><td>${e.numero}</td><td>${e.date}</td><td>${e.montant} Ar</td></tr>`;
    });
    html += '</table>';
    echeancierDiv.innerHTML = html;
  }

  if (id) {
    ajax("PUT", `/prets/${id}`, data, (res) => {
      resultDiv.textContent = res.message || "Prêt modifié";
      resetFormPret();
      chargerPrets();
      echeancierDiv.innerHTML = "";
    });
  } else {
    ajax("POST", "/prets", data, (res) => {
      if (res.success) {
        resultDiv.innerHTML = `<span style='color:green;'>${res.message}</span>`;
        afficherEcheancier(res.echeancier);
        resetFormPret();
        chargerPrets();
      } else {
        resultDiv.innerHTML = `<span style='color:red;'>${res.message || 'Erreur lors de la création du prêt'}</span>`;
        afficherEcheancier(res.echeancier);
      }
    });
  }
}

function remplirFormPret(p) {
  document.getElementById("id_pret").value = p.id_pret;
  document.getElementById("id_client").value = p.id_client;
  document.getElementById("type_pret").value = p.id_type_pret;
  document.getElementById("montant").value = p.montant;
  document.getElementById("duree").value = p.duree;
  document.getElementById("date_demande").value = p.date_demande;
  // if (document.getElementById("id_agent"))
  //   document.getElementById("id_agent").value = p.id_agent;
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
  document.getElementById("type_pret").value = "";
  document.getElementById("montant").value = "";
  document.getElementById("duree").value = "";
  document.getElementById("date_demande").value = "";
  // if (document.getElementById("id_agent"))
  //   document.getElementById("id_agent").value = "";
}

// Charger tous les éléments nécessaires au chargement de la page
window.onload = function() {
  chargerTypesPretSelect();
  chargerClientsSelect();
  // chargerAgentField();
  chargerPrets();
};
</script>
</body>
</html>
