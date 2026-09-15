const urlBase = '/LAMPAPI';
const token = localStorage.getItem("token");
if (!token) { window.location.href = "index.html"; }

let currentPage = 1;
let currentSearch = "";

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

  document.getElementById("prevPage").addEventListener("click", () => {
    loadContacts(currentSearch, currentPage - 1);
  });

  document.getElementById("nextPage").addEventListener("click", () => {
    loadContacts(currentSearch, currentPage + 1);
  });
});

function loadContacts(search = currentSearch, page = 1) {
  currentSearch = search;
  currentPage = page;

  fetch(urlBase + '/SearchContacts.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ token: token, search: search, page: page })
  })
  .then(r => r.json())
  .then(data => {
    const body = document.getElementById("contactsBody");
    body.replaceChildren();
    data.results.forEach(c => {
      renderRow(body, c);
    });

    showResultCount(data, page);
  });
}

// The server only sends one page at a time, so the count comes from the
// total it reports rather than from how many rows are on screen.
function showResultCount(data, page) {
  const first = data.total === 0 ? 0 : (page - 1) * data.pageSize + 1;
  const last = first + data.results.length - 1;

  document.getElementById("resultCount").textContent =
    data.total === 0
      ? "No contacts found"
      : "Showing " + first + " to " + last + " of " + data.total + " contacts";

  document.getElementById("prevPage").disabled = page <= 1;
  document.getElementById("nextPage").disabled = page * data.pageSize >= data.total;
}

function renderRow(body, c) {
  const row = body.insertRow();
  row.dataset.id = c.id;
  row.insertCell().textContent = c.firstName;
  row.insertCell().textContent = c.lastName;
  row.insertCell().textContent = c.phone;
  row.insertCell().textContent = c.email;
  row.insertCell().textContent = c.dateCreated;

  const actionsCell = row.insertCell();

  const editButton = document.createElement("button");
  editButton.type = "button";
  editButton.className = "btn btn-sm btn-outline-primary me-1";
  editButton.textContent = "Edit";
  editButton.addEventListener("click", () => enterEditMode(row, c));

  const deleteButton = document.createElement("button");
  deleteButton.type = "button";
  deleteButton.className = "btn btn-sm btn-outline-danger";
  deleteButton.textContent = "Delete";
  deleteButton.addEventListener("click", () => deleteContact(c.id));

  actionsCell.appendChild(editButton);
  actionsCell.appendChild(deleteButton);
}

function enterEditMode(row, c) {
  row.replaceChildren();

  const firstNameInput = document.createElement("input");
  firstNameInput.type = "text";
  firstNameInput.className = "form-control form-control-sm";
  firstNameInput.value = c.firstName;

  const lastNameInput = document.createElement("input");
  lastNameInput.type = "text";
  lastNameInput.className = "form-control form-control-sm";
  lastNameInput.value = c.lastName;

  const phoneInput = document.createElement("input");
  phoneInput.type = "text";
  phoneInput.className = "form-control form-control-sm";
  phoneInput.value = c.phone;

  const emailInput = document.createElement("input");
  emailInput.type = "email";
  emailInput.className = "form-control form-control-sm";
  emailInput.value = c.email;

  row.insertCell().appendChild(firstNameInput);
  row.insertCell().appendChild(lastNameInput);
  row.insertCell().appendChild(phoneInput);
  row.insertCell().appendChild(emailInput);
  row.insertCell().textContent = c.dateCreated;

  const actionsCell = row.insertCell();

  const saveButton = document.createElement("button");
  saveButton.type = "button";
  saveButton.className = "btn btn-sm btn-outline-success me-1";
  saveButton.textContent = "Save";
  saveButton.addEventListener("click", () => {
    saveEdit(c.id, {
      firstName: firstNameInput.value,
      lastName: lastNameInput.value,
      phone: phoneInput.value,
      email: emailInput.value
    });
  });

  const cancelButton = document.createElement("button");
  cancelButton.type = "button";
  cancelButton.className = "btn btn-sm btn-outline-secondary";
  cancelButton.textContent = "Cancel";
  cancelButton.addEventListener("click", () => loadContacts());

  actionsCell.appendChild(saveButton);
  actionsCell.appendChild(cancelButton);
}

function saveEdit(id, updated) {
  const message = document.getElementById("message");
  message.textContent = "";

  fetch(urlBase + '/UpdateContact.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
      token: token,
      id: id,
      firstName: updated.firstName,
      lastName: updated.lastName,
      phone: updated.phone,
      email: updated.email
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.error && data.error.length > 0) {
      message.textContent = data.error;
      return;
    }
    loadContacts();
  });
}

function deleteContact(id) {
  if (!confirm("Are you sure you want to delete this contact?")) {
    return;
  }

  fetch(urlBase + '/DeleteContact.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ token: token, id: id })
  })
  .then(r => r.json())
  .then(data => {
    if (data.error && data.error.length > 0) {
      document.getElementById("message").textContent = data.error;
      return;
    }
    loadContacts();
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
