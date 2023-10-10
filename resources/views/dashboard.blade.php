<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900">
                    <h1>Date and Time in English and Arabic</h1>

                    <!-- Display English Formatted Date and Time -->
                    <p>
                        <strong>English Date and Time:</strong> {{ \Carbon\Carbon::now()->isoFormat('LLLL') }}
                    </p>

                    <!-- Set Locale to Arabic and Display Arabic Formatted Date and Time -->
                    <p>
                        <strong>Arabic Date and Time:</strong>
                        {{ $date }}
                    </p>
                </div>
            </div>
        </div>

        <style>
            #clock,
            #date {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 10px;
            }
        </style>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class=" overflow-hidden shadow-sm sm:rounded-lg"
                style="    background-image: linear-gradient(93deg, #1f5d90, #41a589);">
                <div class="p-6 text-gray-900 text-center">
                    <div id="clock"></div>
                    <div id="date"></div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
    crossorigin="anonymous"></script>
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

        $.ajax(settings)
            .done(function(response) {
                console.log(response.items);
            })
            .fail(function(xhr, status, error) {
                console.error("Request failed: " + status + " - " + error);
            });
    });

    function updateClock() {
        var now = new Date();
        var hours = now.getHours();
        var minutes = now.getMinutes();
        var seconds = now.getSeconds();

        // Format the time to add leading zeros
        hours = hours < 10 ? '0' + hours : hours;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;

        var timeString = hours + ':' + minutes + ':' + seconds;
        var dateString = now.toDateString();

        // Update the clock and date elements
        document.getElementById('clock').textContent = timeString;
        document.getElementById('date').textContent = dateString;
    }

    // Update clock every second
    setInterval(updateClock, 1000);

    // Call updateClock function immediately to display clock without delay
    updateClock();
</script>
