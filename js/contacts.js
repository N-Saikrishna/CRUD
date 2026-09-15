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

  const addContactForm = document.getElementById("addContactForm");
  addContactForm.addEventListener("submit", addContact);
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

function addContact(e) {
  e.preventDefault();

  const message = document.getElementById("message");
  message.textContent = "";

  const firstName = document.getElementById("addFirstName").value;
  const lastName = document.getElementById("addLastName").value;
  const phone = document.getElementById("addPhone").value;
  const email = document.getElementById("addEmail").value;

  fetch(urlBase + '/AddContact.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ token: token, firstName: firstName, lastName: lastName, phone: phone, email: email })
  })
  .then(r => r.json())
  .then(data => {
    if (data.error && data.error.length > 0) {
      message.textContent = data.error;
      return;
    }
    document.getElementById("addContactForm").reset();
    loadContacts();
  });
}
