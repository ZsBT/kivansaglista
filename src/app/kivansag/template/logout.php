<?php

unset($_SESSION['USER']);
session_destroy();
header("Location: $context/");exit;

