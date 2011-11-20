<?php
class Doctrine2Controller extends Object
{
	/**
	 * Retrieve a list of key/value pair, using for select box
	 * Return a JSON string
	 *
	 * @param string $entity 
	 * @param string $displayField 
	 * @param string $valueField 
	 * @return void
	 * @author Nguyen Huu Thanh
	 */
    public function retrieveLOV($entity, $displayField, $valueField)
    {
		$entity = str_replace(".","\\",$entity);
        $filter = @$_REQUEST["filter"];
        $doctrine = $this->getManager('doctrine2');

	    $collection = $doctrine->retrieveLOV($entity, $displayField, $valueField, $filter);

	    $render = $this->getTemplate("dummy");
	    $render->setData($collection);
        return $render;
    }

    public function test()
    {
    	print_r($this->getManager('doctrine2')->getAssociationMappings('Entities\Hse\HseUser'));
    	#$entity = "Entities\\Base\\Account";
    	#$c = 'username';

    	#$instance = $this->getManager('doctrine2')->createEntityFromRequest($entity);
    	#print_r($instance);
    }
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Nguyen Huu Thanh
	 **/
	public function testAssociation()
	{
		$account = $this->getManager('Account')->getAccount();
		print_r($account->getRolesAsNameArray());
	}
}
?>