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
 * A secure by default session handler.
 */
class Session
{
    /**
     * Construct a new session handler.
     *
     * @param string $name      The name of the session.
     * @param bool   $autostart Should we autostart the session.
     *
     * @return void Returns nothing.
     */
    public function __construct(string $name, public string $code, bool $autostart = true)
    {
        session_name($name);
        if ($autostart) {
            $this->start();
        }
    }

    /**
     * Start a new session.
     *
     * @return bool Returns true if the session has started and false if not.
     */
    public function start(): bool
    {
        if ($this->exists()) {
            return true;
        }
        $session = session_start();
        $fingerprint = hash_hmac('sha256', sprintf('%s|%s', $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_USER_AGENT']), $this->code);
        if (is_null($this->get('fingerprint'))) {
            $this->put('fingerprint', $fingerprint);
        } elseif (hash_equals($this->get('fingerprint', ''), $fingerprint)) {
            return $session;
        }
        $this->destroy();
        return false;
    }

    /**
     * Destory the currently active session.
     *
     * @return bool Returns true if the was destroyed and false if not.
     */
    public function destroy(): bool
    {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        return session_destroy();
    }

    /**
     * Destory the currently active session.
     *
     * @return bool Returns true if the was destroyed and false if not.
     */
    public function exists(): bool
    {
        if (php_sapi_name() !== 'cli') {
            return session_status() === PHP_SESSION_ACTIVE ? true : false;
        }
        return false;
    }

    /**
     * Check to see if this session key is in the session stoarge.
     *
     * @param string $name The session key name to lookup.
     *
     * @return bool Returns true if the key exists and false if not.
     */
    public function has(string $name): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Flash a session key value.
     *
     * @param string $name The session key name to flash.
     *
     * @return mixed Returns the session key value.
     */
    public function flash(string $name, mixed $default = null): mixed
    {
        $value = $this->get($name, $default);
        unset($_SESSION[$key]);
        return $value;
    }

    /**
     * Get the session key value.
     *
     * @param string $name    The session key name to get.
     * @param mixed  $default The default value to return if the key was not found.
     *
     * @return mixed Returns the session key value.
     */
    public function get(string $name, mixed $default = null): mixed
    {
        return $this->has($name) ? $_SESSION[$name] : $default;
    }

    /**
     * Put a new session key with a value in the session stoarge.
     *
     * @return void Returns nothing.
     */
    public function put(string $name, mixed $value = null): void
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Bump a session key from the session stoarge.
     *
     * @param string $name The session key name to bump.
     *
     * @return void Returns nothing.
     */
    public function delete(string $name): void
    {
        unset($_SESSION[$name]);
    }
}
