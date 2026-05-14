<?php

declare(strict_types=1);

namespace Juling\Foundation\Enums;

use Juling\Foundation\Contracts\EnumMethodInterface;
use Juling\Foundation\Support\EnumMethods;

/**
 * 业务错误枚举
 */
enum BusinessEnum: int implements EnumMethodInterface
{
    use EnumMethods;

    /**
     * 查询列表错误
     */
    case QUERY_ERROR = 10001;

    /**
     * 查询数据不存在
     */
    case NOT_FOUND = 10002;

    /**
     * 新增数据失败
     */
    case CREATE_FAIL = 10003;

    /**
     * 新增数据错误
     */
    case CREATE_ERROR = 10004;

    /**
     * 获取详情错误
     */
    case SHOW_ERROR = 10005;

    /**
     * 更新数据失败
     */
    case UPDATE_FAIL = 10006;

    /**
     * 更新数据错误
     */
    case UPDATE_ERROR = 10007;

    /**
     * 删除数据失败
     */
    case DESTROY_FAIL = 10008;

    /**
     * 删除数据错误
     */
    case DESTROY_ERROR = 10009;

    /**
     * 数据已存在
     */
    case DATA_EXIST = 10010;

    /**
     * 数据访问错误
     */
    case ACCESS_ERROR = 10011;

    /**
     * 权限拒绝
     */
    case PERMISSION_DENIED = 10012;
}
