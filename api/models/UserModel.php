<?php

use Base\Model;

/**
 * This is an example class to show how easy is to add a model, but it needs an actual model to be used :/.
 * Check the README file to see how to add one :)
 *
 * Class User
 */
class User extends Model {

    /**
     * User constructor.
     */
    public function __construct () {
        parent::__construct();
        $this->table = 'users';
    }

}