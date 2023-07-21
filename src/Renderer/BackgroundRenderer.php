<?php

namespace App\Renderer;

use App\Component\ExampleImage;
use GL\Math\Mat4;
use GL\Math\Vec3;
use VISU\Geo\Transform;
use VISU\Graphics\GLState;
use VISU\Graphics\QuadVertexArray;
use VISU\Graphics\Rendering\Pass\CallbackPass;
use VISU\Graphics\Rendering\Pass\CameraData;
use VISU\Graphics\Rendering\PipelineContainer;
use VISU\Graphics\Rendering\PipelineResources;
use VISU\Graphics\Rendering\RenderPass;
use VISU\Graphics\Rendering\RenderPipeline;
use VISU\Graphics\Rendering\Resource\RenderTargetResource;
use VISU\Graphics\ShaderCollection;
use VISU\Graphics\ShaderProgram;
use VISU\Graphics\Texture;
use VISU\Graphics\TextureOptions;

class BackgroundRenderer
{
    /**
     * Simple background shader
     */
    private ShaderProgram $backgroundShader;

    /**
     * The background texture
     */
    private Texture $backgroundTexture;

    /**
     * The vertex array for the quad
     */
    private QuadVertexArray $backgroundVA;

    /**
     * Constructor
     */
    public function __construct(
        private GLState $gl,
        private ShaderCollection $shaders,
    )
    {
        // load the background shader
        $this->backgroundShader = $this->shaders->get('background');

        // load the background texture
        // this is pixel artish so we want to use nearest neighbor filtering
        $backgroundOptions = new TextureOptions;
        $backgroundOptions->minFilter = GL_NEAREST;
        $backgroundOptions->magFilter = GL_NEAREST;
        $this->backgroundTexture  = new Texture($gl, 'background');
        $this->backgroundTexture->loadFromFile(VISU_PATH_RESOURCES . '/background/landscape.gif', $backgroundOptions);

        // create the vertex array
        $this->backgroundVA = new QuadVertexArray($gl);
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
    ) : void
    {
        // you do not always have to create a new class for a render pass
        // often its more convenient to just create a closure as showcased here
        // to render the background
        $pipeline->addPass(new CallbackPass(
            'BackgroundPass',
            // setup (we need to declare who is reading and writing what)
            function(RenderPass $pass, RenderPipeline $pipeline, PipelineContainer $data) use($renderTarget) {
                $pipeline->writes($pass, $renderTarget);
            },
            // execute
            function(PipelineContainer $data, PipelineResources $resources) use($renderTarget)
            {
                $resources->activateRenderTarget($renderTarget);

                glDisable(GL_DEPTH_TEST);
                glDisable(GL_CULL_FACE);


                $cameraData = $data->get(CameraData::class);

                // enable our shader and set the uniforms camera uniforms
                $this->backgroundShader->use();
                $this->backgroundShader->setUniformMat4('u_view', false, $cameraData->view);
                $this->backgroundShader->setUniformMat4('u_projection', false, $cameraData->projection);
                $this->backgroundShader->setUniform1i('u_texture', 0);
                
                // bind the texture
                $this->backgroundTexture->bind(GL_TEXTURE0);

                // draw the quad 
                $transform = new Transform;
                // $transform->position->x = 2048;
                // $transform->position->y = 1024;
                $transform->scale->x = 2048 * 2;
                $transform->scale->y = 1024 * 2;
                $this->backgroundShader->setUniformMat4('u_model', false, $transform->getLocalMatrix());
                $this->backgroundVA->draw();
            }
        ));
    }
}
