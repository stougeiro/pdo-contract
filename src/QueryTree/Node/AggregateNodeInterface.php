<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\QueryTree\Node;

use STDW\Contract\Pdo\QueryTree\Type\AggregateFunction;


/**
 * A single aggregation projection: one function over one input column,
 * exposed under an output alias. Projection nodes ride inside
 * QueryInterface (aggregates()); the surrounding where/groupBy/having/order
 * live on the query tree itself (single IR).
 */
interface AggregateNodeInterface
{
    /**
     * Output alias of the computed column.
     * 
     * @return string 
     */
    public function alias(): string;

    /**
     * The aggregation function applied.
     * 
     * @return AggregateFunction 
     */
    public function operation(): AggregateFunction;

    /**
     * Input column. Use "*" for count(*) when no column applies.
     * 
     * @return string 
     */
    public function column(): string;
}
