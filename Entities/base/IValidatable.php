<?php
namespace Entities\Base;

interface IValidatable
{
    /**
     * validate function.
     * 
     * @access public
     * @return array
     */
    public function validate();
}