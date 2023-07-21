<?php

namespace App\Component;

use GL\Math\Vec2;
use GL\Math\Vec3;

class GameCamera2DComponent
{
    /**
     * Point in world space the camera is focused on (looking at)
     */
    public Vec2 $focusPoint;
    
    /**
     * The velocity the focus point is currently moving at
     */
    public Vec2 $focusPointVelocity;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->focusPoint = new Vec2(0.0);
        $this->focusPointVelocity = new Vec2(0.0);
    }
}
