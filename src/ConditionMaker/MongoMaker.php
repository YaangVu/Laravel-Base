<?php
/**
 * @Author yaangvu
 * @Date   Aug 25, 2022
 */

namespace YaangVu\LaravelBase\ConditionMaker;

use YaangVu\LaravelBase\Enums\OperatorEnum;

class MongoMaker extends NoSqlMaker implements Maker
{

    /**
     * @inheritDoc
     */
    public function like(): OperatorEnum
    {
        return OperatorEnum::LIKE;
    }
}
