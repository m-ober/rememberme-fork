<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace mober\Rememberme;

/**
 * Domain object for credential, persistent and transient token
 */
class Triplet implements \Stringable
{
    public const SEPARATOR = '|';

    /**
     * @param mixed $credential
     * @param string $oneTimeToken
     * @param string $persistentToken
     */
    public function __construct(
        private mixed $credential = '',
        private string $oneTimeToken = '',
        private string $persistentToken = '',
    ) {
    }

    /**
     * @param string $tripletString
     * @return Triplet
     */
    public static function fromString(string $tripletString): Triplet
    {
        $parts = explode(self::SEPARATOR, $tripletString);

        // If the credential contains the separator, this check might be pass
        // even though parts are missing.

        if (count($parts) < 3) {
            return new Triplet();
        }

        // Assume that tokens generated by us do NOT contain the separator:
        // Take parts from right to left, put credential back together if
        // it was split because it does contain the separator.

        $persistentToken = array_pop($parts);
        $oneTimeToken = array_pop($parts);
        $credential = implode(self::SEPARATOR, $parts);

        return new Triplet(credential: $credential, oneTimeToken: $oneTimeToken, persistentToken: $persistentToken);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->getCredential() !== '' && $this->getPersistentToken() !== '' && $this->getOneTimeToken() !== '';
    }

    /**
     * @return mixed
     */
    public function getCredential(): mixed
    {
        return $this->credential;
    }

    /**
     * @return string
     */
    public function getPersistentToken(): string
    {
        return $this->persistentToken;
    }

    /**
     * @return string
     */
    public function getOneTimeToken(): string
    {
        return $this->oneTimeToken;
    }

    /**
     * @param string $salt
     * @return string
     */
    public function getSaltedPersistentToken(string $salt): string
    {
        return $this->getPersistentToken() . $salt;
    }

    /**
     * @param string $salt
     * @return string
     */
    public function getSaltedOneTimeToken(string $salt): string
    {
        return $this->getOneTimeToken() . $salt;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return implode(self::SEPARATOR, [
            $this->getCredential(),
            $this->getOneTimeToken(),
            $this->getPersistentToken(),
        ]);
    }
}
