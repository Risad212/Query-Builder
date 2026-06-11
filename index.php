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
    protected $having   = [];
    protected $where    = [];
    protected $select   = [];
    protected $whereOr  = [];
    protected $like     = [];
    protected $join     = [];
    protected $whereIn  = [];
    protected $unions   = [];

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
}

$instance = new QueryBuilder();
$query    = $instance->table('orders')->where('price', '500')
    ->whereOr('price > 40000')
    ->toSQL();;

print($query);

$result = run($query);
echo "<pre>";
print_r($result);
echo "</pre>";