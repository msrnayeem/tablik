<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tablik</title>

    <!-- Bootstrap JS, Popper.js, and jQuery via CDN -->


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #e9e9e9;
            margin-top: 20px;
        }

        .col-6.text-right p {
            text-align: right;
        }

        .card {
            border-radius: 0;
        }

        .time {
            border: 1px solid black;
            background-image: linear-gradient(93deg, #1f5d90, #41a589);
            min-height: 150px;
            color: white;
            font-size: 30px;

        }

        #eng-date {
            font size: 12.8px;
            font-style: normal;
            font-weight: 700;
            font-family: Roboto;
        }

        #hijri-date {
            font-size: 12px;
            font-weight: 400;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <h5 class="card-title bold">Prayer times in Dhaka</h5>
                            </div>
                            <div class="col-6 text-right">
                                <p class="mb-0" id="eng-date">{{ \Carbon\Carbon::now()->format('j F, Y') }}</p>
                                <p class="mt-0" id="hijri-date">Date</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="card time d-flex justify-content-center align-items-center">
                                <div>Upcoming Prayer </div>
                                <div id="time">time</div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="card">
                                <div class="row text-center">
                                    <div class="col-md-2">
                                        <p>Fajr</p>
                                        <p id="fajr">05:00</p>
                                    </div>
                                    <div class="col-md-2">
                                        <p>Sunrise</p>
                                        <p id="sunrise">06:00</p>
                                    </div>
                                    <div class="col-md-2">
                                        <p>Dhuhr</p>
                                        <p id="dhuhr">12:00</p>
                                    </div>
                                    <div class="col-md-2">
                                        <p>Asr</p>
                                        <p id="asr">15:00</p>
                                    </div>
                                    <div class="col-md-2">
                                        <p>Maghrib</p>
                                        <p id="maghrib">18:00</p>
                                    </div>
                                    <div class="col-md-2">
                                        <p>Isha</p>
                                        <p id="isha">20:00</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            const settings = {
                async: true,
                crossDomain: true,
                url: 'https://muslimsalat.p.rapidapi.com/dhaka.json',
                method: 'GET',
                headers: {
                    'X-RapidAPI-Key': '32041a3a6fmsh3304484591e5f79p1bbe88jsn40477c18c2a5',
                    'X-RapidAPI-Host': 'muslimsalat.p.rapidapi.com'
                }
            };

            // Function to fetch prayer times from the API
            function fetchPrayerTimes() {
                return $.ajax(settings);
            }

            // Function to update the HTML elements with prayer times
            function updatePrayerElements(prayerTimes) {
                $("#fajr").text(prayerTimes.fajr);
                $("#sunrise").text(prayerTimes.shurooq);
                $("#dhuhr").text(prayerTimes.dhuhr);
                $("#asr").text(prayerTimes.asr);
                $("#maghrib").text(prayerTimes.maghrib);
                $("#isha").text(prayerTimes.isha);
            }

            // Function to display upcoming prayer time live along with the time remaining
            function displayUpcomingPrayer(prayerTimes) {
                setInterval(function() {
                    var currentTime = new Date();
                    var currentHours = currentTime.getHours();
                    var currentMinutes = currentTime.getMinutes();
                    var currentSeconds = currentTime.getSeconds();
                    var upcomingPrayer = null;

                    // Determine the upcoming prayer
                    if (currentHours < parseInt(prayerTimes.fajr.split(':')[0]) ||
                        (currentHours === parseInt(prayerTimes.fajr.split(':')[0]) && currentMinutes <
                            parseInt(prayerTimes.fajr.split(':')[1]))) {
                        upcomingPrayer = "Fajr";
                    } else if (currentHours < parseInt(prayerTimes.dhuhr.split(':')[0]) ||
                        (currentHours === parseInt(prayerTimes.dhuhr.split(':')[0]) && currentMinutes <
                            parseInt(prayerTimes.dhuhr.split(':')[1]))) {
                        upcomingPrayer = "Dhuhr";
                    } else if (currentHours < parseInt(prayerTimes.asr.split(':')[0]) ||
                        (currentHours === parseInt(prayerTimes.asr.split(':')[0]) && currentMinutes <
                            parseInt(prayerTimes.asr.split(':')[1]))) {
                        upcomingPrayer = "Asr";
                    } else if (currentHours < parseInt(prayerTimes.maghrib.split(':')[0]) ||
                        (currentHours === parseInt(prayerTimes.maghrib.split(':')[0]) &&
                            currentMinutes < parseInt(prayerTimes.maghrib.split(':')[1]))) {
                        upcomingPrayer = "Maghrib";
                    } else if (currentHours < parseInt(prayerTimes.isha.split(':')[0]) ||
                        (currentHours === parseInt(prayerTimes.isha.split(':')[0]) && currentMinutes <
                            parseInt(prayerTimes.isha.split(':')[1]))) {
                        upcomingPrayer = "Isha";
                    } else {
                        upcomingPrayer = "Fajr";
                    }

                    // Calculate time remaining to the upcoming prayer
                    var upcomingPrayerTime = new Date();
                    upcomingPrayerTime.setHours(parseInt(prayerTimes[upcomingPrayer.toLowerCase()].split(
                        ':')[0]));
                    upcomingPrayerTime.setMinutes(parseInt(prayerTimes[upcomingPrayer.toLowerCase()].split(
                        ':')[1]));
                    upcomingPrayerTime.setSeconds(0); // Reset seconds to zero for accuracy

                    var timeDiff = upcomingPrayerTime - currentTime;
                    var hoursRemaining = Math.floor(timeDiff / (1000 * 60 * 60));
                    var minutesRemaining = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                    var secondsRemaining = Math.floor((timeDiff % (1000 * 60)) / 1000);

                    // Format remaining time as hh:mm:ss
                    var formattedTime = padZero(hoursRemaining) + ":" + padZero(minutesRemaining) + ":" +
                        padZero(secondsRemaining);

                    // Display upcoming prayer time along with time remaining
                    $("#time").text(upcomingPrayer + " - " + formattedTime);
                }, 1000);
            }

            // Helper function to pad zero to single-digit numbers
            function padZero(number) {
                return (number < 10 ? '0' : '') + number;
            }



            // Main function to fetch, update, and display prayer times
            function updatePrayerTimes() {
                fetchPrayerTimes()
                    .done(function(response) {
                        console.log(response.items);
                        var prayerTimes = response.items[0];
                        updatePrayerElements(prayerTimes);
                        displayUpcomingPrayer(prayerTimes);
                    })
                    .fail(function(xhr, status, error) {
                        console.error("Request failed: " + status + " - " + error);
                    });
            }

            // Call the main function to fetch, update, and display prayer times
            updatePrayerTimes();


            $.ajax({
                url: "https://api.aladhan.com/v1/gToH",
                method: "GET",
                success: function(response) {
                    var hijriDay = response.data.hijri.day;
                    var hijriMonth = response.data.hijri.month.en;
                    var hijriYear = response.data.hijri.year;

                    var hijriDate = hijriDay + " " + hijriMonth + ", " + hijriYear;

                    $("#hijri-date").text(hijriDate);
                },
                error: function(xhr, status, error) {
                    // Handle errors here
                    console.error("Error: " + status + " - " + error);
                }
            });

        });

        // function updateClock() {
        //     var now = new Date();
        //     var hours = now.getHours();
        //     var minutes = now.getMinutes();
        //     var seconds = now.getSeconds();
        //     var ampm = hours >= 12 ? 'PM' : 'AM';

        //     // Convert hours to 12-hour format
        //     hours = hours % 12;
        //     hours = hours ? hours : 12; // The hour '0' should be '12' in 12-hour clock

        //     // Format the time to add leading zeros
        //     hours = hours < 10 ? '0' + hours : hours;
        //     minutes = minutes < 10 ? '0' + minutes : minutes;
        //     seconds = seconds < 10 ? '0' + seconds : seconds;

        //     var timeString = hours + ':' + minutes + ':' + seconds + ' ' + ampm;


        //  Update the clock and date elements
        //       document.getElementById('clock').textContent = timeString;

        // }

        // // Update clock every second
        //    setInterval(updateClock, 1000);

        // Call updateClock function immediately to display clock without delay
        // updateClock();
    </script>
</body>

</html>
