<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

if (! function_exists('mobile_mask')) {
    /**
     * 显示脱敏手机号码
     */
    function mobile_mask(string $mobile): string
    {
        return Str::mask($mobile, 3, 4);
    }
}

if (! function_exists('is_email')) {
    /**
     * 验证邮箱地址格式
     */
    function is_email(string $email): bool
    {
        return ! (filter_var($email, FILTER_VALIDATE_EMAIL) === false);
    }
}

if (! function_exists('is_mobile')) {
    /**
     * 验证手机号码格式
     */
    function is_mobile(string $mobile): bool
    {
        $rule = '/^1[3-9]\d{9}$/';

        return preg_match($rule, $mobile) === 1;
    }
}

if (! function_exists('error_response')) {
    function error_response(int $code, Throwable $e): JsonResponse
    {
        $data = null;

        // 如果是开发环境，添加额外的错误信息
        if (config('app.debug')) {
            $data = [
                'exception' => get_class($e), // 异常类名
                'trace' => $e->getTraceAsString(), // 异常追踪信息
            ];
        }

        return response()->json([
            'code' => $code,
            'message' => $e->getMessage(),
            'data' => $data,
        ], $code);
    }
}
