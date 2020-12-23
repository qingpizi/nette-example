<?php
declare(strict_types=1);

/**
 * 数组操作
 * User: hjs
 * Date: 2020/12/23
 * Time: 下午2:17
 */

use Nette\Utils\ArrayHash;
use PHPUnit\Framework\TestCase;
use Nette\Utils\Arrays;
use Nette\InvalidArgumentException;

class ArraysTest extends TestCase
{
    public function testEvery()
    {
        $array = [1, 30, 39, 29, 10, 13];
        $isBelowThreshold = function ($value): bool { return $value < 40; };
        $result = Arrays::every($array, $isBelowThreshold); // true
        $this->assertTrue($result);
    }

    public function testFlatten()
    {
        $array = Arrays::flatten([1, 2, [3, 4, [5, 6]]]);
        $this->assertEquals($array, [1, 2, 3, 4, 5, 6]);
    }

    public function testGet()
    {
        $value = Arrays::get([], 'foo', 'default');
        $this->assertEquals($value, 'default');

        $this->expectException(InvalidArgumentException::class);
        Arrays::get([], 'foo');
    }

    public function testGetRef()
    {
        $array = ['a' => 1];
        $valueRef = & Arrays::getRef($array, 'a');
        $this->assertEquals($valueRef, 1);
        $array = ['a' => ['b' => 1], 'c' => 2];
        $valueRef = Arrays::getRef($array, ['a', 'b']);
        $this->assertEquals($valueRef, 1);
    }

    public function testGrep()
    {
        $array = ['a' => 111, 1 => 'b', 'c' => '111b'];
        $filteredArray = Arrays::grep($array, '~^\d+$~');
        $this->assertEquals($filteredArray, ['a' => 111]);
    }

    public function testInsertAfter()
    {
        $array = ['first' => 10, 'second' => 20];
        Arrays::insertAfter($array, 'second', ['hello' => 'world']);
        $this->assertEquals($array, ['first' => 10, 'hello' => 'world', 'second' => 20]);
    }

    public function testInsertBefore()
    {
        $array = ['first' => 10, 'second' => 20];
        Arrays::insertBefore($array, 'first', ['hello' => 'world']);
        $this->assertEquals($array, ['hello' => 'world', 'first' => 10, 'second' => 20]);
    }

    public function testIsList()
    {
        $result = Arrays::isList(['a', 'b', 'c']); // true
        $this->assertTrue($result);
        $result = Arrays::isList([4 => 1, 2, 3]); // false
        $this->assertFalse($result);
        $result = Arrays::isList(['a' => 1, 'b' => 2]); // false
        $this->assertFalse($result);
    }

    public function testMap()
    {
        $array = ['foo', 'bar', 'baz'];
        $result = Arrays::map($array, function ($value): string { return $value . $value; });
        $this->assertEquals($result, ['foofoo', 'barbar', 'bazbaz']);
    }

    /**
     * 递归地合并两个字段。例如，它对于合并树结构非常有用。它的行为类似于数组的+运算符，即它将第二个数组中的键/值对添加到第一个数组中，并在发生键冲突时保留第一个数组中的值
     */
    public function testMergeTree()
    {
        $array1 = ['color' => ['favorite' => 'red'], 5];
        $array2 = [10, 'color' => ['favorite' => 'green', 'blue'], 11];

        $array = Arrays::mergeTree($array1, $array2);
        $this->assertEquals($array, ['color' => ['favorite' => 'red', 'blue'], 5, 11]);
    }

    /**
     * 将数组规格化为关联数组。将数字键替换为它们的值，新值将是$filling
     */
    public function testNormalize()
    {
        $array = Arrays::normalize([1 => 'first', 'a' => 'second']);
        $this->assertEquals($array, ['first' => null, 'a' => 'second']);
        $array = Arrays::normalize([1 => 'first', 'a' => 'second'], 'foobar');
        $this->assertEquals($array, ['first' => 'foobar', 'a' => 'second']);
    }

