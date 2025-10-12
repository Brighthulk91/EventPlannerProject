<?php
require_once 'helpers.php';
session_unset();
session_destroy();
redirect('index.php');
