<?php
$posts = 0;
?>

<!DOCTYPE html>
{{-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> --}}
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #025677;
            color: #fff;
        }

        .container {
            padding: 30px 50px;
        }

        #search-form {
            width: 30%;
            margin: 0 auto;
            position: relative;
        }

        #search-form input {
            width: 100%;
            font-size: 1.5rem;
            padding: 10px 15px;
            border: 2px solid #ccc;
            border-radius: 2px;
        }

        #search-form button {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            background-color: transparent;
            outline: none;
            border: none;
            width: 3rem;
            text-align: center;
            font-size: 1.75rem;
            cursor: pointer;
            color: #333;
        }

        .info {
            margin-top: 0.5rem;
            text-align: center;
            font-size: 0.75rem;
        }

        @media (max-width: 1200px) {
            #search-form {
                width: 50%;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 30px 35px;
            }

            #search-form {
                width: 100%;
            }

            .info {
                font-size: 0.5rem;
            }
        }
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
        integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>

</head>

<body class="antialiased">
    <div class="container">

        <form action="{{ route('search') }}" method="get" id="search-form">
            <input type="text" name="keyword" placeholder="Search..." autocomplete="off" autofocus />
            <small id="info" style="color: rgb(255, 0, 0);"></small> <br>
            {{-- <button type="submit">Search</button> --}}
        </form>
    </div>


    @if (session('results'))
    @if (session('results')->isEmpty())
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Sorry! No Records Found</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @else

    @foreach (session('results') as $post)

    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        You searched for "<strong>{{ $post->name }}</strong>" <br> <br>
        {{ $post->description }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    @endforeach
    @endif
    @endif

    @php
    session()->flush();
    @endphp

</body>

<script>
    const searchForm = document.querySelector("#search-form");
    const searchFormInput = searchForm.querySelector("input"); // <=> document.querySelector("#search-form input");
    const info = document.querySelector(".info");

    // The speech recognition interface lives on the browser’s window object
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition; // if none exists -> undefined

    if (SpeechRecognition) {
        console.log("Your Browser supports speech Recognition");

        const recognition = new SpeechRecognition();
        recognition.continuous = true;
        // recognition.lang = "en-NP";

        searchForm.insertAdjacentHTML("beforeend", '<button type="button"><i class="fas fa-microphone"></i></button>');
        searchFormInput.style.paddingRight = "50px";

        const micBtn = searchForm.querySelector("button");
        const micIcon = micBtn.firstElementChild;

        micBtn.addEventListener("click", micBtnClick);

        function micBtnClick() {
            if (micIcon.classList.contains("fa-microphone")) { // Start Voice Recognition
                recognition.start(); // First time you have to allow access to mic!
            } else {
                recognition.stop();
            }
        }

        recognition.addEventListener("start", startSpeechRecognition); // <=> recognition.onstart = function() {...}
        function startSpeechRecognition() {
            micIcon.classList.remove("fa-microphone");
            micIcon.classList.add("fa-microphone-slash");
            searchFormInput.focus();
            console.log("Voice activated, SPEAK");
        }

        recognition.addEventListener("end", endSpeechRecognition); // <=> recognition.onend = function() {...}
        function endSpeechRecognition() {
            micIcon.classList.remove("fa-microphone-slash");
            micIcon.classList.add("fa-microphone");
            searchFormInput.focus();
            console.log("Speech recognition service disconnected");
        }

        recognition.addEventListener("result",
            resultOfSpeechRecognition
        ); // <=> recognition.onresult = function(event) {...} - Fires when you stop talking
        function resultOfSpeechRecognition(event) {
            const current = event.resultIndex;
            const transcript = event.results[current][0].transcript;

            if (transcript.toLowerCase() == "stop") {
                recognition.stop();
                return 0;
            } else if (!searchFormInput.value) {
                searchFormInput.value = transcript;
            } else {
                if (transcript.toLowerCase().trim() == "go") {
                    searchForm.submit();
                } else if (transcript.toLowerCase().trim() == "clear") {
                    searchFormInput.value = "";
                    return 0;
                } else {
                    searchFormInput.value = transcript;
                }
            }
            searchFormInput.value = transcript;
            searchFormInput.focus();
            setTimeout(() => {
                searchForm.submit();
                recognition.stop();
            }, 500);
        }

        // info.textContent = 'Voice Commands: "stop", "clear", "go"';

    } else {
        window.alert('Your Browser does not support Speech Recognition');
        console.log("Your Browser does not support Speech Recognition");
        document.getElementById('info').innerHTML = '*** Your Browser does not support Speech Recognition***';
    }
</script>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
    integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
    integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
</script>


<script>
    if(typeof window.history.pushState == 'function') {
        window.history.pushState({}, "Hide", "http://127.0.0.1:8000/search");
    }
</script>

</html>