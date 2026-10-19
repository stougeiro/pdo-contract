<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\QueryTree\Type;


/**
 * Where NULLs sort within an ORDER BY clause.
 *
 * Neutral intent: the dialect decides how to express it. Engines with native
 * support emit NULLS FIRST / NULLS LAST; engines without it emulate the
 * positioning (or fall back to their default when the clause is absent).
 */
enum NullOrdering: string
{
    case First = 'first';
    case Last = 'last';
}
