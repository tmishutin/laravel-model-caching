<?php namespace GeneaLabs\LaravelModelCaching\Tests\Integration;

use GeneaLabs\LaravelModelCaching\Tests\Fixtures\Book;
use GeneaLabs\LaravelModelCaching\Tests\Fixtures\UncachedBook;
use GeneaLabs\LaravelModelCaching\Tests\IntegrationTestCase;

class CachedBuilderRelationshipsTest extends IntegrationTestCase
{
    public function testHasRelationshipResults()
    {
        $booksWithStores = (new Book)
            ->with("stores")
            ->has("stores")
            ->get();
        $key = "genealabs:laravel-model-caching:testing::memory::books:genealabslaravelmodelcachingtestsfixturesbook-exists-and_books.id_=_book_store.book_id-testing::memory::stores";
        $tags = [
            "genealabs:laravel-model-caching:testing::memory::genealabslaravelmodelcachingtestsfixturesbook",
            "genealabs:laravel-model-caching:testing::memory::genealabslaravelmodelcachingtestsfixturesstore",
        ];
        $cachedResults = $this
            ->cache()
            ->tags($tags)
            ->get(sha1($key))["value"];

        $this->assertNotEmpty($booksWithStores);
        $this->assertEquals($booksWithStores, $cachedResults);
    }

    public function testWhereHasRelationship()
    {
        $books = (new Book)
            ->with("stores")
            ->whereHas("stores", function ($query) {
                $query->whereRaw('address like ?', ['%s%']);
            })
            ->get();

        $uncachedBooks = (new UncachedBook)
            ->with("stores")
            ->whereHas("stores", function ($query) {
                $query->whereRaw('address like ?', ['%s%']);
            })
            ->get();

        $this->assertEquals($books->pluck("id"), $uncachedBooks->pluck("id"));
    }
}
