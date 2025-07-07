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

    <!-- Tailwind is included -->
    <link rel="stylesheet" href="css/main.css?v=1628755089081">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-left: 220px;
            margin-top: 30px;
            max-width: 500px;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #3b82f6;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 0.75rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #4b5563;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .btn-primary {
            background-color: #3b82f6;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary:hover {
            background-color: #2563eb;
        }
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }
        .alert-error {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
        }
        .alert-success {
            background-color: #dcfce7;
            color: #16a34a;
            border: 1px solid #86efac;
        }
    </style>
</head>

<body>
    <?php include('sidebar.php'); ?>
    
    <div class="card">
        <h2 class="card-title">
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
        
        <div id="erreur" class="alert alert-error" style="display: none;"></div>
        <div id="message" class="alert alert-success" style="display: none;"></div>
    </div>
      <table class="table-centered" style="margin-top:2rem;">
        <thead>
          <tr>
            <th>ID</th>
            <th>montant</th>
          </tr>
        </thead>
        <tbody id="fond-tbody">
          <!-- Les clients seront insérés ici -->
        </tbody>
      </table>
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
        }

        window.onload = function() {
            chargerFond();
        };

        function succes() {
            const successDiv = document.getElementById("message");
            successDiv.innerHTML = "<i class='fas fa-check-circle mr-1'></i> Fonds enregistrés avec succès !";
            successDiv.style.display = "block";
            document.getElementById("montant").value = "";
            
            // Masquer le message après 5 secondes
            setTimeout(() => {
                successDiv.style.display = "none";
            }, 5000);
        }
    </script>
</body>

</html>