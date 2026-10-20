<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\Statement;

use STDW\Contract\Pdo\Param\ParamInterface;
use STDW\Contract\Pdo\QueryTree\QueryInterface;
use STDW\Contract\Pdo\Statement\Type\StatementKind;


/**
 * The executable IR: one low-level operation bound to a table.
 *
 * This is NOT the native \PDOStatement. It is the neutral, pre-SQL envelope
 * the driver consumes: kind (what), table (where), query (which rows, for
 * read/update/delete) and data (the typed values to write). The Model builds
 * it from the array DSL and hands it to DriverInterface::translate(), which
 * renders it to SQL for the Connection to prepare and run; the driver never
 * sees the arrays, and the native statement is created below, at the
 * Connection.
 *
 * Semantics:
 * - Select: query() carries the projection/filter/order/pagination;
 *   data() and returning() are empty;
 * - Insert: query() is null (no WHERE); data() is a non-empty column map;
 *   returning() may list columns to hand back, and the wildcard "*" means
 *   the whole record (e.g. so database-filled columns come back);
 * - Update: query() carries a non-empty WHERE (order/pagination/groupBy/
 *   having/aggregates are not meaningful); data() is a non-empty column map;
 * - Delete: query() carries a non-empty WHERE; data() and returning() are
 *   empty.
 *
 * A malformed envelope (a write without data, an update/delete without a
 * WHERE, an insert carrying a query, returning outside a write) is rejected
 * with StatementExceptionInterface — an unfiltered write is never silently
 * allowed.
 */
interface StatementInterface
{
    /**
     * The operation this statement performs.
     * 
     * @return StatementKind 
     */
    public function kind(): StatementKind;

    /**
     * The target table.
     * 
     * @return string 
     */
    public function table(): string;

    /**
     * The row selection (WHERE/ORDER/pagination/aggregation), when any.
     * 
     * @return null|QueryInterface 
     */
    public function query(): ?QueryInterface;

    /**
     * The typed values to write, keyed by column.
     *
     * @return array<string, ParamInterface>
     */
    public function data(): array;

    /**
     * Columns to hand back after a write; the wildcard "*" means the whole
     * record (so columns the database fills — defaults, timestamps,
     * generated values — are returned).
     *
     * @return array<int, string>
     */
    public function returning(): array;
}
