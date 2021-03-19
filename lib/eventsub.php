<?php
namespace ostilton\Twitch;

class Eventsub {
    const EVENT_URI = 'https://api.twitch.tv/helix/eventsub/subscriptions';
    protected $token;

    public function checkSig($inputSig, $id, $ts, $body) {
        $sig = 'sha256=' . hash_hmac('sha256', join('', [
            $id, $ts, $body
        ]), Config::get('eventSecret'));
        return ($sig === $inputSig);
    }

    public function handleCallback($msg, $body) {
        $json = json_decode($body, true);
        switch ($msg) {
            case 'webhook_callback_verification':
                return $json['challenge'];
            case 'notification':
                \Ratchet\Client\connect('ws://localhost:'.Config::get('wsPort'))->then(function($conn) use($json) {
                    $conn->send(json_encode([
                        'type' => 'EVENT',
                        'content' => $json,
                    ], JSON_UNESCAPED_SLASHES));
                    $conn->close();
                }, function($e) {
                    throw($e);
                });
                return true;
        }
    }

    public function refreshSubscription() {
        $this->login();
        if ($this->token) {
            foreach ($this->list_subs() as $sub) {
                $this->delete_sub($sub['id']);
            }
            $this->subscribe();
        }
    }

    protected function login() {
        $c = curl_init();
        curl_setopt_array($c, [
            CURLOPT_URL => 'https://id.twitch.tv/oauth2/token?'.http_build_query([
                'client_id' => Config::get('clientId'),
                'client_secret' => Config::get('clientSecret'),
                'grant_type' => 'client_credentials',
                'scopes' => 'channel:read:subscriptions',
            ]),
            CURLOPT_TIMEOUT => 5,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);
        $r = curl_exec($c);
        $json = json_decode($r, true);
        if ($json) {
            $this->token = $json['access_token'];
        }
    }

    protected function send($method, $params = null) {
        $c = curl_init();
        $options = [
            CURLOPT_URL => self::EVENT_URI,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Client-ID: '.Config::get('clientId'),
                'Authorization: Bearer '.$this->token,
            ],
        ];
        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                $options[CURLOPT_POST] = true;
                $options[CURLOPT_POSTFIELDS] = json_encode($params, JSON_UNESCAPED_SLASHES);
                break;
            case 'DELETE':
                $options[CURLOPT_URL] = self::EVENT_URI . '?' . http_build_query($params);
                $options[CURLOPT_CUSTOMREQUEST] = $method;
                break;
        }
        curl_setopt_array($c, $options);
        $r = curl_exec($c);
        curl_close($c);

        $response = ['headers' => [], 'body' => null];
        $output = 'headers';
        foreach (explode("\n", $r) as $line) {
            if (trim($line) === '') {
                $output = 'body';
            } else switch ($output) {
                case 'headers':
                    $k = trim(substr($line, 0, strpos($line, ':')));
                    $v = trim(substr($line, strpos($line, ':') + 1));
                    $response['headers'][$k] = $v;
                    break;
                case 'body':
                    $response['body'] = json_decode($line, true);
                    break;
            }
        }
        return $response;
    }

    protected function list_subs() {
        $r = $this->send('GET');
        return $r['body']['data'];
    }

    protected function delete_sub($id) {
        $this->send('DELETE', ['id' => $id]);
    }

    protected function subscribe() {
        $r = $this->send('POST', [
            'type' => 'channel.follow',
            'version' => 1,
            'condition' => [
                'broadcaster_user_id' => (string)Config::get('userId'),
            ],
            'transport' => [
                'method' => 'webhook',
                'callback' => Config::get('eventUri'),
                'secret' => Config::get('eventSecret'),
            ],
        ]);
    }
}
