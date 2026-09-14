const urlBase = '/LAMPAPI';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";

document.addEventListener("DOMContentLoaded", () => {
	const loginForm = document.getElementById("loginForm");
	if (loginForm) {
		loginForm.addEventListener("submit", doLogin);
	}

	const registerForm = document.getElementById("registerForm");
	if (registerForm) {
		registerForm.addEventListener("submit", doRegister);
	}
});

function doLogin(e) {
	e.preventDefault();
	
	userId = 0;
	firstName = "";
	lastName = "";
	
	let login = document.getElementById("loginName").value;
	let password = document.getElementById("loginPassword").value;
	
	let resultP = document.getElementById("loginResult");
	resultP.innerHTML = "";
	resultP.classList.remove("text-success");
	resultP.classList.add("text-danger");

	let tmp = {login:login, password:password};
	let jsonPayload = JSON.stringify(tmp);
	
	let url = urlBase + '/Login.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	
	xhr.onreadystatechange = function() {
		if (this.readyState == 4) {
			try {
				let jsonObject = JSON.parse(xhr.responseText);
				
				if (jsonObject.error && jsonObject.error.length > 0) {
					resultP.innerHTML = jsonObject.error;
					return;
				}

				userId = jsonObject.id;
		
				if (userId < 1) {		
					resultP.innerHTML = "User/Password combination incorrect";
					return;
				}
		
				firstName = jsonObject.firstName;
				lastName = jsonObject.lastName;

				saveCookie();
	
				window.location.href = "color.html";
			} catch(err) {
				resultP.innerHTML = err.message;
			}
		}
	};
	xhr.send(jsonPayload);
}

function doRegister(e) {
	e.preventDefault();
	
	let resultP = document.getElementById("registerResult");
	resultP.innerHTML = "";
	resultP.classList.remove("text-success");
	resultP.classList.add("text-danger");

	let firstNameInput = document.getElementById("registerFirstName").value;
	let lastNameInput = document.getElementById("registerLastName").value;
	let loginInput = document.getElementById("registerLogin").value;
	let passwordInput = document.getElementById("registerPassword").value;
	
	if(passwordInput.length < 8) {
		resultP.innerHTML = "Password must be at least 8 characters.";
		return;
	}

	let tmp = {
		firstName: firstNameInput,
		lastName: lastNameInput,
		login: loginInput,
		password: passwordInput
	};
	let jsonPayload = JSON.stringify(tmp);
	
	let url = urlBase + '/Register.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	
	xhr.onreadystatechange = function() {
		if (this.readyState == 4) {
			try {
				let jsonObject = JSON.parse(xhr.responseText);
				
				if (jsonObject.error && jsonObject.error.length > 0) {
					resultP.innerHTML = jsonObject.error;
					return;
				}

				resultP.classList.remove("text-danger");
				resultP.classList.add("text-success");
				resultP.innerHTML = "Account created successfully! Please log in.";
				
				// Clear form
				document.getElementById("registerForm").reset();
				
			} catch(err) {
				resultP.innerHTML = err.message;
			}
		}
	};
	xhr.send(jsonPayload);
}

function saveCookie() {
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString();
}
