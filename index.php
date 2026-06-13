<?php

require_once 'helpers.php';
require_once 'Trait/QueryCompiler.php';
require_once 'Trait/QuerySetter.php';

class QueryBuilder 
{

   use QuerySetter;
   use QueryCompiler;
   
    protected $table;
    protected $limit;
    protected $offset;
    protected $orderBy;
    protected $groupBy;
    protected $count;
    protected $distinct = false;
    protected $bindings = [];
    protected $having   = [];
    protected $where    = [];
    protected $select   = [];
    protected $whereOr  = [];
    protected $like     = [];
    protected $join     = [];
    protected $whereIn  = [];
    protected $unions   = [];

    /**
     * Get Binding
     * 
     * @return 
     */
    public function getBindings(): array
    {
       return $this->bindings;
     }

    /**
     * Get SQL Query
     * 
     * @return string
     */
      public function toSQL(): string 
      {
         $sql   = [];
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

         if (!empty($this->unions)) {
            foreach ($this->unions as $u) {
                  $type   = $u['all'] ? 'UNION ALL' : 'UNION';
                  $query .= " {$type} " . $u['query'];
            }
         }

         return $query;
      }

      /**
       * Reset Query 
       * 
       * @return self
       */
      public function reset(): self
      {
         $this->table    = null;
         $this->limit    = null;
         $this->offset   = null;
         $this->orderBy  = null;
         $this->groupBy  = null;
         $this->select   = [];
         $this->where    = [];
         $this->whereOr  = [];
         $this->like     = [];
         $this->whereIn  = [];
         $this->having   = [];
         $this->join     = [];
         $this->unions   = [];
         $this->bindings = [];
         $this->distinct = false;

         return $this;
      }
}

$qb = new QueryBuilder();

echo $qb->table('users')
    ->where('id', 1)
    ->toSQL();

echo "<br>";

$qb->reset();

echo $qb->table('posts')
    ->where('status', 'active')
    ->toSQL();
