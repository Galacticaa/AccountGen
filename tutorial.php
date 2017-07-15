<?php require 'vendor/autoload.php';

use AccountGen\Ptc\Account;

$config = include 'config/tutorial.php';

$db = include 'config/database.php';
$db['driver'] = 'mysql';

$capsule = new Capsule;
$capsule->addConnection($db);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$path = trim($config['map_path'], '/').'/'.trim($config['map_accounts_dir'], '/');

if (!($file = fopen($path.'/tutorial.csv', 'w'))) {
    echo "Failed to open accounts file for writing. Aborting.";
    exit;
}

$accounts = Account::whereNull('completed_at')->get();

if ($accounts->isEmpty()) {
    echo "No accounts found.";
    exit(1);
}

foreach ($accounts as $account) {
    fwrite($file, $account->formatForRocketMap().PHP_EOL);
}

fclose($file);

echo "Accounts saved to '{$path}/tutorial.csv'".PHP_EOL;
