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
            'secret' => $this->string(120)->notNull(),
            'max_users' => $this->integer()->notNull()->defaultValue(10)
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

        $this->createTable('document_types', [
            'id' => $this->primaryKey()->notNull(),
            'name' => $this->string(120)
        ]);

        $this->createTable('order_statuses', [
            'id' => $this->primaryKey()->notNull(),
            'name' => $this->string(50)->notNull()
        ]);

        $this->createTable('orders', [
            'id' => $this->bigPrimaryKey()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'document_type_id' => $this->integer()->notNull(),
            'current_status_id' => $this->integer()->notNull(),
            'placed_time' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-orders-user_id',
            'orders',
            'user_id'
        );

        $this->addForeignKey(
            'fk-orders-user_id',
            'orders',
            'user_id',
            'users',
            'id'
        );

        $this->createIndex(
            'idx-orders-document_type_id',
            'orders',
            'document_type_id'
        );

        $this->addForeignKey(
            'fk-orders-document_type_id',
            'orders',
            'document_type_id',
            'document_types',
            'id'
        );

        $this->createIndex(
            'idx-orders-current_status_id',
            'orders',
            'current_status_id'
        );

        $this->addForeignKey(
            'fk-orders-current_status_id',
            'orders',
            'current_status_id',
            'order_statuses',
            'id'
        );

        $this->createTable('order_history', [
            'order_id' => $this->bigInteger()->notNull(),
            'status_id' => $this->integer()->notNull(),
            'event_time' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-order_history-order_id',
            'order_history',
            'order_id'
        );

        $this->addForeignKey(
            'fk-order_history-order_id',
            'order_history',
            'order_id',
            'orders',
            'id'
        );

        $this->createIndex(
            'idx-order_history-status_id',
            'order_history',
            'status_id'
        );

        $this->addForeignKey(
            'fk-order_history-status_id',
            'order_history',
            'status_id',
            'order_statuses',
            'id'
        );

        $this->populate();

        if (YII_ENV_DEV) {
            $this->populateTest();
        }
    }

    private function populate()
    {
        $this->insert('document_types', ['name' => 'Certidão de nascimento']);
        $this->insert('document_types', ['name' => 'Certidão de óbito']);
        $this->insert('document_types', ['name' => 'Certidão de casamento']);
        $this->insert('document_types', ['name' => 'Certidão de protesto']);
        $this->insert('document_types', ['name' => 'Certidão de procuração']);
        $this->insert('document_types', ['name' => 'Certidão de escritura']);
        $this->insert('document_types', ['name' => 'Certidão negativa de débitos trabalhistas - CNDT']);
        $this->insert('document_types', ['name' => 'Certidão negativa de débitos de tributos federais e dívida ativa da União']);
        $this->insert('document_types', ['name' => 'Certidão Negativa de débitos tributários não Inscritos em dívida ativa(São Paulo - Estadual)']);
        $this->insert('document_types', ['name' => 'Certidão negativa do FGTS (Certificado de regularidade fiscal CRF)']);
        $this->insert('document_types', ['name' => 'ITR - Certidão de débitos relativos a tributos federais e à dívida ativa da União de imóvel rural']);

        $this->insert('order_statuses', ['name' => 'Em andamento']);
        $this->insert('order_statuses', ['name' => 'Localizado']);
        $this->insert('order_statuses', ['name' => 'Enviado']);
        $this->insert('order_statuses', ['name' => 'Finalizado']);
        $this->insert('order_statuses', ['name' => 'Não encontrado']);
    }

    private function populateTest()
    {
        $this->insert('companies', [
            'name' => 'JHOB',
            'key' => 'key123',
            'secret' => 'secret123'
        ]);

        $this->insert('users', [
            'name' => 'User default',
            'email' => 'user@gmail.com',
            'password' => password_hash('123', PASSWORD_BCRYPT),
            'company_id' => 1
        ]);

        $this->insert('orders', [
            'user_id' => 1,
            'document_type_id' => 1,
            'current_status_id' => 4
        ]);

        $this->insert('order_history', [
            'order_id' => 1,
            'status_id' => 1
        ]);

        $this->insert('order_history', [
            'order_id' => 1,
            'status_id' => 2
        ]);

        $this->insert('order_history', [
            'order_id' => 1,
            'status_id' => 3
        ]);

        $this->insert('order_history', [
            'order_id' => 1,
            'status_id' => 4
        ]);

        $this->insert('orders', [
            'user_id' => 1,
            'document_type_id' => 2,
            'current_status_id' => 2
        ]);

        $this->insert('order_history', [
            'order_id' => 2,
            'status_id' => 1
        ]);

        $this->insert('order_history', [
            'order_id' => 2,
            'status_id' => 2
        ]);
    }

    public function safeDown()
    {
        $indexes = [
            'users' => 'idx-users-email',
            'users' => 'idx-users-password'
        ];

        foreach ($indexes as $table => $name) {
            $this->dropIndex($name, $table);
        }

        $foreignKeys = [
            'users' => 'fk-users-company_id'
        ];

        foreach ($foreignKeys as $table => $name) {
            $this->dropForeignKey($name, $table);
        }

        $tables = ['users', 'companies'];

        foreach ($tables as $table) {
            $this->dropTable($table);
        }

        return true;
    }
}
