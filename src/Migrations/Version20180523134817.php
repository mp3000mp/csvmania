<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180523134817 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, content_id INT NOT NULL, assignment_id INT NOT NULL, field_id INT NOT NULL, value SMALLINT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_DADD4A2584A0A3ED (content_id), INDEX IDX_DADD4A25D19302F8 (assignment_id), INDEX IDX_DADD4A25443707B0 (field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE assignment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, task_id INT NOT NULL, validated_by_id INT DEFAULT NULL, assigned_at DATETIME NOT NULL, done_at DATETIME DEFAULT NULL, validated_at DATETIME DEFAULT NULL, INDEX IDX_30C544BAA76ED395 (user_id), INDEX IDX_30C544BA8DB60186 (task_id), INDEX IDX_30C544BAC69DE5E5 (validated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, id_sparkup BIGINT NOT NULL, message VARCHAR(1000) NOT NULL, INDEX IDX_FEC530A98DB60186 (task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feeling (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(55) NOT NULL, format SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, feeling_id INT NOT NULL, INDEX IDX_5BF545588DB60186 (task_id), INDEX IDX_5BF54558CB9214C2 (feeling_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, deadline DATETIME NOT NULL, created_at DATETIME NOT NULL, name VARCHAR(55) NOT NULL, priority SMALLINT NOT NULL, nb_answer_needed SMALLINT NOT NULL, language VARCHAR(2) NOT NULL, INDEX IDX_527EDB25B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(50) NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, roles JSON NOT NULL, nb_failed_connexion SMALLINT NOT NULL, created_at DATETIME NOT NULL, active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A2584A0A3ED FOREIGN KEY (content_id) REFERENCES content (id)');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25D19302F8 FOREIGN KEY (assignment_id) REFERENCES assignment (id)');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25443707B0 FOREIGN KEY (field_id) REFERENCES field (id)');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BA8DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BAC69DE5E5 FOREIGN KEY (validated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A98DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF545588DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF54558CB9214C2 FOREIGN KEY (feeling_id) REFERENCES feeling (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25D19302F8');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A2584A0A3ED');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF54558CB9214C2');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25443707B0');
        $this->addSql('ALTER TABLE assignment DROP FOREIGN KEY FK_30C544BA8DB60186');
        $this->addSql('ALTER TABLE content DROP FOREIGN KEY FK_FEC530A98DB60186');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF545588DB60186');
        $this->addSql('ALTER TABLE assignment DROP FOREIGN KEY FK_30C544BAA76ED395');
        $this->addSql('ALTER TABLE assignment DROP FOREIGN KEY FK_30C544BAC69DE5E5');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25B03A8386');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE assignment');
        $this->addSql('DROP TABLE content');
        $this->addSql('DROP TABLE feeling');
        $this->addSql('DROP TABLE field');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE user');
    }
}
