<?php
/**
 * @author Jonathan Moss <xirisr@gmail.com>
 * @copyright 2010 Jonathan Moss
 */

require_once dirname(__FILE__).'/MongoTestCase.php';
require_once dirname(__FILE__).'/test-objects/ComposeOneParent.php';
require_once dirname(__FILE__).'/test-objects/Child.php';

class TestComposeOne extends MongoTestCase
{

    public function setUp()
    {
        parent::setUp();
        $mongo = new Mongo();
        Morph_Storage::init($mongo->selectDB(self::TEST_DB_NAME));
    }

    public function tearDown()
    {
        parent::tearDown();
        Morph_Storage::deInit();
    }

    public function testStoresParentAndChild()
    {
        $parent = new ComposeOneParent();
        $parent->Name = 'Compose One Parent';

        $child = new Child();
        $child->Name = 'Child';

        $parent->Child = $child;

        $parent->save();
        sleep(1); //MongoDB can take a sec to allocate the collection files
        $this->assertCollectionExists('ComposeOneParent');
        $this->assertCollectionDoesNotExist('Child');

        $this->assertDocumentExists('ComposeOneParent', $parent->id());

        $this->assertDocumentPropertyEquals(array('_ns'=>'Child', 'Name'=>'Child'), 'ComposeOneParent', 'Child', $parent->id());
    }

}