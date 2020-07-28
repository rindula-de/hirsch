<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class ForDate extends AbstractMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     * @return void
     */
    public function up()
    {
        $this->table('orders')
            ->addColumn('for', 'date', [
                'after' => 'paypalme',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();

        $this->table('orders')
            ->addForeignKey(
                'paypalme',
                'paypalmes',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'NO_ACTION',
                ]
            )
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
        $this->table('orders')
            ->dropForeignKey(
                'paypalme'
            )->save();

        $this->table('orders')
            ->removeColumn('for')
            ->update();

        $this->table('orders')
            ->addForeignKey(
                'paypalme',
                'paypalmes',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'RESTRICT',
                ]
            )
            ->update();
    }
}
