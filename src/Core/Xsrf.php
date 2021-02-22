<?php declare(strict_types=1);
/**
 * GNU Affero General Public License v3.0
 * 
 * Copyright (c) 2021 Four Way Chess
 * 
 * Permissions of this strongest copyleft license are conditioned on making available complete source code
 * of licensed works and modifications, which include larger works using a licensed work, under the same license.
 * Copyright and license notices must be preserved. Contributors provide an express grant of patent rights.
 * When a modified version is used to provide a service over a network, the complete source code of the
 * modified version must be made available.
 */

namespace FourWayChess\Core;

/**
 * A secure by default xsrf handler.
 */
class Xsrf implements XsrfInterface
{
    /**
     * Construct a new session handler.
     *
     * @param \FourWayChess\Core\SessionInterface $session The secure session handler.
     * @param string                              $name    The name of the token used for HTML output.
     *
     * @return void Returns nothing.
     */
    public function __construct(public SessionInterface $session, public string $name)
    {
        //
    }

    /**
     * Generate a token and generate an html input field for it.
     *
     * @return string Return the html input field for the xsrf token.
     */
    public function output(): string
    {
        $token = $this->preXsrfLoad();
        return "<input type=\"hidden\" name=\"{$this->name}\" value=\"$token\" />";
    }

    /**
     * Verify that the xsrf token is valid.
     *
     * @return bool Returns true if the xsrf token is valid and returns false if not.
     */
    public function validXsrf(array $request): bool
    {
        return !isset($request[$this->name]) || !hash_equals($request[$this->name], $this->session->get('xsrf', ''));
    }

    /**
     * Generate and save the xsrf token
     *
     * @return string Returns the saved token.
     */
    public function preXsrfLoad(): string
    {
        $token = $this->generate();
        $this->session->put('xsrf', $token);
        return $token;
    }

    /**
     * Generate a secure xsrf token.
     *
     * @return string Returns the xsrf token.
     */
    private function generate(): string
    {
        return base64_encode(openssl_random_pseudo_bytes(32));
    }
}
