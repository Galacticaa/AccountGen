<?php

use Phinx\Migration\AbstractMigration;

class AddInstanceToAccounts extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
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
