<?php

namespace App\Traits;

trait NestedArrayHelperTrait
{
    protected function findValueInNestedArray(array $array, string $targetKey): ?array
    {
        foreach ($array as $key => $value) {
            if ($key === $targetKey) {
                return $value;
            }
            if (is_array($value)) {
                $result = $this->findValueInNestedArray($value, $targetKey);
                if ($result !== null) {
                    return $result;
                }
            }
        }

        return null;
    }

    protected function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix === '' ? $key : $prefix . '.' . $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }
}
