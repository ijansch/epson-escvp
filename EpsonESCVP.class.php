<?php

namespace EpsonESCVP;

use Exception;

class EpsonESCVP {
    private $socket;
    private $ip;
    private $port;

    public function __construct() {
        // No IP or port in constructor, allowing reusability
        $this->socket = null;
        $this->ip = null;
        $this->port = null;
    }

    public function init(string $ip, int $port = 3629): bool {
        $this->ip = $ip;
        $this->port = $port;

        // Open a socket connection
        $this->socket = @fsockopen($this->ip, $this->port, $errno, $errstr, 5);
        if (!$this->socket) {
            throw new Exception("Could not connect to projector ($this->ip:$this->port) - $errstr ($errno)");
        }

        // Send handshake
        $handshake = "ESC/VP.net\x10\x03\x00\x00\x00\x00";
        fwrite($this->socket, $handshake);
        usleep(500000); 

        // Wait for projector readiness (Initially the ESC/VP response)
        if (!$this->waitForReady("ESC/VP.net")) {
            fclose($this->socket);
            throw new Exception("Projector at $this->ip:$this->port did not respond with readiness signal.");
        }

        return true; // Return success
    }

    public function execute(string $command): string {
        if (!$this->socket) {
            throw new Exception("Connection not initialized. Call init() first.");
        }

        fwrite($this->socket, $command . "\r");
        usleep(500000); 

        return $this->waitForReady();
    }

    public function finish(): bool {
        if ($this->socket) {
            fclose($this->socket);
            $this->socket = null;
            return true; // Return success
        }
        return false; // Return failure if there was no open socket
    }

    private function waitForReady(string $marker = ":"): string {
        $response = "";
        while (true) {
            $data = fread($this->socket, 1024);
            if ($data === false) {
                throw new Exception("Error reading from projector at $this->ip:$this->port.");
            }

            $response .= trim($data);

            if (strpos($response, $marker) !== false) {
                return trim($response, "\r:");
            }
	    if (strpos($response, "ERR") !== false) {
		throw new Exception("Projector returned ERR (error) response");
            }

            usleep(500000);
        }
    }
}
