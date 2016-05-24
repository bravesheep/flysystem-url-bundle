<?php

namespace Bravesheep\FlysystemUrlBundle\Resolver;

use Bravesheep\FlysystemUrlBundle\Exception\UrlDecodeException;
use Symfony\Component\VarDumper\VarDumper;

class Decoder
{
    public static function decode($url)
    {
        // only do url parsing if the url contains '://'
        if (false === strpos($url, '://')) {
            if (!ProtocolMapping::isValidProtocol($url)) {
                throw new UrlDecodeException("No Protocol found in url '$url'");
            }

            // only a valid protocol name is found, return just the base options
            return ProtocolMapping::getAdapter($url);
        } else {

            // find the protocol name
            list($protocol, $rest) = explode('://', $url, 2);
            if (!ProtocolMapping::isValidProtocol($protocol)) {
                throw new UrlDecodeException("Unknown protocol: '$protocol'");
            }
            
            if ($rest === '') {
                // no parameters other than the protocol, return just the base options
                return ProtocolMapping::getAdapter($protocol);
            } else {

                if (ProtocolMapping::shouldTryAsFile($protocol)) {
                    $data = parse_url('file://' . $rest);
                } else {
                    $data = self::decode($url);
                }

                if (false === $data) {
                    throw new UrlDecodeException("Malformed url: '$url'");
                }

                // scheme is not relevant
                unset($data['scheme']);

                // parse query parameters
                if (isset($data['query'])) {
                    parse_str($data['query'], $query);
                    $data = array_merge($data, $query);
                    unset($data['query']);
                }

                // path without slash
                if (isset($data['path'])) {
                    if (strlen($data['path']) > 0 && $data['path'][0] === '/') {
                        $data['clean_path'] = substr($data['path'], 1);
                    } else {
                        $data['clean_path'] = $data['path'];
                    }
                }

                // get additional adapter options from the protocol
                $adapter = ProtocolMapping::getAdapter($protocol);
                $data = array_merge($adapter, $data);
                $data['original_url'] = $url;

                return $data;
            }
        }
    }

    public static function matchUrl($url)
    {
        $did_match = preg_match(
            ',^(?<scheme>[a-zA-Z]+)://((?<userpass>.+?)@)?(?<domain>[a-zA-Z0-9.-]+)(:(?<port>[0-9]+))?(?<pathquery>/.*)?$,',
            $url,
            $matches
        );

        if ($did_match !== 1) {
            return false;
        }

        $user = null;
        $pass = null;
        if (isset($matches['userpass']) && strlen($matches['userpass']) > 0) {
            $userpass = $matches['userpass'];
            if (false === strpos($userpass, ':')) {
                $user = $userpass;
            } else {
                list($user, $pass) = explode(':', $userpass, 2);
            }
        }

        $path = null;
        $query = null;
        $fragment = null;
        if (isset($matches['pathquery'])) {
            $pathquery = $matches['pathquery'];
            if (false === strpos($pathquery, '?')) {
                $path = $pathquery;
                if (false !== strpos($path, '#')) {
                    list($path, $fragment) = explode('#', $path, 2);
                }
            } else {
                list($path, $query) = explode('?', $pathquery, 2);

                if (false !== strpos($query, '#')) {
                    list($query, $fragment) = explode('#', $query, 2);
                }
            }
        }

        return [
            'scheme' => $matches['scheme'],
            'user' => $user,
            'pass' => $pass,
            'host' => $matches['domain'],
            'port' => isset($matches['port']) ? intval($matches['port'], 10) : null,
            'path' => $path,
            'query' => $query,
            'fragment' => $fragment
        ];
    }
}
