#version 330 core

layout (location = 0) in vec3 a_pos;
layout (location = 1) in vec2 a_uv;

out vec2 v_uv;

uniform mat4 u_view;
uniform mat4 u_projection;
uniform mat4 u_model;

// In this example scale represents size in pixels
// so we need to halve the model scale
mat4 halfscale = mat4(
    0.5, 0.0, 0.0, 0.0,
    0.0, 0.5, 0.0, 0.0,
    0.0, 0.0, 0.5, 0.0,
    0.0, 0.0, 0.0, 1.0
);

void main() {
    v_uv = a_uv; // pass the texture coordinates to the fragment shader
    gl_Position = u_projection * u_view * u_model * vec4(a_pos, 1.0);
}