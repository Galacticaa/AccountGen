<?php require 'vendor/autoload.php';

use AccountGen\Account;
use Illuminate\Database\Capsule\Manager as Capsule;

$config = include 'config/tutorial.php';

$db = include 'config/database.php';
$db['driver'] = 'mysql';

$capsule = new Capsule;
$capsule->addConnection($db);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$path = rtrim($config['map_path'], '/').'/'.rtrim($config['map_accounts_dir'], '/');

$logPath = '/tmp/complete.csv';
$tutPath = $path.'/tutorial.csv';

$logLines = explode("\n", file_get_contents($logPath));

$accounts = Account::whereNull('completed_at')->get();
$completed = 0;

foreach ($accounts as $account) {
    if (!in_array($account->username, $logLines)) {
        continue;
    }

    $account->completed_at = date('Y-m-d H:i:s');
    $account->save();

    $completed++;
}

echo "Marked {$completed} accounts as having completed tutorial.".PHP_EOL;
