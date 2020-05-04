<?php

/**
 * The force is strong with this Response class and you need to extend your response classes from this one.
 *
 * Class Response
 */
class Response {

    /**
     * Nothing special yet
     *
     * Response constructor.
     */
    public function __construct () { }

    /**
     * Seems like a good idea to implement an authorization system
     *
     * @return bool
     */
    public function authorized (): bool { return TRUE; }

}