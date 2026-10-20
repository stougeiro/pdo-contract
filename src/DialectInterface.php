<?php declare(strict_types=1);

namespace STDW\Contract\Pdo;

use STDW\Contract\Pdo\Param\ParamInterface;
use STDW\Contract\Pdo\QueryTree\Node\OrderNodeInterface;
use STDW\Contract\Pdo\QueryTree\Node\PaginationNodeInterface;
use STDW\Contract\Pdo\QueryTree\Type\ConditionOperator;


/**
 * The dialect seam: everything in SQL rendering that differs between
 * engines (sqlite, mysql, postgres). The driver translates the neutral query
 * tree through it; a dialect overrides only what diverges, while the
 * shared compilation (SELECT/INSERT/UPDATE/DELETE, WHERE, ORDER BY,
 * GROUP BY/HAVING, aggregates) lives in the common compiler.
 *
 * It owns identifier quoting, comparison-operator rendering (LIKE/ILIKE),
 * row limiting (LIMIT/OFFSET), NULL ordering, the case-insensitive LIKE
 * decision, the generated-key strategy (RETURNING vs last-insert-id) and
 * the keyset cursor fragment. Enum values stay neutral: ConditionOperator/
 * OrderDirection keep engine-agnostic keys and the dialect renders them.
 * DISTINCT placement is universal and lives on the compiler, not here.
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

    /**
     * Whether a case-insensitive LIKE is native (ILIKE), so the compiler can
     * emit it directly; false means the compiler emulates it (e.g. a lowered
     * LIKE). The comparison operator stays bound either way.
     * 
     * @return bool 
     */
    public function supportsIlike(): bool;

    /**
     * A full ORDER BY item, so NULL positioning can be emulated when the
     * engine lacks a native clause (e.g. rewriting the item with a leading
     * "col IS NULL"). The dialect owns the whole item, not just the suffix.
     * 
     * @param OrderNodeInterface $order 
     * @return string 
     */
    public function orderClause(OrderNodeInterface $order): string;

    /**
     * Whether a write can hand columns back through RETURNING (e.g. the
     * generated key as "INSERT ... RETURNING col"). False means the driver
     * falls back to the last-insert-id (and, when requested, emulates the
     * returned record with a follow-up select).
     * 
     * @return bool 
     */
    public function supportsReturning(): bool;

    /**
     * The keyset WHERE fragment for a cursor, appending its bound param.
     * Returns null when the dialect cannot express it (e.g. no applicable
     * order column). The fragment follows the order semantics of the dialect.
     *
     * @param mixed $cursor
     * @param array<int, OrderNodeInterface> $order
     * @param array<int, ParamInterface> $params
     * @return null|string
     */
    public function cursorClause(mixed $cursor, array $order, array &$params): ?string;
}
