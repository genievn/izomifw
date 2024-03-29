<?php

/**
 * BaseWfPlace
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $wf_id
 * @property integer $place_type
 * @property string $place_name
 * @property string $place_desc
 * @property timestamp $created_date
 * @property integer $created_user
 * @property timestamp $revised_date
 * @property integer $revised_user
 * @property WfWorkflow $WfWorkflow
 * @property Doctrine_Collection $WfArcs
 * @property Doctrine_Collection $WfToken
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseWfPlace extends izDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('wf_place');
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
        $this->hasColumn('place_type', 'integer', 2, array(
             'type' => 'integer',
             'length' => '2',
             ));
        $this->hasColumn('place_name', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('place_desc', 'string', null, array(
             'type' => 'string',
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

        $this->hasMany('WfArc as WfArcs', array(
             'local' => 'id',
             'foreign' => 'place_id'));

        $this->hasMany('WfToken', array(
             'local' => 'id',
             'foreign' => 'place_id'));
    }
}