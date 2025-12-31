<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEURAL-SYNC AI OPTIMIZER v4.0</title>
    <style>
        :root {
            --bad-gradient: linear-gradient(45deg, #ff00ff, #00ffff, #ff00ff, #ffff00);
            --ai-glow: 0 0 20px rgba(0, 255, 255, 0.8), 0 0 40px rgba(255, 0, 255, 0.4);
        }

        body {
            margin: 0;
            padding: 0;
            background: radial-gradient(circle at center, #1a1a2e 0%, #0f0f1a 100%);
            color: white;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
        }

        /* Excessive AI background mesh effect */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(0, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 30px 30px;
            z-index: -1;
        }

        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            padding: 50px;
            border-radius: 30px;
            backdrop-filter: blur(15px);
            border: 2px solid transparent;
            background-clip: padding-box;
            box-shadow: var(--ai-glow);
            border-image: var(--bad-gradient) 1;
        }

        h1 {
            font-size: 3rem;
            text-transform: uppercase;
            letter-spacing: 5px;
            background: var(--bad-gradient);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.3));
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 0.8rem;
            color: #00ffff;
            text-transform: uppercase;
            margin-bottom: 40px;
            letter-spacing: 2px;
            opacity: 0.8;
        }

        #counter {
            font-size: 8rem;
            font-weight: 900;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            text-shadow: 0 0 30px #00ffff;
            background: linear-gradient(to bottom, #fff, #666);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .ai-btn {
            background: var(--bad-gradient);
            background-size: 300% 300%;
            border: none;
            padding: 20px 50px;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            text-transform: uppercase;
            transition: 0.3s;
            animation: gradientMove 3s infinite alternate;
            box-shadow: 0 10px 30px rgba(255, 0, 255, 0.5);
        }

        .ai-btn:hover {
            transform: scale(1.1) rotate(2deg);
            box-shadow: 0 0 50px rgba(0, 255, 255, 1);
        }

        .ai-btn:active {
            transform: scale(0.95);
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }

        .status-bar {
            margin-top: 30px;
            font-size: 12px;
            color: #aaa;
        }

        .pulsing-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            background: #39ff14;
            border-radius: 50%;
            margin-right: 5px;
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.3; transform: scale(1.5); }
            100% { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

    <div class="container">
        <img src="/img/fry.png" class="mx-auto mb-3" alt="Logo" style="width: 180px; object-fit: contain;">
        <h1>Quantum Ultra Gooning Simulator</h1>
        <div class="subtitle">Next-Gen Synthetic Neural Synergy Interface</div>
        
        <div id="counter">000</div>

        <button class="ai-btn" onclick="incrementCounter()">
            Goon more 🫱🍆
        </button>

        <div class="status-bar">
            <span class="pulsing-dot"></span> 
            SYSTEM STATUS: RECURSIVE LEARNING ACTIVE | VERSION: 9.0.4-BETA
        </div>
        
       <div class="status-bar">
            warning: NOT AI CODE! GOONING IS A HUMAN ACTIVITY AND CANNOT BE REPLICATED BY MACHINES.
        </div>
    </div>
    </div>

    <script>
        let count = 0;
        const counterDisplay = document.getElementById('counter');

        function incrementCounter() {
            count++;
            // Keeps the "AI" padding look (001, 002, etc)
            counterDisplay.innerText = count.toString().padStart(3, '0');
            
            // Random jitter effect on click for that "AI glitch" feel
            document.body.style.backgroundColor = count % 2 === 0 ? '#1a1a2e' : '#1f1a2e';
        }
    </script>
</body>
</html>