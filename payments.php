<?php
require_once 'config.php';
require_once 'helpers.php';
require_login();
include 'partials/header.php';

// If admin-style views needed, admin functions would show payments list
echo "<div class='card'><h2>Payments</h2><p>Payment records are stored. View via DB or add admin page.</p></div>";

include 'partials/footer.php';
