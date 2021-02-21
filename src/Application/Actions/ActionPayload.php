<?php declare(strict_types=1);
/**
 * MIT License
 * 
 * Copyright (c) 2021 Four Way Chess
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Application\Actions;

use JsonSerializable;

class ActionPayload implements JsonSerializable
{
    /** @var int $statusCode The HTTP status code. */
    private $statusCode;

    /** @var array|object|null $data The serializable data. */
    private $data;

    /** @var ActionError|null $errors Any errors. */
    private $error;

    /**
     * Construct a serializable object.
     *
     * @param int               $statusCode The HTTP status code.
     * @param array|object|null $data       The serializable data.
     * @param ActionError|null  $error      Any errors.
     *
     * @return void Returns nothing.
     */
    public function __construct(
        int $statusCode = 200,
        $data = null,
        ?ActionError $error = null
    ) {
        $this->statusCode = $statusCode;
        $this->data = $data;
        $this->error = $error;
    }

    /**
     * Get the HTTP status code.
     *
     * @return int The HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the serializable data.
     *
     * @return array|null|object The serializable data.
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get any errors.
     *
     * @return ActionError|null Any errors.
     */
    public function getError(): ?ActionError
    {
        return $this->error;
    }

    /**
     * Serialize the data.
     *
     * @return array Returns the serialized data.
     */
    public function jsonSerialize()
    {
        $payload = [
            'statusCode' => $this->statusCode,
        ];
        if ($this->data !== null) {
            $payload['data'] = $this->data;
        } elseif ($this->error !== null) {
            $payload['error'] = $this->error;
        }
        return $payload;
    }
}
