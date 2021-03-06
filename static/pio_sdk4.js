/* ----

# Pio SDK 2/3/4 support
# By: jupiterbjy
# Last Update: 2021.3.6

To use this, you need to include following sources to your HTML file first.
Basic usage is same with Paul-Pio.

Make sure to call `pio_refresh_style()` upon changing styles on either *pio-container* or *pio* canvas object.

<script src="https://cubism.live2d.com/sdk-web/cubismcore/live2dcubismcore.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/dylanNew/live2d/webgl/Live2D/lib/live2d.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pixi.js@5.3.6/dist/pixi.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pixi-live2d-display/dist/index.min.js"></script>

---- */


function loadlive2d(canvas, json_object_or_url) {
    // Replaces original l2d method 'loadlive2d' for Pio.

    console.log("[Pio] Loading new model!")

    try {
        app.stage.removeChildAt(0)
    } catch (error) {

    }

    let model = PIXI.live2d.Live2DModel.fromSync(json_object_or_url)

    model.once("load", () => {
        app.stage.addChild(model)

        const canvas_ = document.getElementById("pio")

        const vertical_factor = canvas_.height / model.height
        model.scale.set(vertical_factor)

        // match canvas to model width
        canvas_.width = model.width
        pio_refresh_style()

        // check alignment, and align model to corner
        if (document.getElementsByClassName("pio-container").item(0).className.includes("left")){
            model.x = 0
        } else {
            model.x = canvas_.width - model.width
        }

        // Hit callback definition
        model.on('hit', hitAreas => {
            if (hitAreas.includes('body')) {
                console.log(`[Pio] Touch on body (SDK2)`)
                model.motion('tap_body')

            } else if (hitAreas.includes("Body")) {
                console.log(`[Pio] Touch on body (SDK3/4)`)
                model.motion('Tap')

            } else if (hitAreas.includes("head") || hitAreas.includes("Head")){
                console.log(`[Pio] Touch on head`)
                model.expression()
            }
        })
        console.log(`[Pio] New model h/w dimension: ${model.height} ${model.width}`)
        console.log(`[Pio] New model x/y offset: ${model.x} ${model.y}`)
    })
}


function _pio_initialize_container(){

    // Generate structure
    let pio_container = document.createElement("div")
    pio_container.classList.add("pio-container")
    document.body.insertAdjacentElement("beforeend", pio_container)

    // Generate action
    let pio_action = document.createElement("div")
    pio_action.classList.add("pio-action")
    pio_container.insertAdjacentElement("beforeend", pio_action)

    // Generate canvas
    let pio_canvas = document.createElement("canvas")
    pio_canvas.id = "pio"
    pio_container.insertAdjacentElement("beforeend", pio_canvas)
}


function pio_refresh_style(alignment="right"){
    // Had to separate this from PIXI initialization
    // or first loaded Live2D's size will break on resizing.
    //
    // Always make sure to call this after container/canvas style changes!
    // You can set alignment here, but still you can change it manually.

    let pio_container = document.getElementsByClassName("pio-container").item(0)

    pio_container.classList.remove("left", "right")
    pio_container.classList.add(alignment)

    app.resizeTo = document.getElementById("pio")
}


function _pio_initialize_pixi_app() {

    app = new PIXI.Application({
        view: document.getElementById("pio"),
        transparent: true,
        autoStart: true,
    })
}


function _pio_initialize() {
    _pio_initialize_container()
    _pio_initialize_pixi_app()

    pio_refresh_style()
}

let app
_pio_initialize()