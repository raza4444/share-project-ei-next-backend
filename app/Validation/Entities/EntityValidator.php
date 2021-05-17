<?php
/**
 * by stephan scheide
 */

namespace App\Validation\Entities;


use App\Entities\Core\AbstractModel;

class EntityValidator
{

    private $model;

    /**
     * creates new validator with set model
     *
     * @param AbstractModel $model
     * @return EntityValidator
     */
    public static function createWithModel(AbstractModel $model)
    {
        $v = new EntityValidator();
        $v->model = $model;
        return $v;
    }

    /**
     * ensures a non empty string
     *
     * @return $this
     */
    public function notEmpty($name)
    {
        $v = $this->valueOfModel($name);
        $this->_a($v != null && $v && strlen((string)$v) > 0, "string $name not set");
        return $this;
    }

    /**
     * ensures valid id (integer, greater than zero)
     *
     * @param $name
     * @throws EntityValidatorException
     */
    public function validId($name)
    {
        $v = $this->valueOfModel($name);
        $this->_a(is_numeric($v), "$name must be numeric");
        $this->_a(is_int($v), "$name must be integer");
        $this->_a($v > 0, "$name must be greater than zero as an id");
        return $this;
    }

    private function valueOfModel($name)
    {
        return $this->model->$name;
    }

    private function _a($cont, $message)
    {
        if (!$cont) {
            throw new EntityValidatorException($message);
        }
    }

}
