<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\Param;

use STDW\Contract\Pdo\Param\Type\ParamType;


/**
 * A typed value destined to a bind slot. The Connection binds the native PDO
 * type directly instead of casting to string, without guessing from the PHP
 * value alone. It is the only shape a value may take — raw values are never
 * accepted, in the IR or in raw SQL alike.
 */
interface ParamInterface
{
    /**
     * The raw value.
     * 
     * @return mixed 
     */
    public function value(): mixed;

    /**
     * The PDO bind type this value is bound as (see ParamType).
     * 
     * @return ParamType 
     */
    public function type(): ParamType;
}
