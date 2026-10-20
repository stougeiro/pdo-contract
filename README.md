![PHP](https://img.shields.io/badge/PHP-%20^8.2-777BB4)
![PHPStan-Level](https://img.shields.io/badge/PHPStan-Level%209-224488)
![License](https://img.shields.io/badge/License-MIT-777)

# Database Contract

A PDO-based database contract: interfaces and enums only, with the model in front, a single-method, IR-only translator in the middle, and the connection as the sole execution boundary.

The package defines the types that let a single, dialect-agnostic model talk to
SQLite, MySQL and PostgreSQL through PDO. Dialects differ — quoting, pagination,
`LIKE`/`ILIKE`, `NULL` ordering, generated keys — so a neutral intermediate
representation (`QueryTree` + `Statement` + `Param`) normalizes what is common.
The model lowers the array DSL into the IR, and the driver translates that IR
into SQL through its dialect, delegating the actual execution to the
Connection (the only place that talks to the native handle); the result comes
back raw and engine-neutral for the model to standardize.

The pipeline is four stages:

```
Config
  │ dsn + keys
  ▼
Connection
  │ PDO + transactions + raw SQL
  ▼
Driver
  │ IR → SQL (delegates execution)
  ▼
Model
  │ arrays → IR → Result
```

Responsibilities are strictly layered: the **Connection** owns the physical
link, transactions (a transaction is a property of the connection), the raw
SQL boundary and — through the driver — the execution of the translated
statement; the **Driver** translates the IR into SQL and delegates execution
to the Connection; and the **Model** is a spearhead that turns arrays into a
`ResultInterface` — it exposes neither the driver nor the connection.

## ✨ Features

- **Model in front, translator in the middle** — `ModelInterface` is the
  developer surface (CRUD over a named table, driven by arrays, returning
  `ResultInterface`), while `DriverInterface` is a single-method, IR-only
  translator: `translate(StatementInterface): RawInterface` compiles the
  statement to SQL through the dialect, delegates execution to the Connection,
  and returns a normalized raw result, without standardizing it.
- **Static IR builder** — `QueryTreeBuilderInterface` is a static facade that
  lowers the declarative array DSL (filter/order/pagination/columns/groupBy/
  aggregate/having) into the neutral `QueryTree`, failing fast with
  `QueryTreeExceptionInterface` before the driver is reached.
- **Executable IR** — `StatementInterface` is the pre-SQL envelope the driver
  consumes (`kind` + `table` + `query` + `data` + `returning`; not the native
  `\PDOStatement`); the Model builds it from the array DSL and the driver never
  sees the arrays.
- **Normalized raw result** — `DriverInterface::translate()` returns
  `RawInterface` (`rows()`/`affected()`/`insertedId()`): the driver absorbs the
  engine divergences, so an insert yields the generated key whether the engine
  produced it via `RETURNING` or its native last-insert-id, and — when asked —
  the whole stored record (including database-filled defaults) whether the
  engine returns it natively or it is fetched back afterwards. The Model never
  branches on dialect.
- **Transactions on the connection** — `ConnectionInterface` owns
  `begin()`/`commit()`/`rollback()`/`inTransaction()` (a transaction is a
  property of the underlying PDO connection). Sharing one Connection across
  Models is what lets a transaction span several of them.
- **Raw SQL on the connection** — the Connection is the single execution
  boundary and the only place that talks to the native handle, offering the
  raw escape hatch (`fetch()`/`fetchAll()`/`execute()`) for what the neutral IR
  does not model (joins, `WITH`/recursion, window functions). Every value is a
  bound `ParamInterface` — never interpolated — so the caller owns the SQL
  (and the dialect) while the bind stays enforced. The handle itself is not
  exposed.
- **Low-level access outside the model** — the Model exposes neither driver
  nor connection. Cross-model transactions and raw SQL go through the
  Connection (its transaction methods and its raw methods); the Driver stays
  IR-only. Both are resolved by the implementation's registry.
- **Dialect seam** — `DialectInterface` isolates everything that diverges
  between engines: identifier quoting/escaping, operator rendering
  (`LIKE`/`ILIKE` and the native-vs-emulated decision), the row-limiting suffix
  (`LIMIT`/`OFFSET`), `NULL` ordering, the generated-key strategy (`RETURNING`
  vs last-insert-id) and the keyset cursor fragment. The common compiler renders
  everything else; a dialect overrides only what differs.
- **Neutral IR** — `QueryTree\QueryInterface` (projection via `columns()`,
  `distinct()`, filter/order/pagination/aggregation) and typed `ParamInterface`
  values; the model lowers arrays into it and the driver never sees the
  arrays, only the IR.
- **Standardized result** — the Model always returns `ResultInterface`
  (`data()`/`count()`/`isValid()`); the driver returns `RawInterface`.
- **Pagination strategies** — `PaginationNodeInterface` is the shared base
  (`limit()`); `OffsetPaginationNodeInterface` (positional) and
  `CursorPaginationNodeInterface` (seek) carry the two strategies. Page
  numbers are not in the IR.
- **Security by construction** — values are always typed `Param`s (never
  interpolated); operators, directions, functions and logic tokens are enums;
  `LIMIT`/`OFFSET` are validated integers; identifiers (table/column/alias)
  are the dialect's responsibility, where `quote()` **must escape** the
  quote delimiter (the single injection boundary). Malformed DSL/statements
  fail fast, before the engine.
- **Per-dialect config** — `ConfigInterface` exposes the DSN, credentials and
  the generated-key policy. `supportsReturning()` (default true) is the
  higher-priority switch, ANDed with the dialect's own `supportsReturning()`:
  declare false for a server older than the clause. `emulateReturning()` is
  only consulted where `RETURNING` is unavailable. Each dialect value object
  validates its own required keys.
- **Plain table names** — no resource/metadata abstraction; operations take
  `string $table`.
- **Native vocabulary** — `ParamType` values *are* the `PDO::PARAM_*`
  constants (no translation on bind); `LogicOperator`, `OrderDirection` and
  `AggregateFunction` values are the SQL keywords themselves. `ConditionOperator`,
  `NullOrdering` and `StatementKind` keep neutral keys, rendered per dialect by
  `DialectInterface` (`LIKE`/`ILIKE`, `NULLS FIRST/LAST`).
- **Five exception markers** — `DatabaseExceptionInterface` (root),
  `ConnectionExceptionInterface` (link/transaction/raw query),
  `QueryTreeExceptionInterface` (invalid IR/DSL),
  `StatementExceptionInterface` (malformed statement, e.g. an unfiltered
  write), `DriverExceptionInterface` (execution/dialect).
- **No silent dialect gaps** — an operator or clause a dialect cannot
  express throws `DriverExceptionInterface`; a gap is never mistaken for an
  empty result.

---

## 📦 Installation

Install via Composer:

```bash
composer require stougeiro/pdo-contract
```

## 🔧 Implementations

Below is a list of known implementations maintained by the community.

- `stougeiro/pdo-database`  
  Repository: https://github.com/stougeiro/pdo-database

---

## 🧠 Why?

SQL is not one thing. SQLite, MySQL and PostgreSQL disagree on quoting,
pagination, `LIKE`/`ILIKE`, `NULL` ordering and generated keys. A model
written directly against a dialect is trapped in it.

This contract splits the problem: neutral types describe *what* to do
(operators, values, ordering, pagination, aggregation), and each driver
decides *how* its dialect does it. One model, three dialects — the divergence
stays where it belongs, in the driver and its dialect.

---

## 🤝 Contributions

Contributions are welcome.
Feel free to open issues or submit pull requests.

<br>

[<img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" width="170"/>](https://www.buymeacoffee.com/stougeiro)
