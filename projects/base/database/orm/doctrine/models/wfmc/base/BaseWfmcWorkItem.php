<?php

/**
 * BaseWfmcWorkItem
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $workitem_id
 * @property string $process_id
 * @property string $process_definition_id
 * @property string $activity_id
 * @property string $activity_definition_id
 * @property string $instance
 * @property string $role
 * @property string $status
 * @property WfmcProcessInstance $WfmcProcessInstance
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseWfmcWorkItem extends izDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('wfmc_work_item');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('workitem_id', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
        $this->hasColumn('process_id', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('process_definition_id', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('activity_id', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
        $this->hasColumn('activity_definition_id', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('instance', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('role', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('status', 'string', 20, array(
             'type' => 'string',
             'length' => '20',
             ));

        $this->option('type', 'INNODB');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('WfmcProcessInstance', array(
             'local' => 'process_id',
             'foreign' => 'uuid'));
    }
}