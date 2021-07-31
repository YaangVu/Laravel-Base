<?php


namespace YaangVu\LaravelBase\Constants;


class OperatorConstant
{
    const GT     = '>';
    const GE     = '>=';
    const LT     = '<';
    const LE     = '<=';
    const LIKE   = 'like';
    const EQUAL  = '=';
    const I_LIKE = 'ilike'; // specific for postgresql

    const GT_PATTERN    = 'gt';
    const GE_PATTERN    = 'ge';
    const LT_PATTERN    = 'lt';
    const LE_PATTERN    = 'le';
    const LIKE_PATTERN  = '~';
    const EQUAL_PATTERN = 'eq';

    const DEFAULT_OPERATORS
        = [
            self::GT_PATTERN    => self::GT,
            self::GE_PATTERN    => self::GE,
            self::LT_PATTERN    => self::LT,
            self::LE_PATTERN    => self::LE,
            self::LIKE_PATTERN  => self::LIKE,
            self::EQUAL_PATTERN => self::EQUAL
        ];
}
