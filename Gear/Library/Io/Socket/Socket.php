<?php

namespace Gear\Library\Io\Socket;

use Gear\Interfaces\SocketInterface;
use Gear\Library\Io\Io;

class Socket extends Io implements SocketInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    public function safeEof(): bool
    {
        $self = $this;
        $feof = function (&$start) use ($self): bool {
            $start = microtime(true);
            return feof($self->handler);
        };
        $start = null;
        $timeout = (int)ini_get('default_socket_timeout');
        return $feof($start) === false && (microtime(true) - $start) < $timeout;
    }
}
