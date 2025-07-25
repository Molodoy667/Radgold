<?php
namespace App\Core;

class QueryBuilder {
    private $db;
    private $table;
    private $select = '*';
    private $where = [];
    private $orderBy = [];
    private $limit = null;
    private $offset = null;
    private $joins = [];
    private $groupBy = [];
    private $having = [];
    
    public function __construct($table) {
        $this->table = $table;
        $this->db = Router::getDb();
    }
    
    public function select($columns) {
        $this->select = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }
    
    public function where($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $this->where[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'type' => 'AND'
        ];
        
        return $this;
    }
    
    public function orWhere($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $this->where[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'type' => 'OR'
        ];
        
        return $this;
    }
    
    public function whereIn($column, $values) {
        $this->where[] = [
            'column' => $column,
            'operator' => 'IN',
            'value' => $values,
            'type' => 'AND'
        ];
        
        return $this;
    }
    
    public function whereNull($column) {
        $this->where[] = [
            'column' => $column,
            'operator' => 'IS NULL',
            'value' => null,
            'type' => 'AND'
        ];
        
        return $this;
    }
    
    public function whereNotNull($column) {
        $this->where[] = [
            'column' => $column,
            'operator' => 'IS NOT NULL',
            'value' => null,
            'type' => 'AND'
        ];
        
        return $this;
    }
    
    public function join($table, $first, $operator, $second, $type = 'INNER') {
        $this->joins[] = [
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
            'type' => $type
        ];
        
        return $this;
    }
    
    public function leftJoin($table, $first, $operator, $second) {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }
    
    public function rightJoin($table, $first, $operator, $second) {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }
    
    public function orderBy($column, $direction = 'ASC') {
        $this->orderBy[] = $column . ' ' . strtoupper($direction);
        return $this;
    }
    
    public function groupBy($column) {
        $this->groupBy[] = $column;
        return $this;
    }
    
    public function having($column, $operator, $value) {
        $this->having[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];
        
        return $this;
    }
    
    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }
    
    public function offset($offset) {
        $this->offset = $offset;
        return $this;
    }
    
    public function paginate($perPage = 15, $page = 1) {
        $offset = ($page - 1) * $perPage;
        
        $this->limit($perPage);
        $this->offset($offset);
        
        $data = $this->get();
        $total = $this->count();
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
    
    public function get() {
        $sql = $this->buildQuery();
        $params = $this->getParams();
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function first() {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }
    
    public function count() {
        $originalSelect = $this->select;
        $this->select = 'COUNT(*) as count';
        
        $sql = $this->buildQuery();
        $params = $this->getParams();
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->select = $originalSelect;
        
        return (int) $result['count'];
    }
    
    public function exists() {
        return $this->count() > 0;
    }
    
    public function sum($column) {
        $originalSelect = $this->select;
        $this->select = "SUM({$column}) as sum";
        
        $sql = $this->buildQuery();
        $params = $this->getParams();
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->select = $originalSelect;
        
        return (float) $result['sum'];
    }
    
    public function avg($column) {
        $originalSelect = $this->select;
        $this->select = "AVG({$column}) as avg";
        
        $sql = $this->buildQuery();
        $params = $this->getParams();
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->select = $originalSelect;
        
        return (float) $result['avg'];
    }
    
    public function max($column) {
        $originalSelect = $this->select;
        $this->select = "MAX({$column}) as max";
        
        $sql = $this->buildQuery();
        $params = $this->getParams();
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->select = $originalSelect;
        
        return $result['max'];
    }
    
    public function min($column) {
        $originalSelect = $this->select;
        $this->select = "MIN({$column}) as min";
        
        $sql = $this->buildQuery();
        $params = $this->getParams();
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->select = $originalSelect;
        
        return $result['min'];
    }
    
    private function buildQuery() {
        $sql = "SELECT {$this->select} FROM {$this->table}";
        
        // Joins
        foreach ($this->joins as $join) {
            $sql .= " {$join['type']} JOIN {$join['table']} ON {$join['first']} {$join['operator']} {$join['second']}";
        }
        
        // Where
        if (!empty($this->where)) {
            $sql .= " WHERE ";
            $conditions = [];
            
            foreach ($this->where as $i => $condition) {
                if ($i > 0) {
                    $conditions[] = $condition['type'];
                }
                
                if ($condition['operator'] === 'IN') {
                    $placeholders = str_repeat('?,', count($condition['value']) - 1) . '?';
                    $conditions[] = "{$condition['column']} IN ({$placeholders})";
                } elseif (in_array($condition['operator'], ['IS NULL', 'IS NOT NULL'])) {
                    $conditions[] = "{$condition['column']} {$condition['operator']}";
                } else {
                    $conditions[] = "{$condition['column']} {$condition['operator']} ?";
                }
            }
            
            $sql .= implode(' ', $conditions);
        }
        
        // Group By
        if (!empty($this->groupBy)) {
            $sql .= " GROUP BY " . implode(', ', $this->groupBy);
        }
        
        // Having
        if (!empty($this->having)) {
            $sql .= " HAVING ";
            $conditions = [];
            
            foreach ($this->having as $condition) {
                $conditions[] = "{$condition['column']} {$condition['operator']} ?";
            }
            
            $sql .= implode(' AND ', $conditions);
        }
        
        // Order By
        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }
        
        // Limit
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }
        
        // Offset
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }
        
        return $sql;
    }
    
    private function getParams() {
        $params = [];
        
        // Where parameters
        foreach ($this->where as $condition) {
            if ($condition['operator'] === 'IN') {
                $params = array_merge($params, $condition['value']);
            } elseif (!in_array($condition['operator'], ['IS NULL', 'IS NOT NULL'])) {
                $params[] = $condition['value'];
            }
        }
        
        // Having parameters
        foreach ($this->having as $condition) {
            $params[] = $condition['value'];
        }
        
        return $params;
    }
}