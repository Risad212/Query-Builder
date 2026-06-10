<?php

/*
 * Query Builder
 */

require_once 'helpers.php';

class QueryBuilder 
{
  
 protected $table;
 protected $limit;
 protected $offset;
 protected $orderBy;
 protected $groupBy;
 protected $having;
 protected $count;
 protected $distinct = false;
 protected $where    = [];
 protected $select   = [];
 protected $whereOr  = [];
 protected $like     = [];
 protected $join     = [];
 protected $whereIn  = [];
 

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
        $this->select = [];
        $this->select[] = "COUNT({$column}) AS count"; 

        return $this;
      }

      /**
       * Add JOIN table
       * 
       * @param string $table
       * @param string $condition
       * @param string $type
       * @return self
       */
      public function join(string $table, string $condition, string $type = 'INNER'): self 
      {
        $type = strtoupper($type);

        $this->join[] = "{$type} JOIN {$table} ON {$condition}";

        return $this;
      }

      /**
       * Add OFFSET table
       * 
       * @param int $offset
       * @return self
       */
      public function offset(int $offset): self 
      {
        $this->offset = $offset;

        return $this;
      }

      /**
       * Add IN in table
       * 
       * @param string $column
       * @param array  $values
       * @return self
       */
      public function whereIn(string $column, array $values): self 
      {
         $escaped = array_map(fn($v) => addslashes($v), $values);
      
         $this->whereIn[] = "{$column} IN ('" . implode("','", $escaped) . "')";
         
         return $this;
      }

      /**
       * Add SUM in column
       * 
       * @param string $column
       * @return self
       */
      public function sum(string $column): self
      {
         $this->select = ["SUM({$column}) AS total"];

         return $this;
      }


      /**
       * Add AVG in column
       * 
       * @param string $column
       * @return self
       */
      public function avg(string $column): self
      {
         $this->select = ["AVG({$column}) AS average"];

         return $this;
      }

      /**
       * Add MIN in column
       * 
       * @param string $column
       * @return self
       */
      public function min(string $column): self
      {
         $this->select = ["MIN({$column}) AS Minimum"];

         return $this;
      }

       /**
       * Add MAX in column
       * 
       * @param string $column
       * @return self
       */
      public function max(string $column): self
      {
         $this->select = ["MAX({$column}) AS Maximum"];
         return $this;
      }

      /**
       * Add BETWEEN in column
       * 
       * @param string $column
       * @param string $start
       * @param string $end
       * @return self
       */
      public function whereBetween(string $column, string $start, string $end): self 
      {
        $this->where[] = "{$column} BETWEEN {$start} AND {$end}";
        return $this;
      }

      /**
       * Find column where NULL
       * 
       * @param string $column
       * @return self
       */
      public function whereNull(string $column): self
      {
         $this->where[] = "{$column} IS NULL";
         return $this;
      }

      /**
       * Find column where not NULL
       * 
       * @param string $column
       * @return self
       */
      public function whereNotNull(string $column): self
      {
         $this->where[] = "{$column} IS NOT NULL";
         return $this;
      }

      /**
       * Build SELECT clause
       *
       * @return string
       */
      protected function buildSelect(): string
      {
         $select = !empty($this->select)
            ? implode(', ', $this->select)
            : '*';

         return "SELECT {$select}";
      }

      /**
       * Build FROM clause
       *
       * @return string
       */
      protected function buildFrom(): string
      {
         return "FROM {$this->table}";
      }

      /**
       * Build JOIN clause
       *
       * @return string
       */
      protected function buildJoin(): string
      {
         return !empty($this->join)
            ? implode(' ', $this->join)
            : '';
      }

      /**
       * Build WHERE clause
       *
       * @return string
       */
      protected function buildWhere(): string
      {
         $conditions = array_merge($this->where, $this->like, $this->whereIn);

         if (empty($conditions) && empty($this->whereOr)) return '';

         $sql = '';

         if (!empty($conditions)) {
            $sql .= "WHERE " . implode(' AND ', $conditions);
         }

         if (!empty($this->whereOr)) {
            $sql .= empty($conditions) ? "WHERE " : " AND ";
            $sql .= "(" . implode(' OR ', $this->whereOr) . ")";
         }

         return $sql;
      }

      /**
       * Build GROUP BY clause
       *
       * @return string
       */
      protected function buildGroupBy(): string
      {
         return !empty($this->groupBy)
            ? "GROUP BY {$this->groupBy}"
            : '';
      }

      /**
       * Build HAVING clause
       *
       * @return string
       */
      protected function buildHaving(): string
      {
         return !empty($this->having)
            ? "HAVING {$this->having}"
            : '';
      }

      /**
       * Build ORDER BY clause
       *
       * @return string
       */
      protected function buildOrderBy(): string
      {
         return !empty($this->orderBy)
            ? "ORDER BY {$this->orderBy}"
            : '';
      }

      /**
       * Build LIMIT clause
       *
       * @return string
       */
      protected function buildLimit(): string
      {
         $sql = '';

         if (!empty($this->limit)) {
            $sql = "LIMIT {$this->limit}";

            if (!empty($this->offset)) {
               $sql .= " OFFSET {$this->offset}";
            }
         }

         return $sql;
      }

      /**
       * Get SQL Query
       * 
       * @return string
       */
      public function toSQL(): string 
      {
         $sql = [];

         $sql[] = $this->buildSelect();
         $sql[] = $this->buildFrom();
         $sql[] = $this->buildJoin();
         $sql[] = $this->buildWhere();
         $sql[] = $this->buildGroupBy();
         $sql[] = $this->buildHaving();
         $sql[] = $this->buildOrderBy();
         $sql[] = $this->buildLimit();

         $query = implode(' ', array_filter($sql));

         if ($this->distinct) {
            $query = preg_replace('/^SELECT\s+/i', 'SELECT DISTINCT ', $query);
         }

         return $query;
      }
}

$instance = new QueryBuilder();
$query    = $instance->table('users')->whereNull('email')->toSQL();

echo $query;

$result = run($query);

echo "<pre>";
print_r($result);
echo "</pre>";