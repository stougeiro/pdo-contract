<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\Param;

use STDW\Contract\Pdo\Param\Type\ParamType;


/**
 * A typed value destined to a bind slot. Drivers bind the native PDO type
 * directly instead of casting to string, without guessing from the PHP
 * value alone.
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
