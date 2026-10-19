<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\QueryTree\Node;

use STDW\Contract\Pdo\Param\ParamInterface;
use STDW\Contract\Pdo\QueryTree\Type\ConditionOperator;


/**
 * A column comparison: column + operator + typed value.
 */
interface ConditionNodeInterface
{
    /**
     * The compared column.
     * 
     * @return string 
     */
    public function column(): string;

    /**
     * The neutral comparison operator.
     * 
     * @return ConditionOperator 
     */
    public function operator(): ConditionOperator;

    /**
     * The compared value(s) as typed Params — never a raw literal.
     *
     * null only for the null-arity operators (is_null, is_not_null); a
     * single Param for the scalar operators; a list of Params for the
     * list/interval operators (in, not_in, between, not_between). The
     * builder guarantees the arity matches operator().
     *
     * @return null|ParamInterface|array<int, ParamInterface>
     */
    public function value(): null|ParamInterface|array;
}
