<?php

/**
 * BaseTag
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $tag
 * @property string $raw_tag
 * @property TagItem $TagItem
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseTag extends izDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('tag');
        $this->hasColumn('id', 'integer', 10, array(
             'primary' => true,
             'autoincrement' => true,
             'type' => 'integer',
             'length' => '10',
             ));
        $this->hasColumn('tag', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
        $this->hasColumn('raw_tag', 'string', 100, array(
             'type' => 'string',
             'length' => '100',
             ));

        $this->option('type', 'INNODB');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('TagItem', array(
             'local' => 'id',
             'foreign' => 'tag_id'));
    }
}