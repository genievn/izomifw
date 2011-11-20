<?php
class ExceptionPlugin extends Object
{
    public function logExeption($e)
    {
        $this->getManager('exception')->logException($e);
    }
}
?>