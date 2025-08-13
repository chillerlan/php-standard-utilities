# chillerlan/php-standard-utilities

A collection of reusable multi-purpose functions for PHP libraries.

[![PHP Version Support][php-badge]][php]
[![Packagist version][packagist-badge]][packagist]
[![License][license-badge]][license]
[![Continuous Integration][gh-action-badge]][gh-action]
[![CodeCov][coverage-badge]][coverage]
[![Packagist downloads][downloads-badge]][downloads]

[php-badge]: https://img.shields.io/packagist/php-v/chillerlan/php-standard-utilities?logo=php&color=8892BF&logoColor=fff
[php]: https://www.php.net/supported-versions.php
[packagist-badge]: https://img.shields.io/packagist/v/chillerlan/php-standard-utilities.svg?logo=packagist&logoColor=fff
[packagist]: https://packagist.org/packages/chillerlan/php-standard-utilities
[license-badge]: https://img.shields.io/github/license/chillerlan/php-standard-utilities
[license]: https://github.com/chillerlan/php-standard-utilities/blob/main/LICENSE
[gh-action-badge]: https://img.shields.io/github/actions/workflow/status/chillerlan/php-standard-utilities/ci.yml?branch=main&logo=github&logoColor=fff
[gh-action]: https://github.com/chillerlan/php-standard-utilities/actions/workflows/ci.yml?query=branch%3Amain
[coverage-badge]: https://img.shields.io/codecov/c/github/chillerlan/php-standard-utilities.svg?logo=codecov&logoColor=fff
[coverage]: https://codecov.io/github/chillerlan/php-standard-utilities
[downloads-badge]: https://img.shields.io/packagist/dt/chillerlan/php-standard-utilities.svg?logo=packagist&logoColor=fff
[downloads]: https://packagist.org/packages/chillerlan/php-standard-utilities/stats

## Overview

### Features

This library features some common functions to reduce overall duplication and avoid certain ugly workarounds (looking at you, phpstan...).


### Requirements

- PHP 8.1+
	- extensions: `json`, `mbstring`, `sodium`

## API

### `Arr`

