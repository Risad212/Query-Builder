
# 📦 PHP Query Builder Documentation

Simple fluent SQL Query Builder using method chaining.

---

# 📌 SELECT (Get Data)

- `table()` → set table name
- `select()` → choose columns
- `count()` → count rows
- `aggregate()` → use SUM, AVG, MAX, MIN

---

# 📌 WHERE (Filter Rows)

- `where()` → AND condition
- `whereOr()` → OR condition
- `whereIn()` → multiple values match
- `whereBetween()` → range condition
- `whereNull()` → NULL check
- `whereExists()` → subquery check
- `like()` → search pattern

---

# 📌 GROUPING

- `groupBy()` → group similar rows
- `having()` → filter grouped data
- `havingGroup()` → multiple HAVING conditions

---

# 📌 JOIN & UNION

- `join()` → combine tables
- `union()` → merge queries

---

# 📌 SORTING

- `orderBy()` → sort results ASC/DESC

---

# 📌 LIMITATION

- `limit()` → limit results
- `offset()` → skip results

---

# 📌 OTHER

- `distinct()` → remove duplicates
- `toSQL()` → generate final SQL query

---

# 🔥 USAGE EXAMPLE

```php id="example1"
$query = $instance
    ->table('users')
    ->select('name')
    ->count('name')
    ->groupBy('name')
    ->having('COUNT(name) > 1')
    ->orderBy('name', 'ASC')
    ->limit(10)
    ->offset(5)
    ->toSQL();

echo $query;