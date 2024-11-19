<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signature Pad</title>
    <style>
        .signature-pad {
            border: 1px solid #000;
            width: 100%;
            height: 300px;
        }
    </style>
</head>
<body>
    <h2>Sign Here:</h2>
    <canvas id="signaturePad" class="signature-pad"></canvas>
    <br>
    <button id="clearBtn">Clear</button>
    <button id="saveBtn">Save</button>

    <script>
        // Set up canvas
        var canvas = document.getElementById("signaturePad");
        var ctx = canvas.getContext("2d");
        var isDrawing = false;

        // Set canvas size
        canvas.width = 500;
        canvas.height = 200;

        // Handle mouse events for drawing
        canvas.addEventListener("mousedown", startDrawing);
        canvas.addEventListener("mousemove", draw);
        canvas.addEventListener("mouseup", stopDrawing);
        canvas.addEventListener("mouseleave", stopDrawing);

        function startDrawing(e) {
            isDrawing = true;
            draw(e);
        }

        function draw(e) {
            if (!isDrawing) return;
            ctx.lineWidth = 3;
            ctx.lineCap = "round";
            ctx.strokeStyle = "#000";

            ctx.lineTo(e.clientX - canvas.offsetLeft, e.clientY - canvas.offsetTop);
            ctx.stroke();
        }

        function stopDrawing() {
            isDrawing = false;
            ctx.beginPath();
        }

        // Clear the canvas
        document.getElementById("clearBtn").addEventListener("click", function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        // Save the signature and send it to the server
        document.getElementById("saveBtn").addEventListener("click", function() {
            var dataUrl = canvas.toDataURL("image/png");
            var formData = new FormData();
            formData.append("signature", dataUrl);

            fetch("save_signature.php", {
                method: "POST",
                body: formData
            }).then(response => response.json())
              .then(data => {
                  alert(data.message);
              }).catch(error => {
                  console.error("Error:", error);
              });
        });
    </script>
</body>
</html>
