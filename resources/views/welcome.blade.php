<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Image Classification</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap core CSS -->
    <link href="/css/app.css" rel="stylesheet">

    <style>
        #btn2 {
            position: sticky;
            top: 50%;
            left: 50%;
            width: 24%;
            transform: translate(-50%, -50%);
        }

    </style>
</head>

<body class="antialiased">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 p-0">
                <div class="jumbotron min-vh-100 text-center m-0 bg-info d-flex flex-column justify-content-center">
                    <h3 class="display-5">Image Classification of Rice Disease</h3>
                    <p class="lead">This is a Image Classification .</p>
                    <p class="lead" id="webcam-container"></p>
                    <div class="lead">
                        <input id="btn2" type="file" class="form-control" id="inputGroupFile01">
                        <button id="btn" class="btn btn-primary btn-lg w-25 m-2" type="button" onclick="play()">Start
                            Webcam</button>
                    </div>
                    <p class="lead" id="label-container"></p>
                </div>
            </div>
        </div>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@1.3.1/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@0.8/dist/teachablemachine-image.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script type="text/javascript">
    // More API functions here:
    // https://github.com/googlecreativelab/teachablemachine-community/tree/master/libraries/image

    // the link to your model provided by Teachable Machine export panel
    const URL = "./my_model/";

    let model, webcam, labelContainer, maxPredictions, canvasImage;

    // Load the image model and setup the webcam
    async function init() {
        const modelURL = URL + "model.json";
        const metadataURL = URL + "metadata.json";

        // load the model and metadata
        // Refer to tmImage.loadFromFiles() in the API to support files from a file picker
        // or files from your local hard drive
        // Note: the pose library adds "tmImage" object to your window (window.tmImage)
        model = await tmImage.load(modelURL, metadataURL);
        maxPredictions = model.getTotalClasses();

        const flip = true; // whether to flip the webcam
        webcam = new tmImage.Webcam(200, 200, flip); // width, height, flip
    }

    init();
    let isNotPlay = true;


    async function play() {
        if (isNotPlay) {
            // Convenience function to setup a webcam
            await webcam.setup(); // request access to the webcam
            await webcam.play();

            document.getElementById("btn").innerHTML = "Stop Webcam";
            document.getElementById("btn2").style.display = 'none';;
            
            isNotPlay = false;
            window.requestAnimationFrame(loop);
            // append elements to the DOM
            canvasImage = document.getElementById("webcam-container").appendChild(webcam.canvas);
            labelContainer = document.getElementById("label-container").appendChild(document.createElement("div"));
            for (let i = 0; i < maxPredictions && !isNotPlay; i++) { // and class labels
                labelContainer.appendChild(document.createElement("div"));
            }
        } else {
            isNotPlay = true;
            await webcam.stop();
            canvasImage.remove();
            labelContainer.remove();
            document.getElementById("btn2").style.display = 'block';;
            document.getElementById("btn").innerHTML = "Start Webcam";
        }
    }

    async function getImage() {

    }

    async function loop() {
        webcam.update(); // update the webcam frame
        await predict();
        window.requestAnimationFrame(loop);
    }

    // run the webcam image through the image model
    async function predict() {
        // predict can take in an image, video or canvas html element
        const prediction = await model.predict(webcam.canvas);
        for (let i = 0; i < maxPredictions; i++) {
            const classPrediction = prediction[i].className + ": " + prediction[i].probability.toFixed(2);
            labelContainer.childNodes[i].innerHTML = classPrediction;
        }

    }
</script>

<script src="/js/app.js"></script>

</html>
