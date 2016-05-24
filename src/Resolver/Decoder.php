<?php

namespace Bravesheep\FlysystemUrlBundle\Resolver;

use Bravesheep\FlysystemUrlBundle\Exception\UrlDecodeException;

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
                $data = parse_url($url);

                if (false === $data && ProtocolMapping::shouldTryAsFile($protocol)) {
                    $data = parse_url('file://' . $rest);
                    if (false === $data) {
                        throw new UrlDecodeException("Malformed url: '$url'");
                    }
                }

                // scheme is not relevant
                unset($data['scheme']);

                // parse query parameters
                if (isset($data['query'])) {
                    parse_str($data['query'], $query);
                    $data = array_merge($data, $query);
                    unset($data['query']);
                }

                // get additional adapter options from the protocol
                $adapter = ProtocolMapping::getAdapter($protocol);
                $data = array_merge($adapter, $data);
                $data['original_url'] = $url;

                return $data;
            }
        }
    }
}
