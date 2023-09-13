<?php

namespace shiyunWechat\docs\annotation;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class DocsTitle
{
    public function __construct(
        string $str = '', // 标题 
    ) {
    }
}
