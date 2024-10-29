// Check if the browser supports the Web Speech API
if (!('webkitSpeechRecognition' in window)) {
    alert("Your browser doesn't support voice recognition.");
} else {
    const recognition = new webkitSpeechRecognition();
    recognition.lang = 'en-US';
    recognition.interimResults = false;
    recognition.continuous = false;

    // Track speech recognition state using sessionStorage
    let isRecognitionActive = sessionStorage.getItem('isRecognitionActive') === 'true';

    // Get the button and text elements
    const recognitionButton = document.getElementById('voice-recognition-btn');
    const buttonText = document.getElementById('button-text');

    // Set initial button text based on session state
    if (buttonText) {
        buttonText.textContent = isRecognitionActive ? 'Disable Voice' : 'Enable Voice';
    }

    // Toggle speech recognition on button click
    recognitionButton?.addEventListener('click', function () {
        if (isRecognitionActive) {
            recognition.stop(); // Stop recognition
            isRecognitionActive = false;
            if (buttonText) buttonText.textContent = 'Enable Voice';
            sessionStorage.setItem('isRecognitionActive', 'false'); // Save state
        } else {
            recognition.start(); // Start recognition
            isRecognitionActive = true;
            if (buttonText) buttonText.textContent = 'Disable Voice';
            sessionStorage.setItem('isRecognitionActive', 'true'); // Save state
        }
    });

    // Start recognition if session state is active
    if (isRecognitionActive) {
        recognition.start();
    }

    // Handle recognition results
    recognition.onresult = function (event) {
        const spokenText = event.results[0][0].transcript.toLowerCase();
        console.log('User said:', spokenText);

        // Define voice commands and navigation actions
        if (spokenText.includes('courses')) {
            window.location.href = 'courses.php';
        } else if (spokenText.includes('profile')) {
            window.location.href = 'profile.php';
        } else if (spokenText.includes('home')) {
            window.location.href = 'home.php';
        } else {
            alert("Command not recognized. Please try again.");
        }
    };

    // Restart recognition if it ends while active
    recognition.onend = function () {
        if (isRecognitionActive) {
            recognition.start(); // Automatically restart if still active
        }
    };

    // Handle errors and reset state if necessary
    recognition.onerror = function (event) {
        console.error('Speech recognition error:', event.error);
        alert('An error occurred. Please try again.');
        isRecognitionActive = false; // Reset state on error
        if (buttonText) buttonText.textContent = 'Enable Voice'; // Update button text
        sessionStorage.setItem('isRecognitionActive', 'false'); // Save state
    };
}
