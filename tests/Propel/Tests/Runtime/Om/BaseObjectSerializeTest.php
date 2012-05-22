<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 */

namespace Propel\Tests\Runtime\Om;

use Propel\Tests\Helpers\Bookstore\BookstoreTestBase;
use Propel\Tests\Bookstore\Author;
use Propel\Tests\Bookstore\Book;
use Propel\Tests\Bookstore\BookPeer;
use Propel\Tests\Bookstore\BookQuery;

/**
 * Test class for BaseObject serialization.
 *
 * @author Francois Zaninotto
 * @version    $Id: CollectionTest.php 1348 2009-12-03 21:49:00Z francois $
 */
class BaseObjectSerializeTest extends BookstoreTestBase
{
    public function testSerializeEmptyObject()
    {
        $book = new Book();
        $sb = serialize($book);
        $this->assertEquals($book, unserialize($sb));
    }

    public function testSerializePopulatedObject()
    {
        $book = new Book();
        $book->setTitle('Foo1');
        $book->setISBN('1234');
        $sb = serialize($book);
        $this->assertEquals($book, unserialize($sb));
    }

    public function testSerializePersistedObject()
    {
        $book = new Book();
        $book->setTitle('Foo2');
        $book->setISBN('1234');
        $book->save();
        $sb = serialize($book);
        $this->assertEquals($book, unserialize($sb));
    }

    public function testSerializeHydratedObject()
    {
        $book = new Book();
        $book->setTitle('Foo3');
        $book->setISBN('1234');
        $book->save();
        BookPeer::clearInstancePool();

        $book = BookQuery::create()->findOneByTitle('Foo3');
        $sb = serialize($book);
        $this->assertEquals($book, unserialize($sb));
    }

    public function testSerializeObjectWithRelations()
    {
        $author = new Author();
        $author->setFirstName('John');
        $book = new Book();
        $book->setTitle('Foo4');
        $book->setISBN('1234');
        $book->setAuthor($author);
        $book->save();
        $b = clone $book;
        $sb = serialize($b);
        $book->clearAllReferences();
        $this->assertEquals($book, unserialize($sb));
    }

    public function testSerializeObjectWithCollections()
    {
        $book1 = new Book();
        $book1->setTitle('Foo5');
        $book1->setISBN('1234');
        $book2 = new Book();
        $book2->setTitle('Foo6');
        $book2->setISBN('1234');
        $author = new Author();
        $author->setFirstName('JAne');
        $author->addBook($book1);
        $author->addBook($book2);
        $author->save();
        $a = clone $author;
        $sa = serialize($a);
        $author->clearAllReferences();
        $this->assertEquals($author, unserialize($sa));
    }
}
