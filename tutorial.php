<?php require 'vendor/autoload.php';

use AccountGen\Ptc\Account;
use Illuminate\Database\Capsule\Manager as Capsule;

$config = include 'config/tutorial.php';

$db = include 'config/database.php';
$db['driver'] = 'mysql';

$capsule = new Capsule;
$capsule->addConnection($db);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$path = rtrim($config['map_path'], '/').'/'.rtrim($config['map_accounts_dir'], '/');

if (!($file = fopen($path.'/tutorial.csv', 'w'))) {
    echo "Failed to open accounts file for writing. Aborting.".PHP_EOL;
    exit;
}

$accounts = Account::whereNotNull('registered_at')
                   ->whereNotNull('activated_at')
                   ->whereNull('completed_at')->get();

if ($accounts->isEmpty()) {
    echo "No accounts found.".PHP_EOL;
    exit(1);
}

foreach ($accounts as $account) {
    fwrite($file, $account->formatForRocketMap().PHP_EOL);
}

fclose($file);

echo "Accounts saved to '{$path}/tutorial.csv'".PHP_EOL;
