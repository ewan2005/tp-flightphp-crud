<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Profil client</title>
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
  <?php include('sidebar.php'); ?>
  <div style="max-width:500px;margin:40px auto;">
    <h2>Profil du client</h2>

      <ul>
        <li></li>
      </ul>
    <a id="lien-pret" href="#">Créer un prêt pour ce client</a>
    <a href="liste_client.php">Retour à la liste</a>
  </div>
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

function chargerProfilClient(id) {
  ajax("GET", `/clients/${id}`, null, (res, status) => {
    if (status === 200 && res && res.data) {
      const client = res.data;
      document.querySelector("h2").innerText = `Profil de ${client.nom} ${client.prenom}`;
      const ul = document.querySelector("ul");
      ul.innerHTML = `
        <li>ID: ${client.id_client}</li>
        <li>Nom: ${client.nom}</li>
        <li>Prénom: ${client.prenom}</li>
        <li>Email: ${client.email}</li>
        <li>Téléphone: ${client.telephone || 'N/A'}</li>
        <li>Date de naissance: ${new Date(client.date_naissance).toLocaleDateString()}</li>
      `;
     document.getElementById("lien-pret").href = `pret_gestion.php?id_client=${client.id_client}`;

    } else {
      alert("Erreur lors du chargement du profil");
    }
  });
}

window.onload = function() {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");
  if (id) {
    alert ("Chargement du profil pour l'ID: " + id);
    chargerProfilClient(id);
  } else {
    alert("Aucun identifiant client fourni.");
  }
};
</script>