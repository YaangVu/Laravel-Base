<?php
/**
 * @Author yaangvu
 * @Date   Aug 25, 2022
 */

namespace YaangVu\LaravelBase\Base\Clauses\LikeConditionMaker;

use YaangVu\LaravelBase\Base\Enums\OperatorEnum;

class PostgresMaker extends SqlMaker implements Maker
{
    /**
     * @inheritDoc
     */
    public function like(): OperatorEnum
    {
        return OperatorEnum::I_LIKE;
    }
}
