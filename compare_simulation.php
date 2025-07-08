<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Comparaison de simulations</title>
      <link rel="stylesheet" href="css/main.css?v=1628755089081">

  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #4a6fa5;
      --secondary-color: #166088;
      --accent-color: #4fc3f7;
      --light-color: #f8f9fa;
      --dark-color: #343a40;
      --success-color: #28a745;
      --danger-color: #dc3545;
      --border-radius: 8px;
      --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    body {
      font-family: 'Roboto', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f5f7fa;
      color: var(--dark-color);
      line-height: 1.6;
    }
    
    .main-section {
      max-width: 1200px;
      margin: 20px auto;
      padding: 20px;
      background-color: white;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
    }
    
    h2 {
      color: var(--secondary-color);
      margin-bottom: 30px;
      padding-bottom: 10px;
      border-bottom: 2px solid var(--accent-color);
    }
    
    h3 {
      color: var(--primary-color);
      margin-top: 30px;
    }
    
    h4 {
      color: var(--secondary-color);
      margin-bottom: 15px;
    }
    
    .simulation-list {
      display: flex;
      justify-content: space-between;
      gap: 30px;
      margin-bottom: 30px;
      flex-wrap: wrap;
    }
    
    .simulation-column {
      flex: 1;
      min-width: 300px;
      background-color: white;
      padding: 20px;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      border-top: 4px solid var(--accent-color);
    }
    
    .simulation-list ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .simulation-list li {
      margin-bottom: 10px;
      padding: 12px;
      background-color: var(--light-color);
      border-radius: var(--border-radius);
      transition: all 0.3s ease;
    }
    
    .simulation-list li:hover {
      transform: translateY(-2px);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .simulation-list label {
      display: flex;
      align-items: center;
      cursor: pointer;
      width: 100%;
    }
    
    .simulation-list input[type="checkbox"] {
      margin-right: 10px;
      transform: scale(1.2);
      accent-color: var(--primary-color);
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      box-shadow: var(--box-shadow);
      border-radius: var(--border-radius);
      overflow: hidden;
    }
    
    th, td {
      padding: 12px 15px;
      text-align: center;
      border: 1px solid #e0e0e0;
    }
    
    th {
      background-color: var(--primary-color);
      color: white;
      font-weight: 500;
    }
    
    tr:nth-child(even) {
      background-color: #f8f9fa;
    }
    
    .winner {
      background-color: rgba(40, 167, 69, 0.2);
      font-weight: 500;
      position: relative;
    }
    
    .winner::after {
      content: "✓";
      color: var(--success-color);
      position: absolute;
      right: 10px;
      font-weight: bold;
    }
    
    .loser {
      background-color: rgba(220, 53, 69, 0.1);
    }
    
    #resultat-comparaison {
      margin-top: 30px;
      background-color: white;
      padding: 25px;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      border-top: 4px solid var(--accent-color);
    }
    
    .comparison-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    
    .simulation-card {
      flex: 1;
      padding: 15px;
      margin: 0 10px;
      background-color: var(--light-color);
      border-radius: var(--border-radius);
      text-align: center;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .simulation-card h4 {
      margin-top: 0;
      color: var(--primary-color);
    }
    
    @media (max-width: 768px) {
      .simulation-list {
        flex-direction: column;
      }
      
      .simulation-column {
        width: 100%;
      }
      
      .comparison-header {
        flex-direction: column;
      }
      
      .simulation-card {
        margin: 10px 0;
      }
    }
  </style>
</head>
<body>
  <?php include('sidebar.php'); ?>
  <section class="main-section">
    <h2>Comparaison des simulations de prêt</h2>
    <p>Sélectionnez deux simulations à comparer dans les listes ci-dessous :</p>
    
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
        label.appendChild(document.createTextNode(` Simulation #${sim.id_simulation} - ${sim.montant}Ar (${sim.duree} mois)`));
        li.appendChild(label);
        list.appendChild(li);
      });

      container.innerHTML = `<h4>Sélectionnez une simulation</h4>`;
      container.appendChild(list);
    }

    function formatCurrency(amount) {
      return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'MGA' }).format(amount);
    }

    function formatMonths(months) {
      return months >= 12 ? `${Math.floor(months/12)} an(s) et ${months%12} mois` : `${months} mois`;
    }

    function formatPercentage(rate) {
      return `${parseFloat(rate).toFixed(2)}%`;
    }

    function tryCompare() {
      if (selected1 && selected2) {
        const resDiv = document.getElementById("resultat-comparaison");
        resDiv.innerHTML = `
          <h3>Résultats de la comparaison</h3>
          <div class="comparison-header">
            <div class="simulation-card">
              <h4>Simulation #${selected1.id_simulation}</h4>
              <p><strong>Montant:</strong> ${formatCurrency(selected1.montant)}</p>
              <p><strong>Durée:</strong> ${formatMonths(selected1.duree)}</p>
            </div>
            <div class="simulation-card">
              <h4>Simulation #${selected2.id_simulation}</h4>
              <p><strong>Montant:</strong> ${formatCurrency(selected2.montant)}</p>
              <p><strong>Durée:</strong> ${formatMonths(selected2.duree)}</p>
            </div>
          </div>
        `;

        const fields = [
          { key: "montant", label: "Montant du prêt", formatter: formatCurrency },
          { key: "duree", label: "Durée", formatter: formatMonths },
          { key: "taux_annuel", label: "Taux annuel", formatter: formatPercentage },
          { key: "taux_assurance", label: "Taux d'assurance", formatter: formatPercentage },
          { key: "mensualite", label: "Mensualité", formatter: formatCurrency },
          { key: "mensualite_totale", label: "Mensualité avec assurance", formatter: formatCurrency },
          { key: "cout_total", label: "Coût total du crédit", formatter: formatCurrency }
        ];

        const table = document.createElement("table");
        const thead = document.createElement("thead");
        thead.innerHTML = `
          <tr>
            <th>Critère</th>
            <th>Simulation #${selected1.id_simulation}</th>
            <th>Simulation #${selected2.id_simulation}</th>
          </tr>
        `;
        table.appendChild(thead);

        const tbody = document.createElement("tbody");

        fields.forEach(field => {
          const tr = document.createElement("tr");
          const val1 = parseFloat(selected1[field.key]);
          const val2 = parseFloat(selected2[field.key]);

          tr.innerHTML = `<td><strong>${field.label}</strong></td>`;

          if (val1 < val2) {
            tr.innerHTML += `
              <td class="winner">${field.formatter ? field.formatter(val1) : val1}</td>
              <td class="loser">${field.formatter ? field.formatter(val2) : val2}</td>
            `;
          } else if (val2 < val1) {
            tr.innerHTML += `
              <td class="loser">${field.formatter ? field.formatter(val1) : val1}</td>
              <td class="winner">${field.formatter ? field.formatter(val2) : val2}</td>
            `;
          } else {
            tr.innerHTML += `
              <td>${field.formatter ? field.formatter(val1) : val1}</td>
              <td>${field.formatter ? field.formatter(val2) : val2}</td>
            `;
          }

          tbody.appendChild(tr);
        });

        table.appendChild(tbody);
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