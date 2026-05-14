<?php

declare(strict_types=1);

namespace Juling\Foundation\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionProperty;
use stdClass;
use Throwable;

trait DTOHelper
{
    public function __construct(array $data = [])
    {
        $this->loadData($data);
    }

    /**
     * 将数组批量赋值到对象属性
     */
    public function loadData(array $data = []): void
    {
        foreach ($data as $col => $val) {
            if (! is_null($val)) {
                $setMethod = 'set'.Str::studly($col);
                if (method_exists($this, $setMethod)) {
                    $this->$setMethod($val);
                }
            }
        }
    }

    /**
     * 将对象转换到数组
     */
    public function toData(bool $zeroValFilter = true): array
    {
        try {
            $data = [];
            foreach ($this as $k => $v) {
                $data[$k] = $v;
            }

            if ($zeroValFilter) {
                return $data;
            }

            $properties = $this->getProperties();
            foreach ($properties as $item) {
                if (isset($data[$item->getName()])) {
                    continue;
                }
                $data[$item->getName()] = $this->getDefaultValByType($item);
            }

            return $data;
        } catch (Throwable $e) {
            Log::error($e);

            return [];
        }
    }

    /**
     * 获取数据表数据
     */
    public function toEntity(bool $zeroValFilter = true): array
    {
        $data = [];
        foreach ($this->toData($zeroValFilter) as $key => $val) {
            $data[Str::snake($key)] = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : $val;
        }

        return $data;
    }

    /**
     * 获取数组数据
     */
    public function toArray(bool $zeroValFilter = true): array
    {
        return $this->toData($zeroValFilter);
    }

    /**
     * 获取JSON数据
     */
    public function toJson(bool $zeroValFilter = true): string
    {
        return json_encode($this->toData($zeroValFilter), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取Collection
     */
    public function collect(bool $zeroValFilter = true): Collection
    {
        return new Collection($this->toData($zeroValFilter));
    }

    /**
     * 获取对象属性
     */
    private static function getProperties(): array
    {
        $reflectionClass = new ReflectionClass(self::class);

        return $reflectionClass->getProperties(ReflectionProperty::IS_PRIVATE);
    }

    /**
     * 获取类型的默认值
     */
    private function getDefaultValByType(ReflectionProperty $property): mixed
    {
        $type = $property->getType();
        if ($type->isBuiltin()) {
            return match ($type->getName()) {
                // 标量类型
                'int' => 0,
                'float' => 0.0,
                'string' => '',
                'bool' => false,
                // 复合类型
                'array' => [],
                'object' => new stdClass,
                // 其他类型：resource, null
                default => null,
            };
        } else {
            return new ($type->getName());
        }
    }
}
