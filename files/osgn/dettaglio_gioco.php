<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <title>Game Details</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 45%;
            width: 100%;
            box-sizing: border-box; /* Corrected the property name */
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 10px;
        }

        strong {
            font-weight: bold;
        }

        .error {
            color: red;
        }

        .slideshow {
            height: auto;
            margin-bottom: 20px;
        }

        .slideshow img {
            height: 400px;
            width: auto;
            object-fit: contain;
        }

        .slick-prev, .slick-next {
            background-color: green;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        .slick-prev:hover, .slick-next:hover {
            opacity: 0.8;
        }

        .active {
            background-color: #717171;
        }
    </style>
</head>
<body>
<div class="container">
<?php
// Connessione al database e recuperare i dettagli del gioco basato sull'ID ricevuto dalla query
$servername = "tmhome.tplinkdns.com";
$username = "dbview";
$password = "viewpsw";
$dbname = "tdbosgn";
$port = "11506";

// Connessione al database
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Controlla la connessione
if ($conn->connect_error) {
    die("<p class='error'>Connection failed: " . $conn->connect_error . "</p>");
}

// Ricevi l'ID del gioco dalla richiesta GET
if(isset($_GET['id'])) {
    $game_id = intval($_GET['id']); // Use intval to sanitize input

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT name, id, tag, date, des, meta FROM games WHERE id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Controlla se ci sono risultati
    if ($result->num_rows > 0) {
        // Output dei dettagli del gioco
        while($row = $result->fetch_assoc()) {
            echo "<h2>".$row["name"]."</h2>";
            echo "<p><strong>ID:</strong> ".$row["id"]."</p>";
            echo "<p><strong>Tag:</strong> ".$row["tag"]."</p>";
            echo "<p><strong>Date:</strong> ".$row["date"]."</p>";
            $description = str_replace('/n', '<br>', $row["des"]); // Replace "/n" with "<br>"
            echo "<p><strong>Description:</strong> ".$description."</p>";

            // Controllo se ci sono foto/video allegati
            if (substr($row["meta"], 0, 1) === '#') {
                $attachments = explode('#', $row["meta"]);
                if (count($attachments) > 1) {
                    echo "<div class='slideshow'>";
                    foreach ($attachments as $index => $attachment) {
                        if ($index !== 0) {
                            if (strpos($attachment, 'youtube.com') !== false || strpos($attachment, 'youtu.be') !== false) {
                                $video_id = '';
                                parse_str(parse_url($attachment, PHP_URL_QUERY), $video_params);
                                if (isset($video_params['v'])) {
                                    $video_id = $video_params['v'];
                                }
                                if ($video_id) {
                                    echo "<div><iframe width='100%' height='315' src='https://www.youtube.com/embed/$video_id' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe></div>";
                                }
                            } else {
                                echo "<div><img src='$attachment' style='width:100%'></div>";
                            }
                        }
                    }
                    echo "</div>";
                }
            }
        }
    } else {
        echo "<p class='error'>No game details found</p>";
    }

    $stmt->close(); // Close the statement
}

// Chiudi la connessione al database
$conn->close();
?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
<script>
    $(document).ready(function(){
        $('.slideshow').slick({
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            arrows: false,
            dots: true,
        });

        var contentHeight = document.querySelector('.container').offsetHeight;
        var slideshowHeight = document.querySelector('.slideshow').offsetHeight;
        var totalHeight = contentHeight + slideshowHeight;
        document.querySelector('.container').style.minHeight = totalHeight + 'px';
    });
</script>
</body>
</html>
