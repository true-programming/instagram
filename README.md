# Instagram
    Author: Michael Semle
    E-Mail: git@mikeproduction.de
    repo:   https://github.com/true-programming/instagram

TYPO3 extension to import instagram posts from user accounts and refresh tokens.

## Preparations
* If not available, create a facebook developer account
* Kickstart a facebook app with basic instagram graph api
* Save secret and client app tokens for later use

## Usage
* Create new account record in the instagram module
* Fill out all fields
* After saving, click link to create a token
* Login into instagram
* After coming back to your site, the record should have a valid token
* In the dashboard click the import button

## Features
* CLI command to import feeds of all configured accounts
* CLI command to refresh account tokens
* Cache tag `tx_instagram_feed` that gets flushed after import of feeds. The tag can be added to content elements and pages to ensure, new posts appear as soon as they were imported.

## How to install this extension?

You can set this up via composer (`composer require trueprogramming/instagram`).

## Requirements

* TYPO3 v12.

## License

The extension is licensed under GPL v2+, same as the TYPO3 Core.

For details see the LICENSE file in this repository.
