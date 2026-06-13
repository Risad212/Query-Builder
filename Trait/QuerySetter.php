<?php

trait QuerySetter  
{

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
     $this->where[]    = "{$column} = ? ";
     $this->bindings[] = $value;

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
      public function whereOr( string $column, mixed $value, bool $not = false ): self 
      {
         $operator         = $not ? '!=' : '=';
         $this->whereOr[]  = "{$column} {$operator} ?";
         $this->bindings[] = $value;

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
         $value            = strtolower($value);
         $this->like[]     = "LOWER({$column}) LIKE ?";
         $this->bindings[] = '%' . $value . '%';

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
      public function having(string $column, string $operator, mixed $value): self
      {
         $this->having[] = "{$column} {$operator} ?";
         $this->bindings[] = $value;

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
        $type         = strtoupper($type);
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
       * Add WHERE IN in table
       * 
       * @param string $column
       * @param array  $values
       * @return self
       */
      public function whereIn(string $column, array $values, $not = false): self
      {
         if (empty($values)) {
            return $this;
         }

         $placeholders    = implode(',', array_fill(0, count($values), '?'));
         $operator        = $not ? 'NOT IN' : 'IN';
         $this->whereIn[] = "{$column} {$operator} ({$placeholders})";

         foreach ($values as $value) {
            $this->bindings[] = $value;
         }

         return $this;
      }

      /**
       * Add aggregate in column
       * 
       * @param string $column
       * @param string type
       * @return self
       */
      public function aggregate(string $type, string $column): self
      {
         $typeUpper    = strtoupper( $type );
         $this->select = ["{$typeUpper}({$column}) AS {$type}"];

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
      public function whereBetween(string $column, string $start, string $end , bool $not = false): self 
      {
         $operator         = $not ? 'NOT BETWEEN' : 'BETWEEN';
         $this->where[]    = "{$column} {$operator} ? AND ?";
         $this->bindings[] = $start;
         $this->bindings[] = $end;
        
         return $this;
      }

      /**
       * Find column where NULL
       * 
       * @param string $column
       * @return self
       */
       public function whereNull(string $column, bool $not = false): self
        {
          $this->where[] = $not ? "{$column} IS NOT NULL" : "{$column} IS NULL";
          return $this;
        }

      /**
       * Add UNION
       * 
       * @param string $query
       * @return self
       */
      public function union(string $query, bool $all = false): self
      {
         $this->unions[] = [
            'query' => $query,
            'all'   => $all,
         ];
         return $this;
      }

      /**
       * Add WHERE GROUP
       * 
       * @param
       * @return self
       */
      public function whereGroup(callable $callback): self
      {
         $group = new self();
         $callback($group);
         $this->where[]  = '(' . implode(' AND ', $group->where) . ')';
         $this->bindings = array_merge($this->bindings, $group->bindings);

         return $this;
      }

      /**
       * Add WHERE EXIST
       * 
       * @param string $query
       * @param bool   $not
       * @return self
       */
      public function whereExists(string $query, array $bindings = [], bool $not = false): self
      {
         $operator = $not ? 'NOT EXISTS' : 'EXISTS';

         $this->where[] = "{$operator} ({$query})";

         foreach ($bindings as $value) {
            $this->bindings[] = $value;
         }

         return $this;
      }

      /**
       * Add HAVING GROUP
       * 
       * @param callback $callback
       * @return self
       */
      public function havingGroup(callable $callback): self
      {
         $group = new self();
         $callback($group);
         $this->having[] = '(' . implode(' AND ', $group->having) . ')';
         $this->bindings = array_merge($this->bindings, $group->bindings);
         
         return $this;
      }
}