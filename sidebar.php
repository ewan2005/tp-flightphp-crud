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
        <a href="index.php">
          <span class="icon"><i class="mdi mdi-desktop-mac"></i></span>
          <span class="menu-item-label">Dashboard</span>
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
      <!-- autres éléments du menu -->
    </ul>
  </div>
  <?php
  session_start();
  if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    echo '<div style="background:#f2f2f2;padding:10px 12px 10px 12px;margin-bottom:8px;border-radius:6px;display:flex;flex-direction:column;align-items:flex-start;">';
    echo 'Connecté : <b>' . htmlspecialchars($user['nom']) . '</b> (' . htmlspecialchars($user['role']) . ')<br>';
    echo '<a href="logout.php" style="color:#fff;background:#d9534f;padding:4px 10px;border-radius:4px;text-decoration:none;margin-top:6px;">Déconnexion</a>';
    echo '</div>';
  }
  ?>
</aside>