    /**
     * 返回并从数组中删除项的值。如果不存在，则抛出异常，或返回$default（如果提供）。
     */
    public function testPick()
    {
        $array = [1 => 'foo', null => 'bar'];
        $result = Arrays::pick($array, null);
        $this->assertEquals($result, 'bar');
        $result = Arrays::pick($array, 'not-exists', 'foobar');
        $this->assertEquals($result, 'foobar');

        $this->expectException(InvalidArgumentException::class);
        Arrays::pick($array, 'not-exists');
    }

    /**
     * 重命名数组key。如果在数组中找到键，则返回true
     */
    public function testRenameKey()
    {
        $array = ['first' => 10, 'second' => 20];
        $result = Arrays::renameKey($array, 'first', 'renamed');
        $this->assertTrue($result);
        $this->assertEquals($array, ['renamed' => 10, 'second' => 20]);
    }

    /**
     * 返回给定数组键的零索引位置。如果找不到键，则返回null
     */
    public function testSearchKey()
    {
        $array = ['first' => 10, 'second' => 20];
        $position = Arrays::getKeyOffset($array, 'first');
        $this->assertEquals($position, 0);
        $position = Arrays::getKeyOffset($array, 'second');
        $this->assertEquals($position, 1);
        $position = Arrays::getKeyOffset($array, 'not-exists');
        $this->assertNull($position);

    }

    /**
     * 测试数组中是否至少有一个元素通过由提供的回调方法实现
     */
    public function testSome()
    {
        $array = [1, 2, 3, 4];
        $isEven = function ($value): bool { return $value % 2 === 0; };
        $result = Arrays::some($array, $isEven);
        $this->assertTrue($result);
    }

    /**
     * 将值转换为数组键，数组键可以是整数或字符串。
     */
    public function testToKey()
    {
        $result = Arrays::toKey('1');
        $this->assertEquals($result, 1);
        $result = Arrays::toKey('01');
        $this->assertEquals($result, '01');
    }

    public function testToObject()
    {
        $obj = new \stdClass();
        $array = ['foo' => 1, 'bar' => 2];
        Arrays::toObject($array, $obj);
        $this->assertEquals($obj->foo, 1);
        $this->assertEquals($obj->bar, 2);
    }

    /**
     * Object Nette\Utils\ArrayHash是匿名stdClass的后代，并将其扩展到可以将其视为数组的能力，例如，使用方括号访问成员：
     */
    public function testArrayHash()
    {
        $hash = new ArrayHash();
        $hash['foo'] = 123;
        $hash->bar = 456; // also works object notation
        $hash->foo; // 123
        $this->assertEquals($hash->foo, 123);
        $this->assertEquals($hash->bar, 456);
        $this->assertEquals($hash->count(), 2);
        $this->assertTrue($hash->offsetExists('foo'));
        $hash->offsetSet('foo', 9);
        $this->assertEquals($hash->offsetGet('foo'), 9);
        $hash->offsetUnset('foo');
        $this->assertFalse($hash->offsetExists('foo'));
    }

    /**
     * 可以使用from()将现有数组转换为ArrayHash对象
     */
    public function testArrayToArrayHashObject()
    {
        $array = ['foo' => 123, 'bar' => 456, 'inner' => ['a' => 'b']];
        $hash = ArrayHash::from($array);
        $this->assertEquals($hash->foo, 123);
        $this->assertEquals($hash->bar, 456);
        $this->assertIsObject($hash->inner);
        $this->assertEquals($hash->inner->a, 'b');
        $this->assertEquals($hash['inner']['a'], 'b');
    }

    /**
     * 可以使用from()将现有数组转换为ArrayHash数组
     */
    public function testArrayToArrayHashArray()
    {
        $array = ['foo' => 123, 'bar' => 456, 'inner' => ['a' => 'b']];
        $hash = ArrayHash::from($array, false);
        $this->assertIsObject($hash);
        $this->assertIsArray($hash->inner);
        $this->assertEquals($hash['inner']['a'], 'b');
        $this->assertIsArray((array) $hash);
    }
}