<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\Exception;


/**
 * The neutral query IR is invalid and was rejected before reaching the
 * engine: unknown operator, malformed value list, conflicting clauses.
 * Fail-fast at the tree edge; drivers never see a broken IR.
 */
interface QueryTreeExceptionInterface extends DatabaseExceptionInterface
{ }
