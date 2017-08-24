<?php namespace AccountGen\Instances;

use DateTime;
use Exception;
use AccountGen\Instance;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{
    protected function configure()
    {
        $this->setName('instance:update');
        $this->setDescription("Update accounts and restart the specified instance");

        $this->addArgument('instance', InputArgument::REQUIRED, "The instance to update accounts for");
        $this->addOption('force', 'f', InputOption::VALUE_NONE, "Whether to force the instance(s) to update");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $instances = $this->loadInstances(
            $input->getArgument('instance'),
            $force = $input->getOption('force')
        );

        if ($instances->isEmpty()) {
            $output->writeLn("No instances found matching criteria.");
            exit(1);
        }

        $config = @include 'config/tutorial.php';

        foreach ($instances as $instance) {
            $name = $instance->name;

            $this->loadInstanceAccounts($instance, $config);

            if (!$this->canContinue($output, $instance, $force)) {
                continue;
            }

            if (!$this->writeInstanceAccounts($output, $instance, $config)) {
                $output->writeLn("Account write failed! Not restarting {$name}.");
                continue;
            }

            $this->restartInstance($output, $instance);
        }
    }

    protected function canContinue(OutputInterface $output, $instance, $force = false)
    {
        $data = $this->instances[$instance->name];

        if ($data->complete <= 1) {
            $output->writeLn("Instance {$instance->name} needs more accounts. {$data->complete} complete.");

            return false;
        }

        if ($data->complete == $instance->last_total) {
            $action = $force ? 'Forcing write for ' : 'Not writing accounts for ';
            $output->writeLn('No change since last run! '.$action.$instance->name);

            return $force;
        }

        return true;
    }

    protected function loadInstances($instance, $force = false)
    {
        $instances = Instance::query();

        if ($instance != 'all') {
            $instances = $instances->whereName($instance);
        }

        if (!$force) {
            $interval = (new DateTime())->modify('-30 minutes')->format('Y-m-d H:i:s');

            $instances = $instances->whereNull('last_restart')
                                    ->orWhere('last_restart', '<', $interval);
        }

        return $instances->with('accounts')->get();
    }

    protected function loadInstanceAccounts($instance, $config)
    {
        $data = (object) [
            'name' => $instance->name,
            'accounts' => [],
            'incomplete' => 0,
            'old' => 0,
        ];

        foreach ($instance->accounts as $account) {
            if ($account->batch != $instance->current_batch) {
                $data->old++;
                continue;
            }

            $complete_key = $config['use_tutorial'] ? 'completed_at' : 'activated_at';

            if (null === $account->$complete_key) {
                $data->incomplete++;
                continue;
            }

            $data->accounts[] = $account->formatForRocketMap();
        }

        $data->complete = count($data->accounts);
        $this->instances[$instance->name] = $data;

        echo sprintf("Instance '%s' has %s prepared accounts, %s incomplete and %s old.",
            $instance->name, $data->complete, $data->incomplete, $data->old
        ).PHP_EOL;

        return $data;
    }

    protected function writeInstanceAccounts(OutputInterface $output, $instance, $config)
    {
        $rmap_path = rtrim($config['map_path'], '/');
        $acct_path = $rmap_path.'/'.rtrim($config['map_accounts_dir'], '/');

        $file = strtolower($instance->name).'.csv';

        $output->writeLn("Writing accounts to {$acct_path}/{$file}...");

        return file_put_contents(
            $acct_path.'/'.$file,
            implode("\n", $this->instances[$instance->name]->accounts)
        );
    }

    protected function restartInstance(OutputInterface $output, $instance)
    {
        $generator = $this->getApplication()->find('service');
        $generator->run(new ArrayInput([
            'action' => 'restart',
            'service' => 'ns',
            '--instance' => $instance->name,
        ]), $output);

        $instance->last_total = $this->instances[$instance->name]->complete;
        $instance->last_restart = date('Y-m-d H:i:s');
        $instance->save();

        return $instance;
    }
}
