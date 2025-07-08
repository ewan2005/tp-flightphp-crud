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
    <title>Ajout de fonds - Gestion Bancaire</title>
    <link rel="stylesheet" href="css/main.css?v=1628755089081">
</head>
<body>
    <?php include('sidebar.php'); ?>
    <section class="section dashboard-section" style="margin-left: 260px;width:80%;">
    <section class="dashboard-section" style="max-width:600px;margin:40px auto 0 auto;">
        <div class="card" style="margin:0;max-width:100%;">
            <div class="card-content">
                <h2 class="dashboard-title" style="font-size:1.5rem;margin-bottom:1.5rem;text-align:left;">
                    <i class="fas fa-money-bill-wave mr-2"></i>Ajout de fonds
                </h2>
                <input type="hidden" id="id" name="id" value="<?php echo $id ?>">
                <div class="form-group">
                    <label for="montant" class="form-label">Montant à ajouter</label>
                    <input type="number" name="montant" id="montant" class="form-input" placeholder="Entrez un montant positif">
                </div>
                <button type="button" onclick="return verification()" class="btn-primary">
                    <i class="fas fa-check-circle mr-1"></i> Valider
                </button>
                <div id="message" class="success" style="display: none;"></div>
                <div id="erreur" class="error" style="display: none;"></div>
            </div>
        </div>
        <div class="card" style="margin:2rem 0 0 0;max-width:100%;">
            <div class="card-content">
                <h3 style="font-size:1.1rem;font-weight:600;color:#2a4d69;margin-bottom:1rem;">Solde des établissements</h3>
                <table class="table-centered" style="width:100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody id="fond-tbody">
                        <!-- Les établissements seront insérés ici -->
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    </section>
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
        function chargerFond(){
            ajax("GET","/fond",null, (data)=> {
                const tbody = document.getElementById("fond-tbody");
                tbody.innerHTML = "";
                data.forEach(fond => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${fond.id_etablissement}</td>
                        <td>${fond.solde}</td>
                    `;
                    tbody.appendChild(tr);
                });
            });
        }

        function verification() {
            const errorDiv = document.getElementById("erreur");
            const successDiv = document.getElementById("message");
            const montant = document.getElementById("montant").value;
            const ids = document.getElementById("id").value;
            const date = new Date().toISOString().split('T')[0];
            errorDiv.style.display = "none";
            successDiv.style.display = "none";
            if (montant === "" || montant <= 0) {
                errorDiv.innerHTML = "Erreur : Le montant doit être un nombre positif.";
                errorDiv.style.display = "block";
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
            // location.reload();
        }
        window.onload = function() {
            chargerFond();
        };
        function succes() {
            const successDiv = document.getElementById("message");
            successDiv.innerHTML = "<i class='fas fa-check-circle mr-1'></i> Fonds enregistrés avec succès !";
            successDiv.className = "success";
            successDiv.style.display = "block";
            document.getElementById("montant").value = "";
            chargerFond(); // Recharge le tableau immédiatement
            setTimeout(() => {
                successDiv.style.display = "none";
            }, 5000);
        }
    </script>
</body>
</html>