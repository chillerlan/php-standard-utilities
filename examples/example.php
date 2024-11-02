<?php
/**
 * example.php
 *
 * @created      30.10.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

use chillerlan\Utilities\Crypto;

require_once __DIR__.'/../vendor/autoload.php';

var_dump(Crypto::randomString(128));
