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
 * A secure by default password hasher.
 */
class PasswordHashing implements PasswordHashingInterface
{
    /**
     * Construct a new password hasher.
     *
     * @param int   $passwordAlgo    The password hasher algo.
     * @param array $passwordOptions The password hasher options.
     *
     * @return void Returns nothing.
     */
    public function __construct(public int $passwordAlgo, public array $passwordOptions)
    {
        //
    }

    /**
     * Compute a new hash text.
     *
     * @param string $password The password to hash.
     *
     * @return string Returns the hashed text.
     */
    public function compute(string $password): string
    {
        return password_hash($password, $this->passwordAlgo, $this->passwordOptions);
    }
}
