<?php
namespace Xenolope\Cartographer\Exception;

class JsonDecodingException extends \Exception
{

    public function __construct($message)
    {
        parent::__construct(sprintf('An error occurred while decoding JSON: %s', $message));
    }
}
