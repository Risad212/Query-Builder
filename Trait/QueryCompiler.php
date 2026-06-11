<?php

trait QueryCompiler 
{

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
            if (empty($q->having)) {
              return '';
            }

          return 'HAVING ' . implode(' AND ', $q->having);
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
}