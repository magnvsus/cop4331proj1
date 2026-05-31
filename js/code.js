const urlBase = '/LAMPAPI';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";

/* =========================
   LOGIN
========================= */

function doLogin()
{
    userId = 0;
    firstName = "";
    lastName = "";

    let login = document.getElementById("loginName").value;
    let password = document.getElementById("loginPassword").value;

    document.getElementById("loginResult").innerHTML = "";

    let tmp = {
        login: login,
        password: password,
        Login: login,
        Password: password
    };

    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/Login.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    xhr.onreadystatechange = function()
    {
        if (this.readyState === 4)
        {
            console.log("Status:", this.status);
            console.log("Response:", xhr.responseText);

            try
            {
                let jsonObject = JSON.parse(xhr.responseText);

                userId =
                    jsonObject.ID ||
                    jsonObject.id ||
                    jsonObject.UserID ||
                    jsonObject.userId ||
                    0;

                if (this.status !== 200 || userId < 1)
                {
                    document.getElementById("loginResult").innerHTML =
                        "User/Password combination incorrect";
                    return;
                }

                firstName =
                    jsonObject.FirstName ||
                    jsonObject.firstName ||
                    "";

                lastName =
                    jsonObject.LastName ||
                    jsonObject.lastName ||
                    "";

                saveCookie();

                window.location.href = "contacts.html";
            }
            catch (err)
            {
                console.log("Login parse error:", err);
                document.getElementById("loginResult").innerHTML =
                    "Login failed. Check console.";
            }
        }
    };

    xhr.send(jsonPayload);
}

/* =========================
   COOKIES
========================= */

function saveCookie()
{
    let minutes = 20;
    let date = new Date();
    date.setTime(date.getTime() + (minutes * 60 * 1000));

    document.cookie =
        "firstName=" + firstName +
        ",lastName=" + lastName +
        ",userId=" + userId +
        ";expires=" + date.toGMTString();
}

function readCookie()
{
    userId = -1;

    let data = document.cookie;
    let splits = data.split(",");

    for (let i = 0; i < splits.length; i++)
    {
        let thisOne = splits[i].trim();
        let tokens = thisOne.split("=");

        if (tokens[0] === "firstName")
        {
            firstName = tokens[1];
        }
        else if (tokens[0] === "lastName")
        {
            lastName = tokens[1];
        }
        else if (tokens[0] === "userId")
        {
            userId = parseInt(tokens[1].trim());
        }
    }

    if (userId < 0)
    {
        window.location.href = "index.html";
    }
    else
    {
        if (document.getElementById("userName"))
        {
            document.getElementById("userName").innerHTML =
                "Logged in as " + firstName + " " + lastName;
        }
    }
}

function doLogout()
{
    userId = 0;
    firstName = "";
    lastName = "";

    document.cookie = "firstName= ; expires=Thu, 01 Jan 1970 00:00:00 GMT";
    document.cookie = "lastName= ; expires=Thu, 01 Jan 1970 00:00:00 GMT";
    document.cookie = "userId= ; expires=Thu, 01 Jan 1970 00:00:00 GMT";

    window.location.href = "index.html";
}

/* =========================
   ADD CONTACT
========================= */

function addContact()
{
    let contactFirstName = document.getElementById("contactFirstName").value;
    let contactLastName = document.getElementById("contactLastName").value;
    let contactPhone = document.getElementById("contactPhone").value;
    let contactEmail = document.getElementById("contactEmail").value;

    document.getElementById("contactAddResult").innerHTML = "";

    if (
        contactFirstName.trim() === "" ||
        contactLastName.trim() === "" ||
        contactPhone.trim() === "" ||
        contactEmail.trim() === ""
    )
    {
        document.getElementById("contactAddResult").innerHTML =
            "Please fill in all fields.";
        return;
    }

    let tmp = {
        UserID: userId,
        FirstName: contactFirstName,
        LastName: contactLastName,
        Phone: contactPhone,
        Email: contactEmail
    };

    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/AddContact.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    xhr.onreadystatechange = function()
    {
        if (this.readyState === 4)
        {
            if (this.status === 200 || this.status === 201)
            {
                document.getElementById("contactAddResult").innerHTML =
                    "Contact has been added.";

                document.getElementById("contactFirstName").value = "";
                document.getElementById("contactLastName").value = "";
                document.getElementById("contactPhone").value = "";
                document.getElementById("contactEmail").value = "";
            }
            else
            {
                document.getElementById("contactAddResult").innerHTML =
                    "Unable to add contact.";
            }
        }
    };

    xhr.send(jsonPayload);
}

/* =========================
   SEARCH CONTACT
========================= */

