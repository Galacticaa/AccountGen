<?php namespace AccountGen\Accounts;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this->setName('accounts:generate');
        $this->setDescription("Add a new batch of accounts to an instance's account pools");

        $this->addArgument('instance', InputArgument::OPTIONAL, "Set the instance to apply accounts to");
        $this->addOption('batch', null, InputOption::VALUE_REQUIRED, "Batch number to which accounts will be assigned");

        $this->addOption('unique', 'u', InputOption::VALUE_REQUIRED, "How many base names to generate");
        $this->addOption('multiples', 'm', InputOption::VALUE_REQUIRED, "Number of accounts per base name");
        $this->addOption('username', null, InputOption::VALUE_REQUIRED, "Define a custom base name, eg 'joeBloggs###??'");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $instance = $input->getArgument('instance');
        $custom = $input->getOption('username');

        if ($instance !== null) {
            $instances = @include 'config/instances.php';

            if (!$instances || !count($instances)) {
                throw new Exception("No instances defined in config/instances.php");
            }

            if (!array_key_exists($instance, $instances)) {
                throw new Exception("Instance '{$instance}' not found in config/instances.php");
            }

            $unique = $input->getOption('unique') ?? $instances[$instance]['names'] ?? 1;
            $multiples = $input->getOption('multiples');

            if ($multiples === null) {
                $max = $instances[$instance]['total'];
                $multiples = ceil($max / $unique);
            } else {
                $max = $unique * $multiples;
            }
        } else {
            $unique = $input->getOption('unique') ?? 1;
            $multiples = $input->getOption('multiples') ?? 15;
            $max = $unique * $multiples;
        }

        $output->writeLn("#username;email;password;dob;country");

        $factory = new Generator($instance, $input->getOption('batch'));

        for ($i = 0; $i < $unique; $i++) {
            $count = (($i + 1) * $multiples) < $max
                ? $multiples
                : $max - $multiples * $i;

            if ($count < 1) {
                // More unique names than accounts needed
                break;
            }

            $accounts = $factory->generateBatch($count, $i === 0 ? $custom : null);

            foreach ($accounts as $account) {
                $output->writeLn($account->formatForKinan());
            }
        }
    }
}
