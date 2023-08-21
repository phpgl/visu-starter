<?php

namespace App\System;

use App\Component\GameCamera2DComponent;
use App\Debug\DebugTextOverlay;
use GL\Math\Vec2;
use GL\Math\Vec3;
use VISU\ECS\EntitiesInterface;
use VISU\Graphics\Camera;
use VISU\Graphics\CameraProjectionMode;
use VISU\Graphics\Rendering\RenderContext;
use VISU\OS\Input;
use VISU\OS\InputContextMap;
use VISU\Signal\Dispatcher;
use VISU\Signals\Input\CursorPosSignal;
use VISU\Signals\Input\ScrollSignal;
use VISU\System\VISUCameraSystem;

class CameraSystem2D extends VISUCameraSystem
{
    /**
     * Default camera mode is game in the game... 
     */
    protected int $visuCameraMode = self::CAMERA_MODE_GAME;

    /**
     * Constructor
     */
    public function __construct(
        Input $input,
        Dispatcher $dispatcher,
        protected InputContextMap $inputContext,
    )
    {
        parent::__construct($input, $dispatcher);
    }

    /**
     * Registers the system, this is where you should register all required components.
     * 
     * @return void 
     */
    public function register(EntitiesInterface $entities) : void
    {
        parent::register($entities);

        $gameCamera = new GameCamera2DComponent;
        $gameCamera->focusPoint = new Vec2(2048, 1024); // in the center of the world (map is 4096x2048)
        $entities->setSingleton($gameCamera);

        // create an inital camera entity
        $cameraEntity = $entities->create();
        $camera = $entities->attach($cameraEntity, new Camera(CameraProjectionMode::orthographicScreen));
        $camera->nearPlane = -10;
        $camera->farPlane = 10;

        // make the camera the active camera
        $this->setActiveCameraEntity($cameraEntity);
    }

    /**
     * Unregisters the system, this is where you can handle any cleanup.
     * 
     * @return void 
     */
    public function unregister(EntitiesInterface $entities) : void
    {
        parent::unregister($entities);

        $entities->removeSingleton(GameCamera2DComponent::class);
    }

    /**
     * Override this method to handle the cursor position in game mode
     * 
     * @param CursorPosSignal $signal 
     * @return void 
     */
    protected function handleCursorPosVISUGame(EntitiesInterface $entities, CursorPosSignal $signal) : void
    {
        // handle mouse movement
    }

    /**
     * Override this method to handle the scroll wheel in game mode
     * 
     * @param ScrollSignal $signal
     * @return void 
     */
    protected function handleScrollVISUGame(EntitiesInterface $entities, ScrollSignal $signal) : void
    {
        // handle mouse scroll
    }

    /**
     * Override this method to update the camera in game mode
     * 
     * @param EntitiesInterface $entities
     */
    public function updateGameCamera(EntitiesInterface $entities, Camera $camera) : void
    {
        $gameCamera = $entities->getSingleton(GameCamera2DComponent::class);

        if ($this->inputContext->actions->isButtonDown('camera_move_left')) {
            $gameCamera->focusPoint->x = $gameCamera->focusPoint->x - $gameCamera->acceleration;
        }

        if ($this->inputContext->actions->isButtonDown('camera_move_right')) {
            $gameCamera->focusPoint->x = $gameCamera->focusPoint->x + $gameCamera->acceleration;
        }

        if ($this->inputContext->actions->isButtonDown('camera_move_up')) {
            $gameCamera->focusPoint->y = $gameCamera->focusPoint->y - $gameCamera->acceleration;
        }

        if ($this->inputContext->actions->isButtonDown('camera_move_down')) {
            $gameCamera->focusPoint->y = $gameCamera->focusPoint->y + $gameCamera->acceleration;
        }
    }

    /**
     * Handles rendering of the scene, here you can attach additional render passes,
     * modify the render pipeline or customize rendering related data.
     * 
     * @param RenderContext $context
     */
    public function render(EntitiesInterface $entities, RenderContext $context) : void
    {
        $camera = $this->getActiveCamera($entities);
        $gameCamera = $entities->getSingleton(GameCamera2DComponent::class);

        // get current render target
        $renderTarget = $context->resources->getActiveRenderTarget();

        $renderWidth = $renderTarget->width() / $renderTarget->contentScaleX;
        $renderHeight = $renderTarget->height() / $renderTarget->contentScaleY;

        // ensure we don't move the camera outside of the world
        $gameCamera->focusPoint->x = max($renderWidth * 0.5, $gameCamera->focusPoint->x);
        $gameCamera->focusPoint->y = max($renderHeight * 0.5, $gameCamera->focusPoint->y);
        $gameCamera->focusPoint->x = min(4096 - ($renderWidth * 0.5), $gameCamera->focusPoint->x);
        $gameCamera->focusPoint->y = min(2048 - ($renderHeight * 0.5), $gameCamera->focusPoint->y);

        // move the actual camera in a way that the focus 
        // point is in the middle of the screen
        $camera->transform->position->x = $gameCamera->focusPoint->x - ($renderTarget->width() / $renderTarget->contentScaleX * 0.5);
        $camera->transform->position->y = $gameCamera->focusPoint->y - ($renderTarget->height() / $renderTarget->contentScaleY * 0.5);
        $camera->transform->markDirty();

        parent::render($entities, $context);
    }
}
