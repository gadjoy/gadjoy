<?php
require_once('/var/www/html/wp-includes/class-phpass.php');
$wp_hasher = new PasswordHash(8, true);
echo $wp_hasher->HashPassword('VWxSnbF9oreOH$WJoN');
?> 