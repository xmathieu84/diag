document.addEventListener('DOMContentLoaded', () => {
    replaceChecks(false);
})



var is64;
var inputs;
var states = ['On.png', 'Off.png', 'DownOn.png', 'DownOff.png', 'RollOn.png', 'RollOff.png'];
var states64 = ['imageOn', 'imageOff', 'imageDownOn', 'imageDownOff', 'imageRollOn', 'imageRollOff'];

function setImage(input, state) {
    if (inputs[input].getAttribute('images').charAt(state) === '1') {
        document.getElementById(inputs[input].getAttribute('id') + "_img").src = getSrc(input, state);
    }
}

function getSrc(input, state) {
    var src;
    if (is64) {
        src = inputs[input].getAttribute(states64[state]);
    } else {
        src = inputs[input].getAttribute('imageName') + states[state];
    }
    return src;
}

function replaceChecks(isBase64) {

    is64 = isBase64;
    //get all the input fields on the page
    inputs = document.getElementsByTagName('input');

    //cycle trough the input fields
    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].hasAttribute('images'))

            //check if the input is a checkbox
            if (inputs[i].getAttribute('class') !== 'idr-hidden' && inputs[i].getAttribute('data-imageAdded') !==
                'true' &&
                (inputs[i].getAttribute('type') === 'checkbox' || inputs[i].getAttribute('type') === 'radio')) {

                //create a new image
                var img = document.createElement('img');

                //check if the checkbox is checked
                if (inputs[i].checked) {
                    if (inputs[i].getAttribute('images').charAt(0) === '1')
                        img.src = getSrc(i, 0);
                } else {
                    if (inputs[i].getAttribute('images').charAt(1) === '1')
                        img.src = getSrc(i, 1);
                }

                //set image ID
                img.id = inputs[i].getAttribute('id') + "_img";

                //set action associations
                img.onclick = new Function('checkClick(' + i + ')');
                img.onmousedown = new Function('checkDown(' + i + ')');
                img.onmouseover = new Function('checkOver(' + i + ')');
                img.onmouseup = new Function('checkRelease(' + i + ')');
                img.onmouseout = new Function('checkRelease(' + i + ')');

                img.style.position = "absolute";
                var style = window.getComputedStyle(inputs[i]);
                img.style.top = style.top;
                img.style.left = style.left;
                img.style.width = style.width;
                img.style.height = style.height;
                img.style.zIndex = style.zIndex;

                //place image in front of the checkbox
                inputs[i].parentNode.insertBefore(img, inputs[i]);
                inputs[i].setAttribute('data-imageAdded', 'true');

                //hide the checkbox
                inputs[i].style.display = 'none';
            }
    }
}

//change the checkbox status and the replacement image
function checkClick(i) {
    if (!inputs[i].hasAttribute('images')) return;
    if (inputs[i].checked) {
        inputs[i].checked = '';
        setImage(i, 1);
    } else {
        inputs[i].checked = 'checked';

        setImage(i, 0);

        if (inputs[i].getAttribute('name') !== null) {
            for (var index = 0; index < inputs.length; index++) {
                if (index !== i && inputs[index].getAttribute('name') === inputs[i].getAttribute('name')) {
                    inputs[index].checked = '';
                    setImage(index, 1);
                }
            }
        }
    }
}

function checkRelease(i) {
    if (!inputs[i].hasAttribute('images')) return;
    if (inputs[i].checked) {
        setImage(i, 0);
    } else {
        setImage(i, 1);
    }
}

function checkDown(i) {
    if (!inputs[i].hasAttribute('images')) return;
    if (inputs[i].checked) {
        setImage(i, 2);
    } else {
        setImage(i, 3);
    }
}

function checkOver(i) {
    if (!inputs[i].hasAttribute('images')) return;
    if (inputs[i].checked) {
        setImage(i, 4);
    } else {
        setImage(i, 5);
    }
}
replaceChecks(false);