<?php

namespace pietras;

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

    public function arrayQuery(string $sql, string $types = null): array
    {
        $args = func_get_args();
        $stmt = $this->getStmtUsingRightNumberOfArguments($args);
        return $this->queryToArray($stmt);
    }

    public function singleValueQuery(string $sql, string $types = null)
    {
        $args = func_get_args();
        $stmt = $this->getStmtUsingRightNumberOfArguments($args);
        return $this->queryToValue($stmt);
    }

    public function SQL(string $sql, string $types = null)
    {
        $stmt = false;
        $this->sqlsHistory[] = $sql;
        if ($types !== null) {
            $stmt = $this->prepare($sql);
            if ($stmt == false and $this->debug) {
                throw new \Exception("Błąd Database->SQL->prepare(),\r\n " . $this->error . ",\r\n $sql");
            }
            $res = $this->bindParams($stmt, func_get_args());
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

    private function bindParams(\mysqli_stmt $stmt, array $args): bool
    {
        $types = $args[1];
        $i = 2;
        switch (strlen($types)) {
            case 1:
                return $stmt->bind_param($types, $args[$i]);
                break;
            case 2:
                return $stmt->bind_param($types, $args[$i], $args[$i + 1]);
                break;
            case 3:
                return $stmt->bind_param($types, $args[$i], $args[$i + 1], $args[$i + 2]);
                break;
            case 4:
                return $stmt->bind_param($types, $args[$i], $args[$i + 1], $args[$i + 2], $args[$i + 3]);
                break;
            case 5:
                return $stmt->bind_param($types, $args[$i], $args[$i + 1], $args[$i + 2], $args[$i + 3], $args[$i + 4]);
                break;
            case 6:
                return $stmt->bind_param($types, $args[$i], $args[$i + 1], $args[$i + 2], $args[$i + 3], $args[$i + 4], $args[$i + 5]);
                break;
            case 7:
                return $stmt->bind_param($types, $args[$i], $args[$i + 1], $args[$i + 2], $args[$i + 3], $args[$i + 4], $args[$i + 5], $args[$i + 6]);
                break;
            case 8:
                return $stmt->bind_param($types, $args[$i], $args[$i + 1], $args[$i + 2], $args[$i + 3], $args[$i + 4], $args[$i + 5], $args[$i + 6], $args[$i + 7]);
                break;
            case 9:
                return $stmt->bind_param($types, $args[$i], $args[$i + 1], $args[$i + 2], $args[$i + 3], $args[$i + 4], $args[$i + 5], $args[$i + 6], $args[$i + 7], $args[$i + 8]);
                break;
            case 13:
                return $stmt->bind_param($types, $args[$i], $args[$i + 1], $args[$i + 2], $args[$i + 3], $args[$i + 4], $args[$i + 5], $args[$i + 6], $args[$i + 7], $args[$i + 8], $args[$i + 9], $args[$i + 10], $args[$i + 11], $args[$i + 12]);
                break;
            default:
                throw new \Exception("Błąd " . __METHOD__ . " Metoda nie przystosowana do tak dużej liczby argumentów.");
        }
    }

    private function getStmtUsingRightNumberOfArguments(array $args)
    {
        $args = current(func_get_args());
        $sql = $args[0];
        if (isset($args[1])) {
            $types = $args[1];
        }
        switch (count($args)) {
            case 1:
                return $this->SQL($sql);
                break;
            case 2:
                return $this->SQL($sql, $types);
                break;
            case 3:
                return $this->SQL($sql, $types, $args[2]);
                break;
            case 4:
                return $this->SQL($sql, $types, $args[2], $args[3]);
                break;
            case 5:
                return $this->SQL($sql, $types, $args[2], $args[3], $args[4]);
                break;
            case 6:
                return $this->SQL($sql, $types, $args[2], $args[3], $args[4], $args[5]);
                break;
            case 7:
                return $this->SQL($sql, $types, $args[2], $args[3], $args[4], $args[5], $args[6]);
                break;
            case 8:
                return $this->SQL($sql, $types, $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]);
                break;
            case 9:
                return $this->SQL($sql, $types, $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8]);
                break;
            case 13:
                return $this->SQL($sql, $types, $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9], $args[10], $args[11], $args[12]);
                break;
            default:
                throw new \Exception("Błąd " . __METHOD__ . " Metoda nie przystosowana do tak dużej liczby argumentów.");
        }
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
                if (array_key_exists('ID', $row)) {
                    $id = $row['ID'];
                } elseif (array_key_exists('id', $row)) {
                    $id = $row['id'];
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
