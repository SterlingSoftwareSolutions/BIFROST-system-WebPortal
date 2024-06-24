<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    @extends('mobile.layout.mobile-layout')

    @section('content')
        <div class="w-full  flex flex-col justify-between h-screen h-100%">
            <div class="flex-grow items-center justify-center m-0 p-4 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('img/valhalla-bg.jpg') }}');">
                <div class="justify-center items-center text-white text-center h-full pt-20">
                        <div class="text-xl mb-5">Conditioning (Cardio)</div>
                        <div class="text-base mb-5">AMRAP</div>
                        <div class="text-base mb-12">Rounds</div>
                        <div class="border-b border-white mb-12"></div>
                        <div class="mb-16 text-xl">
                            <div class="mb-4">Box Jumps x10</div>
                            <div class="mb-4">Push Ups x10</div>
                            <div>Farmers Carry 40m</div>
                        </div>
                        <div class="flex justify-center items-center mb-10">
                            <button id="decreaseRounds" class="bg-transparent border border-white text-3xl text-white w-16 h-20 cursor-pointer">-</button>
                            <input  id="roundsInput" value="1" min="1" class="w-24 h-20 text-center text-3xl text-black">
                            <button id="increaseRounds" class="bg-transparent border border-white text-3xl text-white w-16 h-20 cursor-pointer">+</button>
                        </div>
                        <div class="mb-0">
                            <div class="text-base mb-2">TIME TO COMPLETE</div>
                            <div id="timer" class="text-5xl text-black bg-white p-2">00:00:00</div>
                            <div class="flex w-full">
                                <button id="startButton" class="w-1/2 h-16 bg-green-600 text-white cursor-pointer ">START</button>
                                <button id="stopButton" class="w-1/2 h-16 bg-red-600 text-white cursor-pointer">STOP</button>
                            </div>  
                        </div>
                </div>
            </div>
        </div>
       
        <script>
            class Timer 
                {
                    constructor(displayElement) 
                    {
                        this.displayElement = displayElement;
                        this.seconds = 0;
                        this.timer = null;
                    }
                        
                    updateDisplay() 
                    {
                        const hrs = Math.floor(this.seconds / 3600);
                        const mins = Math.floor((this.seconds % 3600) / 60);
                        const secs = this.seconds % 60;
                        this.displayElement.textContent = 
                        `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                    }
                        
                    start() 
                    {
                        clearInterval(this.timer);
                            this.timer = setInterval(() => {
                            this.seconds++;
                            this.updateDisplay();
                                    }, 1000);
                    }
                        
                    stop() 
                    {
                        clearInterval(this.timer);
                     }
                }
                        
                    const timerDisplay = document.getElementById('timer');
                    const timer = new Timer(timerDisplay);
                        
                    document.getElementById('startButton').addEventListener('click', () => timer.start());
                    document.getElementById('stopButton').addEventListener('click', () => timer.stop());
                        
                    const increaseRoundsButton = document.getElementById('increaseRounds');
                    const decreaseRoundsButton = document.getElementById('decreaseRounds');
                    const roundsInput = document.getElementById('roundsInput');
                        
                    increaseRoundsButton.addEventListener('click', () => {
                        roundsInput.value = parseInt(roundsInput.value) + 1;
                    });
                        
                    decreaseRoundsButton.addEventListener('click', () => 
                    {
                        if (roundsInput.value > 1)
                            {
                                roundsInput.value = parseInt(roundsInput.value) - 1;
                            }
                    });

        </script>
    @endsection
</body>


</html>
