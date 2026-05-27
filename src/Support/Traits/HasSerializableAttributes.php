<?php

declare(strict_types=1);

namespace Juling\Foundation\Support\Traits;

trait HasSerializableAttributes
{
    /**
     * 将构造函数设为 final，确保 new static() 安全调用
     */
    final public function __construct() {}

    /**
     * 将对象转换为数组
     */
    public function toArray(): array
    {
        $array = [];
        $reflection = new \ReflectionClass($this);

        foreach ($reflection->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            // 避免未初始化属性触发错误
            if (! $property->isInitialized($this)) {
                continue;
            }

            $key = $property->getName();

            $array[$key] = $property->getValue($this);
        }

        return $array;
    }

    /**
     * 将对象转换为适用于数据表字段的数组
     */
    public function toEntity(): array
    {
        $data = [];

        foreach ($this->toArray() as $key => $value) {
            // 将驼峰式命名转换为蛇形命名 (例如: startCityId -> start_city_id)
            $key = \strtolower(\preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
            if ($value instanceof \BackedEnum) {
                $data[$key] = $value->value;
            } elseif ($value instanceof \UnitEnum) {
                $data[$key] = $value->name;
            } elseif (\is_array($value)) {
                $data[$key] = \json_encode($value, JSON_UNESCAPED_UNICODE);
            } elseif ($value instanceof \JsonSerializable) {
                $data[$key] = \json_encode($value, JSON_UNESCAPED_UNICODE);
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * 从数组中自动填充并实例化对象
     */
    public static function from(array $data): static
    {
        $instance = new static;

        foreach ($data as $key => $value) {
            if ($value === null) {
                continue;
            }

            // 将蛇形命名转换为驼峰式属性名 (例如: start_city_id -> startCityId)
            $propertyName = \lcfirst(\str_replace(' ', '', \ucwords(\str_replace('_', ' ', $key))));

            // 优先通过 Setter 方法进行赋值以确保类型安全与逻辑完整
            $setter = 'set'.ucfirst($propertyName);
            if (\method_exists($instance, $setter)) {
                $instance->$setter($value);
            } else {
                // 如果没有 Setter，通过反射直接赋值
                try {
                    $reflection = new \ReflectionProperty($instance, $propertyName);
                    $reflection->setValue($instance, $value);
                } catch (\ReflectionException $e) {
                    // 忽略不存在的属性
                }
            }
        }

        return $instance;
    }

    /**
     * 实现 JsonSerializable 接口，支持 json_encode() 自动调用
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * 将对象转为 JSON 字符串
     */
    public function toJson(int $options = JSON_UNESCAPED_UNICODE): string
    {
        return \json_encode($this->toArray(), $options);
    }

    /**
     * 支持对象直接作为字符串使用（如日志记录、字符串拼接）
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * 从 JSON 字符串中自动填充并实例化对象
     */
    public static function fromJson(string $json): static
    {
        $data = \json_decode($json, true);

        return static::from(\is_array($data) ? $data : []);
    }

    /**
     * 复制当前对象并更新部分字段，返回一个全新的实例
     */
    public function copy(array $data = []): static
    {
        return static::from([...$this->toArray(), ...$data]);
    }
}
