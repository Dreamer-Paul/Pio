/* ----

# Pio SDK 2/3/4 support
# By: jupiterbjy
# Last Update: 2021.4.22

To use this, you need to include following sources to your HTML file first.
With this script, you don't have to include `l2d.js`. Testing is done without it.
Basic usage is same with Paul-Pio.

Make sure to call `pio_refresh_style()` upon changing styles on anything related to 'pio-container' and it's children.

To change alignment, modify variable `pio_alignment` to either `left` or `right`, then call `pio_refresh_style()`.

<script src="https://cubism.live2d.com/sdk-web/cubismcore/live2dcubismcore.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/dylanNew/live2d/webgl/Live2D/lib/live2d.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pixi.js@5.3.6/dist/pixi.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pixi-live2d-display/dist/index.min.js"></script>

If you have trouble setting up this, check following example's sources.
https://jupiterbjy.github.io/PaulPio_PIXI_Demo/

---- */


function loadlive2d(canvas_id, json_object_or_url) {
    // Replaces original l2d method 'loadlive2d' for Pio.
    // Heavily relies on pixi_live2d_display.

    console.log("[Pio] Loading new model")

    const canvas = document.getElementById(canvas_id)

    // When pio was start minimized on browser refresh or reload,
    // canvas is set to 0, 0 dimension and need to be changed.
    if (canvas.width === 0) {
        canvas.removeAttribute("height")
        pio_refresh_style()
    }

    // Try to remove previous model, if any exists.
    try {
        app.stage.removeChildAt(0)
    } catch (error) {

    }

    let model = PIXI.live2d.Live2DModel.fromSync(json_object_or_url)

    model.once("load", () => {
        app.stage.addChild(model)

        const vertical_factor = canvas.height / model.height
        model.scale.set(vertical_factor)

        // match canvas to model width
        canvas.width = model.width
        pio_refresh_style()

        // check alignment, and align model to corner
        if (document.getElementsByClassName("pio-container").item(0).className.includes("left")){
            model.x = 0
        } else {
            model.x = canvas.width - model.width
        }

        // Hit callback definition
        model.on("hit", hitAreas => {
            if (hitAreas.includes("body")) {
                console.log("[Pio] Touch on body (SDK2)")
                model.motion('tap_body')

            } else if (hitAreas.includes("Body")) {
                console.log("[Pio] Touch on body (SDK3/4)")
                model.motion("Tap")

            } else if (hitAreas.includes("head") || hitAreas.includes("Head")){
                console.log("[Pio] Touch on head")
                model.expression()
            }
        })
    })
}


function _pio_initialize_container(){

    // Generate structure
    let pio_container = document.createElement("div")
    pio_container.classList.add("pio-container")
    pio_container.id = "pio-container"
    document.body.insertAdjacentElement("beforeend", pio_container)

    // Generate action
    let pio_action = document.createElement("div")
    pio_action.classList.add("pio-action")
    pio_container.insertAdjacentElement("beforeend", pio_action)

    // Generate canvas
    let pio_canvas = document.createElement("canvas")
    pio_canvas.id = "pio"
    pio_container.insertAdjacentElement("beforeend", pio_canvas)

    console.log("[Pio] Initialized container.")
}


function pio_refresh_style(){
    // Always make sure to call this after container/canvas style changes!
    // You can set alignment here, but still you can change it manually.

    let pio_container = document.getElementsByClassName("pio-container").item(0)

    pio_container.classList.remove("left", "right")
    pio_container.classList.add(pio_alignment)

    app.resizeTo = document.getElementById("pio")
}


function _pio_initialize_pixi() {
    // Initialize html elements and pixi app.
    // Must run before pio init.

    _pio_initialize_container()

    app = new PIXI.Application({
        view: document.getElementById("pio"),
        transparent: true,
        autoStart: true,
    })

    pio_refresh_style()
}


// change alignment to left by modifying this value in other script.
// Make sure to call `pio_refresh_style` to apply changes!
let pio_alignment = "right"


let app
window.addEventListener("DOMContentLoaded", _pio_initialize_pixi)
