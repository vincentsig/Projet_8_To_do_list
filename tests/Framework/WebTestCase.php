<?php

namespace App\Tests\Framework;

use Throwable;
use App\Entity\User;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    protected $client;
    protected $em;
    protected $crawler;
    protected $response;
    protected $responseContent;


    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();


        $this->em  = $this->getDoctrine()->getManager();

        static $metadata = null;

        if (!$metadata) {
            $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        }

        $schemaTool = new SchemaTool($this->em);
        $schemaTool->dropDatabase();

        if (!empty($metadata)) {
            $schemaTool->createSchema($metadata);
        }
    }

    protected function getAdminLogin()
    {
        $this->client->loginUser($this->createUsers());
    }

    protected function createUsers($overrides = []): User
    {
        $data = array_merge([
            'username' => 'username_test',
            'roles' => ['ROLE_ADMIN'],
            'password' => '12345',
            'email' => 'test@gmail.com',

        ], $overrides);

        $user = (new User($data))
            ->setUsername($data['username'])
            ->setRoles($data['roles'])
            ->setpassword($data['password'])
            ->setEmail($data['email']);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }


    /**
     * visit
     * Execute the request corresponding to the url($url)
     * and stock the response and the responseContent in protected
     * attributes $response and $responseContent.
     * @param  mixed $url
     * @return self
     */
    protected function visit($url): self
    {
        $this->crawler = $this->client->request('GET', $url);
        $this->response  = $this->client->getResponse();
        $this->responseContent  = $this->response->getContent();

        return $this;
    }

    protected function seeStatusCode(int $expectedStatusCode): self
    {
        $this->assertResponseStatusCodeSame($expectedStatusCode);

        return $this;
    }

    protected function assertResponseOK(): self
    {
        return $this->seeStatusCode(200);
    }

    /**
     * check if the text is in the responseContent
     * @param string $text
     * @return WebTestCase
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    protected function seeText(string $text): self
    {
        $this->assertStringContainsString($text, $this->responseContent);

        return $this;
    }

    protected function dontSeeText(string $text): self
    {
        if (!empty($text)) {
            $this->assertStringNotContainsString($text, $this->responseContent);
        }

        return $this;
    }

    protected function ClickLink(string $text): self
    {
        $link = $this->crawler->filter($text);

        if ($link->count() === 0) {
            $link = $this->crawler->selectLink($text);

            if ($link->count() === 0) {
                throw new \InvalidArgumentException("Couldn't find a link with the body/id/class '{$text}'.");
            }
        }

        return $this->visit($link->link()->getUri());
    }

    protected function seePageIs(string $url)
    {
        $url = str_replace('/', '\/', $url);

        $this->assertMatchesRegularExpression(sprintf('/%s/', $url), $this->client->getRequest()->getUri());

        return $this;
    }

    protected function assertElementTextContains($text, $element)
    {
        $this->assertStringContainsString($text, $element->text());

        return $this;
    }

    /**
     * This method is called when a test method did not execute successfully,
     * and return an explicite exception message.
     *
     * @throws Throwable
     */
    protected function onNotSuccessfulTest(Throwable $t): void
    {
        if ($this->crawler && $this->crawler->filter('h1.exception-message')->count() > 0) {
            $throwableClass = get_class($t);

            $explicitExceptionMessage = $this->crawler->filter('h1.exception-message')->eq(0)->text();

            throw new  $throwableClass(
                $t->getMessage() . ' | ' . $this->response->getStatusCode() . ' | ' . $explicitExceptionMessage
            );
        }

        throw $t;
    }

    protected function dump(): void
    {
        $content = $this->responseContent;

        $json = json_decode($content);

        if (json_last_error() == JSON_ERROR_NONE) {
            $content = $json;
        }
        dd($content);
    }


    protected function getDoctrine()
    {
        return static::$container->get('doctrine');
    }

    protected function getParameter($name)
    {
        return static::$container->getParameter($name);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }
}
