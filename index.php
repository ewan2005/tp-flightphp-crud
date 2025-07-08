<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion utilisateur</title>
  <link rel="stylesheet" href="css/main.css">
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }
    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f3f4f6;
    }
    .main-section {
      min-height: unset;
      height: auto;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  </style>
</head>
<body>
  <div class="main-section">
    <div class="login-box">
      <h2>Connexion</h2>
      <div id="message"></div>
      <form id="loginForm" onsubmit="login(event)">
        <input type="email" id="email" placeholder="Email" required><br>
        <input type="password" id="mot_de_passe" placeholder="Mot de passe" required><br>
        <button type="submit">Se connecter</button>
      </form>
    </div>
  </div>

  <script>
    const apiBase = "http://localhost/tp-flightphp-crud/ws";

    function ajax(method, url, data, callback) {
      const xhr = new XMLHttpRequest();
      xhr.open(method, apiBase + url, true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = () => {
        if (xhr.readyState === 4) {
          callback(JSON.parse(xhr.responseText));
        }
      };
      xhr.send(data);
    }

  function login(event) {
    event.preventDefault();
    const email = document.getElementById("email").value;
    const mot_de_passe = document.getElementById("mot_de_passe").value;
    const messageDiv = document.getElementById("message");

    const data = `email=${encodeURIComponent(email)}&mot_de_passe=${encodeURIComponent(mot_de_passe)}`;

    ajax("POST", "/login", data, (res) => {
      if (res.success) {
        messageDiv.innerHTML = "<span class='success'>Connexion réussie !</span>";
        setTimeout(() => { window.location = "dashboard.php"; }, 1000); // Redirection ici
      } else {
        messageDiv.innerHTML = "<span class='error'>" + (res.message || "Erreur de connexion") + "</span>";
      }
    });
  }
  </script>
</body>
</html>