<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\QueryTree;

use STDW\Contract\Pdo\QueryTree\Node\AggregateNodeInterface;
use STDW\Contract\Pdo\QueryTree\Node\GroupNodeInterface;
use STDW\Contract\Pdo\QueryTree\Node\OrderNodeInterface;
use STDW\Contract\Pdo\QueryTree\Node\PaginationNodeInterface;


/**
 * The single, neutral query IR: projection, filter, order, pagination and
 * the aggregation clauses (groupBy, aggregates, having) in one structure.
 *
 * It rides inside StatementInterface, which the driver renders to SQL
 * through its dialect. The model lowers the array DSL into it through
 * QueryTreeBuilderInterface; drivers never see the arrays, only this IR.
 *
 * Semantics:
 * - columns() empty and aggregates() empty is SELECT *;
 * - where()/having() are null when absent; an absent where() is a full
 *   enumeration;
 * - distinct() folds duplicate rows (SELECT DISTINCT); its placement is
 *   universal and handled by the compiler, not the dialect;
 * - groupBy() without aggregates() is a plain GROUP BY;
 * - aggregates() without groupBy() aggregates the whole table;
 * - pagination() carries exactly one strategy (offset or cursor) and the
 *   row cap shared by both; keyset ordering comes from order().
 */
interface QueryInterface
{
    /**
     * Whether duplicate rows are folded (SELECT DISTINCT).
     * 
     * @return bool 
     */
    public function distinct(): bool;

    /**
     * Projected columns. Empty (with no aggregates()) means SELECT *.
     *
     * @return array<int, string>
     */
    public function columns(): array;

    /**
     * Row filter (WHERE).
     * 
     * @return null|GroupNodeInterface 
     */
    public function where(): ?GroupNodeInterface;

    /**
     * ORDER BY clauses, in order.
     *
     * @return array<int, OrderNodeInterface>
     */
    public function order(): array;

    /**
     * The pagination strategy: offset or cursor, plus the row cap.
     * 
     * @return null|PaginationNodeInterface 
     */
    public function pagination(): ?PaginationNodeInterface;

    /**
     * GROUP BY columns.
     *
     * @return array<int, string>
     */
    public function groupBy(): array;

    /**
     * Row filter applied to groups (HAVING).
     * 
     * @return null|GroupNodeInterface 
     */
    public function having(): ?GroupNodeInterface;

    /**
     * Aggregation projections.
     *
     * @return array<int, AggregateNodeInterface>
     */
    public function aggregates(): array;
}
