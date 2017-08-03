<?php

use Phinx\Migration\AbstractMigration;

class AddInstanceToAccounts extends AbstractMigration
{
    public function change()
    {
        $instances = $this->table('instances', [
            'id' => false,
            'primary_key' => 'name',
        ]);

        $instances->addColumn('name', 'string', ['limit' => 50, 'default' => ''])
                  ->addColumn('last_restart', 'datetime', ['null' => true])
                  ->addColumn('last_total', 'integer', ['limit' => 5, 'default' => 0]);

        $instances->create();

        $accounts = $this->table('accounts');
        $accounts->addColumn('instance', 'string', [
            'after' => 'username',
            'limit' => 50,
            'null' => true
        ]);

        $accounts->update();
    }
}
