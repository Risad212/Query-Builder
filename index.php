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
    protected $having;
    protected $count;
    protected $distinct = false;
    protected $where    = [];
    protected $select   = [];
    protected $whereOr  = [];
    protected $like     = [];
    protected $join     = [];
    protected $whereIn  = [];

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
$query    = $instance->table('orders')->aggregate('avg', 'price')->toSQL();

echo $query;
$result = run($query);
echo "<pre>";
print_r($result);
echo "</pre>";