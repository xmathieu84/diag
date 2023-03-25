var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab

function goToTab(n){
	nextPrev(n - currentTab);
}

function showTab(n) { // This function will display the specified tab of the form...
	var x = document.getElementsByClassName("tab");
	x[n].style.display = "block";
	// ... and fix the Previous/Next buttons:
	if (n == 0) {
		document.getElementById("prevBtn").style.display = "none";
	} else {
		document.getElementById("prevBtn").style.display = "inline";
	}
	if (n == (x.length - 1)) {
		document.getElementById("nextBtn").innerHTML = "Envoyer";
	} else {
		document.getElementById("nextBtn").innerHTML = "Suivant";
	}
	// ... and run a function that will display the correct step indicator:
	fixStepIndicator(n);
	document.querySelector('.container').scrollIntoView();
}

function nextPrev(n) { // This function will figure out which tab to display
	var x = document.getElementsByClassName("tab");
	// Exit the function if any field in the current tab is invalid:
	if (n >= 1 && !validateForm()) 
		return false;

	// if you have reached the end of the form...
	if ((currentTab + n) >= x.length) { // ... the form gets submitted:
		document.querySelector('form').submit();
		return false;
	} else {
		// Hide the current tab:
		x[currentTab].style.display = "none";
		currentTab = currentTab + n;
		// Otherwise, display the correct tab:
		showTab(currentTab);
	}
	
}

function validateForm() { // This function deals with validation of the form fields
	var x, y, i, valid = true;
	x = document.getElementsByClassName("tab");
	y = x[currentTab].querySelectorAll("input,select");

	let validRadioGroups = [];
	// A loop that checks every input field in the current tab:
	for (i = 0; i < y.length; i++) { 
		// If a field is a radio button...
		if(y[i].type == "radio" && y[i].required && !(y[i].name in validRadioGroups)){
			let radioGroupName = y[i].name;
			let radioGroup = document.getElementsByName(radioGroupName);
			let checked = false;
			for (var j = radioGroup.length - 1; j >= 0; j--) {
				if(radioGroup[j].checked) {

					checked = true;
					validRadioGroups.push(radioGroupName);
					document.querySelector('legend[for="'+ radioGroupName +'"]').classList.remove('text-danger');
				}
			}
			if(!checked){
				valid = false;
				document.querySelector('legend[for="'+ radioGroupName +'"]').classList.add('text-danger')
				document.querySelector('legend[for="'+ radioGroupName +'"]').scrollIntoView({block: "center"});
				document.getElementsByClassName("fa-check")[currentTab].classList.add("d-none");
				return;
			}
			
		} else if (y[i].value == "") {
			// add an "invalid" class to the field:

			// and set the current valid status to false
			// if fields is required and value is empty, return false, otherwise, valid is true
			if (y[i].required && y[i].value == "") {
				y[i].classList.add("invalid");
				y[i].scrollIntoView({block: "center"});
				document.getElementsByClassName("fa-check")[currentTab].classList.add("d-none");
				valid = false;
				return;
			}
			
		} else if(y[i].required && y[i].classList.contains('confirm_password')){
			let passInput = x[currentTab].querySelector('.password');
			if(passInput.value !== y[i].value){
				y[i].classList.add("invalid");
				x[currentTab].querySelector('.passwordHelper').classList.remove('d-none');
				document.getElementsByClassName("fa-check")[currentTab].classList.add("d-none");
				valid = false;
				return;
			} else x[currentTab].querySelector('.passwordHelper').classList.add('d-none');

		}
		y[i].classList.remove("invalid");
		if(!y[i].checkValidity()){
			y[i].reportValidity();
			valid = false;
			return;
		} else valid = true;
	}
	// If the valid status is true, mark the step as finished and valid:
	if (valid) {
		document.getElementsByClassName("fa-check")[currentTab].classList.remove("d-none");
	}
	return valid; // return the valid status
}

function fixStepIndicator(n) { // This function removes the "active" class of all steps...
	var i,
	x = document.getElementsByClassName("page-item");
	for (i = 0; i < x.length; i++) {
		x[i].className = x[i].className.replace(" active", "");
	}
	// ... and adds the "active" class on the current step:
	x[n].className += " active";
}

function togglePasswordVisibility(event){
	let passwordInputs = document.querySelectorAll('input[type="password"]');
	if(passwordInputs.length > 0){
		passwordInputs.forEach(function(input){
			input.type = "text";
			
		});
		event.target.classList.remove('fa-eye-slash');
		event.target.classList.add('fa-eye');
		event.target.classList.remove('text-muted');
		event.target.classList.add('text-primary');
	} else {
		passwordInputs = document.querySelectorAll('input.password, input.confirm_password');
		passwordInputs.forEach(function(input){
			input.type = "password";
			
		});
		event.target.classList.add('fa-eye-slash');
		event.target.classList.remove('fa-eye');
		event.target.classList.add('text-muted');
		event.target.classList.remove('text-primary');
	}	
}
