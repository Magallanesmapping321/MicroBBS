# Not supported anymore. I'll only fix security issues.

<h1 align="center"> MicroTXT  🗣️️</h1>

A tiny [textboard style](https://en.wikipedia.org/wiki/Textboard) software written in PHP, no database server required.

It is meant to be simple to host and use.

## Features

* ✅ Only PHP 7.x+ required (No database server, only sqlite3!)
* ✅ Hidden/unlisted threads (start your thread title with .)
* ✅ MOTDs
* ✅ Less than 300kb in size.
* ✅ Markdown for parent posts
* ✅ No JavaScript required (JavaScript is used minimally but only to increase usability)

## Installing

MicroTXT is only tested in a Linux environment, however, it should work on Windows/Unix with little to no modification.

Simply download and place the files in your PHP 7.0+ enabled website directory, and edit php/settings.php to your liking. You should probably also change rules.txt and faq.txt too. Make sure PHP has the permissions required to read/write the files.

**PHP 5.X support has been dropped because PHP 5.X is end of life**

**YOU NEED PHP MBSTRING/PHP-XML AND SQLITE3 LIBRARIES INSTALLED** (ubuntu: sudo apt install php7.1-xml php7.1-gd php7.1-gd) Package names may vary.

**You also need the GD PHP library installed on your PHP instance if you want to use the included captcha**

## Configuring

Just edit php/settings.php

Be careful what you put in settings.php, since it is executable code.

To disable the captcha just change $captcha to false, or to make the captcha appear every time, change $postsBeforeCaptcha to 0.

Board appearance: MicroTXT only has 1 CSS file, so if you know CSS you can change the appearance by editing theme.css.

### Admin Panel

A not yet finished admin panel is available at /admin.php, but to enable it you need to modify php/moderators.php. Set the values to true and change the password.

## Warnings

This is new, there may be some issues with it.

Don't rely on it for huge communities, it doesn't scale for very high traffic projects (it's not meant to).

Change the salt in settings.php, otherwise tripcodes may be easier to brute force.

**Prior to version 1.2, salts were not being applied to tripcodes (due to a bug), resulting in potentially easy to brute force tripcodes when bad passwords were used**

**Prior to version 1.6, links in parent posts could potentially cause XSS with javascript: and data uris (reported by @arinerron)**

Tripcodes are 'secure enough' if you set a good salt and good passwords are used for the codes, but in general they should not be considered to be 100% proof of a poster's identity.

## Demo

You can use the demo board [on my website](https://www.chaoswebs.net/mt/).

## Contributing

I will accept pull requests if they fix bugs or improve the software in a way I think fits the goals of the project.

Try to follow the coding style of existing code, and comment any non-simple code.

### Bug Reports

Well structured & polite bug reports are appreciated. Please try to include the following information in any bug reports:

* PHP version
* Web server version
* Operating system version
* MicroTXT Version (specified in settings.php)
* What you have tried so far
* Screenshots are helpful, but not necessarily required.

## Development Roadmap & Planned features

* Better post & reply formatting
* Admin panel for setup, configuration, and moderation
* Easy to use installation script (For Linux)
* Perhaps a Docker container if there is demand
* Code refactoring

## Contacting me

You can find my contact info at my website: [ChaosWebs.net](https://www.chaoswebs.net/)

*I will probably help you if you ask for assistance, but I am not obligated to do so.*

## Support Development 💲

A dollar or two would be appreciated. If you give me money, I will be more likely to fix bugs or add features you want.


Bitcoin: 1Hek4bVGsxSFA1QpTXryMZcP88agGMKfAU
Paypal: kevinfroman12@gmail.com
Ko-Fi: https://www.ko-fi.com/beardogkf
