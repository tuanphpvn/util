<?php

namespace go1\util\tests;

use go1\util\award\AwardHelper;
use go1\util\schema\mock\AwardMockTrait;
use go1\util\Text;

class AwardHelperTest extends UtilTestCase
{
    use AwardMockTrait;

    private $awardData;

    public function setUp()
    {
        parent::setUp();

        $this->awardData = [
            'id'          => 1,
            'revision_id' => 1,
            'instance_id' => 1,
            'user_id'     => 1,
            'title'       => 'String',
            'description' => 'Just a string',
            'tags'        => '[force] [award]',
            'locale'      => null,
            'data'        => [
                'image'       => 'http://abc/abc.jpg',
                'image_cover' => 'http://abc/abc.jpg',
            ],
            'published'   => 1,
            'quantity'    => 1,
            'expire'      => 1,
            'created'     => 1,
            'enrolment'   => 'false',
            'items'       => [
                [
                    'award_revision_id' => 1,
                    'entity_id'         => 1,
                    'quantity'          => 1,
                ],
            ],
        ];
    }

    public function testAwardFormat()
    {
        $c = $this->getContainer();

        $awardData = json_decode(json_encode($this->awardData));
        $correct = clone $awardData;
        $wrong = clone $awardData;

        $correct->tags = Text::parseInlineTags($correct->tags);
        $correct->locale = [];
        AwardHelper::format($awardData, $c['html']);
        $this->assertEquals($correct, $awardData);

        // For some string value, its should convert to int
        $wrong->id = '1';
        $wrong->revision_id = '1';
        AwardHelper::format($wrong, $c['html']);
        $this->assertEquals($correct, $wrong);
    }

    public function dataLoad()
    {
        return [
            ['load'],
            ['loadByRevision'],
        ];
    }

    /** @dataProvider dataLoad */
    public function testLoad($methodName)
    {
        $awardData = $this->awardData;
        $awardData['data'] = json_encode($awardData['data']);
        $awardId = $this->createAward($this->db, $awardData);

        $award = call_user_func([AwardHelper::class, $methodName], $this->db, $awardId);
        $this->assertInternalType('int' , $award->id);
        $this->assertInternalType('int' , $award->revision_id);
        $this->assertInternalType('int' , $award->instance_id);
        $this->assertInternalType('int' , $award->user_id);
        $this->assertInternalType('int' , $award->published);
        $this->assertInternalType('int' , $award->expire);
        $this->assertInternalType('int' , $award->created);
        $this->assertInternalType('float' , $award->quantity);
        $this->assertEquals((object)$this->awardData['data'], $award->data);
        $this->assertEquals(['force', 'award'], $award->tags);
        $this->assertEquals([], $award->locale);

        $emptyAward = call_user_func([AwardHelper::class, $methodName], $this->db, 99);
        $this->assertEmpty($emptyAward);
    }

    public function testLoadItem()
    {
        $awardManualItemId = $this->createAwardItemManual($this->db, [
            'award_id' => 1,
            'data'     => $data = [
                'certificate' => [
                    'url'  => 'foo.com',
                    'size' => '1MB',
                    'name' => 'foo',
                ],
            ],
        ]);
        $awardManualItem = AwardHelper::loadManualItem($this->db, $awardManualItemId);

        $this->assertInternalType('object', $awardManualItem->data);
        $this->assertEquals(json_decode(json_encode($data)), $awardManualItem->data);
    }
}
