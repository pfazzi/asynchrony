<?php
declare(strict_types=1);

namespace Asynchrony;

final class Response
{
    /**
     * @var callable
     */
    private $resolve;

    private function __construct(callable $resolve)
    {
        $this->resolve = $resolve;
    }

    public static function create(callable $resolve): self
    {
        return new self($resolve);
    }

    public function send($content): void
    {
        $response = new \React\Http\Response(
            200,
            array(
                'Content-Type' => 'text/plain'
            ),
            $content
        );

        call_user_func($this->resolve, $response);
    }
}
