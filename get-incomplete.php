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

$accounts = Account::whereNull('registered_at')->get();

if ($accounts->isEmpty()) {
    exit(1);
}

echo '#username;email;password;dob;country'.PHP_EOL;

foreach ($accounts as $account) {
    echo $account->formatForKinan().PHP_EOL;
}
