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
<style>
  .success { color: #155724; background: #d4edda; border: 1px solid #c3e6cb; padding: 8px; border-radius: 4px; }
  .error { color: #721c24; background: #f8d7da; border: 1px solid #f5c6cb; padding: 8px; border-radius: 4px; }
</style>
<body>
        <?php include('sidebar.php'); ?>
        <section class="main-section">
          <h2>Gestion des Prêts</h2>
          <form id="pretForm" onsubmit="event.preventDefault(); ajouterOuModifierPret();" style="width:100%;display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;align-items:end;max-width:1100px;margin:0 auto 2rem auto;">
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
              <input type="number" id="montant" step="0.01" min="0" required style="width:100%;" placeholder="Montant du prêt">
            </div>
            <div>
              <label>Durée (mois) :</label>
              <input type="number" id="duree" min="1" required style="width:100%;" placeholder="Durée en mois">
            </div>
            <div>
              <label>Date de début :</label>
              <input type="date" id="date_demande" required style="width:100%;" placeholder="Date de début du prêt">
            </div>
            <div>
              <label>Assurance :</label>
              <input type="number" id="assurance" step="0.01" min="0" required style="width:100%;">
            </div>
            <div>
                <label>Délai avant 1er remboursement (mois) :</label>
                <input type="number" id="delai_remboursement" min="0" value="0" style="width:100%;">
            </div>
            <button type="button" onclick="simulerPret()">Simuler</button>
          </form>
          <div id="result" style="margin: 10px 0;"></div>
          <div id="echeancier" style="margin:20px 0;"></div>
          <h3>Liste des Prêts</h3>
          <table border="1" id="table-prets">
              <thead>
                  <tr>
                      <th>ID</th><th>Client</th><th>Type</th><th>Montant</th><th>Durée</th><th>Date</th><th>Statut</th><th>Agent</th><th>Actions</th><th>taux</th>
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

// function getTauxTypePret(id){
//   ajax("GET", `/typePret/${id}/taux`, null, (data) => {
//     return data.taux_annuel;
//   });
// }


function afficherMessage(message, type = "success") {
  const resultDiv = document.getElementById("result");
  resultDiv.innerHTML = `<div class="${type}">${message}</div>`;
  resultDiv.style.opacity = 1;
  // Disparition fluide après 3 secondes
  setTimeout(() => {
    resultDiv.style.transition = "opacity 0.5s";
    resultDiv.style.opacity = 0;
  }, 3000);
}

function chargerTypesPretSelect() {
  ajax("GET", "/typePret", null, (data) => {
    const select = document.getElementById("type_pret");
    select.innerHTML = '<option value="">Sélectionner un type de prêt</option>';
    data.forEach(t => {
      const option = document.createElement("option");
      option.value = t.id_type_pret;
      option.textContent = t.nom;
      option.setAttribute("data-taux", t.taux_annuel); // Ajoute le taux ici
      select.appendChild(option);
    });
  });
}

function simulerPret(){ //hijerevena hoe somme que le client doit payer toute la duree du pret
  const montant = parseFloat(document.getElementById("montant").value);
  const duree = parseInt(document.getElementById("duree").value, 10);
  const typePretSelect = document.getElementById("type_pret");
  const selectedOption = typePretSelect.options[typePretSelect.selectedIndex];
  const taux = parseFloat(selectedOption.getAttribute("data-taux")) || 0;
  const assurance = parseFloat(document.getElementById("assurance").value) || 0;

  const tauxMensuel = taux / 100 / 12;
  const mensualite = (montant * tauxMensuel) / (1- Math.pow(1+tauxMensuel,-duree));
  const mensualite_assurance = montant * (assurance / 100);
  const mensualite_totale = mensualite + mensualite_assurance;

  
  // Affichage simulation
  const resultDiv = document.getElementById("result");
  resultDiv.innerHTML=`
    <div class="success">
      <b>Simulation :</b><br>
      Mensualité estimée (hors assurance) : ${mensualite.toFixed(2)} Ar<br>
      Mensualité assurance : ${mensualite_assurance.toFixed(2)} Ar<br>
      <b>Mensualité totale : ${mensualite_totale.toFixed(2)} Ar</b><br>
      Coût total (avec assurance) : ${(mensualite_totale * duree).toFixed(2)} Ar<br>
      Taux annuel : ${taux} %<br>
      Taux assurance : ${assurance} %<br>
      <button onclick='ajouterOuModifierPret()'>Valider le pret</button>
      <button id="btnEnregistrerSimulation" onclick='enregistrerSimulation()'>Enregistrer la simulation</button>
    </div>
  `;

  // Affichage échéancier
  let echeancierHtml = '<h4>Échéancier prévisionnel</h4><table border="1"><tr><th>#</th><th>Mensualité</th><th>Assurance</th><th>Total à payer</th></tr>';
  for(let i=1; i<=duree; i++){
    echeancierHtml += `<tr><td>${i}</td><td>${mensualite.toFixed(2)} Ar</td><td>${mensualite_assurance.toFixed(2)} Ar</td><td>${mensualite_totale.toFixed(2)} Ar</td></tr>`;
  }
  echeancierHtml += '</table>';
  document.getElementById("echeancier").innerHTML = echeancierHtml;
}

function enregistrerSimulation() {
  const montant = parseFloat(document.getElementById("montant").value);
  const duree = parseInt(document.getElementById("duree").value, 10);
  const typePretSelect = document.getElementById("type_pret");
  const selectedOption = typePretSelect.options[typePretSelect.selectedIndex];
  const taux = parseFloat(selectedOption.getAttribute("data-taux")) || 0;
  const assurance = parseFloat(document.getElementById("assurance").value) || 0;
  const delai_remboursement = parseInt(document.getElementById("delai_remboursement").value) || 0;

  const tauxMensuel = taux / 100 / 12;
  const mensualite = (montant * tauxMensuel) / (1- Math.pow(1+tauxMensuel,-duree));
  const mensualite_assurance = montant * (assurance / 100);
  const mensualite_totale = mensualite + mensualite_assurance;

  ajax("POST", "/simulation", {
    montant,
    duree,
    taux,
    assurance,
    delai_remboursement,
    mensualite,
    mensualite_assurance,
    mensualite_totale
  }, (response) => {
    if (response.success) {
      afficherMessage("Simulation enregistrée avec succès !");
      document.getElementById("btnEnregistrerSimulation").style.display = "none";
    } else {
      afficherMessage("Erreur lors de l'enregistrement : " + response.message, "error");
    }
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
  const assurance = document.getElementById("assurance").value;
  const delai_remboursement = document.getElementById("delai_remboursement").value;
  const data = {
    id_client,
    id_type_pret,
    montant,
    duree,
    date_demande,
    id_agent,
    assurance,
    delai_remboursement
  };

  const xhr = new XMLHttpRequest();
  const url = id ? `/prets/${id}` : "/prets";
  const method = id ? "PUT" : "POST";

  xhr.open(method, apiBase + url, true);
  xhr.setRequestHeader("Content-Type", "application/json");
  
  xhr.onload = function() {
    if (xhr.status === 200) {
      const res = JSON.parse(xhr.responseText);
      if (res.success) {
        afficherMessage(res.message, "success");
        if (res.echeancier) {
          afficherEcheancier(res.echeancier);
        }
        resetFormPret();
        chargerPrets();
      } else {
        afficherMessage(res.message, "error");
      }
    } else {
      try {
        const errorResponse = JSON.parse(xhr.responseText);
        afficherMessage(errorResponse.message || "Une erreur est survenue", "error");
      } catch (e) {
        afficherMessage("Erreur lors de la communication avec le serveur", "error");
      }
    }
  };

  xhr.onerror = function() {
    afficherMessage("Erreur réseau - Impossible de contacter le serveur", "error");
  };

  xhr.send(JSON.stringify(data));
}

function afficherEcheancier(echeancier) {
  const echeancierDiv = document.getElementById("echeancier");
  if (!echeancier || echeancier.length === 0) { 
    echeancierDiv.innerHTML = ""; 
    return; 
  }
  
  let html = '<h4>Échéancier prévisionnel</h4><table border="1"><tr><th>#</th><th>Date</th><th>Montant</th></tr>';
  echeancier.forEach(e => {
    html += `<tr><td>${e.numero}</td><td>${e.date}</td><td>${e.montant} Ar</td></tr>`;
  });
  html += '</table>';
  echeancierDiv.innerHTML = html;
}

function remplirFormPret(p) {
  document.getElementById("id_pret").value = p.id_pret;
  document.getElementById("id_client").value = p.id_client;
  document.getElementById("type_pret").value = p.id_type_pret;
  document.getElementById("montant").value = p.montant;
  document.getElementById("duree").value = p.duree;
  document.getElementById("date_demande").value = p.date_demande;
  document.getElementById("assurance").value = p.assurance || 0; // Assure que l'assurance est définie
  document.getElementById("delai_remboursement").value = p.delai_premier_remboursement || 0; // Assure que le délai est défini
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
  document.getElementById("assurance").value = 0; // Réinitialise l'assurance à 0
  document.getElementById("delai_remboursement").value = 0; // Réinitialise le délai à 0
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