(we can't use `array` as class name because reasons)

| method                            | description                                                                |
|-----------------------------------|----------------------------------------------------------------------------|
| `Arr::first(array $array):mixed`  | Returns the first element of an array, `null` if the given array is empty. |
| `Arr::last(array $array):mixed`   | Returns the last element of an array, `null` if the given array is empty.  |
| `Arr::random(array $array):mixed` | Returns a random element of an array, `null` if the given array is empty.  |


### `Crypto`

| method                                                                                                | description                                                                                                                            |
|-------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------|
| `Crypto::sha256(string $data, bool $binary = false):string`                                           | Generates an SHA-256 hash for the given value                                                                                          |
| `Crypto::sha512(string $data, bool $binary = false):string`                                           | Generates an SHA-512 hash for the given value                                                                                          |
| `Crypto::randomString(int $length, string $keyspace = Crypto::ASCII_COMMON_PW):string`                | Generates a secure random string of the given `$length`, using the characters (8-bit byte) in the given `$keyspace`.                   |
| `Crypto::createEncryptionKey():string`                                                                | Creates a new cryptographically secure random encryption key for use with `encrypt()` and `decrypt()` (returned in hexadecimal format) |
| `Crypto::encrypt(string $data, string $keyHex, int $format = Crypto::ENCRYPT_FORMAT_HEX):string`      | Encrypts the given `$data` with `$key`, formats the output according to `$format` \[binary, base64, hex\]                              |
| `Crypto::decrypt(string $encrypted, string $keyHex, int $format = Crypto::ENCRYPT_FORMAT_HEX):string` | Decrypts the given `$encrypted` data with `$key` from input formatted according to `$format` \[binary, base64, hex\]                   |

The `Crypto` class defines the following public constants:

pre-defined character maps for use with `Crypto::randomString()` as  `$keyspace`:

- `NUMERIC`: numbers `0-9`
- `ASCII_LOWER`: ASCII `a-z`
- `ASCII_UPPER`: ASCII `A-Z`
- `ASCII_SYMBOL`: ASCII printable symbols  ``!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~``
- `HEXADECIMAL`: numbers `0-9` + ASCII `a-f`
- `ASCII_ALPHANUM`: numbers `0-9` + ASCII `a-z` + `A-Z`
- `ASCII_PRINTABLE`: numbers `0-9` + ASCII `a-z` + `A-Z` + printable symbols
- `ASCII_COMMON_PW`: ASCII alphanum + most of ASCII printable symbols `!#$%&()*+,-./:;<=>?@[]~_|` (minus a few troublemakers)

output and input `$format` for the functions `Crypto::encrypt()` and `Crypto::decrypt()`, respectively:

- `ENCRYPT_FORMAT_BINARY`: raw binary
- `ENCRYPT_FORMAT_BASE64`: mime base64
- `ENCRYPT_FORMAT_HEX`: hexadecimal


### `Directory`

| method                                                                                                | description                                        |
|-------------------------------------------------------------------------------------------------------|----------------------------------------------------|
| `Directory::exists(string $dir):bool`                                                                 | Checks whether a directory exists                  |
| `Directory::isReadable(string $dir):bool`                                                             | Checks whether the given directory is readable     |
| `Directory::isWritable(string $dir):bool`                                                             | Checks whether the given directory is writable     |
| `Directory::create(string $dir, int $permissions = 0o777, bool $recursive = true):string`             | Creates a directory                                |
| `Directory::remove(string $dir):bool`                                                                 | Removes a directory                                |
| `Directory::relativePath(string $path, string $from, string $separator = DIRECTORY_SEPARATOR):string` | Returns the relative path from the given directory |


### `File`

| method                                                                                       | description                                                        |
|----------------------------------------------------------------------------------------------|--------------------------------------------------------------------|
| `File::exists(string $file):bool`                                                            | Checks whether a file exists                                       |
| `File::isReadable(string $file):bool`                                                        | Checks whether the given file is readable                          |
| `File::isWritable(string $file):bool`                                                        | Checks whether the given file is writable                          |
| `File::realpath(string $path):string`                                                        | Returns the absolute real path to the given file or directory      |
| `File::delete(string $file):bool`                                                            | Deletes a file                                                     |
| `File::load(string $file, int $offset = 0, int\|null $length = null):string`                 | reads the given file into a string                                 |
| `File::save(string $file, string $data):int`                                                 | saves the given data string to the given file path                 |
| `File::loadJSON(string $file, bool $associative = false, int $flags = 0):mixed`              | load a JSON string from file into an array or object (convenience) |
| `File::saveJSON(string $file, mixed $data, int $flags = Str::JSON_ENCODE_FLAGS_DEFAULT):int` | save to a JSON file (convenience)                                  |


### `Str`

(see `Arr`)

| method                                                                                    | description                                                                                   |
|-------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------------|
| `Str::filter(array $mixed):array`                                                         | Filters an array and removes all elements that are not strings. Array keys are *not* retained |
| `Str::toUpper(array $strings):array`                                                      | Converts the strings in an array to uppercase                                                 |
| `Str::toLower(array $strings):array`                                                      | Converts the strings in an array to lowercase                                                 |
| `Str::startsWith(string $haystack, array $needles, bool $ignoreCase = false):bool`        | Checks whether the given string starts with *any* of the given array of needles               |
| `Str::containsAll(string $haystack, array $needles, bool $ignoreCase = false):bool`       | Checks whether the given string (haystack) contains *all* of the given array of needles       |
| `Str::containsAny(string $haystack, array $needles, bool $ignoreCase = false):bool`       | Checks whether the given string (haystack) contains *any* of the given array of needles       |
| `Str::jsonDecode(string $json, bool $associative = false, int $flags = 0):mixed`          | Decodes a JSON string                                                                         |
| `Str::jsonEncode(mixed $data, int $flags = self::JSON_ENCODE_FLAGS_DEFAULT):string`       | Encodes a value into a JSON representation                                                    |
| `Str::base64encode(string $string, int $variant = SODIUM_BASE64_VARIANT_ORIGINAL):string` | Encodes a binary string to base64 (timing-safe)                                               |
| `Str::base64decode(string $base64, int $variant = SODIUM_BASE64_VARIANT_ORIGINAL):string` | Decodes a base64 string into binary (timing-safe)                                             |


## Disclaimer

Use at your own risk!
