<?php


use \Core\Data\Migration;

class ExampleMigration extends Migration
{
    public function up()
    {
		if (!$this->hasTable('user')) {
			$this->table('user')
				->addColumn('name', 'string', ['limit' => 255])
				->addColumn('email', 'string', ['limit' => 500])
				->addColumn('password', 'string', ['limit' => 32])
				->addTimestamps()
				->addColumn('deleted_at', 'timestamp', ['null' => true])
				->save();
		}
		$this->insert('user', ['name' => 'Admin', 'email' => 'admin@admin.com', 'password' => md5('123456')]);
		parent::up();
    }

    public function down()
	{
		$this->dropTableIfExists('user');
		parent::down();
	}
}
