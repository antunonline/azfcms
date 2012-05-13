<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TestTreeModel
 *
 * @author Antun Horvat <at> it-branch.com
 */
;

// TODO Add tree assertions to move tests
class Azf_Model_Tree_DefaultTest extends PHPUnit_Framework_TestCase {

    /**
     *
     * @var Azf_Model_Tree_Abstract
     */
    protected $model;

    public function getTreeSum($start, $end) {
        $result = 0;
        while ($start <= $end) {
            $result += $start;
            $start++;
        }

        return $result;
    }

    public function getDbTreeSum() {
        return Azf_PHPUnit_Db_DbModel::getTreeSum();
    }

    public static function setUpDBTable() {
        if(!Azf_PHPUnit_Db_Utils::tableExist("Tree")){
            Azf_PHPUnit_Db_Utils::createTable("Tree");
        }
    }

    public static function setUpBeforeClass() {
        Azf_PHPUnit_Db_ConnectionFactory::initDefaultDbTableAdapter();
        self::setUpDBTable();
    }

    public function setUp() {
        
        Azf_PHPUnit_Db_DbModel::reset();
        Azf_PHPUnit_Db_DbModel::populate(__DIR__ . "/TestTreeModel//SampleDataSet.xml");
        $this->model = new Azf_Model_Tree_Default();
    }

    protected function tearDown() {
//        PHPUnit_TreeModel_DBModel::reset();
    }

    public function testInitTree1() {
        $this->setExpectedException("RuntimeException");
        $this->model->initTree(null);
    }

    public function testInitTree3() {

        $this->model->setTid(3);
        $this->model->initTree("TREE Three");
        $this->assertEquals(array('id' => 41, 'l' => 1, 'r' => 2, 'parentId' => null, 'value' => 'TREE Three', 'childNodes' => array()), $this->model->getTree(null, true));
    }

