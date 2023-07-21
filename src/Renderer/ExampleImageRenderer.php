<?php

namespace App\Renderer;

use App\Component\ExampleImage;
use App\Pass\ExampleImagePass;
use VISU\Graphics\GLState;
use VISU\Graphics\Rendering\RenderPipeline;
use VISU\Graphics\Rendering\Resource\RenderTargetResource;
use VISU\Graphics\ShaderCollection;
use VISU\Graphics\ShaderProgram;
use VISU\Graphics\Texture;
use VISU\Graphics\TextureOptions;

class ExampleImageRenderer
{
    private ShaderProgram $imageShader;

    /**
     * The background texture
     */
    private Texture $elephantSprite;

    public function __construct(
        private GLState $gl,
        private ShaderCollection $shaders,
    )
    {
        // create the shader program
        $this->imageShader = $this->shaders->get('example_image');

        // this is pixel artish so we want to use nearest neighbor filtering
        $backgroundOptions = new TextureOptions;
        $backgroundOptions->minFilter = GL_NEAREST;
        $backgroundOptions->magFilter = GL_NEAREST;
        $this->elephantSprite  = new Texture($gl, 'visuphpant');
        $this->elephantSprite->loadFromFile(VISU_PATH_RESOURCES . '/sprites/visuphpant.png', $backgroundOptions);
    }

    /**
     * Attaches a render pass to the pipeline
     * 
     * @param RenderPipeline $pipeline 
     * @param RenderTargetResource $renderTarget
     * @param array<ExampleImage> $exampleImages
     */
    public function attachPass(
        RenderPipeline $pipeline, 
        RenderTargetResource $renderTarget,
        array $exampleImages
    ) : void
    {
        $pipeline->addPass(new ExampleImagePass(
            $this->gl,
            $this->imageShader,
            $this->elephantSprite,
            $renderTarget,
            $exampleImages
        ));
    }
}
