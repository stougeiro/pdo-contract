![PHP](https://img.shields.io/badge/PHP-%20^8.2-777BB4)
![PHPStan-Level](https://img.shields.io/badge/PHPStan-Level%209-224488)
![License](https://img.shields.io/badge/License-MIT-777)

# Database Contract

A PDO-based database contract: interfaces and enums only, with the model in front and a single-method, IR-driven PDO driver as the executor.

The package defines the types that let a single, dialect-agnostic model talk to
SQLite, MySQL and PostgreSQL through PDO. Dialects differ — quoting, pagination,
`LIKE`/`ILIKE`, `NULL` ordering, generated keys — so a neutral intermediate
representation (`QueryTree` + `Statement` + `Param`) normalizes what is common.
The model lowers the array DSL into the IR, and the driver renders that IR
through its dialect — the only place that knows it — returning a raw,
engine-neutral result the model standardizes.

The pipeline is four stages:

```
Config
  │ dsn + keys
  ▼
Connection
  │ PDO + transactions
  ▼
Driver
  │ IR → SQL → RAW
  ▼
Model
  │ arrays → IR → Result
```

Responsibilities are strictly layered: the **Connection** owns the physical
link and transactions (a transaction is a property of the connection), the
**Driver** normalizes the IR into SQL and a raw result, and the **Model** is a
spearhead that turns arrays into a `ResultInterface` — it exposes neither the
driver nor the connection.

## ✨ Features

- **Model in front, driver as executor** — `ModelInterface` is the
  developer surface (CRUD over a named table, driven by arrays, returning
  `ResultInterface`), while `DriverInterface` is a single-method, IR-driven
  executor: `execute(StatementInterface): RawInterface` renders the statement
  through the dialect and returns a normalized raw result, without
  standardizing it.
- **Static IR builder** — `QueryTreeBuilderInterface` is a static facade that
  lowers the declarative array DSL (filter/order/pagination/columns/groupBy/
  aggregate/having) into the neutral `QueryTree`, failing fast with
  `QueryTreeExceptionInterface` before the driver is reached.
- **Executable IR** — `StatementInterface` is the envelope the driver
  consumes (`kind` + `table` + `query` + `data` + `returning`); the Model
  builds it from the array DSL and the driver never sees the arrays.
- **Normalized raw result** — `DriverInterface::execute()` returns
  `RawInterface` (`rows()`/`affected()`/`insertedId()`): the driver absorbs the
  engine divergences, so an insert yields the generated key whether the engine
  produced it via `RETURNING` or its native last-insert-id. The Model never
  branches on dialect.
- **Transactions on the connection** — `ConnectionInterface` owns
  `begin()`/`commit()`/`rollback()`/`inTransaction()` (a transaction is a
  property of the underlying PDO connection). Sharing one Connection across
  Models is what lets a transaction span several of them.
- **Low-level access outside the model** — the Model exposes neither driver
  nor connection. Cross-model transactions and raw SQL go through the
  Connection (its transaction methods and its PDO handle); the Driver stays
  IR-only. Both are resolved by the implementation's registry.
- **Dialect seam** — `DialectInterface` isolates what diverges between
  engines: identifier quoting/escaping, operator rendering (`LIKE`/`ILIKE`),
  the row-limiting suffix (`LIMIT`/`OFFSET`) and `NULL` ordering. The common
  compiler renders everything else; a dialect overrides only what differs.
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
- **Per-dialect config** — `ConfigInterface` exposes the DSN and
  credentials; each dialect value object validates its own required keys.
- **Plain table names** — no resource/metadata abstraction; operations take
  `string $table`.
- **Native vocabulary** — `ParamType` values *are* the `PDO::PARAM_*`
  constants (no translation on bind); `LogicOperator`, `OrderDirection` and
  `AggregateFunction` values are the SQL keywords themselves. `ConditionOperator`,
  `NullOrdering` and `StatementKind` keep neutral keys, rendered per dialect by
  `DialectInterface` (`LIKE`/`ILIKE`, `NULLS FIRST/LAST`).
- **Five exception markers** — `DatabaseExceptionInterface` (root),
  `ConnectionExceptionInterface` (link/transaction), `QueryTreeExceptionInterface`
  (invalid IR/DSL), `StatementExceptionInterface` (malformed statement, e.g.
  an unfiltered write), `DriverExceptionInterface` (execution/dialect).
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

- `stougeiro/database`  
  Repository: https://github.com/stougeiro/database

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
