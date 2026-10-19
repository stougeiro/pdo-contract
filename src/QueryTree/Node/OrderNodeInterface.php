<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\QueryTree\Node;

use STDW\Contract\Pdo\QueryTree\Type\NullOrdering;
use STDW\Contract\Pdo\QueryTree\Type\OrderDirection;


/**
 * A single ORDER BY clause.
 */
interface OrderNodeInterface
{
    /**
     * The ordered column.
     * 
     * @return string
     */
    public function column(): string;

    /**
     * The neutral order direction.
     * 
     * @return OrderDirection
     */
    public function direction(): OrderDirection;

    /**
     * Where NULLs sort for this clause, or null to leave it to the engine.
     * The dialect renders it (native NULLS FIRST/LAST when supported).
     * 
     * @return null|NullOrdering
     */
    public function nulls(): ?NullOrdering;
}
