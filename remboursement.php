<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
$id = $_SESSION['user']['id_utilisateur'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Remboursement</title>

    <link rel="stylesheet" href="css/main.css?v=1628755089081">

    <style>
        .form-container {
            max-width: 400px;
            margin: 40px auto;
            border: 1px solid #ccc;
            padding: 30px;
            border-radius: 8px;
        }
        label, input, select {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }
        button {
            padding: 8px 16px;
        }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    <div style="margin-left: 250px; width: 500px;">
        <form action="traitement.php" method="post">
            <h2>Remboursement :</h2>
        <br>
            <label for="montant">Montant :</label>
            <input type="number" name="montant" id="montant" required>

            <div id="sectionUser">
                
            </div>
        <br>
            <input type="submit" value="Valider">
        </form>
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

        function getClient() {
            ajax("GET", "/clients", null, (data) => {
                const section = document.getElementById("sectionUser");

                const label = document.createElement("label");
                label.setAttribute("for", "clients");
                label.textContent = "Choisir un client";

                const select = document.createElement("select");
                select.name = "idClient";
                select.id = "clients";

                const defaultOption = document.createElement("option");
                defaultOption.value = "";
                defaultOption.textContent = "-- Choisir le client qui va rembourser --";
                select.appendChild(defaultOption);

                data.forEach(client => {
                    const option = document.createElement("option");
                    option.value = client.id_client;
                    option.textContent = client.nom + " - " + client.email;
                    select.appendChild(option);
                });

                section.appendChild(label);
                section.appendChild(select);
            });
        }

        window.onload = function () {
            getClient();
        };
    </script>
</body>
</html>
