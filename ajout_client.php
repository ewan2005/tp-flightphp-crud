<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
      <link rel="stylesheet" href="css/main.css?v=1628755089081">

    <title>Ajouter un client</title>
</head>
<body>
    <?php include('sidebar.php'); ?>
    <section class="main-section">
      <h2>Ajouter un client</h2>
      <form onsubmit="ajouterClient(event)" style="width:100%;display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:end;max-width:900px;margin:0 auto 2rem auto;">
        <div>
          <label>Nom:</label><br>
          <input type="text" id="nom" name="nom" required style="width:100%;" placeholder="Nom du client">
        </div>
        <div>
          <label>Prénom:</label><br>
          <input type="text" id="prenom" name="prenom" required style="width:100%;" placeholder="Prénom du client">
        </div>
        <div>
          <label>Date de naissance:</label><br>
          <input type="date" id="date_naissance" name="date_naissance" required style="width:100%;" placeholder="Date de naissance">
        </div>
        <div>
          <label>Email:</label><br>
          <input type="email" id="email" name="email" style="width:100%;" placeholder="Adresse email">
        </div>
        <div>
          <label>Téléphone:</label><br>
          <input type="text" id="telephone" name="telephone" style="width:100%;" placeholder="Numéro de téléphone">
        </div>
        <div style="display:flex;align-items:end;height:100%;">
          <button type="submit" style="width:100%;">Ajouter</button>
        </div>
      </form>
      <div id="message" style="margin-bottom:1rem;"></div>
      <table class="table-centered" style="margin-top:2rem;">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Profil</th>
          </tr>
        </thead>
        <tbody id="clients-tbody">
          <!-- Les clients seront insérés ici -->
        </tbody>
      </table>
    </section>
</body>
</html>
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

    function showMessage(msg, type) {
        const messageDiv = document.getElementById("message");
        messageDiv.innerHTML = `<div class="${type}">${msg}</div>`;
    }

    function ajouterClient(event){
        event.preventDefault();
        const nom = document.getElementById("nom").value;
        const prenom = document.getElementById("prenom").value;
        const date_naissance = document.getElementById("date_naissance").value;
        const email = document.getElementById("email").value;
        const telephone = document.getElementById("telephone").value;

        const data = `nom=${encodeURIComponent(nom)}&prenom=${encodeURIComponent(prenom)}&date_naissance=${encodeURIComponent(date_naissance)}&email=${encodeURIComponent(email)}&telephone=${encodeURIComponent(telephone)}`;
        ajax("POST", "/clients", data, (res, status) => {
            if (status === 200 && res.success) {
                showMessage("Client ajouté avec succès !", "success");
                //window.location.href = "profil_client.php";
                chargerClients();
            } else {
                showMessage("Erreur lors de l'ajout du client : " + (res.message || "Erreur inconnue"), "error");
            }
            location.reload();
        });
    }

    function chargerClients() {
        ajax("GET", "/clients", null, (data) => {
            const tbody = document.getElementById("clients-tbody");
            tbody.innerHTML = "";
            data.forEach(client => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${client.id_client}</td>
                    <td>${client.nom}</td>
                    <td>${client.prenom}</td>
                    <td>${client.email}</td>
                    <td><a href="profil_client.php?id=${client.id_client}">Voir Profil</a></td>
                `;
                tbody.appendChild(tr);
            });
        });
    }
    window.onload = chargerClients;
</script>
