<!-- aside.html -->
<aside class="aside is-placed-left is-expanded">
  <div class="aside-tools">
    <div>
      Gestion <b class="font-black">Banquaire</b>
    </div>
  </div>
  <div class="menu is-menu-main">
    <ul class="menu-list">
      <li class="active">
        <a href="dashboard.php">
          <span class="icon"><i class="mdi mdi-desktop-mac"></i></span>
          <span class="menu-item-label">Dashboard</span>
        </a>
      </li>
    </ul>
    <ul class="menu-list">
      <li>
        <a href="AjouteFond.php">
          <span class="icon"><i class="mdi mdi-bank"></i></span>
          <span class="menu-item-label">Ajoute Fond</span>
        </a>
      </li>
    </ul>
    <ul class="menu-list">
      <li>
        <a href="remboursement.php">
          <span class="icon"><i class="mdi mdi-bank"></i></span>
          <span class="menu-item-label">rembource Prêts</span>
        </a>
      </li>
    </ul>
    <ul class="menu-list">
      <li>
        <a href="typePret.php">
          <span class="icon"><i class="mdi mdi-bank"></i></span>
          <span class="menu-item-label">Types de Prêt</span>
        </a>
      </li>
    </ul>
        <ul class="menu-list">
        <li>
          <a href="pret_gestion.php">
            <span class="icon"><i class="mdi mdi-bank"></i></span>
            <span class="menu-item-label">Gestion des Prêts</span>
          </a>
        </li>
      </ul>
        <ul class="menu-list">
        <li>
          <a href="pret_validation.php">
            <span class="icon"><i class="mdi mdi-bank"></i></span>
            <span class="menu-item-label">Gestion des Validations Prêts</span>
          </a>
        </li>
      </ul>
      <ul class="menu-list">
        <li>
          <a href="ajout_client.php">
            <span class="icon"><i class="mdi mdi-bank"></i></span>
            <span class="menu-item-label">Ajouter client</span>
          </a>
        </li>
      </ul>
      <ul class="menu-list">
        <li>
          <a href="interets.php">
            <span class="icon"><i class="mdi mdi-percent"></i></span>
            <span class="menu-item-label">Gestion des Intérêts</span>
          </a>
        </li>
      </ul>
    </div>
    <?php
    if (isset($_SESSION['user'])) {
      $user = $_SESSION['user'];
      echo '<div style="background:#f9f9f9;padding:16px 18px 18px 18px;margin:0 0 80px 0;border-radius:14px 14px 0 0;box-shadow:0 -2px 8px rgba(0,0,0,0.04);display:flex;flex-direction:column;align-items:flex-start;width:100%;">';
      echo '<div style="color:#222;font-size:15px;margin-bottom:6px;">Connecté :</div>';
      echo '<div style="font-weight:bold;font-size:16px;margin-bottom:2px;">' . htmlspecialchars($user['nom']) . '</div>';
      echo '<div style="font-size:13px;margin-bottom:12px;">(' . htmlspecialchars($user['role']) . ')</div>';
      echo '<a href="logout.php" style="width:100%;display:block;text-align:center;color:#fff;background:#d9534f;padding:10px 0;border-radius:6px;text-decoration:none;font-weight:600;letter-spacing:1px;font-size:15px;">Déconnexion</a>';
      echo '</div>';
    }
    ?>
  </div>
</aside>
