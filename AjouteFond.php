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
$id = $_SESSION['user']['id_utilisateur'];
?>
<!DOCTYPE html>
<html lang="fr">

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
    <div style="margin-left: 200px;width: 500px; ">
        <h2>Ajout de fonds</h2>
        <br>
        <input type="hidden" id="id" name="id" value="<?php echo $id ?>">
        <label for="montant">Montant à ajouter :</label>
        <input type="number" name="montant" id="montant" placeholder="montant >0"><br>
        <input type="button" onclick="return verification()" value="Valider"><br>
        <div id="erreur" style="color:red; margin-top:10px;"></div>
        <div id="message" style="color:rgb(9, 101, 19); margin-top:10px;"></div>

    </div>
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

        function verification() {
            const aff = document.getElementById("erreur");
            const montant = document.getElementById("montant").value;
            const ids = document.getElementById("id").value;
            const date = new Date().toISOString().split('T')[0];

            aff.innerHTML = "";

            if (montant === "" || montant <= 0) {
                aff.innerHTML = "Erreur ! Le montant doit être un nombre positif.";
                return false;
            }
            
            insertFond(ids, montant, date);
        }

        function insertFond(id, montant, date) {
            const desc = "Ajout Fond";
            if (id) {
                const data1 = `id_utilisateur=${id}&montant=${montant}&date_ajout=${date}`;
                const data2 = `solde=${montant}`;
                const data3 = `id_utilisateur=${id}&montant=${montant}&description=${desc}&date=${date}`;

                ajax("POST", "/addFond", data1, () => {
                    ajax("POST", `/updateFond/${id}`, data2, () => {
                        ajax("POST", "/addHistorique", data3, () => {
                            succes();
                        });
                    });
                });
            }
        }

        function succes() {
            document.getElementById("message").innerHTML = "Fonds enregistrés avec succès !";
            document.getElementById("montant").value = "";
        }
    </script>
</body>

</html>