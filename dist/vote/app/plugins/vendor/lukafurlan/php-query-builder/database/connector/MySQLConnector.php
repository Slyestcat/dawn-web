<?php
namespace lukafurlan\database\connector;
use PDO;

/**
 * @author Luka Furlan <Luka.furlan9@gmail.com>
 * @copyright 2018 Luka Furlan
 */

class MySQLConnector extends Connector {

    public function connect() {
        try {
            $dsn = "mysql:host=".MYSQL_HOST.";port=".MYSQL_PORT.";dbname=".MYSQL_DATABASE;
            $options = [
                PDO::ATTR_PERSISTENT => true,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ];
    
            $this->connection = new PDO($dsn, MYSQL_USERNAME, MYSQL_PASSWORD, $options);
    
            // Optional: Set charset (if needed)
            $this->connection->exec("SET NAMES utf8");
    
            // Optional: Enable SSL (if needed)
            $this->connection->setAttribute(PDO::MYSQL_ATTR_SSL_CERT, MYSQL_CERT);
    
            return $this->connection;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage() . " Code: " . $e->getCode());
        }
    }
    

}