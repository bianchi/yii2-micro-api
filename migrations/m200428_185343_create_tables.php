<?php

use api\models\Invoice;
use api\models\Service;
use yii\db\Migration;

class m200428_185343_create_tables extends Migration
{
    public function safeUp()
    {
        $this->createTable('customers', [
            'id' => $this->primaryKey()->notNull(),
            'name' => $this->string(100)->notNull(),
            'corporate_name' => $this->string(100),
            'entity_type' => "ENUM('PF', 'PJ') NOT NULL",
            'document_number' => $this->string(14)->notNull(),
            'address_zip_code' => $this->string(8),
            'address_public_place' => $this->string(120),
            'address_number' => $this->string(8),
            'address_complement' => $this->string(60),
            'address_neighborhood' => $this->string(80),
            'address_city' => $this->string(120),
            'address_uf' => $this->string(2),
            'backoffice_email' => $this->string(120),
            'backoffice_password' => $this->string(120),
            'max_users' => $this->integer()->notNull()->defaultValue(10)
        ]);

        $this->createTable('users', [
            'id' => $this->primaryKey()->notNull(),
            'name' => $this->string(60)->notNull(),
            'email' => $this->string(200)->notNull(),
            'password' => $this->string(255)->notNull(),
            'phone' => $this->string(15),
            'access_token' => $this->string(255),
            'last_login' => $this->dateTime(),
            'last_api_request' => $this->dateTime(),
            'is_admin' => $this->boolean()->notNull()->defaultValue(false),
            'can_order_services' => $this->boolean()->notNull()->defaultValue(false),
            'can_insert_credits' => $this->boolean()->notNull()->defaultValue(false),
            'can_see_reports' => $this->boolean()->notNull()->defaultValue(false),
            'can_see_invoices' => $this->boolean()->notNull()->defaultValue(false),
            'customer_id' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->notNull()->defaultValue(false),
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

        $this->createTable('services', [
            'id' => $this->primaryKey()->notNull(),
            'name' => $this->string(120),
            'type' => "ENUM('Certificate', 'Research')"
        ]);

        $this->createTable('order_statuses', [
            'id' => $this->primaryKey()->notNull(),
            'name' => $this->string(50)->notNull()
        ]);

        $this->createTable('orders', [
            'id' => $this->bigPrimaryKey()->notNull(),
            'customer_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'document_id' => $this->integer()->notNull(),
            'current_status_id' => $this->integer()->notNull(),
            // 'name' => $this->string(80)->notNull(),
            'priority' => $this->boolean()->defaultValue(false),
            'placed_time' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'estimated_time' => $this->dateTime()->notNull(),
            'delivered_time' => $this->dateTime()
        ]);

        $this->createIndex(
            'idx-orders-customer_id',
            'orders',
            'customer_id'
        );

        $this->addForeignKey(
            'fk-orders-customer_id',
            'orders',
            'customer_id',
            'customers',
            'id'
        );

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
            'idx-orders-document_id',
            'orders',
            'document_id'
        );

        $this->addForeignKey(
            'fk-orders-document_id',
            'orders',
            'document_id',
            'services',
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

        $this->createTable('invoices', [
            'id' => $this->bigPrimaryKey()->notNull(),
            'customer_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'order_id' => $this->bigInteger(),
            'operation' => "ENUM('C', 'D') NOT NULL",
            'placed_time' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'approved_time' => $this->dateTime(),
            'amount' => $this->double()->notNull(),
            'payment_method' => "ENUM('CC', 'BO')",
        ]);

        $this->createIndex(
            'idx-invoices-customer_id',
            'invoices',
            'customer_id'
        );

        $this->addForeignKey(
            'fk-invoices-customer_id',
            'invoices',
            'customer_id',
            'customers',
            'id'
        );

        $this->createIndex(
            'idx-invoices-user_id',
            'invoices',
            'user_id'
        );

        $this->addForeignKey(
            'fk-invoices-user_id',
            'invoices',
            'user_id',
            'users',
            'id'
        );

        $this->createIndex(
            'idx-invoices-order_id',
            'invoices',
            'order_id'
        );

        $this->addForeignKey(
            'fk-invoices-order_id',
            'invoices',
            'order_id',
            'orders',
            'id'
        );

        $this->createTable('password_reset', [
            'token' => "varchar(255) primary key not null",
            'user_id' => $this->integer()->notNull(),
            'requested_time' => $this->datetime()->notNull(),
            'expiration_time' => $this->datetime()->notNull(),
            'already_used' => $this->boolean()->notNull()->defaultValue(false)
        ]);

        $this->createIndex(
            'idx-password_reset-user_id',
            'password_reset',
            'user_id'
        );

        $this->addForeignKey(
            'fk-password_reset-user_id',
            'password_reset',
            'user_id',
            'users',
            'id'
        );

        $this->populate();

        if (YII_ENV_DEV) {
            $this->populateTest();
        }
    }

    private function populate()
    {
        $this->insert('services', ['id' => 1, 'name' => 'Nascimento', 'type' => Service::TYPE_CERTIFICATE]);
        $this->insert('services', ['id' => 2, 'name' => 'Casamento', 'type' => Service::TYPE_CERTIFICATE]);
        $this->insert('services', ['id' => 3, 'name' => 'Óbito', 'type' => Service::TYPE_CERTIFICATE]);
        $this->insert('services', ['id' => 4, 'name' => 'Protesto', 'type' => Service::TYPE_CERTIFICATE]);
        $this->insert('services', ['id' => 5, 'name' => 'Imóvel', 'type' => Service::TYPE_CERTIFICATE]);
        $this->insert('services', ['id' => 6, 'name' => 'Negativa de Débitos Trabalhistas - CNDT', 'type' => Service::TYPE_CERTIFICATE]);
        $this->insert('services', ['id' => 7, 'name' => 'Negativa de Débitos de Tributos Federais e Dívida Ativa da União', 'type' => Service::TYPE_CERTIFICATE]);
        $this->insert('services', ['id' => 8, 'name' => 'Negativa de Débitos Tributários (São Paulo - Estadual) Não Inscritos em Dívida Ativa', 'type' => Service::TYPE_CERTIFICATE]);
        $this->insert('services', ['id' => 9, 'name' => 'Negativa do FGTS (Certificado de Regularidade Fiscal CRF)"', 'type' => Service::TYPE_CERTIFICATE]);
        $this->insert('services', ['id' => 10, 'name' => 'ITR - Débitos Relativos a Tributos Federais e à Dívida Ativa da União de Imóvel Rural', 'type' => Service::TYPE_CERTIFICATE]);
        $this->insert('services', ['id' => 11, 'name' => 'Procuração', 'type' => Service::TYPE_RESEARCH]);
        $this->insert('services', ['id' => 12, 'name' => 'Escritura', 'type' => Service::TYPE_RESEARCH]);
        $this->insert('services', ['id' => 13, 'name' => 'Interdição', 'type' => Service::TYPE_CERTIFICATE]);
        $this->insert('services', ['id' => 16, 'name' => 'Bens', 'type' => Service::TYPE_RESEARCH]);
        $this->insert('services', ['id' => 17, 'name' => 'Junta Comercial', 'type' => Service::TYPE_RESEARCH]);

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
            'entity_type' => 'PJ',
            'document_number' => '11111111111180',
            'address_zip_code' => '85914153',
            'address_public_place' => 'Rua Maranhão',
            'address_number' => '1170',
            'address_neighborhood' => 'Jd. Pancera',
            'address_city' => 'Toledo',
            'address_uf' => 'PR',
            'backoffice_email' => 'empresa@teste.com',
            'backoffice_password' => 'Emp@1234#'
        ]);

        $this->insert('customers', [
            'name' => 'João Pedro da Silva',
            'entity_type' => 'PF',
            'document_number' => '11111111111',
            'address_zip_code' => '81010001',
            'address_public_place' => 'Av Presidente Wenceslau Braz',
            'address_number' => '2776',
            'address_complement' => '2º andar',
            'address_neighborhood' => 'Lindóia',
            'address_city' => 'Curitiba',
            'address_uf' => 'PR',
        ]);


        $this->insert('users', [
            'name' => 'Admin from JHOB',
            'email' => 'jhob@gmail.com',
            'phone' => '45999191341',
            'password' => password_hash('123', PASSWORD_BCRYPT),
            'customer_id' => 1,
            'is_admin' => 1,
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
            'name' => 'Admin from Koodari',
            'email' => 'koodari@gmail.com',
            'phone' => '4132770707',
            'password' => password_hash('123', PASSWORD_BCRYPT),
            'customer_id' => 2
        ]);

        $estimatedTime = new \Datetime;
        $estimatedTime->modify('+7 days');

        $this->insert('orders', [
            'user_id' => 1,
            'customer_id' => 1,
            'document_id' => 1,
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
            'customer_id' => 1,
            'document_id' => 2,
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
            'customer_id' => 1,
            'document_id' => 3,
            'current_status_id' => 1,
            'estimated_time' => $estimatedTime->format('Y-m-d H:i:s')
        ]);

        $this->insert('order_history', [
            'order_id' => 3,
            'status_id' => 1
        ]);

        $this->insert('orders', [
            'user_id' => 3,
            'customer_id' => 2,
            'document_id' => 7,
            'current_status_id' => 1,
            'estimated_time' => $estimatedTime->format('Y-m-d H:i:s')
        ]);

        $this->insert('order_history', [
            'order_id' => 4,
            'status_id' => 1
        ]);

        $this->insert('invoices', [
            'user_id' => 1,
            'customer_id' => 1,
            'amount' => 3000,
            'operation' => 'C',
            'placed_time' => date('Y-m-d H:i:s'),
            'approved_time' => date('Y-m-d H:i:s'),
            'payment_method' => Invoice::PAYMENT_METHOD_CREDIT_CARD
        ]);

        $this->insert('invoices', [
            'user_id' => 1,
            'customer_id' => 1,
            'order_id' => 1,
            'amount' => 100,
            'operation' => 'D',
            'placed_time' => date('Y-m-d H:i:s'),
            'approved_time' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('invoices', [
            'user_id' => 1,
            'customer_id' => 1,
            'order_id' => 2,
            'amount' => 175.80,
            'operation' => 'D',
            'placed_time' => date('Y-m-d H:i:s'),
            'approved_time' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('invoices', [
            'user_id' => 2,
            'customer_id' => 1,
            'order_id' => 3,
            'amount' => 45,
            'operation' => 'D',
            'placed_time' => date('Y-m-d H:i:s'),
            'approved_time' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('invoices', [
            'user_id' => 3,
            'customer_id' => 2,
            'amount' => 100,
            'operation' => 'C',
            'placed_time' => date('Y-m-d H:i:s'),
            'approved_time' => date('Y-m-d H:i:s'),
            'payment_method' => Invoice::PAYMENT_METHOD_BOLETO
        ]);

        $this->insert('invoices', [
            'user_id' => 3,
            'customer_id' => 2,
            'order_id' => 3,
            'amount' => 95,
            'operation' => 'D',
            'placed_time' => date('Y-m-d H:i:s'),
            'approved_time' => date('Y-m-d H:i:s'),
        ]);
    }

    public function safeDown()
    {
        $foreignKeys = [
            'users' => 'fk-users-customer_id',
            'orders' =>'fk-orders-user_id',
            'orders' =>'fk-orders-customer_id',
            'orders' => 'fk-orders-document_id',
            'orders' => 'fk-orders-current_status_id',
            'order_history' => 'fk-order_history-order_id',
            'order_history' => 'fk-order_history-status_id',
            'invoices' => 'fk-invoices-customer_id',
            'invoices' => 'fk-invoices-user_id',
            'invoices' => 'fk-invoices-order_id',
            'password_reset' => 'fk-password_reset-user_id'
        ];

        foreach ($foreignKeys as $table => $name) {
            $this->dropForeignKey($name, $table);
        }

        $indexes = [
            'users' => 'idx-users-email',
            'users' => 'idx-users-password',
            'orders' =>'idx-orders-user_id',
            'orders' =>'idx-orders-customer_id',
            'orders' => 'idx-orders-document_id',
            'orders' => 'idx-orders-current_status_id',
            'order_history' => 'idx-order_history-order_id',
            'order_history' => 'idx-order_history-status_id',
            'invoices' => 'idx-invoices-customer_id',
            'invoices' => 'idx-invoices-user_id',
            'invoices' => 'idx-invoices-order_id',
            'password_reset' => 'idx-password_reset-user_id'
        ];

        foreach ($indexes as $table => $name) {
            $this->dropIndex($name, $table);
        }


        $tables = ['invoices', 'order_history', 'orders', 'order_statuses', 'services', 'users', 'customers', 'password_reset'];

        foreach ($tables as $table) {
            $this->dropTable($table);
        }

        return true;
    }
}
