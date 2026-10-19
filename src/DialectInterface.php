<?php declare(strict_types=1);

namespace STDW\Contract\Pdo;

use STDW\Contract\Pdo\QueryTree\Node\OrderNodeInterface;
use STDW\Contract\Pdo\QueryTree\Node\PaginationNodeInterface;
use STDW\Contract\Pdo\QueryTree\Type\ConditionOperator;


/**
 * The dialect seam: everything in SQL rendering that differs between
 * engines (sqlite, mysql, postgres). The driver renders the neutral query
 * tree through it; a dialect overrides only what diverges, while the
 * shared compilation (SELECT/INSERT/UPDATE/DELETE, WHERE, ORDER BY,
 * GROUP BY/HAVING, aggregates) lives in the common compiler.
 *
 * It owns identifier quoting, comparison-operator rendering
 * (LIKE/ILIKE), row limiting (LIMIT/OFFSET) and NULL ordering. Enum values
 * stay neutral: ConditionOperator keeps engine-agnostic keys and the
 * dialect renders them. DISTINCT placement is universal and lives on the
 * compiler, not here.
 *
 * This is the last line of defense for identifiers: everything a value is
 * bound as a Param, but a column/table/alias reaches SQL as an identifier,
 * so quote() MUST escape it (see below). The compiler relies on that.
 */
interface DialectInterface
{
    /**
     * Quotes and ESCAPES an identifier in this dialect's style ("col", `col`).
     *
     * The closing delimiter MUST be doubled so the identifier cannot break
     * out of its quotes (e.g. `a"b` -> "a""b"); this is the injection
     * boundary for identifiers. Qualified names are quoted segment by
     * segment ("t"."col") and the wildcard "*" is never quoted.
     * 
     * @param string $identifier 
     * @return string 
     */
    public function quote(string $identifier): string;

    /**
     * Renders a neutral comparison operator to this dialect's SQL.
     * Throws DriverExceptionInterface when the operator has no rendering.
     * Ilike/NotIlike render natively (ILIKE) where supported and are
     * emulated (e.g. a lowered LIKE) where they are not.
     * 
     * @param ConditionOperator $operator 
     * @return string 
     */
    public function operator(ConditionOperator $operator): string;

    /**
     * The row-limiting suffix, e.g. " LIMIT 10 OFFSET 20". Empty when absent.
     * Uses limit() from the base node; offset() is read only from an
     * OffsetPaginationNodeInterface (a cursor node renders the cap alone).
     * limit()/offset() are inlined as validated integers, never bound — the
     * engine's own syntax applies (an offset without a limit still needs a
     * limit clause on some engines).
     * 
     * @param null|PaginationNodeInterface $pagination 
     * @return string 
     */
    public function limitOffset(?PaginationNodeInterface $pagination): string;

    /**
     * Renders the NULL-positioning of an ORDER BY clause (e.g. "NULLS LAST"),
     * reading nulls() from the order node. Empty when the node asks for no
     * positioning or the engine leaves it to its default.
     * 
     * @param OrderNodeInterface $order 
     * @return string 
     */
    public function nulls(OrderNodeInterface $order): string;
}
