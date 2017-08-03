<?php namespace AccountGen\Instances;

use Exception;
use AccountGen\Instance;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RegenCommand extends Command
{
    protected function configure()
    {
        $this->setName('instance:regen');
        $this->setDescription("Replace all accounts for the specified instance");

        $this->addArgument('instance', InputArgument::REQUIRED, "Set the instance to apply accounts to");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $instance = ucfirst($input->getArgument('instance'));
        $config = @include 'config/instances.php';

        if (!$config || !count($config)) {
            throw new Exception("No instances defined in config/instances.php");
        }

        if ($instance != 'all' && !array_key_exists($instance, $config)) {
            throw new Exception("Instance '{$instance}' not found in config/instances.php");
        }

        $names = $instance == 'All' ? array_keys($config) : [$instance];
        $instances = Instance::whereIn('name', $names)->get();

        foreach ($instances as $instance) {
            $name = $instance->name;
            $batch = $instance->current_batch;

            $instance->current_batch++;
            $instance->save();

            $output->writeLn(["Bumped instance '{$name}' from batch {$batch} to {$instance->current_batch}", '']);

            $generator = $this->getApplication()->find('accounts:generate');
            $generator->run(new ArrayInput([
                'command' => 'accounts:generate',
                'instance' => $name,
                '--batch' => $instance->current_batch,
            ]), $output);
        }
    }
}
