<?php

namespace Alura\Leilao\Infra;


class ConnectionCreator
{
    private static $pdo = null;
    
    public static function getConnection(): \PDO
    {
        if (is_null(self::$pdo)) {
            include('configDataBase.php');
            self::$pdo = new \PDO($dsn, $username, $password);
            self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }
}
