<?php

namespace Framework;

use PDO;

class Database
{
  public $conn;

  /**
   * Constructor for Database class
   * 
   * @param array $config
   */
  public function __construct($config)
  {
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";

    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      // to fetch data as associative array
      // PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
      // fetch data as object
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ];

    try {
      $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);
    } catch (\PDOException $e) {
      throw new \Exception("Database connection failed: {$e->getMessage()}");
    }
  }

  /**
   * Query database
   *
   * @param string $query
   * @param array $params
   * @return PDOStatement
   * @throws PDOException
   */
  public function query($query, $params = [])
  {
    try {
      $sth = $this->conn->prepare($query);

      // bind named params
      foreach ($params as $param => $value) {
        $sth->bindValue(':' . $param, $value);
      }

      $sth->execute();
      return $sth;
    } catch (\PDOException $e) {
      throw new \Exception("Query failed to execute: {$e->getMessage()}");
    }
  }
}
