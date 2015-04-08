

var text = "Easily add an anonymous chat to your event, everyone can watch it live.";
var charCount = text.length;
var currentLetterCount = 0;
var speed = 30; // How fast should it type?
var $input = $(".description");

function writeLetter() {
    var currentText = $input.text();
    var currentLetter = text.charAt(currentLetterCount);
    currentLetterCount++;
    $input.text(currentText + currentLetter);
    if(currentLetterCount == charCount)
        clearInterval(timerId);
}

var timerId = setInterval(writeLetter, speed);
