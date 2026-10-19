<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\QueryTree\Type;


/**
 * Neutral operators used by ConditionNodeInterface.
 * Values are engine-agnostic keys; each dialect renders them to its
 * native representation (SQL symbols, etc.). Operators a dialect cannot
 * express make it throw DriverExceptionInterface — never silently
 * yield an empty result.
 *
 * Like/NotLike map to the engine's case-sensitive pattern match; Ilike/
 * NotIlike ask for a case-insensitive one — native on engines that have it
 * (ILIKE), emulated by others.
 */
enum ConditionOperator: string
{
    case Eq = 'eq';
    case Neq = 'neq';
    case Gt = 'gt';
    case Gte = 'gte';
    case Lt = 'lt';
    case Lte = 'lte';
    case In = 'in';
    case NotIn = 'not_in';
    case Like = 'like';
    case NotLike = 'not_like';
    case Ilike = 'ilike';
    case NotIlike = 'not_ilike';
    case IsNull = 'is_null';
    case IsNotNull = 'is_not_null';
    case Between = 'between';
    case NotBetween = 'not_between';
}
