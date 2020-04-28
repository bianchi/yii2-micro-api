<?php

use yii\db\Migration;

class m200428_185343_create_tables extends Migration
{
    public function safeUp()
    {
        $this->createTable('companies', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('companies');

        return true;
    }
}
