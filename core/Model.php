<?php


namespace core;


abstract class Model
{
    abstract public function tableName(): string;

    protected function bind(Model $model, array $data)
    {
        foreach ($data as $field => $value) {
            if (property_exists($model, $field)) {
                $model->$field = $value;
            }
        }
        return $model;
    }

    public function truncate(Wordpress $wordpress, string $tableName)
    {
        return $wordpress->db->exec('truncate ' . $tableName);
    }
}