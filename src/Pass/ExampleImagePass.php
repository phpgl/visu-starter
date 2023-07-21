<?php

namespace App\Pass;

use VISU\Graphics\GLState;
use VISU\Graphics\Rendering\PipelineContainer;
use VISU\Graphics\Rendering\PipelineResources;
use VISU\Graphics\Rendering\RenderPass;
use VISU\Graphics\Rendering\RenderPipeline;
use VISU\Graphics\Rendering\Resource\RenderTargetResource;
use VISU\Graphics\ShaderProgram;

class ExampleImagePass extends RenderPass
{
    /**
     * Constructor
     *
     * @param array<ExampleImage> $exampleImages
     */
    public function __construct(
        private GLState $gl,
        private ShaderProgram $shader,
        private RenderTargetResource $renderTarget,
        private array $exampleImages
    ) {
    }

    /**
     * Executes the render pass
     */
    public function setup(RenderPipeline $pipeline, PipelineContainer $data): void
    {
    }

    /**
     * Executes the render pass
     */
    public function execute(PipelineContainer $data, PipelineResources $resources): void
    {
        $resources->activateRenderTarget($this->renderTarget);

        glClearColor(0.5, 1, 0.2, 1);
        glClear(GL_COLOR_BUFFER_BIT);
    }
}
