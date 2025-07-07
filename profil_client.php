<?php
require_once 'models/Client.php'; // adapte le chemin selon ton projet
$clients = Client::getAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des clients</title>
</head>
<body>
    <h2>Liste des clients</h2>
    <a href="ajout_client.html">Ajouter un nouveau client</a><br><br>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date de naissance</th>
                <th>Email</th>
                <th>Téléphone</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</body>
</html>
