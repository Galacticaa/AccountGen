<?php namespace AccountGen\Services;

class Server
{
    protected $output;

    protected $mapdir = '/rmap';

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function start()
    {
        $this->output->write("Starting RocketMap server... ");

        $pids = $this->getPids();

        if (count($pids)) {
            $this->output->writeLn('Already running!');
            return;
        }

        system("cd {$this->mapdir} && tmux new-session -s \"mapserver\" -d ./runserver.py");

        $this->output->writeLn('Done!');
    }

    public function stop()
    {
        $this->output->write("Stopping RocketMap server... ");

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
        $cmd = "ps axf | grep runserver.py | grep -v grep | grep -v tmux | grep -v '\-cf' | awk '{ print \$1 }'";

        exec($cmd, $pids);

        return $pids;
    }
}
