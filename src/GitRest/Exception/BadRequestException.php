<?php

namespace GitRest\Exception;

class BadRequestException extends GitRestException
{
    protected $message = 'Request Error :(';
}
