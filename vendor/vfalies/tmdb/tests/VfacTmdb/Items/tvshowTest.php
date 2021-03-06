<?php

namespace VfacTmdb\Items;

use PHPUnit\Framework\TestCase;
use VfacTmdb\lib\Guzzle\Client as HttpClient;

class TVShowTest extends TestCase
{
    protected $tmdb  = null;
    protected $tv_id = 253;

    public function setUp()
    {
        parent::setUp();

        $this->tmdb = $this->getMockBuilder(\VfacTmdb\Tmdb::class)
                ->setConstructorArgs(array('fake_api_key', 3, new \Monolog\Logger('Tmdb', [new \Monolog\Handler\StreamHandler('logs/unittest.log')]), new HttpClient(new \GuzzleHttp\Client())))
                ->setMethods(['sendRequest', 'getConfiguration'])
                ->getMock();
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->tmdb = null;
    }

    private function setRequestOk()
    {
        $json_object = json_decode(file_get_contents('tests/json/configurationOk.json'));
        $this->tmdb->method('getConfiguration')->willReturn($json_object);

        $json_object = json_decode(file_get_contents('tests/json/TVShowOk.json'));
        $this->tmdb->method('sendRequest')->willReturn($json_object);
    }

    private function setRequestTVShowEmpty()
    {
        $json_object = json_decode(file_get_contents('tests/json/configurationOk.json'));
        $this->tmdb->method('getConfiguration')->willReturn($json_object);

        $json_object = json_decode(file_get_contents('tests/json/TVShowEmptyOk.json'));
        $this->tmdb->method('sendRequest')->willReturn($json_object);
    }

    private function setRequestConfigurationEmpty()
    {
        $json_object = json_decode(file_get_contents('tests/json/configurationEmptyOk.json'));
        $this->tmdb->method('getConfiguration')->willReturn($json_object);

        $json_object = json_decode(file_get_contents('tests/json/TVShowOk.json'));
        $this->tmdb->method('sendRequest')->willReturn($json_object);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function testContructFailure()
    {
        $this->tmdb->method('sendRequest')->will($this->throwException(new \Exception()));

        new TVShow($this->tmdb, $this->tv_id);
    }

    /**
     * @test
     */
    public function testGetPosterPath()
    {
        $this->setRequestOk();

        $tvshow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertInternalType('string', $tvshow->getPosterPath());
        $this->assertNotEmpty($tvshow->getPosterPath());
    }

    /**
     * @test
     */
    public function testGetPosterPathFailure()
    {
        $this->setRequestTVShowEmpty();

        $tvshow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertInternalType('string', $tvshow->getPosterPath());
        $this->assertEmpty($tvshow->getPosterPath());
    }

    /**
     * @test
     */
    public function testGetBackdropPath()
    {
        $this->setRequestOk();

        $tvshow = new TVShow($this->tmdb, $this->tv_id);
        $this->assertInternalType('string', $tvshow->getBackdropPath());
        $this->assertNotEmpty($tvshow->getBackdropPath());
    }

    /**
     * @test
     */
    public function testGetBackdropPathFailure()
    {
        $this->setRequestTVShowEmpty();
        $tvshow = new TVShow($this->tmdb, $this->tv_id);
        $this->assertInternalType('string', $tvshow->getBackdropPath());
        $this->assertEmpty($tvshow->getBackdropPath());
    }

    /**
     * @test
     */
    public function testGetGenres()
    {
        $this->setRequestOk();
        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $genres = $TVShow->getGenres();
        $this->assertInternalType('array', $genres);
    }

    /**
     * @test
     */
    public function testGetGenresEmpty()
    {
        $this->setRequestTVShowEmpty();
        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $genres = $TVShow->getGenres();
        $this->assertInternalType('array', $genres);
        $this->assertEmpty($genres);
    }

    /**
     * @test
     */
    public function testGetNote()
    {
        $this->setRequestOk();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertInternalType('double', $TVShow->getNote());
        $this->assertEquals('7.9', $TVShow->getNote());
    }

    /**
     * @test
     */
    public function testGetNoteEmpty()
    {
        $this->setRequestTVShowEmpty();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertEquals(0, $TVShow->getNote());
    }

    /**
     * @test
     */
    public function testGetNumberEpisodes()
    {
        $this->setRequestOk();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertEquals(79, $TVShow->getNumberEpisodes());
    }

    /**
     * @test
     */
    public function testGetNumberEpisodesEmpty()
    {
        $this->setRequestTVShowEmpty();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertEquals(0, $TVShow->getNumberEpisodes());
    }

    /**
     * @test
     */
    public function testGetNumberSeasons()
    {
        $this->setRequestOk();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertEquals(3, $TVShow->getNumberSeasons());
    }

    /**
     * @test
     */
    public function testGetNumberSeasonsEmpty()
    {
        $this->setRequestTVShowEmpty();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertEquals(0, $TVShow->getNumberSeasons());
    }

    /**
     * @test
     */
    public function testGetOriginalTitle()
    {
        $this->setRequestOk();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertEquals('Star Trek', $TVShow->getOriginalTitle());
    }

    /**
     * @test
     */
    public function testOriginalTitleFailure()
    {
        $this->setRequestTVShowEmpty();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertEmpty($TVShow->getOriginalTitle());
    }

    /**
     * @test
     */
    public function testGetOverview()
    {
        $this->setRequestOk();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertInternalType('string', $TVShow->getOverview());
        $this->assertStringStartsWith('Star Trek, ou Patrouille du cosmos au Qu??bec et au Nouveau-Brunswick', $TVShow->getOverview());
    }

    /**
     * @test
     */
    public function testGetOverviewFailure()
    {
        $this->setRequestTVShowEmpty();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);
        $this->assertEmpty($TVShow->getOverview());
    }

