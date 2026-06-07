<?php

/*
 * Query Builder
 */

class QueryBuilder 
{
  
 protected $table;
 protected $limit;
 protected $orderBy;
 protected $where   = [];
 protected $select  = [];
 protected $whereOr = [];
 

 /*
  * set table name
  *
  * @param string $table
  * @return self
  */
 public function table( string $table ): self 
 {
    $this->table = $table;

    return $this;
 }

 /*
  * Add A WHERE Condition
  *
  * @param string $column
  * @param mixed $value
  * @return self
  */
  public function where(string $column, mixed $value): self 
  {
     $this->where[] = "{$column} = '{$value}'";

     return $this;
  }

  /**
   * Select columns
   * 
   * @param mixed
   * @return self
   */
   public function select(...$columns): self 
   {
      $this->select = $columns;

      return $this;
   }

   /**
    * Limit number or result
    *
    * @param int $limit
    * @return self 
    */
    public function limit(int $limit): self 
    {
       $this->limit = $limit;

       return $this;
    }

    /**
     * Order by column
     * 
     * @param string $column
     * @param string $direction
     * @return self
     */
     public function orderBy( string $column, string $direction = 'ASC' ): self 
     {
       $this->orderBy = "{$column} {$direction}";
       
       return $this;
     }

     /**
      * Add OR WHERE condition
      * 
      * @param string $column
      * @param mixed  $value
      * @return self
      */
      public function whereOr( string $column, mixed $value ): self 
      {
        $this->whereOr[] = "{$column} {$value}";

        return $this;
      }

     /**
      * Get SQL Query
      * 
      * @return string
      */
      public function toSQL(): string 
      {
         $columns = ! empty($this->select)
                     ? implode(', ', $this->select)
                     : '*';
         

         $sql = "SELECT {$columns} FROM {$this->table}";

         if( ! empty( $this->where ) ){
               $sql .= " WHERE " . implode(' AND ', $this->where);
         }

         if (!empty($this->whereOr)) {
            $sql .= (!empty($this->where) ? " OR " : " WHERE ");
            $sql .= implode(' OR ', $this->whereOr);
         }

         if (!empty($this->limit)) {
            $sql .= " LIMIT {$this->limit}";
         }

         if (!empty($this->orderBy)) {
            $sql .= " ORDER BY {$this->orderBy}";
         }

         return $sql;
      }

}

$instance = new QueryBuilder();
$query    = $instance->table('users')->where('name', 'John')->whereOr('name', 'Ali')->toSQL();
echo $query;
$pdo      = new PDO( "mysql:host=host;dbname=dbname", "youruser","yourpasss" );


function run($query, $pdo)
{
    $sql = $query;

    $stmt = $pdo->query($sql);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$result   = run($query, $pdo);

echo "<pre>";
  print_r($result);
echo "</pre>";
