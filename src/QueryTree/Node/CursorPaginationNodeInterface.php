<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\QueryTree\Node;


/**
 * Cursor (seek) pagination: resume after an opaque marker emitted by the
 * previous page. The marker is engine/driver-defined; the deterministic
 * ordering it relies on comes from QueryInterface::order().
 */
interface CursorPaginationNodeInterface extends PaginationNodeInterface
{
    /**
     * Opaque resume marker, or null when absent.
     * 
     * @return mixed 
     */
    public function cursor(): mixed;
}