    public function testInitTree3AndReadIt() {
        $this->model->setTid(3);
        $this->model->initTree("HELLO");
        $this->assertTree($this->model->getBranch(1, null, true));
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testInsertInto1() {

        $this->assertTrue(false != $this->model->insertInto(1, "testInsertInto1"));
        $this->assertEquals($this->getTreeSum(1, 42), $this->getDbTreeSum());
    }

    public function testInsertInto6() {

        $this->assertTrue(false !== $this->model->insertInto(6, "testInsertInto6"));
        $this->assertEquals($this->getTreeSum(1, 42), $this->getDbTreeSum());
    }

    public function testInsertInto11() {

        $this->assertTrue(false !== $this->model->insertInto(11, "testInsertInto11"));

        $this->assertEquals($this->getTreeSum(1, 42), $this->getDbTreeSum());
    }

    public function testInsertAfter1() {
        $this->assertFalse($this->model->insertAfter(1, "testInsertAfter1"));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
    }

    public function testInsertAfter4() {
        $this->model->insertAfter(4, "testInsertAfter4");

        $this->assertEquals($this->getTreeSum(1, 42), $this->getDbTreeSum());
    }

    public function testInsertBefore6() {
        $this->model->insertBefore(6, "testInsertBefore6");

        $this->assertEquals($this->getTreeSum(1, 42), $this->getDbTreeSum());
    }

    public function testInsertDoubleBefore6() {
        $this->model->insertBefore(6, "testInsertDoubleBefore6");
        $this->model->insertBefore(6, "testInsertDoubleBefore6 double");

        $this->assertEquals($this->getTreeSum(1, 44), $this->getDbTreeSum());
    }

    public function testDelete6() {
        $this->assertTrue($this->model->delete(6));
    }

    public function testDelete13And6() {
        $this->assertTrue($this->model->delete(13));
        $this->assertTrue($this->model->delete(6));

        $this->assertEquals($this->getTreeSum(1, 36), $this->getDbTreeSum());
    }

    public function testDelete14() {
        $this->assertTrue($this->model->delete(14));
        $this->assertEquals($this->getTreeSum(1, 26), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove14Into2() {
        $this->assertTrue($this->model->moveInto(14, 2));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove13Into1() {
        $this->assertTrue($this->model->moveInto(13, 1));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove2Into14() {
        $this->assertTrue($this->model->moveInto(2, 14));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove12Into2() {
        $this->assertTrue($this->model->moveInto(12, 2));
        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove2Into12() {
        $this->assertTrue($this->model->moveInto(2, 12));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove21Into12() {
        $this->assertTrue($this->model->moveInto(20, 12));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove5Into6() {
        $this->assertFalse($this->model->moveInto(5, 6));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove2Before14() {
        $this->assertTrue($this->model->moveBefore(2, 14));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove2Before16() {
        $this->assertTrue($this->model->moveBefore(2, 16));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove14Before2() {
        $this->assertTrue($this->model->moveBefore(14, 2));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove12Before20() {
        $this->assertTrue($this->model->moveBefore(12, 20));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove5Before6() {
        $this->assertFalse($this->model->moveBefore(5, 6));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove15Before7() {
        $this->assertTrue($this->model->moveBefore(15, 7));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove14Before1() {
        $this->assertFalse($this->model->moveBefore(15, 1));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove14After2() {
        $this->assertTrue($this->model->moveAfter(14, 2));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove2After14() {
        $this->assertTrue($this->model->moveAfter(2, 14));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove4After14() {
        $this->assertTrue($this->model->moveAfter(4, 14));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove4After16() {
        $this->assertTrue($this->model->moveAfter(4, 16));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove5After6() {
        $this->assertFalse($this->model->moveAfter(5, 6));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove2After20() {
        $this->assertTrue($this->model->moveAfter(2, 20));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove15After7() {
        $this->assertTrue($this->model->moveAfter(15, 7));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove7After15() {
        $this->assertTrue($this->model->moveAfter(7, 15));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testMove14After1() {
        $this->assertFalse($this->model->moveAfter(14, 1));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testTreeModel() {
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testTreeWithTwoAdditionalNodes() {
        $this->model->insertAfter(16, "NODE");
        $this->model->insertAfter(18, "NODE");
        $this->assertTree($this->model->getTree(null, true));
    }

    public function testGetDeletedTree() {
        $this->model->deleteTree();
        $this->assertFalse($this->model->getTree(null, true));
    }

    public function testDeleteTree() {
        $this->model->deleteTree();
        $this->assertEquals(0, $this->getDbTreeSum());
    }

    public function testGetBranch1() {
        $this->assertTree($this->model->getBranch(1, null, true));
    }

    public function testGetBranch2() {
        $this->assertTree($this->model->getBranch(1, null, true), 1, 40);
        $this->assertEquals(array('id' => 2, 'l' => 2, 'r' => 3, 'parentId' => 1, 'value' => 'Node', 'childNodes' => array()), $this->model->getBranch(2, "*", true));
    }

    public function testGetBranch3() {
        $this->assertTree($this->model->getBranch(3, null, true), 4, 5);
    }

    public function testGetBranch20() {
        $this->assertTree($this->model->getBranch(20, null, true), 35, 36);
    }

    public function testGetBranch0() {
        $this->assertEquals(false, $this->model->getBranch(0, null, true));
    }

    public function testGetBranch14() {
        $this->assertTree($this->model->getBranch(10, null, true), 14, 21);
    }

    public function testAssertTree() {
        $this->assertTree(include __DIR__ . "/TestTreeModel/Tree.php");
    }

    public function testGetVisible1() {
        $this->assertTree($this->model->getVisible(1, null, true), null, null, false);
    }

    public function testGetVisible4() {
        $this->assertTree($this->model->getVisible(4, null, true), null, null, false);
    }

    public function testGetVisible20() {
        $this->assertTree($this->model->getVisible(20, null, true), null, null, false);
    }

    public function testVisible100() {
        $this->assertFalse($this->model->getVisible(100, null, true), null, null, false);
    }

    public function testComplexExample1() {
        $this->model->setTid(2);
        $this->model->initTree("TWO");
    }

    public function testGetParents1() {
        $this->assertFalse($this->model->getParents(1));
    }

    public function testGetParents2() {
        $this->assertEquals(array(
            "id" => 1,
            "l" => 1,
            'r' => 40,
            'parentId' => null,
            'value' => "Node",
            'childNodes' => array()), $this->model->getParents(2, null, true));
    }

    public function testGetParents11() {
        $actual = $this->model->getParents(11, array('id'), false);
        $expected = array(
            'childNodes' => array(
                array(
                    'childNodes' => array(
                        array(
                            'childNodes' => array(
                                array(
                                    'childNodes' => array(
                                        array(
                                            'childNodes' => array(
                                                array(
                                                    'id' => "10",
                                                    'childNodes' => array()
                                                )
                                            ),
                                            'id' => "9"
                                        )
                                    ),
                                    'id' => "7"
                                )
                            ),
                            'id' => 5,
                        )
                    ),
                    'id' => "4",
                )
            ),
            'id' => 1
        );

        $this->assertEquals($expected, $actual);
    }

    public function testGetParents2OnlyLandR() {
        $this->assertEquals(array(
            "l" => 1,
            'r' => 40, 'childNodes' => array()), $this->model->getParents(2, array('l', 'r'), true));
    }

    public function testGetParents6() {
        
    }

    public function testFinnaly() {
        $this->assertTrue($this->model->moveInto(2, 12));

        $this->assertEquals($this->getTreeSum(1, 40), $this->getDbTreeSum());
        $this->assertTree($this->model->getTree(null, true));
    }

    public function assertTree($tree, $l = 0, $r = 0, $checkChildNodes = true) {
        if (!is_array($tree)) {
            throw new Exception("No tree array provided");
        }

        if (!$l && !$r) {
            $l = $tree['l'];
            $r = $tree['r'];
        }
        $p = $tree['id'];
        $pid = $tree['parentId'];

        if (count($tree['childNodes']) == 0 && $r - $l > 1) {
            throw new Exception(sprintf("Bound of parent node are (%d,%d) but no child node is present", $l, $r));
        } else if ($tree['l'] != $l || $tree['r'] != $r) {
            throw new Exception(sprintf("Provided tree bounds are (%d,%d) but given tree is within (%d,%d) bounds", $l, $r, $tree['l'], $tree['r']));
        }


        for ($i = 0; $i < sizeof($tree['childNodes']); $i++) {
            $n = $tree['childNodes'][$i]; // Node
            $bn = $i == 0 ? null : $tree['childNodes'][$i - 1]; // Before node
            $an = ($i + 1) == sizeof($tree['childNodes']) ? null : $tree['childNodes'][$i + 1]; // After node

            if ($n['parentId'] !== $p) {
                throw new Exception(sprintf("The parentId value of node (id=%d,parentId=%d) does not match its parents id (id=%d,parentId=%d)", $n['id'], $n['parentId'], $p, $pid));
            }

            if (($l < $n['l']) && ($r > $n['r'])) {
                if ($bn === null && ($n['l'] - 1 != $l)) {
                    throw new Exception(sprintf("First child of parent (%d,%d) does starts with invalid bounds values (%d,%d)", $l, $r, $n['l'], $n['r']));
                } else if ($bn && ($bn['r'] + 1 != $n['l'])) {
                    throw new Exception(sprintf("Nodes bounds (%d,%d) are not continuing previous sibling ordering (%d,%d)", $n['l'], $n['r'], $bn['l'], $bn['r']));
                } else if (sizeof($tree['childNodes']) < $i + 1 && ($n['r'] != $r)) {
                    throw new Exception(sprintf("The last node  in current branch does not have the value of %d-1, but has %d", $r, $n['r']));
                }

                if ($n['r'] - $n['l'] > 1) {
                    if ($checkChildNodes) {
                        $this->assertTree($n);
                    }
                }
            } else {
                throw new Exception(sprintf("Node should be within parent bounds (%d,%d), but it contains values (%d,%d)", $l, $r, $n['l'], $n['r']));
            }
        }
    }

}
