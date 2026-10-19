<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\QueryTree\Node;


/**
 * A set of nodes grouped together (parentheses).
 *
 * The group is the root of the condition expression: a single
 * ConditionNodeInterface is a leaf; a GroupNodeInterface composes leaves,
 * logic tokens and nested groups. There is no separate root node.
 *
 * Precedence of LogicNodeInterface tokens inside nodes(): evaluated
 * left-to-right; a LogicNode applies to the operand that follows it;
 * NOT negates only the next node/group; a group's default join is AND.
 *
 * Engines that cannot express a node must fail fast: the driver throws
 * DriverExceptionInterface rather than silently yielding an empty result,
 * so a dialect gap is never mistaken for "no matches".
 */
interface GroupNodeInterface
{
    /**
     * The ordered children: conditions, logic tokens and nested groups.
     *
     * @return array<int, ConditionNodeInterface|LogicNodeInterface|GroupNodeInterface>
     */
    public function nodes(): array;
}
