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
        button, input[type="submit"] {
            padding: 8px 16px;
        }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
<?php include('sidebar.php'); ?>
<div style="margin-left: 250px; width: 500px;">
    <h2>Remboursement :</h2>
    <form id="remboursementForm">
        <div id="sectionUser"></div>
        <br>
        <div id="sectionPret"></div>
        <br>
        <div id="sectionMontant"></div>
        <br>
        <input type="submit" value="Valider">
        <div id="okey"></div>
    </form>
</div>

<script>
const apiBase = "http://localhost/tp-flightphp-crud/ws";

function ajax(method, url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, apiBase + url, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    console.log("Réponse brute :", xhr.responseText);
    xhr.onreadystatechange = () => {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    callback(JSON.parse(xhr.responseText));
                } catch (e) {
                    console.error("Erreur JSON:", e);
                }
            } else {
                document.getElementById("okey").innerHTML = '<div class="error">Erreur lors du remboursement</div>';
            }
        }
    };
    xhr.send(data);
}

function remboursement(event) {
    event.preventDefault();

    const idClient = document.getElementById("clients").value;
    const idPret = document.getElementById("pret").value;
    const montant = document.querySelector("input[name='montant']").value;

    if (!idClient || !idPret || !montant) {
        alert("Veuillez remplir tous les champs !");
        return;
    }

    const data = `idClient=${encodeURIComponent(idClient)}&idPret=${encodeURIComponent(idPret)}&montant=${encodeURIComponent(montant)}`;

    ajax("POST", "/traitement_annuite", data, (response) => {
        if (response.success) {
            document.getElementById("okey").innerHTML = `<div class="success">${response.message}</div>`;
            
        } else {
            document.getElementById("okey").innerHTML = `<div class="error">${response.error}</div>`;
        }
    });
}


function getClient() {
    ajax("GET", "/clients", null, (data) => {
        const section = document.getElementById("sectionUser");
        section.innerHTML = "";

        const label = document.createElement("label");
        label.textContent = "Choisir un client";

        const select = document.createElement("select");
        select.name = "idClient";
        select.id = "clients";

        select.innerHTML = `<option value="">-- Choisir le client --</option>`;

        data.forEach(client => {
            const option = new Option(`${client.nom} - ${client.email}`, client.id_client);
            select.appendChild(option);
        });

        section.appendChild(label);
        section.appendChild(select);

        select.addEventListener("change", () => {
            getPrets(select.value);
        });
    });
}

function getPrets(clientId) {
    const sectionPret = document.getElementById("sectionPret");
    const sectionMontant = document.getElementById("sectionMontant");
    sectionPret.innerHTML = sectionMontant.innerHTML = "";

    ajax("GET", `/Pret/${clientId}`, null, (data) => {
        if (!data || data.length === 0) {
            sectionPret.innerHTML = `<div class="error">Aucun prêt trouvé.</div>`;
            return;
        }

        const label = document.createElement("label");
        label.textContent = "Choisir un prêt";

        const select = document.createElement("select");
        select.name = "idPret";
        select.id = "pret";

        select.innerHTML = `<option value="">-- Choisir le prêt --</option>`;

        data.forEach(pret => {
            const option = new Option(`id_echeance: ${pret.id_echeance} | Montant: ${pret.montant} Ar | Durée: ${pret.duree} mois`, pret.id_pret);
            select.appendChild(option);
        });

        sectionPret.appendChild(label);
        sectionPret.appendChild(select);

        select.addEventListener("change", () => {
            getMontantAnnuite(select.value);
        });
    });
}

function getMontantAnnuite(idPret) {
    const sectionMontant = document.getElementById("sectionMontant");
    sectionMontant.innerHTML = "";

    ajax("GET", `/Annuite/${idPret}`, null, (data) => {
        const label = document.createElement("label");
        label.textContent = "Montant de l'échéance actuelle (annuité constante)";

        const input = document.createElement("input");
        input.type = "number";
        input.name = "montant";
        input.step = "0.01";
        input.required = true;
        input.value = data.montant_annuite || 0;

        const hiddenPret = document.createElement("input");
        hiddenPret.type = "hidden";
        hiddenPret.name = "idPret";
        hiddenPret.value = idPret;

        sectionMontant.appendChild(label);
        sectionMontant.appendChild(input);
        sectionMontant.appendChild(hiddenPret);
    });
}

window.onload = getClient;
document.getElementById("remboursementForm").addEventListener("submit", remboursement);
</script>
</body>
</html>
