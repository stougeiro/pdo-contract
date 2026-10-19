<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\QueryTree\Type;


/**
 * Neutral order directions used by OrderNodeInterface. Values are the SQL
 * keywords themselves (identical across dialects), so drivers emit them
 * without translation.
 */
enum OrderDirection: string
{
    case Asc = 'ASC';
    case Desc = 'DESC';
}
