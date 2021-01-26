<?php


namespace core;


abstract class Model
{

    protected function bind(Model $model, array $data)
    {
        foreach ($data as $field => $value) {
            if (property_exists($model, $field)) {
                $model->$field = $value;
            }
        }
        return $model;
    }

    public function truncate(string $tableName)
    {
        WpDb::db()->table($tableName)->truncate();
    }
}