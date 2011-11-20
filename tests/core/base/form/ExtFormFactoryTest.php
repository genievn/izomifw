<?php
require_once 'PHPUnit/Framework.php';
//require_once 'form/ExtFormFactory.php';

class ExtFormFactoryTest extends PHPUnit_Framework_Testcase{


	public function testCreateFormFromYaml(){

		$file = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR."test_form_config.yml";
		$form = ExtFormFactory::createFormFromYaml($file);
		//print $form->doHtml();
	}

	/*
	 * function testCreateGridFromYaml
	 * @param None
	 */

	public function testCreateGridFromYaml() {
		$file = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR."test_grid_config.yml";
		$grid = ExtFormFactory::createGridFromYaml($file);
	}

	/*
	 * function testHasChildren
	 *
	 */

	function testHasChildren() {
		$form = object("ExtForm",object("ExtBaseFormElement"));
		$child = object("ExtTextField",object("ExtBaseFormElement"));
		$form->addChild($child);
		$this->assertTrue($form->hasChildren());
		$form = $child = null;
	}

	/*
	 * function testSyncData
	 *
	 */

	function testSyncData() {
		$file = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR."test_form_config.yml";
		$form = ExtFormFactory::createFormFromYaml($file);

		$objectA = object("MockObject");
		$objectA->setTitle("Test sync data");
		$objectA->setEmail("nguyenhuuthanh@gmail.com");
		$objectA->setAge(27);
		$objectA->setRadio1(2);
		$objectA->setCheckbox1(array(2));
		$objectA->setCombobox1('billy@exttest.com');
		//$objectA->setBody('hello world');

		$form->sync($objectA, $mode="set");
		//print $form->doHtml();
		//print_r($objectA);
		$objectB = $objectA->copy();
		#change a property
		$objectB->setRadio1(4);
		$objectB->setCheckbox1(3);
		//print_r($objectB);
		$form->sync($objectB, $mode="get");

		$this->assertEquals($objectA->properties(), $objectB->properties());
	}
}
?>