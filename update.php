<?php require 'vendor/autoload.php';

use AccountGen\Instance;
use Illuminate\Database\Capsule\Manager as Capsule;

$config = include 'config/tutorial.php';

$db = include 'config/database.php';
$db['driver'] = 'mysql';

$capsule = (new Capsule);
$capsule->addConnection($db);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$restartInterval = (new DateTime())->modify('-30 minutes')->format('Y-m-d H:i:s');

$instances = Instance::whereNull('last_restart')->orWhere('last_restart', '<', $restartInterval)->with('accounts')->get();

if ($instances->isEmpty()) {
    echo "All instances have been restarted recently!";
}

$output = [];

foreach ($instances as $instance) {
    $incomplete = $old = 0;
    $accounts = [];

    foreach ($instance->accounts as $account) {
        if ($account->batch != $instance->current_batch) {
            $old++;
            continue;
        }

        if (null === $account->completed_at) {
            $incomplete++;
            continue;
        }

        $accounts[] = $account->formatForRocketMap();
    }

    $complete = count($accounts);

    echo sprintf("Instance '%s' has %s prepared accounts, %s incomplete and %s old.",
        $instance->name, $complete, $incomplete, $old
    ).PHP_EOL;

    if ($complete === 0) {
        echo "Instance {$instance->name} has no accounts to write.".PHP_EOL.PHP_EOL;
        continue;
    } elseif ($complete === 1) {
        echo "Instance {$instance->name} needs more accounts. Only 1 complete.".PHP_EOL.PHP_EOL;
        continue;
    } elseif ($complete == $instance->last_total) {
        echo "Not writing accounts for {$instance->name}, no change since last run.".PHP_EOL.PHP_EOL;
        continue;
    }

    $rmap_path = rtrim($config['map_path'], '/');
    $acct_path = $rmap_path.'/'.rtrim($config['map_accounts_dir'], '/');
    $file = strtolower($instance->name).'.csv';
    echo "Writing accounts to {$acct_path}/{$file}...".PHP_EOL.PHP_EOL;

    file_put_contents($acct_path.'/'.$file, implode("\n", $accounts));

    system('/bin/bash restart.sh '.strtolower($instance->name));

    $instance->last_total = $complete;
    $instance->last_restart = date('Y-m-d H:i:s');
    $instance->save();
}
