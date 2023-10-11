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
                                        <p id="fajr">00:00</p>
                                    </div>
                                    <div class="col-md-2">
                                        <p>Sunrise</p>
                                        <p id="shurooq">00:00</p>
                                    </div>
                                    <div class="col-md-2">
                                        <p>Dhuhr</p>
                                        <p id="dhuhr">00:00</p>
                                    </div>
                                    <div class="col-md-2">
                                        <p>Asr</p>
                                        <p id="asr">00:00</p>
                                    </div>
                                    <div class="col-md-2">
                                        <p>Maghrib</p>
                                        <p id="maghrib">00:00</p>
                                    </div>
                                    <div class="col-md-2">
                                        <p>Isha</p>
                                        <p id="isha">00:00</p>
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


            var keyValueArray = [];
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

            var prayerTimesArray = [];

            $.ajax(settings).done(function(response) {
                var prayerTimes = response.items[0];
                var isFirstItem = true;

                for (var key in prayerTimes) {
                    if (prayerTimes.hasOwnProperty(key)) {
                        if (isFirstItem) {
                            isFirstItem = false;
                            continue; // Skip the first key-value pair
                        }

                        prayerTimesArray.push({
                            key: key,
                            value: prayerTimes[key]
                        });
                    }
                }

                updatePrayerElements(prayerTimesArray);

                var upcomingPrayerInfo = findUpcomingPrayer(prayerTimesArray);

            });

            function updatePrayerElements(prayerTimesArray) {
                for (var i = 0; i < prayerTimesArray.length; i++) {
                    var key = prayerTimesArray[i].key;
                    var value = prayerTimesArray[i].value;
                    $("#" + key).text(value);
                }
            }


            function findUpcomingPrayer(prayerTimesArray) {
                var currentTime = new Date();
                var upcomingPrayerTime;
                var upcomingPrayerName;

                for (var i = 0; i < prayerTimesArray.length; i++) {
                    var prayerTime = new Date(prayerTimesArray[i].value);

                    // Check if the prayer time is in the future
                    if (prayerTime > currentTime) {
                        upcomingPrayerTime = prayerTime;
                        upcomingPrayerName = prayerTimesArray[i].key;
                        break;
                    }
                }

                if (upcomingPrayerTime) {
                    var timeDifference = upcomingPrayerTime - currentTime;
                    var hoursRemaining = Math.floor(timeDifference / (1000 * 60 * 60));
                    var minutesRemaining = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
                    var secondsRemaining = Math.floor((timeDifference % (1000 * 60)) / 1000);
                    time
                    console.log("Upcoming Prayer: " + upcomingPrayerName);
                    console.log("Time remaining: " + hoursRemaining + "h " + minutesRemaining + "m " +
                        secondsRemaining + "s");
                } else {
                    //calculate esha to fajr difference
                    var currentTime = new Date();
                    var currentHours = currentTime.getHours();
                    var currentMinutes = currentTime.getMinutes();

                    var currentTimeInMin = currentHours * 60 + currentMinutes;
                    var nextDayFajr = convertToMinutes(prayerTimesArray[0].value);


                    var remainingTime = nextDayFajr - currentTimeInMin + (24 * 60);
                    // Convert remaining time back to hours and minutes
                    var remainingHours = Math.floor(remainingTime / 60);
                    var remainingMinutes = remainingTime % 60;

                    // Add leading zeros if necessary
                    if (remainingHours < 10) {
                        remainingHours = "0" + remainingHours;
                    }

                    if (remainingMinutes < 10) {
                        remainingMinutes = "0" + remainingMinutes;
                    }

                    // Create the formatted time string
                    var time = remainingHours + " : " + remainingMinutes;

                    updateClock("Fajr : ", time);
                }
            }


            function convertToMinutes(timeString) {
                var timeArray = timeString.split(":");
                var hours = parseInt(timeArray[0], 10);
                var minutes = parseInt(timeArray[1], 10);
                return hours * 60 + minutes;
            }

            function updateClock(prayername, timeString) {
                // Split the time string into hours and minutes
                var timeArray = timeString.split(" : ");
                var hours = parseInt(timeArray[0], 10);
                var minutes = parseInt(timeArray[1], 10);

                // Update the clock every second
                var interval = setInterval(function() {
                    // Decrease one second
                    seconds--;
                    if (seconds < 0) {
                        seconds = 59;
                        if (minutes === 0) {
                            minutes = 59;
                            if (hours === 0) {
                                // Stop the clock when time is up
                                clearInterval(interval);
                                console.log("Time's up!");
                            } else {
                                hours--;
                            }
                        } else {
                            minutes--;
                        }
                    }

                    // Format hours, minutes, and seconds with leading zeros if necessary
                    var Hours = hours < 10 ? "0" + hours : hours;
                    var Minutes = minutes < 10 ? "0" + minutes : minutes;
                    var Seconds = seconds < 10 ? "0" + seconds : seconds;

                    // Update the clock display
                    $("#time").text(prayername + Hours + " : " + Minutes + " : " + Seconds);

                }, 1000); // Update every second (1000 milliseconds)

                // Initial seconds
                var seconds = 0;
            }




        });
    </script>
</body>

</html>
