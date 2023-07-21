<?php

namespace App\System;

use App\Component\ExampleImage;
use GL\Math\{GLM, Quat, Vec2, Vec3};
use VISU\ECS\EntitiesInterface;
use VISU\ECS\SystemInterface;
use VISU\Geo\Transform;
use VISU\Graphics\Rendering\RenderContext;

class VisuPhpantSystem implements SystemInterface
{   
    /**
     * Registers the system, this is where you should register all required components.
     * 
     * @return void 
     */
    public function register(EntitiesInterface $entities) : void
    {
        $entities->registerComponent(ExampleImage::class);

        // create a bunch of phpants
        for ($i = 0; $i < 20; $i++) {
            $entity = $entities->create();
            $entities->attach($entity, new ExampleImage);
            $transform = $entities->attach($entity, new Transform);
            $transform->position = new Vec3($i * 0.1, 0.5, 0);
            $transform->scale = new Vec3(32, 32, 1);
        }
    }

    /**
     * Unregisters the system, this is where you can handle any cleanup.
     * 
     * @return void 
     */
    public function unregister(EntitiesInterface $entities) : void
    {
    }

    /**
     * Updates handler, this is where the game state should be updated.
     * 
     * @return void 
     */
    public function update(EntitiesInterface $entities) : void
    {
    }

    /**
     * Handles rendering of the scene, here you can attach additional render passes,
     * modify the render pipeline or customize rendering related data.
     * 
     * @param RenderContext $context
     */
    public function render(EntitiesInterface $entities, RenderContext $context) : void
    {
    }
}