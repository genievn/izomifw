<?php

/**
 * BaseWfmcProcessInstance
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $uuid
 * @property string $instance
 * @property Doctrine_Collection $WfmcWorkItems
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseWfmcProcessInstance extends izDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('wfmc_process_instance');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'autoincrement' => true,
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('uuid', 'string', 255, array(
             'type' => 'string',
             'unique' => true,
             'length' => '255',
             ));
        $this->hasColumn('instance', 'string', null, array(
             'type' => 'string',
             ));

        $this->option('type', 'INNODB');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('WfmcWorkItem as WfmcWorkItems', array(
             'local' => 'uuid',
             'foreign' => 'process_id'));
    }
}