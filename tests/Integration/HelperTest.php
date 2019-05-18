<?php namespace GeneaLabs\LaravelModelCaching\Tests\Integration;

use GeneaLabs\LaravelModelCaching\Tests\Fixtures\Author;
use GeneaLabs\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use GeneaLabs\LaravelModelCaching\Tests\IntegrationTestCase;

class HelperTest extends IntegrationTestCase
{
    public function testClosureRunsWithCacheDisabled()
    {
        $key = sha1('genealabs:laravel-model-caching:testing::memory::authors:genealabslaravelmodelcachingtestsfixturesauthor');
        $tags = ['genealabs:laravel-model-caching:testing::memory::genealabslaravelmodelcachingtestsfixturesauthor'];

        $authors = app("model-cache")->runDisabled(function () {
            return (new Author)
                ->get();
        });

        $cachedResults1 = $this->cache()
            ->tags($tags)
            ->get($key)["value"];
        (new Author)
            ->get();
        $cachedResults2 = $this->cache()
            ->tags($tags)
            ->get($key)["value"];
        $liveResults = (new UncachedAuthor)
            ->get();

        $this->assertEquals($liveResults->toArray(), $authors->toArray());
        $this->assertNull($cachedResults1);
        $this->assertEquals($authors->toArray(), $cachedResults2->toArray());
    }
}
