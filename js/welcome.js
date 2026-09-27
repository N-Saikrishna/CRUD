document.addEventListener("DOMContentLoaded", () => {
	const registerPanel = document.getElementById("registerPanel");

	document.getElementById("showRegisterButton").addEventListener("click", () => {
		registerPanel.hidden = false;
	});

	document.getElementById("registerClose").addEventListener("click", () => {
		registerPanel.hidden = true;
	});
});
