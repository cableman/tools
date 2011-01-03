<?php
// Set the access key, so not every body can POST your server to pull from github
define('ACCESS_KEY', '123456789');

// Site configuration
define('BASE_PATH', '/var/www');
define('SUB_PATH', '/htdocs/');

// Error code
define('ERROR_ACCESS', -1);
define('ERROR_PAYLOAD', -2);

// Validate access key
if (!(isset($_POST['key']) && (ACCESS_KEY == $_POST['key']))) {
  error_log("bad key: none set");
  exit(ERROR_ACCESS);
}


// POST json payload
$json = NULL;
if (isset($_POST['payload'])) {
  $json = $_POST['payload'];
}
else {
  error_log("bad payload: none set");
  exit(ERROR_PAYLOAD);
}

// POST path to pull
if (isset($_POST['path'])) {
  $path = BASE_PATH . $_POST['path'] . SUB_PATH;
}

// Pull repos (run as www-data, so sudo)
$cmd  = 'sudo -u deploy sh -c "';
$cmd .= 'cd ' . $path . ' && git pull';
$cmd .= '"';
exec($cmd, $output);
error_log('Git pull request: ' . implode(' - ', $output));

$cmd  = 'sudo -u deploy sh -c "';
$cmd .= 'drush -r ' . $path . ' cc all';
$cmd .= '"';
exec($cmd, $output1);
error_log('Drush: ' . implode(' - ', $output1));

?>
