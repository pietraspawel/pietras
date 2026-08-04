<?php

namespace pietras\basic;

use PHPUnit\Framework\TestCase;
use pietras\basic\model\Config;

class DatabaseIntegrationTest extends TestCase
{
    private Database $db;

    protected function setUp(): void
    {
        $config = Config::createFromYaml("config/database_pietras_test.yaml");

        $this->db = Database::createFromConfig($config);
        $this->db->setDebug(true);

        //Wygląd bazy do testów.
        // $this->db->SQL("
        //     CREATE TABLE IF NOT EXISTS users (
        //         id INT AUTO_INCREMENT PRIMARY KEY,
        //         name VARCHAR(255),
        //         age INT
        //     )
        // ");

        // Czyścimy bazę przed każdym testem.
        $this->db->SQL("TRUNCATE TABLE users");
        $this->db->commit();
    }

    protected function tearDown(): void
    {
        $this->db->SQL("TRUNCATE TABLE users");
        $this->db->commit();

        $this->db->close();
    }

    public function testInsertUser()
    {
        $this->db->SQL("INSERT INTO users(name, age) VALUES(?, ?)", "si", ["Paweł", 46]);
        $this->db->commit();

        $name = $this->db->singleValueQuery("SELECT name FROM users WHERE age=?", "i", [46]);
        $this->assertSame("Paweł", $name);
    }

    public function testArrayQuery()
    {
        $this->db->SQL("INSERT INTO users(name) VALUES(?)", "s", ["Jan"]);
        $this->db->SQL("INSERT INTO users(name) VALUES(?)", "s", ["Adam"]);
        $this->db->commit();

        $rows = $this->db->arrayQuery("SELECT name FROM users ORDER BY name");

        $this->assertCount(2, $rows);
        $this->assertSame("Adam", $rows[0]["name"]);
        $this->assertSame("Jan", $rows[1]["name"]);
    }

    public function testRollback()
    {
        $this->db->SQL("INSERT INTO users(name) VALUES(?)", "s", ["Test"]);
        $this->db->rollback();

        $count = $this->db->singleValueQuery("SELECT COUNT(*) FROM users");
        $this->assertSame("0", $count);
    }

    public function testSqlHistory()
    {
        $this->db->SQL("SELECT ?", "i", [123]);
        $history = $this->db->getSqlsHistory();

        $this->assertSame("SELECT 123", end($history));
    }
}
