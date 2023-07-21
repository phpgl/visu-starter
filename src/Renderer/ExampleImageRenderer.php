<?php

namespace App\Renderer;

use App\Component\ExampleImage;
use App\Pass\ExampleImagePass;
use VISU\Graphics\GLState;
use VISU\Graphics\Rendering\RenderPipeline;
use VISU\Graphics\Rendering\Resource\RenderTargetResource;
use VISU\Graphics\ShaderCollection;
use VISU\Graphics\ShaderProgram;

class ExampleImageRenderer
{
    private ShaderProgram $imageShader;

    public function __construct(
        private GLState $gl,
        private ShaderCollection $shaders,
    )
    {
        // create the shader program
        $this->imageShader = $this->shaders->get('example_image');
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
            $renderTarget,
            $exampleImages
        ));
    }
}
