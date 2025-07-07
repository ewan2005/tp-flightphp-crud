<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
      <link rel="stylesheet" href="css/main.css?v=1628755089081">

    <title>Ajouter un client</title>
</head>
<body>
    <h2>Ajouter un client</h2>
        <?php include('sidebar.php'); ?>

    <form onsubmit="ajouterClient(event)">
        <label>Nom:</label><br>
        <input type="text" id="nom" name="nom" required><br><br>

        <label>Prénom:</label><br>
        <input type="text" id="prenom" name="prenom" required><br><br>

        <label>Date de naissance:</label><br>
        <input type="date" id="date_naissance" name="date_naissance" required><br><br>

        <label>Email:</label><br>
        <input type="email" id="email" name="email"><br><br>

        <label>Téléphone:</label><br>
        <input type="text" id="telephone" name="telephone"><br><br>

        <button type="submit">Ajouter</button>
    </form>
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
                alert("Client ajouté avec succès !");
                //window.location.href = "profil_client.php";
            } else {
                alert("Erreur lors de l'ajout du client : " + (res.message || "Erreur inconnue"));
            }
        });
    }
</script>
