<?php

use yii\db\Migration;

class m200428_185343_create_tables extends Migration
{
    public function safeUp()
    {
        $this->createTable('companies', [
            'id' => $this->primaryKey()->notNull(),
            'name' => $this->string(50)->notNull(),
            'key' => $this->string(120)->notNull(),
            'secret' => $this->string(120)->notNull()
        ]);

        $this->createTable('users', [
            'id' => $this->primaryKey()->notNull(),
            'name' => $this->string(60)->notNull(),
            'email' => $this->string(200)->notNull(),
            'password' => $this->string(255)->notNull(),
            'access_token' => $this->string(255),
            'last_login' => $this->dateTime(),
            'last_api_request' => $this->dateTime(),
            'is_admin' => $this->boolean()->notNull()->defaultValue(false),
            'can_order_document' => $this->boolean()->notNull()->defaultValue(false),
            'can_insert_credits' => $this->boolean()->notNull()->defaultValue(false),
            'can_see_reports' => $this->boolean()->notNull()->defaultValue(false),
            'can_see_billing' => $this->boolean()->notNull()->defaultValue(false),
            'company_id' => $this->integer()->notNull()
        ]);

        $this->createIndex(
            'idx-users-email',
            'users',
            'email'
        );

        $this->createIndex(
            'idx-users-password',
            'users',
            'password'
        );

        $this->addForeignKey(
            'fk-users-company_id',
            'users',
            'company_id',
            'companies',
            'id'
        );

        $this->insert('companies', [
            'name' => 'JHOB',
            'key' => 'key123',
            'secret' => 'secret123'
        ]);

        $this->insert('users', [
            'name' => 'Jhonatan',
            'email' => 'jhonatanbianchi@gmail.com',
            'password' => password_hash('123', PASSWORD_BCRYPT),
            'access_token' => 'abc123',
            'company_id' => 1
        ]);
    }

    public function safeDown()
    {
        $this->dropIndex('idx-users-email', 'users');
        $this->dropIndex('idx-users-password', 'users');
        $this->dropForeignKey('fk-users-company_id', 'users');
        $this->dropTable('users');
        $this->dropTable('companies');

        return true;
    }
}
