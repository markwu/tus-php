<?php

namespace TusPhp\Test;

use TusPhp\Request;
use PHPUnit\Framework\TestCase;
use Illuminate\Http\Request as HttpRequest;

/**
 * @coversDefaultClass \TusPhp\Request
 */
class RequestTest extends TestCase
{
    /** @var Request */
    protected $request;

    /**
     * Prepare vars.
     *
     * @return void
     */
    public function setUp()
    {
        $this->request = new Request;

        parent::setUp();
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::method
     */
    public function it_returns_current_request_method()
    {
        $this->assertEquals('GET', $this->request->method());

        $request = new Request;

        $request->getRequest()->server->set('REQUEST_METHOD', 'POST');

        $this->assertEquals('POST', $request->method());
    }

    /**
     * @test
     *
     * @covers ::checksum
     */
    public function it_should_return_checksum_from_request_url()
    {
        $checksum = '74f02d6da32082463e382f2274e85fd8eae3e81f739f8959abc91865656e3b3a';

        $this->request->getRequest()->server->set('REQUEST_URI', '/files/' . $checksum);

        $this->assertEquals($checksum, $this->request->checksum());
    }

    /**
     * @test
     *
     * @covers ::allowedHttpVerbs
     */
    public function it_returns_allowed_http_verbs()
    {
        $this->assertEquals([
            'GET',
            'POST',
            'PATCH',
            'DELETE',
            'HEAD',
            'OPTIONS',
        ], $this->request->allowedHttpVerbs());
    }

    /**
     * @test
     *
     * @covers ::header
     */
    public function it_extracts_header()
    {
        $this->request->getRequest()->headers->set('content-length', 100);

        $this->assertEquals(100, $this->request->header('content-length'));
    }

    /**
     * @test
     *
     * @covers ::url
     */
    public function it_gets_root_url()
    {
        $this->request->getRequest()->server->add([
            'SERVER_NAME' => 'tus.local',
            'SERVER_PORT' => 80,
        ]);

        $this->assertEquals('http://tus.local', $this->request->url());
    }

    /**
     * @test
     *
     * @covers ::extractFileName
     */
    public function it_return_null_if_it_cannot_extract_filename()
    {
        $this->assertNull($this->request->extractFileName());
    }

    /**
     * @test
     *
     * @covers ::extractFileName
     */
    public function it_extracts_file_name()
    {
        $filename = 'file.txt';

        $this->request->getRequest()->headers->set('Upload-Metadata', 'filename ' . base64_encode($filename));

        $this->assertEquals($filename, $this->request->extractFileName());
    }

    /**
     * @test
     *
     * @covers ::getRequest
     */
    public function it_gets_request()
    {
        $this->assertInstanceOf(HttpRequest::class, $this->request->getRequest());
    }
}
