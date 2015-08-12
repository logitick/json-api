<?php

/**
 * Copyright 2015 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CloudCreativity\JsonApi\Error;

class SourceObjectTest extends \PHPUnit_Framework_TestCase
{

    const POINTER = '/foo/bar/baz';
    const PARAMETER = 'foobar';

    protected $data;
    protected $arr;

    protected function setUp()
    {
        $data = new \stdClass();
        $data->{SourceObject::POINTER} = self::POINTER;
        $data->{SourceObject::PARAMETER} = self::PARAMETER;

        $this->data = $data;
        $this->arr = get_object_vars($data);
    }

    public function testConstruct()
    {
        $object = new SourceObject($this->data);

        $this->assertEquals($this->data->{SourceObject::POINTER}, $object->{SourceObject::POINTER});
        $this->assertEquals($this->data->{SourceObject::PARAMETER}, $object->{SourceObject::PARAMETER});

        return $object;
    }

    /**
     * @depends testConstruct
     */
    public function testJsonSerialize(SourceObject $object)
    {
        $this->assertEquals(json_encode($this->data), json_encode($object));
    }

    /**
     * @depends testConstruct
     */
    public function testToArray(SourceObject $object)
    {
        $this->assertEquals($this->arr, $object->toArray());
    }

    /**
     * @depends testConstruct
     */
    public function testAddCustom(SourceObject $object)
    {
        $key = 'foo';
        $value = 'bar';

        $object->{$key} = $value;

        $this->assertEquals($value, $object->{$key});
    }

    public function testSetPointer()
    {
        $object = new SourceObject();

        $this->assertNull($object->getPointer());
        $this->assertSame($object, $object->setPointer(static::POINTER));
        $this->assertSame(static::POINTER, $object->{SourceObject::POINTER});
        $this->assertSame(static::POINTER, $object->getPointer());

        return $object;
    }

    /**
     * @depends testSetPointer
     */
    public function testSetPointerWithCallback(SourceObject $object)
    {
        $prefix = '/prefix';
        $expected = sprintf('%s%s', $prefix, static::POINTER);

        $object->setPointer(function ($current) use ($prefix) {
            return sprintf('%s%s', $prefix, $current);
        });

        $this->assertSame($expected, $object->getPointer());
    }

    public function testSetParameter()
    {
        $object = new SourceObject();

        $this->assertNull($object->getParameter());
        $this->assertSame($object, $object->setParameter(static::PARAMETER));
        $this->assertSame(static::PARAMETER, $object->{SourceObject::PARAMETER});
        $this->assertSame(static::PARAMETER, $object->getParameter());
    }
}
