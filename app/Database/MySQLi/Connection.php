<?php

namespace App\Database\MySQLi;

use CodeIgniter\Database\MySQLi\Connection as BaseMySQLiConnection;

class Connection extends BaseMySQLiConnection
{
    private static bool $isLogging = false;

    public function connect(bool $persistent = false)
    {
        $conn = parent::connect($persistent);

        if ($conn && !self::$isLogging) {
            self::$isLogging = true;
            try {
                $now = date('Y-m-d H:i:s');
                $sql = "INSERT INTO `db_connection_logs` (`created_at`) VALUES ('{$now}')";
                if ($conn instanceof \mysqli) {
                    @$conn->query($sql);
                } elseif (is_object($conn)) {
                    @mysqli_query($conn, $sql);
                }
            } catch (\Throwable) {
            } finally {
                self::$isLogging = false;
            }
        }

        return $conn;
    }

    public function reconnect()
    {
        parent::reconnect();
    }
}
