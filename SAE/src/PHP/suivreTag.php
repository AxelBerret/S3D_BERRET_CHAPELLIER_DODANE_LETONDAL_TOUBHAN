<?php

declare(strict_types=1);

class suivreTag{

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

}