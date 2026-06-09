<?php

/*
 * Query Builder
 */

require_once 'helpers.php';

class QueryBuilder 
{
  
 protected $table;
 protected $limit;
 protected $orderBy;
 protected $groupBy;
 protected $having;
 protected $count;
 protected $distinct = false;
 protected $where    = [];
 protected $select   = [];
 protected $whereOr  = [];
 protected $like     = [];
 

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
        $this->whereOr[] = "{$column} = '{$value}'";

        return $this;
      }

      /**
       * Add LIKE search pattern
       * 
       * @param string $column
       * @param string $value
       * @return self
       */
      public function like(string $column, string $value): self
       {
         $value = strtolower($value);
         $this->like[] = "LOWER({$column}) LIKE '%{$value}%'";
         return $this;
       }

      /**
       * Add GROUP BY column
       * 
       * @param string $column
       * @return self
       */
      public function groupBy(string $column): self 
      {
          $this->groupBy = $column;
          return $this;
      }

     /**
      * Add HAVING condition
      *
      * @param string $condition
      * @return self
      */
     public function having(string $condition): self
      {
         $this->having = $condition;
         return $this;
      }

      /**
       * Select DISTINCT for remove duplicate
       * 
       * @return self
       */
      public function distinct(): self 
      {
        $this->distinct = true;
        
        return $this;
      }

      /**
       * Add COUNT for row
       * 
       * @param string $count
       * @return self
       */
      public function count(string $column = "*"): self 
      {
        $this->select = ["COUNT(  $column  ) AS count"];
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

         if (!empty($this->where) || !empty($this->like)) {
            $sql .= " WHERE ";
            $sql .= implode(' AND ', array_merge($this->where, $this->like));
         }

         if (!empty($this->whereOr)) {
            if (!empty($this->where) || !empty($this->like)) {
                $sql .= " AND (" . implode(' OR ', $this->whereOr) . ")";
            } else {
                $sql .= " WHERE " . implode(' OR ', $this->whereOr);
            }
         }

         if (!empty($this->orderBy)) {
            $sql .= " ORDER BY {$this->orderBy}";
         }

         if (!empty($this->groupBy)) {
            $sql .= " GROUP BY {$this->groupBy}";
         }

          if (!empty($this->having)) {
            $sql .= " HAVING {$this->having}";
         }

         if (!empty($this->limit)) {
            $sql .= " LIMIT {$this->limit}";
         }

         if ($this->distinct) {
            $sql = str_replace("SELECT", "SELECT DISTINCT", $sql);
         }

         return $sql;
      }

}

$instance = new QueryBuilder();
$query    = $instance->table('users')->count('email')->toSQL();

echo $query;

$result   = run($query);

echo "<pre>";
  print_r($result);
echo "</pre>";
