<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251108121000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE delivery_status (
            id SERIAL PRIMARY KEY,
            name VARCHAR(50) NOT NULL
        )');

        $this->addSql('CREATE TABLE "order" (
            id SERIAL PRIMARY KEY,
            user_id INT NOT NULL,
            cart_id INT NOT NULL,
            delivery_status_id INT NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            CONSTRAINT FK_ORDER_USER FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            CONSTRAINT FK_ORDER_CART FOREIGN KEY (cart_id) REFERENCES cart (id) ON DELETE CASCADE,
            CONSTRAINT FK_ORDER_DELIVERY_STATUS FOREIGN KEY (delivery_status_id) REFERENCES delivery_status (id) ON DELETE CASCADE
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE "order"');
        $this->addSql('DROP TABLE delivery_status');
    }
}

