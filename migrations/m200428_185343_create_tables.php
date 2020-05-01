<?php

use yii\db\Migration;

class m200428_185343_create_tables extends Migration
{
    public function safeUp()
    {
        $this->createTable('customers', [
            'id' => $this->primaryKey()->notNull(),
            'name' => $this->string(100)->notNull(),
            'entity_type' => "ENUM('PF', 'PJ') NOT NULL",
            'document_number' => $this->string(14)->notNull(),
            'zip_code' => $this->string(8)->notNull(),
            'public_place' => $this->string(120)->notNull(),
            'number' => $this->string(8)->notNull(),
            'complement' => $this->string(60),
            'key' => $this->string(120),
            'secret' => $this->string(120),
            'max_users' => $this->integer()->notNull()->defaultValue(10)
        ]);

        $this->createTable('users', [
            'id' => $this->primaryKey()->notNull(),
            'name' => $this->string(60)->notNull(),
            'phone' => $this->string(15)->notNull(),
            'email' => $this->string(200)->notNull(),
            'password' => $this->string(255)->notNull(),
            'access_token' => $this->string(255),
            'last_login' => $this->dateTime(),
            'last_api_request' => $this->dateTime(),
            'is_admin' => $this->boolean()->notNull()->defaultValue(false),
            'can_order_document' => $this->boolean()->notNull()->defaultValue(false),
            'can_insert_credits' => $this->boolean()->notNull()->defaultValue(false),
            'can_see_reports' => $this->boolean()->notNull()->defaultValue(false),
            'can_see_financial_transactions' => $this->boolean()->notNull()->defaultValue(false),
            'customer_id' => $this->integer()->notNull()
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
            'fk-users-customer_id',
            'users',
            'customer_id',
            'customers',
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
            'priority' => $this->boolean()->defaultValue(false),
            'placed_time' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'estimated_time' => $this->dateTime()->notNull(),
            'delivered_time' => $this->dateTime()
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

        $this->createTable('financial_transactions', [
            'id' => $this->bigPrimaryKey()->notNull(),
            'customer_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'order_id' => $this->bigInteger(),
            'operation' => "ENUM('C', 'D') NOT NULL",
            'amount' => $this->double()->notNull(),
        ]);

        $this->createIndex(
            'idx-financial_transactions-customer_id',
            'financial_transactions',
            'customer_id'
        );

        $this->addForeignKey(
            'fk-financial_transactions-customer_id',
            'financial_transactions',
            'customer_id',
            'customers',
            'id'
        );

        $this->createIndex(
            'idx-financial_transactions-user_id',
            'financial_transactions',
            'user_id'
        );

        $this->addForeignKey(
            'fk-financial_transactions-user_id',
            'financial_transactions',
            'user_id',
            'users',
            'id'
        );

        $this->createIndex(
            'idx-financial_transactions-order_id',
            'financial_transactions',
            'order_id'
        );

        $this->addForeignKey(
            'fk-financial_transactions-order_id',
            'financial_transactions',
            'order_id',
            'orders',
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
        $this->insert('customers', [
            'name' => 'JHOB',
            'key' => 'key123',
            'secret' => 'secret123',
            'zip_code' => '85914153',
            'public_place' => 'Rua Maranhão',
            'number' => '1170',
            'entity_type' => 'PJ',
            'document_number' => '11111111111180',
        ]);

        $this->insert('customers', [
            'name' => 'Koodari',
            'key' => 'key456',
            'secret' => 'secret456',
            'zip_code' => '81010001',
            'public_place' => 'Av Presidente Wenceslau Braz',
            'number' => '2776',
            'complement' => '2º andar',
            'entity_type' => 'PF',
            'document_number' => '11111111111',
        ]);


        $this->insert('users', [
            'name' => 'Admin from JHOB',
            'email' => 'jhob@gmail.com',
            'phone' => '45999191341',
            'password' => password_hash('123', PASSWORD_BCRYPT),
            'customer_id' => 1,
            'is_admin' => 1
        ]);

        $this->insert('users', [
            'name' => 'User from JHOB',
            'email' => 'jhob2@gmail.com',
            'phone' => '41984450172',
            'password' => password_hash('123', PASSWORD_BCRYPT),
            'customer_id' => 1,
            'is_admin' => 0
        ]);

        $this->insert('users', [
            'name' => 'User from Koodari',
            'email' => 'koodari@gmail.com',
            'phone' => '4132770707',
            'password' => password_hash('123', PASSWORD_BCRYPT),
            'customer_id' => 2
        ]);

        $estimatedTime = new \Datetime;
        $estimatedTime->modify('+7 days');

        $this->insert('orders', [
            'user_id' => 1,
            'document_type_id' => 1,
            'current_status_id' => 4,
            'estimated_time' => $estimatedTime->format('Y-m-d H:i:s')
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
            'current_status_id' => 2,
            'estimated_time' => $estimatedTime->format('Y-m-d H:i:s')
        ]);

        $this->insert('order_history', [
            'order_id' => 2,
            'status_id' => 1
        ]);

        $this->insert('order_history', [
            'order_id' => 2,
            'status_id' => 2
        ]);

        $this->insert('orders', [
            'user_id' => 2,
            'document_type_id' => 3,
            'current_status_id' => 1,
            'estimated_time' => $estimatedTime->format('Y-m-d H:i:s')
        ]);

        $this->insert('order_history', [
            'order_id' => 3,
            'status_id' => 1
        ]);

        $this->insert('orders', [
            'user_id' => 3,
            'document_type_id' => 7,
            'current_status_id' => 1,
            'estimated_time' => $estimatedTime->format('Y-m-d H:i:s')
        ]);

        $this->insert('order_history', [
            'order_id' => 4,
            'status_id' => 1
        ]);

        $this->insert('financial_transactions', [
            'user_id' => 1,
            'customer_id' => 1,
            'amount' => 3000,
            'operation' => 'C'
        ]);

        $this->insert('financial_transactions', [
            'user_id' => 1,
            'customer_id' => 1,
            'order_id' => 1,
            'amount' => 100,
            'operation' => 'D'
        ]);

        $this->insert('financial_transactions', [
            'user_id' => 1,
            'customer_id' => 1,
            'order_id' => 2,
            'amount' => 175.80,
            'operation' => 'D'
        ]);

        $this->insert('financial_transactions', [
            'user_id' => 2,
            'customer_id' => 1,
            'order_id' => 3,
            'amount' => 45,
            'operation' => 'D'
        ]);

        $this->insert('financial_transactions', [
            'user_id' => 3,
            'customer_id' => 2,
            'amount' => 100,
            'operation' => 'C'
        ]);

        $this->insert('financial_transactions', [
            'user_id' => 3,
            'customer_id' => 2,
            'order_id' => 3,
            'amount' => 95,
            'operation' => 'D'
        ]);
    }

    public function safeDown()
    {
        $foreignKeys = [
            'users' => 'fk-users-customer_id',
            'orders' =>'fk-orders-user_id',
            'orders' => 'fk-orders-document_type_id',
            'orders' => 'fk-orders-current_status_id',
            'order_history' => 'fk-order_history-order_id',
            'order_history' => 'fk-order_history-status_id',
            'financial_transactions' => 'fk-financial_transactions-customer_id',
            'financial_transactions' => 'fk-financial_transactions-user_id',
            'financial_transactions' => 'fk-financial_transactions-order_id',
        ];

        foreach ($foreignKeys as $table => $name) {
            $this->dropForeignKey($name, $table);
        }

        $indexes = [
            'users' => 'idx-users-email',
            'users' => 'idx-users-password',
            'orders' =>'idx-orders-user_id',
            'orders' => 'idx-orders-document_type_id',
            'orders' => 'idx-orders-current_status_id',
            'order_history' => 'idx-order_history-order_id',
            'order_history' => 'idx-order_history-status_id',
            'financial_transactions' => 'idx-financial_transactions-customer_id',
            'financial_transactions' => 'idx-financial_transactions-user_id',
            'financial_transactions' => 'idx-financial_transactions-order_id',
        ];

        foreach ($indexes as $table => $name) {
            $this->dropIndex($name, $table);
        }


        $tables = ['financial_transactions', 'order_history', 'orders', 'order_statuses', 'document_types', 'users', 'customers'];

        foreach ($tables as $table) {
            $this->dropTable($table);
        }

        return true;
    }
}