    /**
     * @test
     */
    public function testGetReleaseDate()
    {
        $this->setRequestOk();

        $tvshow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertEquals('1966-09-08', $tvshow->getReleaseDate());
    }

    /**
     * @test
     */
    public function testGetReleaseDateFailure()
    {
        $this->setRequestTVShowEmpty();

        $tvshow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertEmpty($tvshow->getReleaseDate());
    }

    /**
     * @test
     */
    public function testGetStatus()
    {
        $this->setRequestOk();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertEquals('Ended', $TVShow->getStatus());
    }

    /**
     * @test
     */
    public function testGetStatusEmpty()
    {
        $this->setRequestTVShowEmpty();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertEquals('', $TVShow->getStatus());
    }

    /**
     * @test
     */
    public function testGetTitle()
    {
        $this->setRequestOk();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertEquals('/3/tv/'.$this->tv_id, parse_url($this->tmdb->url, PHP_URL_PATH));
        $this->assertEquals('Star Trek', $TVShow->getTitle());
    }

    /**
     * @test
     */
    public function testGetTitleEmpty()
    {
        $this->setRequestTVShowEmpty();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $this->assertEquals('', $TVShow->getTitle());
    }

    /**
     * @test
     */
    public function testGetSeasons()
    {
        $this->setRequestOk();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $seasons = $TVShow->getSeasons();
        $this->assertInstanceOf(\Generator::class, $seasons);
        foreach ($seasons as $season) {
            $this->assertInstanceOf(\VfacTmdb\Results\TVSeason::class, $season);
        }
    }

    /**
     * test
     */
    public function testGetSeasonsEmpty()
    {
        $this->setRequestTVShowEmpty();

        $TVShow  = new TVShow($this->tmdb, $this->tv_id);
        $seasons = $TVShow->getSeasons();

        $this->assertInstanceOf(\Generator::class, $seasons);
        $this->assertNull($seasons->current());
    }

    public function testGetBackdrops()
    {
        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $json_object = json_decode(file_get_contents('tests/json/imagesOk.json'));
        $this->tmdb->method('sendRequest')->willReturn($json_object);

        $backdrops = $TVShow->getBackdrops();

        $this->assertInstanceOf(\Generator::class, $backdrops);

        foreach ($backdrops as $b) {
            $this->assertEquals('/3/tv/'.$this->tv_id.'/images', parse_url($this->tmdb->url, PHP_URL_PATH));
            $this->assertInstanceOf(\VfacTmdb\Results\Image::class, $b);
        }
    }

    public function testGetPosters()
    {
        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $json_object = json_decode(file_get_contents('tests/json/imagesOk.json'));
        $this->tmdb->method('sendRequest')->willReturn($json_object);

        $posters = $TVShow->getPosters();

        $this->assertInstanceOf(\Generator::class, $posters);

        foreach ($posters as $p) {
            $this->assertEquals('/3/tv/'.$this->tv_id.'/images', parse_url($this->tmdb->url, PHP_URL_PATH));
            $this->assertInstanceOf(\VfacTmdb\Results\Image::class, $p);
        }
    }

    public function testGetNetworks()
    {
        $this->setRequestOk();

        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $networks = $TVShow->getNetworks();
        $this->assertInstanceOf(\Generator::class, $networks);

        foreach ($networks as $n) {
            $this->assertObjectHasAttribute('id', $n);
            $this->assertObjectHasAttribute('name', $n);
        }
    }

    public function testGetSimilarTVShow()
    {
        $TVShow = new TVShow($this->tmdb, $this->tv_id);

        $json_object = json_decode(file_get_contents('tests/json/TVShowSimilarOK.json'));
        $this->tmdb->method('sendRequest')->willReturn($json_object);

        $similar = $TVShow->getSimilar();

        $this->assertInstanceOf(\Generator::class, $similar);

        foreach ($similar as $s) {
            $this->assertEquals('/3/tv/'.$this->tv_id.'/similar', parse_url($this->tmdb->url, PHP_URL_PATH));
            $this->assertInstanceOf(\VfacTmdb\Results\TVShow::class, $s);
        }
    }
}
