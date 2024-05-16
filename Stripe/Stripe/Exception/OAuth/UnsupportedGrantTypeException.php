<?php declare(strict_types=1);

namespace Stripe\Exception\OAuth;

/**
 * UnsupportedGrantTypeException is thrown when an unuspported grant type
 * parameter is specified.
 */
class UnsupportedGrantTypeException extends OAuthErrorException
{
}
