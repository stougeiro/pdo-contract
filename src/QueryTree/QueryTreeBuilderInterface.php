<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\QueryTree;

use STDW\Contract\Pdo\Exception\QueryTreeExceptionInterface;
use STDW\Contract\Pdo\Param\ParamInterface;
use STDW\Contract\Pdo\QueryTree\Node\AggregateNodeInterface;
use STDW\Contract\Pdo\QueryTree\Node\GroupNodeInterface;
use STDW\Contract\Pdo\QueryTree\Node\OrderNodeInterface;
use STDW\Contract\Pdo\QueryTree\Node\PaginationNodeInterface;


/**
 * Static facade that lowers the declarative array DSL into the neutral
 * query tree. The arrays are the caller-facing surface; they lower to the
 * Node\ interfaces. Drivers never see the arrays, only the nodes.
 *
 * The facets validate and build one piece at a time; query() assembles the
 * whole QueryInterface the driver consumes. Anything invalid fails fast
 * with QueryTreeExceptionInterface, before the driver is reached.
 *
 * filter()/having() accept either:
 * - a Predicate: map of "column OP" => value (entries ANDed);
 * - a list of Clauses: Predicate nodes, the tokens "AND"/"OR"/"NOT",
 *   and nested lists as groups (parentheses).
 * The key is "column OP" (space-separated; OP defaults to EQ). The
 * operator vocabulary follows ConditionOperator (case-insensitive).
 *
 * order() accepts either a map of column => direction ("asc"|"desc") or a
 * list of "column", "column asc" / "column desc". pagination() accepts
 * limit/offset, or a cursor; a cursor selects the seek strategy.
 * columns() is a list of projected column names (empty means SELECT *).
 * groupBy() is a list of columns. aggregate() is a list of
 * {alias, function, column} entries (function case-insensitive; "*" is the
 * count column).
 *
 * Security and validity rules (each violation throws
 * QueryTreeExceptionInterface, before any driver is reached):
 * - values become typed Params and are never interpolated; the builder
 *   auto-wraps a raw value, or rejects one it cannot type;
 * - columns, tables, aliases and other identifiers are validated and
 *   handed to the dialect as-is — the dialect quotes and escapes them;
 * - "column OP" must parse to a known ConditionOperator; an empty filter
 *   yields no WHERE (null), never an empty group;
 * - operator arity is enforced (in/not_in need a non-empty list;
 *   between/not_between need exactly two; is_null/is_not_null take no
 *   value, hence a null value());
 * - pagination accepts either limit/offset or a cursor, never both; limit/
 *   offset are non-negative integers and the cursor stays a bound value;
 * - malformed logic (leading/trailing/duplicated tokens, a dangling NOT,
 *   an empty group) is rejected.
 *
 * @phpstan-type CriteriaValue ParamInterface|array<int, ParamInterface>|null
 * @phpstan-type Predicate array<string, CriteriaValue>
 * @phpstan-type Clause Predicate|'AND'|'OR'|'NOT'|array<int, Predicate|'AND'|'OR'|'NOT'|array<int, mixed>>
 * @phpstan-type Filter Predicate|array<int, Clause>
 * @phpstan-type Order array<int, string>|array<string, string>
 * @phpstan-type Pagination array{limit?: int, offset?: int, cursor?: mixed}
 * @phpstan-type AggregateSpec array{alias: string, function: string, column: string}
 */
interface QueryTreeBuilderInterface
{
    /**
     * Lowers a filter (WHERE) into its condition tree.
     *
     * @param Filter $filter
     * @return GroupNodeInterface
     * @throws QueryTreeExceptionInterface
     */
    public static function filter(array $filter): GroupNodeInterface;

    /**
     * Lowers a filter applied to groups (HAVING) into its condition tree.
     *
     * @param Filter $filter
     * @return GroupNodeInterface
     * @throws QueryTreeExceptionInterface
     */
    public static function having(array $filter): GroupNodeInterface;

    /**
     * Lowers the order DSL into ORDER BY clauses.
     *
     * @param Order $order
     * @return array<int, OrderNodeInterface>
     * @throws QueryTreeExceptionInterface
     */
    public static function order(array $order): array;

    /**
     * Lowers the pagination DSL into its node (offset or cursor strategy).
     *
     * @param Pagination $pagination
     * @return PaginationNodeInterface
     * @throws QueryTreeExceptionInterface
     */
    public static function pagination(array $pagination): PaginationNodeInterface;

    /**
     * Validates and returns the projected columns (empty means SELECT *).
     *
     * @param array<int, string> $columns
     * @return array<int, string>
     * @throws QueryTreeExceptionInterface
     */
    public static function columns(array $columns): array;

    /**
     * Validates and returns the GROUP BY columns.
     * 
     * @param array<int, string> $groupBy
     * @return array<int, string>
     * @throws QueryTreeExceptionInterface
     */
    public static function groupBy(array $groupBy): array;

    /**
     * Lower the aggregation projections.
     *
     * @param array<int, AggregateSpec> $aggregates
     * @return array<int, AggregateNodeInterface>
     * @throws QueryTreeExceptionInterface
     */
    public static function aggregate(array $aggregates): array;

    /**
     * Assembles the whole neutral query tree from the array DSL.
     *
     * @param Filter $filter
     * @param Order $order
     * @param Pagination $pagination
     * @param array<int, string> $groupBy
     * @param array<int, AggregateSpec> $aggregates
     * @param Filter $having
     * @param array<int, string> $columns
     * @return QueryInterface
     * @throws QueryTreeExceptionInterface
     */
    public static function query(
        array $filter = [],
        array $order = [],
        array $pagination = [],
        array $groupBy = [],
        array $aggregates = [],
        array $having = [],
        bool $distinct = false,
        array $columns = [],
    ): QueryInterface;
}
