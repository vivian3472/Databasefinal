<?php
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\InclusionIn;

class Relationship extends Model
{
    public function validation()
    {
            // Check if any messages have been produced
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }
}
?>