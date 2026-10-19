<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\QueryTree\Node;


/**
 * Offset (positional) pagination: skip offset rows, take limit rows.
 * The classic LIMIT n OFFSET m.
 */
interface OffsetPaginationNodeInterface extends PaginationNodeInterface
{
    /**
     * Number of leading rows to skip, or null for none.
     * 
     * @return null|int 
     */
    public function offset(): ?int;
}
