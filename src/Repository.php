<?php

namespace pietras\basic;

/**
 * Klasa rodzica dla repozytoriów.
 */
abstract class Repository
{
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function commit()
    {
        $this->database->commit();
    }

    abstract public function fetchAll(?string $suffix = null): array;
}
