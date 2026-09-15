const urlBase = '/LAMPAPI';
const token = localStorage.getItem("token");
if (!token) { window.location.href = "index.html"; }

document.addEventListener("DOMContentLoaded", () => {
  loadContacts();

  const searchButton = document.getElementById("searchButton");
  const searchInput = document.getElementById("searchInput");

  searchButton.addEventListener("click", () => {
    loadContacts(searchInput.value);
  });

  searchInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      loadContacts(searchInput.value);
    }
  });
});

function loadContacts(search = "") {
  fetch(urlBase + '/SearchContacts.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ token: token, search: search, page: 1 })
  })
  .then(r => r.json())
  .then(data => {
    const body = document.getElementById("contactsBody");
    body.innerHTML = "";
    data.results.forEach(c => {
      const row = body.insertRow();
      row.insertCell().textContent = c.firstName;
      row.insertCell().textContent = c.lastName;
      row.insertCell().textContent = c.phone;
      row.insertCell().textContent = c.email;
      row.insertCell().textContent = c.dateCreated;
    });
  });
}
