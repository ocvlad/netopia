<?php

namespace App\Validators;

class DepartmentValidator
{
    public function validateCreate(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Department name is required.';
        }

        if (isset($data['parent_id']) && !is_int($data['parent_id'])) {
            $errors[] = 'Parent ID must be an integer.';
        }

        if (isset($data['flags']) && !is_int($data['flags'])) {
            $errors[] = 'Flags must be an integer.';
        }

        return $errors;
    }

    public function validateUpdate(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Department name is required.';
        }

        if (isset($data['parent_id']) && !is_int($data['parent_id'])) {
            $errors[] = 'Parent ID must be an integer.';
        }

        if (isset($data['flags']) && !is_int($data['flags'])) {
            $errors[] = 'Flags must be an integer.';
        }

        return $errors;
    }

    public function validateId($id): array
    {
        $errors = [];

        if (!is_int($id) || $id <= 0) {
            $errors[] = 'The ID must be a positive integer.';
        }

        return $errors;
    }
}
