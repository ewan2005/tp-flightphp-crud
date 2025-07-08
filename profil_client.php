<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil client - Gestion Bancaire</title>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    /* Style cohérent avec la sidebar */
    .content-wrapper {
      margin-left: 220px;
      padding: 30px;
      min-height: 100vh;
      background-color: #f8fafc;
    }
    
    .client-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      padding: 30px;
      max-width: 800px;
      margin: 0 auto;
    }
    
    .client-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #e2e8f0;
      padding-bottom: 20px;
      margin-bottom: 25px;
    }
    
    .client-title {
      font-size: 1.75rem;
      font-weight: 600;
      color: #2d3748;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .client-title i {
      color: #4299e1;
      font-size: 1.5rem;
    }
    
    .client-info {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }
    
    .info-item {
      margin-bottom: 15px;
    }
    
    .info-label {
      font-weight: 500;
      color: #4a5568;
      margin-bottom: 5px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .info-label i {
      color: #718096;
      font-size: 0.9rem;
    }
    
    .info-value {
      padding: 10px 15px;
      background-color: #f8fafc;
      border-radius: 6px;
      border-left: 3px solid #4299e1;
    }
    
    .action-buttons {
      display: flex;
      gap: 15px;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #e2e8f0;
    }
    
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 12px 24px;
      border-radius: 6px;
      font-weight: 500;
      font-size: 15px;
      cursor: pointer;
      transition: all 0.3s;
      text-decoration: none;
    }
    
    .btn-primary {
      background-color: #4299e1;
      color: white;
      border: none;
    }
    
    .btn-primary:hover {
      background-color: #3182ce;
    }
    
    .btn-secondary {
      background-color: #e2e8f0;
      color: #4a5568;
      border: none;
    }
    
    .btn-secondary:hover {
      background-color: #cbd5e0;
    }
    
    .btn i {
      margin-right: 8px;
    }
    
    @media (max-width: 768px) {
      .client-info {
        grid-template-columns: 1fr;
      }
      
      .content-wrapper {
        margin-left: 0;
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <?php include('sidebar.php'); ?>
  <section class="section dashboard-section" style="margin-left: 260px;width:80%;">
  <div class="content-wrapper">
    <div id="message" style="max-width:800px;margin:0 auto 1rem auto;"></div>
    <div class="client-card">
      <div class="client-header">
        <h1 class="client-title">
          <i class="fas fa-user-circle"></i>
          <span id="client-name">Profil du client</span>
        </h1>
      </div>
      
      <div class="client-info" id="client-details">
        <!-- Les informations seront chargées dynamiquement ici -->
      </div>
      
      <div class="action-buttons">
        <a id="lien-pret" href="#" class="btn btn-primary">
          <i class="fas fa-hand-holding-usd"></i> Créer un prêt
        </a>
        <a href="liste_client.php" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
      </div>
    </div>
  </div>
  <section class="section dashboard-section" style="margin-left: 260px;width:80%;">
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

    function chargerProfilClient(id) {
      ajax("GET", `/clients/${id}`, null, (res, status) => {
        if (status === 200 && res && res.data) {
          const client = res.data;
          
          // Mise à jour du titre
          document.getElementById("client-name").textContent = `Profil de ${client.nom} ${client.prenom}`;
          
          // Mise à jour des informations client
          const detailsContainer = document.getElementById("client-details");
          detailsContainer.innerHTML = `
            <div class="info-item">
              <div class="info-label">
                <i class="fas fa-id-card"></i> ID Client
              </div>
              <div class="info-value">${client.id_client}</div>
            </div>
            <div class="info-item">
              <div class="info-label">
                <i class="fas fa-user"></i> Nom
              </div>
              <div class="info-value">${client.nom}</div>
            </div>
            <div class="info-item">
              <div class="info-label">
                <i class="fas fa-user"></i> Prénom
              </div>
              <div class="info-value">${client.prenom}</div>
            </div>
            <div class="info-item">
              <div class="info-label">
                <i class="fas fa-envelope"></i> Email
              </div>
              <div class="info-value">${client.email}</div>
            </div>
            <div class="info-item">
              <div class="info-label">
                <i class="fas fa-phone"></i> Téléphone
              </div>
              <div class="info-value">${client.telephone || 'Non renseigné'}</div>
            </div>
            <div class="info-item">
              <div class="info-label">
                <i class="fas fa-birthday-cake"></i> Date de naissance
              </div>
              <div class="info-value">${client.date_naissance ? new Date(client.date_naissance).toLocaleDateString('fr-FR') : 'Non renseignée'}</div>
            </div>
          `;
          
          // Mise à jour du lien pour créer un prêt
          document.getElementById("lien-pret").href = `pret_gestion.php?id_client=${client.id_client}`;
          
        } else {
          showMessage("Erreur lors du chargement du profil client", "error");
          const detailsContainer = document.getElementById("client-details");
          detailsContainer.innerHTML = `
            <div class="info-item" style="grid-column: span 2;">
              <div class="info-value" style="color: #e53e3e;">
                <i class="fas fa-exclamation-triangle"></i> Erreur lors du chargement du profil client
              </div>
            </div>
          `;
        }
      });
    }

    window.onload = function() {
      const params = new URLSearchParams(window.location.search);
      const id = params.get("id");
      if (id) {
        chargerProfilClient(id);
      } else {
        const detailsContainer = document.getElementById("client-details");
        detailsContainer.innerHTML = `
          <div class="info-item" style="grid-column: span 2;">
            <div class="info-value" style="color: #e53e3e;">
              <i class="fas fa-exclamation-triangle"></i> Aucun identifiant client fourni
            </div>
          </div>
        `;
      }
    };
  </script>
</body>
</html>