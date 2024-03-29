<?php

/**
 * BaseWfArc
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $wf_id
 * @property integer $transition_id
 * @property integer $place_id
 * @property integer $direction
 * @property integer $arc_type
 * @property string $pre_condition
 * @property timestamp $created_date
 * @property integer $created_user
 * @property timestamp $revised_date
 * @property integer $revised_user
 * @property WfWorkflow $WfWorkflow
 * @property WfTransition $WfTransition
 * @property WfPlace $WfPlace
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseWfArc extends izDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('wf_arc');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'autoincrement' => true,
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('wf_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('transition_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('place_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('direction', 'integer', 1, array(
             'type' => 'integer',
             'length' => '1',
             ));
        $this->hasColumn('arc_type', 'integer', 2, array(
             'type' => 'integer',
             'length' => '2',
             ));
        $this->hasColumn('pre_condition', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('created_date', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('created_user', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('revised_date', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('revised_user', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));

        $this->option('type', 'INNODB');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('WfWorkflow', array(
             'local' => 'wf_id',
             'foreign' => 'id'));

        $this->hasOne('WfTransition', array(
             'local' => 'transition_id',
             'foreign' => 'id'));

        $this->hasOne('WfPlace', array(
             'local' => 'place_id',
             'foreign' => 'id'));
    }
}