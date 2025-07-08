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
</head>
<body>
<?php include('sidebar.php'); ?>
<div class="main-section">
    <h2>Remboursement</h2>
    <form id="remboursementForm">
        <div id="sectionUser" style="min-width:220px;flex:1 1 220px;"></div>
        <div id="sectionPret" style="min-width:220px;flex:1 1 220px;"></div>
        <div id="sectionMontant" style="min-width:220px;flex:1 1 220px;"></div>
        <button type="submit" class="button" style="margin-top:1rem;min-width:180px;">Valider</button>
        <div id="okey" style="width:100%;margin-top:1rem;"></div>
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
        document.getElementById("okey").innerHTML = '<div class="error">Veuillez remplir tous les champs !</div>';
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
        // data doit contenir : montant_annuite, montant_assurance
        const annuite = data.montant_annuite || 0;
        const assurance = data.montant_assurance;
        const total = annuite + assurance;

        let html = `<label>Montant de l'échéance actuelle</label>`;
        html += `<div><b>Annuité constante :</b> ${annuite.toFixed(2)} Ar</div>`;
        html += `<div><b>Assurance :</b> ${assurance.toFixed(2)} Ar</div>`;
        html += `<div><b>Total à payer :</b> ${total.toFixed(2)} Ar</div>`;
        html += `<input type="number" name="montant" step="0.01" required value="${total.toFixed(2)}">`;
        html += `<input type="hidden" name="idPret" value="${idPret}">`;
        sectionMontant.innerHTML = html;
    });
}

window.onload = getClient;
document.getElementById("remboursementForm").addEventListener("submit", remboursement);
</script>
</body>
</html>
