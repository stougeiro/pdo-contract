<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\QueryTree\Type;


/**
 * Neutral logical operators used by LogicNodeInterface. Values are the SQL
 * keywords themselves (identical across dialects), so drivers emit them
 * without translation.
 */
enum LogicOperator: string
{
    case And = 'AND';
    case Or = 'OR';
    case Not = 'NOT';
}
