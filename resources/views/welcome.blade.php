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
                var prayerTimesArray = [];

                for (var key in prayerTimes) {
                    if (prayerTimes.hasOwnProperty(key) && !isFirstItem) {
                        prayerTimesArray.push(prayerTimes[key]);
                    }
                    isFirstItem = false;
                }
                updatePrayerElements(prayerTimesArray);
                var convertedTimesArray = convertTo24HourFormat(prayerTimesArray);


                var nextTime = findNextTime(convertedTimesArray);
                var prayerName = getPrayerName(convertedTimesArray, nextTime.time);
                console.log(prayerName);
                console.log(nextTime);

                //  var convertedTime12hr = convertTo12HourFormat(nextTime);
                var timeDifference = calculateTimeDifference(nextTime.time, nextTime.isNextDay);
                updateClock(prayerName, timeDifference.hours + " : " + timeDifference.minutes);
            });

            function calculateTimeDifference(inputTime, isNextDay) {
                var inputHours = parseInt(inputTime.split(':')[0], 10);
                var inputMinutes = parseInt(inputTime.split(':')[1], 10);

                var currentDate = new Date();
                var currentHours = currentDate.getHours();
                var currentMinutes = currentDate.getMinutes();

                // Convert current time to minutes for easier comparison
                var currentTimeInMinutes = currentHours * 60 + currentMinutes;
                var inputTimeInMinutes = inputHours * 60 + inputMinutes;
                if (isNextDay) {
                    inputTimeInMinutes += 24 * 60;
                }
                // Calculate the time difference in minutes
                var timeDifferenceInMinutes = inputTimeInMinutes - currentTimeInMinutes;

                // Convert the time difference back to hours and minutes
                var hoursDifference = Math.floor(timeDifferenceInMinutes / 60);
                var minutesDifference = timeDifferenceInMinutes % 60;

                return {
                    hours: hoursDifference,
                    minutes: minutesDifference
                };
            }

            function findNextTime(timesArray) {
                var currentDate = new Date();
                var currentHours = currentDate.getHours();
                var currentMinutes = currentDate.getMinutes();

                // Ensure current hours and minutes are in two-digit format
                currentHours = currentHours < 10 ? '0' + currentHours : currentHours;
                currentMinutes = currentMinutes < 10 ? '0' + currentMinutes : currentMinutes;

                // Current time in HH:mm format
                var currentTime = currentHours + ':' + currentMinutes;
                var nextDay = false;
                // Find the next upcoming time from the timesArray
                var nextTime = null;
                for (var i = 0; i < timesArray.length; i++) {
                    if (timesArray[i] > currentTime) {
                        nextTime = timesArray[i];
                        break;
                    }
                }
                if (!nextTime) {
                    nextTime = timesArray[0];
                    nextDay = true;
                }

                return {
                    time: nextTime,
                    isNextDay: nextDay
                }
            }

            function getPrayerName(timesArray, specificTime) {
                var prayerNames = ['Fajr', 'Shurooq', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];

                // Find the index of the specific time in the timesArray
                var timeIndex = timesArray.indexOf(specificTime);

                // Determine the prayer name based on the time index
                var prayerName = 'Unknown';
                if (timeIndex >= 0 && timeIndex < prayerNames.length) {
                    prayerName = prayerNames[timeIndex];
                }

                return prayerName;
            }

            function convertTo24HourFormat(timeArray) {
                var convertedArray = [];

                for (var i = 0; i < timeArray.length; i++) {
                    var time12hr = timeArray[i];
                    var [time, period] = time12hr.split(' ');
                    var [hours, minutes] = time.split(':');

                    hours = parseInt(hours, 10);
                    minutes = parseInt(minutes, 10);

                    if (period === 'pm' || period === 'PM') {
                        if (hours !== 12) {
                            hours += 12;
                        }
                    } else {
                        if (hours === 12) {
                            hours = 0;
                        }
                    }

                    // Ensure hours and minutes are in two-digit format
                    hours = hours < 10 ? '0' + hours : hours;
                    minutes = minutes < 10 ? '0' + minutes : minutes;

                    // Construct the time string in 24-hour format
                    var time24hr = hours + ':' + minutes;
                    convertedArray.push(time24hr);
                }

                return convertedArray;
            }

            function updatePrayerElements(prayerTimesArray) {

                var prayerIds = ['fajr', 'shurooq', 'dhuhr', 'asr', 'maghrib', 'isha'];

                for (var i = 0; i < prayerTimesArray.length; i++) {
                    $("#" + prayerIds[i]).text(prayerTimesArray[i]);
                }
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
                    $("#time").text(prayername + "- " + Hours + " : " + Minutes + " : " + Seconds);

                }, 1000); // Update every second (1000 milliseconds)

                // Initial seconds
                var seconds = 0;
            }

        });
    </script>
</body>

</html>
