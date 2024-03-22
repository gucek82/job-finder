<?php

namespace Framework;

use Framework\Session;

class Authorization {
    /**
     * Check if current logged in user owns a resouce
     * 
     * @param int $resource_id
     * @return bool
     */
    public static function isOwner($resource_id) {
        $sessionUser = Session::get('user');
        if($sessionUser !== null && isset($sessionUser['id'])) {
            $sessionUserId = (int)$sessionUser['id'];

            return $sessionUserId === $resource_id;
        }

        return false;
    }
}