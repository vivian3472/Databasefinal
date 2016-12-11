<?php
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\InclusionIn;

class User extends Model
{	
   public function validation()
    {

        //Robot name must be unique
        $this->validate(new Uniqueness(
            array(
                "field"   => "UserId",
                "message" => "The UserId name must be unique"
            )
        ));
        //Check if any messages have been produced
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }
}
