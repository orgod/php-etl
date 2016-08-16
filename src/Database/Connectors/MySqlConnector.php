<?php

namespace Marquine\Etl\Database\Connectors;

use PDO;

class MySqlConnector extends Connector
{
    /**
    * Connect to a database.
    *
    * @param array $config
    * @return \PDO
    */
    public function connect($config)
    {
        $dsn = $this->getDsn($config);

        $connection = $this->createConnection($dsn, $config);

        $this->postConnection($connection, $config);

        return $connection;
    }

    /**
     * Get the DSN string.
     *
     * @param array $config
     * @return string
     */
    public function getDsn($config)
    {
        extract($config, EXTR_SKIP);

        $dsn = [];

        if (isset($unix_socket)) {
            $dsn['unix_socket'] = $unix_socket;
        }

        if (isset($host) && ! isset($unix_socket)) {
            $dsn['host'] = $host;
        }

        if (isset($port) && ! isset($unix_socket)) {
            $dsn['port'] = $port;
        }

        if (isset($database) && ! isset($unix_socket)) {
            $dsn['dbname'] = $database;
        }

        return 'mysql:' . http_build_query($dsn, '', ';');
    }

    /**
     * Handle post connection setup.
     *
     * @param \PDO $connection
     * @param array $config
     * @return void
     */
    public function postConnection($connection, $config)
    {
        extract($config, EXTR_SKIP);

        if (isset($database)) {
            $connection->exec("use `$database`");
        }

        if (isset($charset)) {
            $statement = "set names '$charset'";

            if (isset($collation)) {
                $statement .= " collate '$collation'";
            }

            $connection->prepare($statement)->execute();
        }

        if (isset($timezone)) {
            $connection->prepare("set time_zone = '$timezone'")->execute();
        }

    }
}
