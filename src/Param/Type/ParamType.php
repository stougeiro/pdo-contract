<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\Param\Type;

use PDO;


/**
 * The PDO bind type of a value. Each case value IS the matching
 * PDO binding constant — there is no translation step: the Connection binds
 * with $param->type()->value directly.
 *
 * Float has no PDO bind type (PDO has no PARAM_FLOAT); floats are bound as
 * String. Binary maps to LOB.
 */
enum ParamType: int
{
    case Null = PDO::PARAM_NULL;
    case Bool = PDO::PARAM_BOOL;
    case Int = PDO::PARAM_INT;
    case String = PDO::PARAM_STR;
    case Binary = PDO::PARAM_LOB;
}
