<?php

declare(strict_types=1);

namespace pietras\basic;

use pietras\basic\Model\Config;

class Database extends \mysqli
{
    private $debug;
    private $sqlsHistory;

    public function __construct(string $host, string $user, string $pass, string $databaseName)
    {
        parent::__construct($host, $user, $pass, $databaseName);
        $this->debug = false;
        $this->autocommit(false);
        $this->sqlsHistory = [];
    }

    public static function createFromConfig(Config $config): self
    {
        $host = $config->get("DB_HOST");
        $user = $config->get("DB_USER");
        $pass = $config->get("DB_PASS");
        $databaseName = $config->get("DB_NAME");
        return new Database($host, $user, $pass, $databaseName);
    }

    public function getDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $bool): self
    {
        $this->debug = $bool;
        return $this;
    }

    public function getSqlsHistory(): array
    {
        return $this->sqlsHistory;
    }

    public function arrayQuery(string $sql, string $types = null, array $parameters = null): array
    {
        $stmt = $this->SQL($sql, $types, $parameters);
        return $this->queryToArray($stmt);
    }

    public function indexedQuery(
        string $sql,
        string $types = null,
        array $parameters = null,
        string $index = "id"
    ): array {
        $stmt = $this->SQL($sql, $types, $parameters);

        return $this->queryToIndexedArray($stmt, $index);
    }

    public function singleValueQuery(string $sql, string $types = null, array $parameters = null)
    {
        $stmt = $this->SQL($sql, $types, $parameters);
        return $this->queryToValue($stmt);
    }

    public function SQL(string $sql, string $types = null, array $parameters = null)
    {
        $stmt = false;
        $this->sqlsHistory[] = $this->interpolateSql($sql, $types, $parameters);
        if ($types !== null) {
            $stmt = $this->prepare($sql);
            if ($stmt == false and $this->debug) {
                throw new \Exception("Błąd Database->SQL->prepare(),\r\n " . $this->error . ",\r\n $sql");
            }
            $res = $stmt->bind_param($types, ...$parameters);
            if ($res == false and $this->debug) {
                throw new \Exception("Błąd Database->SQL->bindParams(),\r\n " . $this->error . ",\r\n $sql");
            }
            $res = $stmt->execute();
            if ($res == false and $this->debug) {
                throw new \Exception("Błąd Database->SQL->execute(),\r\n " . $this->error . ",\r\n $sql");
            }
        } else {
            $stmt = $this->query($sql);
            if ($stmt == false and $this->debug) {
                throw new \Exception("Błąd Database->SQL->query(),\r\n " . $this->error . ",\r\n $sql");
            }
        }
        return $stmt;
    }

    private function interpolateSql(string $sql, ?string $types, ?array $parameters): string
    {
        if ($parameters === null) {
            return $sql;
        }

        foreach ($parameters as $i => $parameter) {
            $type = $types[$i] ?? 's';

            if ($parameter === null) {
                $value = 'NULL';
            } else {
                switch ($type) {
                    case 'i':
                        $value = (string) (int) $parameter;
                        break;

                    case 'd':
                        $value = (string) (float) $parameter;
                        break;

                    case 'b':
                        $value = "'<BLOB>'";
                        break;

                    case 's':
                    default:
                        if ($parameter === null) {
                            $value = 'NULL';
                        } else {
                            $value = "'" . $this->real_escape_string((string) $parameter) . "'";
                        }
                        break;
                }
            }
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }

        return $sql;
    }

    private function queryToArray($stmt)
    {
        if (get_class($stmt) == 'mysqli_stmt') {
            $stmt = $stmt->get_result();
        }
        $tab = array();
        $id = -1;
        if (!empty($stmt)) {
            while ($row = $stmt->fetch_assoc()) {
                $id++;
                foreach ($row as $key => $value) {
                    $tab[$id][$key] = $value;
                }
            }
        }
        return $tab;
    }

    private function queryToIndexedArray($stmt, string $indexName): array
    {
        if (get_class($stmt) == 'mysqli_stmt') {
            $stmt = $stmt->get_result();
        }
        $tab = array();
        $id = -1;
        if (!empty($stmt)) {
            while ($row = $stmt->fetch_assoc()) {
                if (array_key_exists($indexName, $row)) {
                    $id = $row[$indexName];
                } else {
                    $id++;
                }
                foreach ($row as $key => $value) {
                    $tab[$id][$key] = $value;
                }
            }
        }
        return $tab;
    }

    private function queryToValue($stmt)
    {
        $ret = null;

        if (get_class($stmt) == 'mysqli_stmt') {
            $stmt = $stmt->get_result();
        }
        if (!empty($stmt)) {
            while ($rekord = $stmt->fetch_assoc()) {
                $ret = current($rekord);
            }
        }

        return $ret;
    }
}
