<?php

use Phinx\Migration\AbstractMigration;

class AddBatchCounters extends AbstractMigration
{
    public function change()
    {
        $accounts = $this->table('accounts');
        $instances = $this->table('instances');

        $instances->addColumn('current_batch', 'integer', ['default' => 0, 'null' => false]);
        $instances->update();

        $accounts->addColumn('batch', 'integer', ['null' => true]);
        $accounts->update();
    }
}
