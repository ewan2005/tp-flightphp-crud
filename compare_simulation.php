<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Compare des simulations</title>
  <link rel="stylesheet" href="css/main.css?v=1628755089081">
  <style>
    .simulation-list {
      display: flex;
      justify-content: space-between;
      gap: 40px;
      margin-bottom: 20px;
    }
    .simulation-column {
      flex: 1;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 8px;
      border: 1px solid #ccc;
      text-align: center;
    }
    .winner {
      background-color: #c8e6c9;
    }
    .loser {
      background-color: #ffcdd2;
    }
  </style>
</head>
<body>
  <?php include('sidebar.php'); ?>
  <section class="main-section">
    <h2>Comparaison Simulation Prêt</h2>
    <div class="simulation-list">
      <div class="simulation-column" id="liste1"></div>
      <div class="simulation-column" id="liste2"></div>
    </div>
    <div id="resultat-comparaison"></div>
  </section>

  <script>
    const apiBase = "http://localhost/tp-flightphp-crud/ws";
    let selected1 = null;
    let selected2 = null;

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

    function createSelectList(data, containerId, side) {
      const container = document.getElementById(containerId);
      const list = document.createElement("ul");

      data.forEach(sim => {
        const li = document.createElement("li");
        const label = document.createElement("label");
        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.name = side;
        checkbox.value = JSON.stringify(sim);

        checkbox.addEventListener("change", function () {
          document.querySelectorAll(`input[name=${side}]`).forEach(cb => cb !== this && (cb.checked = false));
          if (this.checked) {
            if (side === 'left') selected1 = sim;
            else selected2 = sim;
            tryCompare();
          } else {
            if (side === 'left') selected1 = null;
            else selected2 = null;
            document.getElementById("resultat-comparaison").innerHTML = "";
          }
        });

        label.appendChild(checkbox);
        label.appendChild(document.createTextNode(` Simulation #${sim.id_simulation} | ${sim.montant}Ar / ${sim.duree} mois`));
        li.appendChild(label);
        list.appendChild(li);
      });

      container.innerHTML = "<h4>Simulations</h4>";
      container.appendChild(list);
    }

    function tryCompare() {
      if (selected1 && selected2) {
        const fields = ["montant", "duree", "taux_annuel", "taux_assurance", "mensualite", "mensualite_totale", "cout_total"];
        const table = document.createElement("table");
        const thead = document.createElement("thead");
        thead.innerHTML = `<tr><th>Critère</th><th>Simulation 1</th><th>Simulation 2</th></tr>`;
        table.appendChild(thead);

        const tbody = document.createElement("tbody");

        fields.forEach(field => {
          const tr = document.createElement("tr");
          const val1 = parseFloat(selected1[field]);
          const val2 = parseFloat(selected2[field]);

          tr.innerHTML = `<td>${field.replace('_', ' ')}</td>`;

          if (val1 < val2) {
            tr.innerHTML += `<td class="winner">${val1}</td><td class="loser">${val2}</td>`;
          } else if (val2 < val1) {
            tr.innerHTML += `<td class="loser">${val1}</td><td class="winner">${val2}</td>`;
          } else {
            tr.innerHTML += `<td>${val1}</td><td>${val2}</td>`;
          }

          tbody.appendChild(tr);
        });

        table.appendChild(tbody);
        const resDiv = document.getElementById("resultat-comparaison");
        resDiv.innerHTML = "<h3>Comparaison Détaillée</h3>";
        resDiv.appendChild(table);
      }
    }

    function getAllSimulation() {
    ajax("GET", "/allSimulation", null, (data) => {
        console.log("Simulations récupérées :", data); 
        createSelectList(data, "liste1", "left");
        createSelectList(data, "liste2", "right");
    });
    }

    window.onload = getAllSimulation;
  </script>
</body>
</html>
