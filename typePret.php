<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" class="">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Creer type pret</title>

  <!-- Tailwind is included -->
  <link rel="stylesheet" href="css/main.css?v=1628755089081">

    <style>
      .form-container { max-width: 400px; margin: 40px auto; border: 1px solid #ccc; padding: 30px; border-radius: 8px; }
      label, input { display: block; width: 100%; margin-bottom: 10px; }
      button { padding: 8px 16px; }
      .success { color: green; }
      .error { color: red; }
    </style>
</head>
<body>

    <?php include('sidebar.php'); ?>
    <section class="main-section" style="margin:2rem auto;max-width:1200px;width:95vw;">
      <h2>Créer un Type de Prêt</h2>
      <div id="message"></div>
      <form id="typePretForm" onsubmit="ajouterTypePret(event)" style="width:100%;display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:end;max-width:700px;margin:0 auto 2rem auto;">
        <div>
          <label for="nom">Nom du type de prêt</label>
          <input type="text" id="nom" name="nom" required style="width:100%;" placeholder="Nom du type de prêt">
        </div>
        <div>
          <label for="taux">Taux d’intérêt annuel (%)</label>
          <input type="number" id="taux" name="taux" step="0.01" min="0.01" max="100" required style="width:100%;" placeholder="Taux annuel (%)">
        </div>
        <div>
          <label for="duree">Durée maximale (en mois)</label>
          <input type="number" id="duree" name="duree" min="1" required style="width:100%;" placeholder="Durée maximale (mois)">
        </div>
        <div>
          <label for="montant_min">Montant minimum (Ar)</label>
          <input type="number" id="montant_min" name="montant_min" min="1000" required style="width:100%;" placeholder="Montant minimum (Ar)">
        </div>
        <div>
          <label for="montant_max">Montant maximum (Ar)</label>
          <input type="number" id="montant_max" name="montant_max" min="1000" required style="width:100%;" placeholder="Montant maximum (Ar)">
        </div>
        <div style="display:flex;align-items:end;height:100%;grid-column:span 2;">
          <button type="submit" style="width:100%;">Créer</button>
        </div>
      </form>
      <h1>Liste des types pret</h1>
      <table id="type-pret">
            <thead>
            <tr>
              <th>ID</th><th>ID Etablissement</th><th>Nom</th><th>Taux d’intérêt annuel</th><th>Durée maximale (en mois)</th><th>Montant minimum (Ar)</th><th>Montant maximum (Ar)</th><th>Actions</th>
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
          xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
              let res = {};
              try { res = JSON.parse(xhr.responseText); } catch {}
              callback(res, xhr.status);
            }
          };
          xhr.send(data);
        }

        function ajouterTypePret(event) {
          event.preventDefault();
          const nom = document.getElementById("nom").value;
          const taux = document.getElementById("taux").value;
          const duree = document.getElementById("duree").value;
          const montant_min = document.getElementById("montant_min").value;
          const montant_max = document.getElementById("montant_max").value;
          const messageDiv = document.getElementById("message");

            const data = `nom=${encodeURIComponent(nom)}&taux_annuel=${encodeURIComponent(taux)}&duree_max=${encodeURIComponent(duree)}&montant_min=${encodeURIComponent(montant_min)}&montant_max=${encodeURIComponent(montant_max)}`;
          ajax("POST", "/typePret", data, (res, status) => {
            if (status === 200 && res.success) {
              messageDiv.innerHTML = "<span class='success'>Type de prêt créé avec succès !</span>";
              document.getElementById("typePretForm").reset();
            } else {
              messageDiv.innerHTML = "<span class='error'>" + (res.message || "Erreur lors de la création") + "</span>";
            }
            location.reload();
          });
        }

        function chargerTypesPret() {
          ajax("GET", "/typePret", null, (res) => {
            const tbody = document.querySelector("#type-pret tbody");
            tbody.innerHTML = ""; // Vider le tableau avant de le remplir
            if (res && res.length > 0) {
              res.forEach(type => {
                const row = document.createElement("tr");
                row.innerHTML = `
                  <td>${type.id_type_pret}</td>
                  <td>${type.id_etablissement}</td>
                  <td>${type.nom}</td>
                  <td>${type.taux_annuel}</td>
                  <td>${type.duree_max}</td>
                  <td>${type.montant_min}</td>
                  <td>${type.montant_max}</td>
                  <td><button onclick="supprimerTypePret(${type.id_type_pret})">Supprimer</button></td>
                `;
                tbody.appendChild(row);
              });
            } else {
              tbody.innerHTML = "<tr><td colspan='8'>Aucun type de prêt trouvé</td></tr>";
            }
          });
        }
        window.onload = function() {
          chargerTypesPret();
        };
    </script>

</body>
</html>