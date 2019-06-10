<?php

if (!function_exists('createIfNoneExistsReturnFirstOtherwise')) {
    /**
     * checks the database whether an instance of the given model-class exists and returns it
     * if there is no existing instance one will be created, stored, and returned instead
     *
     * @param string $fully_qualified_model_class_name The fully qualified classname of the model to check/create
     *                                                     e.g. 'App/Model/Impact'
     * @param array  $object_parameters                Parameters the returned model should have
     *                                                     e.g. ['type' => 'province']
    * @return Illuminate\Database\Eloquent\Model       The found or created model matching the parameters
     */
    function createIfNoneExistsReturnFirstOtherwise($fully_qualified_model_class_name, $object_parameters = [])
    {
        $first_model = call_user_func([$fully_qualified_model_class_name, 'where'], $object_parameters)->first();
        if ($first_model) {
            return $first_model;
        } else {
            return factory($fully_qualified_model_class_name, 1)->create($object_parameters)->first();
        }
    }
}
