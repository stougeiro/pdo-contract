<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\QueryTree\Type;


/**
 * Aggregation functions used by AggregateNodeInterface. Values are the SQL
 * function names themselves (identical across dialects), so drivers emit
 * them without translation.
 */
enum AggregateFunction: string
{
    case Count = 'COUNT';
    case Sum = 'SUM';
    case Avg = 'AVG';
    case Min = 'MIN';
    case Max = 'MAX';
}
