<?php namespace AccountGen\Services;

class Kinan
{
    protected $output;

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function start()
    {
        $this->output->write("Starting Kinan... ");

        $pids = $this->getPids();

        if (count($pids)) {
            $this->output->writeLn('Already running!');
            return;
        }

        system('cd Kinan && tmux new-session -s KinanMail -d java -jar KinanCity-mail-1.3.2-SNAPSHOT.jar');

        $this->output->writeLn('Done!');
    }

    public function stop()
    {
        $this->output->write("Stopping kinan... ");

        $pids = $this->getPids();

        if (!count($pids)) {
            $this->output->writeLn("Not running!");
            return;
        }

        foreach ($pids as $pid) {
            $this->output->write('Stopping '.$pid.'... ');
            system(sprintf("kill -15 %s", $pid));
        }

        $this->output->writeLn('Done!');
    }

    protected function getPids()
    {
        exec('ps axf | grep "KinanCity-mail" | grep -v grep | grep -v tmux | awk \'{ print $1 }\'', $pids);

        return $pids;
    }
}
