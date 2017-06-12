var captionLength = 0;
var caption = '';


document.addEventListener("DOMContentLoaded", function(event) {
    captionEl = document.getElementById('console');
    testTypingEffect();
});

function testTypingEffect() {
    caption = document.getElementById('console').innerHTML;
    type();
}

function type() {
    captionEl.innerHTML = caption.substr(0, captionLength++);
    window.scrollTo(0,document.body.scrollHeight);
    if(captionLength < caption.length+1) {
        setTimeout('type()', Math.random() * 2);
    } else {
        captionLength = 0;
        caption = '';
    }
}