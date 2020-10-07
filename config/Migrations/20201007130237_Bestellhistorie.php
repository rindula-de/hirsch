<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Bestellhistorie extends AbstractMigration
{
    public $autoId = false;

    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     * @return void
     */
    public function up()
    {
        $this->table('payhistory')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('paypalme_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'paypalme_id',
                ]
            )
            ->create();

        $this->table('payhistory')
            ->addForeignKey(
                'paypalme_id',
                'paypalmes',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                ]
            )
            ->update();

        $this->table('paypalmes')
            ->addColumn('email', 'string', [
                'after' => 'name',
                'default' => null,
                'length' => 255,
                'null' => true,
            ])
            ->update();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-down-method
     * @return void
     */
    public function down()
    {
        $this->table('payhistory')
            ->dropForeignKey(
                'paypalme_id'
            )->save();

        $this->table('paypalmes')
            ->removeColumn('email')
            ->update();

        $this->table('payhistory')->drop()->save();
    }
}
