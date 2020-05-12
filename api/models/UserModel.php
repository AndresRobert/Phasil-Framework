<?php

use Base\Model;

/**
 * This is an example class to show how easy is to add a model, but it needs an actual model to be used :/.
 *
 * Class User
 */
class User extends Model {

    /**
     * User constructor.
     */
    public function __construct () {
        $this->table = 'users';
        parent::__construct();
    }

}

/*
-- You can use this
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id int(255) NOT NULL AUTO_INCREMENT,
  user_name varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  full_name varchar(255) DEFAULT NULL,
  device varchar(255) DEFAULT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  modified timestamp NULL DEFAULT NULL,
  deleted timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE users
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY user_name (user_name),
  ADD UNIQUE KEY email (email);
*/