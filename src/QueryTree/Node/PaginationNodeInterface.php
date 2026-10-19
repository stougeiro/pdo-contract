<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\QueryTree\Node;


/**
 * Base of the pagination strategies: the row cap shared by both.
 *
 * The positioning diverges — offset (positional) in
 * OffsetPaginationNodeInterface, cursor (seek) in
 * CursorPaginationNodeInterface. A query carries exactly one strategy.
 */
interface PaginationNodeInterface
{
    /**
     * Maximum number of rows in the page, or null for no cap.
     * 
     * @return null|int 
     */
    public function limit(): ?int;
}