function searchContact()
{
    let srch = document.getElementById("searchText").value;

    document.getElementById("contactSearchResult").innerHTML = "";
    document.getElementById("contactList").innerHTML = "";

    if (srch.trim() === "")
    {
        document.getElementById("contactSearchResult").innerHTML =
            "Please enter something to search.";
        return;
    }

    let tmp = {
        UserID: userId,
        Search: srch
    };

    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/SearchContact.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    xhr.onreadystatechange = function()
    {
        if (this.readyState === 4)
        {
            if (this.status !== 200)
            {
                document.getElementById("contactSearchResult").innerHTML =
                    "No contacts found.";
                return;
            }

            try
            {
                let jsonObject = JSON.parse(xhr.responseText);
                let contacts = jsonObject.Results;

                if (!contacts || contacts.length === 0)
                {
                    document.getElementById("contactSearchResult").innerHTML =
                        "No contacts found.";
                    return;
                }

                document.getElementById("contactSearchResult").innerHTML =
                    "Contacts retrieved.";

                let contactList = "";

                for (let i = 0; i < contacts.length; i++)
                {
                    let contact = contacts[i];

                    contactList += `
                        <div class="contact-card">
                            <strong>${contact.FirstName} ${contact.LastName}</strong>
                            <p>Phone: ${contact.Phone}</p>
                            <p>Email: ${contact.Email}</p>

                            <button type="button" onclick="showEditContact(
                                ${contact.ContactID},
                                '${contact.FirstName}',
                                '${contact.LastName}',
                                '${contact.Phone}',
                                '${contact.Email}'
                            )">
                                Edit
                            </button>

                            <button type="button" onclick="deleteContact(${contact.ContactID})">
                                Delete
                            </button>
                        </div>
                    `;
                }

                document.getElementById("contactList").innerHTML = contactList;
            }
            catch (err)
            {
                document.getElementById("contactSearchResult").innerHTML =
                    "Error reading contacts.";
            }
        }
    };

    xhr.send(jsonPayload);
}

/* =========================
   DELETE CONTACT
========================= */

function deleteContact(contactId)
{
    let tmp = {
        UserID: userId,
        ContactID: contactId
    };

    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/DeleteContact.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    xhr.onreadystatechange = function()
    {
        if (this.readyState === 4)
        {
            document.getElementById("contactSearchResult").innerHTML =
                "Contact deleted.";

            searchContact();
        }
    };

    xhr.send(jsonPayload);
}

/* =========================
   EDIT / UPDATE CONTACT
========================= */

function showEditContact(contactId, first, last, phone, email)
{
    document.getElementById("editContactID").value = contactId;
    document.getElementById("editFirstName").value = first;
    document.getElementById("editLastName").value = last;
    document.getElementById("editPhone").value = phone;
    document.getElementById("editEmail").value = email;

    document.getElementById("editContactDiv").style.display = "block";
}

function updateContact()
{
    let contactId = document.getElementById("editContactID").value;
    let contactFirstName = document.getElementById("editFirstName").value;
    let contactLastName = document.getElementById("editLastName").value;
    let contactPhone = document.getElementById("editPhone").value;
    let contactEmail = document.getElementById("editEmail").value;

    let tmp = {
        UserID: userId,
        ContactID: contactId,
        FirstName: contactFirstName,
        LastName: contactLastName,
        Phone: contactPhone,
        Email: contactEmail
    };

    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/UpdateContact.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    xhr.onreadystatechange = function()
    {
        if (this.readyState === 4)
        {
            document.getElementById("editContactDiv").style.display = "none";
            document.getElementById("contactSearchResult").innerHTML =
                "Contact updated.";

            searchContact();
        }
    };

    xhr.send(jsonPayload);
}

/* =========================
   REGISTER
========================= */

function showLogin()
{
    document.getElementById("registerDiv").style.display = "none";
    document.getElementById("loginDiv").style.display = "block";
}

function showRegister()
{
    document.getElementById("loginDiv").style.display = "none";
    document.getElementById("registerDiv").style.display = "block";
}

function doRegister()
{
    let first = document.getElementById("registerFirstName").value;
    let last = document.getElementById("registerLastName").value;
    let login = document.getElementById("registerLogin").value;
    let password = document.getElementById("registerPassword").value;

    document.getElementById("registerResult").innerHTML = "";

    let tmp = {
        FirstName: first,
        LastName: last,
        Login: login,
        Password: password
    };

    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/Register.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    xhr.onreadystatechange = function()
    {
        if (this.readyState === 4)
        {
            if (this.status === 200 || this.status === 201)
            {
                document.getElementById("registerResult").innerHTML =
                    "Account created. You can log in now.";
            }
            else
            {
                document.getElementById("registerResult").innerHTML =
                    "Registration failed.";
            }
        }
    };

    xhr.send(jsonPayload);
}