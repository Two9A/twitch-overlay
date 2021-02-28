<?php
namespace ostilton\Twitch;

use Ratchet\Http\HttpServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class SocketServer implements MessageComponentInterface {
    protected $clients;

    protected function _log($msg) {
        printf("[%s] %s\n", date('YmdHis'), $msg);
    }

    public function start($port) {
        $this->_log('Server started on port '.$port);
        $server = IoServer::factory(new HttpServer(new WsServer($this)), $port);
        $server->run();
    }

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $this->_log('New connection: '.$conn->resourceId);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $this->_log('From: '.$from->resourceId.'; Message: '.$msg);
        $json = json_decode($msg, true);
        switch ($json['type']) {
            case 'PING':
                $this->_log('    Replying with PONG');
                $from->send(json_encode(['type' => 'PONG']));
                break;

            default:
                foreach ($this->clients as $client) {
                    if ($from !== $client) {
                        $this->_log('    Sending to: '.$client->resourceId);
                        $client->send($msg);
                    }
                }
                break;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        $this->_log('Connection closed: '.$conn->resourceId);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
        $this->_log('Connection error: '.$e->getMessage());
    }
}
