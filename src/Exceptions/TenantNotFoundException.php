<?php

namespace Core\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TenantNotFoundException extends NotFoundHttpException
{
    /**
     * Create a new exception instance.
     *
     * @param string $message
     */
    public function __construct($message = 'এই নামে কোন পৌরসভা পাওয়া যায়নি!')
    {
        parent::__construct($message, null, 404);
    }
}
