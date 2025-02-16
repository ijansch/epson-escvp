<?php

require_once('EpsonESCVP.class.php');

use EpsonESCVP\EpsonESCVP;

try {
    $projector = new EpsonESCVP();

    // Connect to the projector
    echo "Connecting...\n";
    if ($projector->init("192.168.10.157")) {
        echo "Connected to projector at 192.168.10.157\n";
    }

    $response = $projector->execute("PWR?");
    echo "Power state response: '$response'\n";

    if ($response != "PWR=01") { // lamp on 

        $response = $projector->execute("PWR ON");  // Turn on projector
        echo "Power On response: '$response'\n";

	// It will take a while before the projector is actually on, so we sleep a bit here,.
	echo "Sleeping 20 seconds to wait for projector to wake up\n";
	sleep(20);
    } else {
        echo "Projector was on, immediately proceeding with next steps\n";
    }
	 

    $response = $projector->execute("SOURCE 02");  // Change source to INPUT 1 (typically hdmi1)
    echo "Source Change response: '$response'\n";

    $response = $projector->execute("SNO?");  // Get the serial number of the projector
    echo "Serial number response: '$response'\n";

    $response = $projector->execute("LAMP?");  // Return the lamp operation time
    echo "Lamp operation time response: '$response'\n";

    if ($projector->finish()) {
        echo "Connection closed successfully.\n";
    } else {
        echo "No active connection to close.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
