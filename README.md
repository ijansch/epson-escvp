# EpsonESCVP - PHP Control Library for Epson Projectors

## Overview
EpsonESCVP is a PHP library for controlling **Epson projectors** using the **ESC/VP21** protocol over TCP/IP.  
It allows you to **power on/off, switch sources, adjust volume**, and more.

I created it because I wanted to automate lens memory operations via the network, and regular PJLink implementations to not support this command. The ESC/VP21 protocol is much more powerful and has a huge list of possible commands. 

**Warning:**: The ESCP/VP21 protocol also contains commands to adjust settings, so use with care. Sending the wrong commands can get your projector in an unusable state. Use of this library is entirely at your own risk.

Tested with:

* Epson TW9400

It should work with any Epson projector that supports the ESC/VP21 protocol. If you've had success with a different model, please send me a pull request to update this README.

## Usage

1. Initialize the projector connection:

```php
use EpsonESCVP\EpsonESCVP;

$projector = new EpsonESCVP();
$projector->init("192.168.1.100"); // Replace with the projector's IP
```

2. Send commands:

```php
$response = $projector->execute("PWR ON");  // Turn on the projector
echo "Power On Response: $response\n";
```

The class will throw exceptions so you should wrap commands in try/catch statements,

See the included example.php for a more elaborate example.

3. Close the connection when you're done sending all your commands.

```php
$projector->finish();
```

## Available commands

The available commands vary per projector. Epson provides an elaborate excel file with the commands for each projector. You can find it on the support page for your projector on the Epson website.

## Contributing

If you want to contribute, feel free to fork this repository and send me a pull request,

The following are on my todo/wishlist:

* Instead of sending the raw commands (PWR ON, POPLP 01 etc) it would be great if the class had proper methods, eg. $projector->powerOn(), $projector->lensMemory(1); 
* Same for the return values, instead of PWR=02 it could have proper constants such as Projector::WARMING_UP

If you find a bug, need a feature, or want to contribute, feel free to:

* Submit a pull request
* Open an issue
* Suggest improvements

I welcome contributions from the community! Keep in mind that this is built entirely in my spare time, so no guarantees!
