// Access page 
// edit button acction
function editAction(id) {
    const editButton = document.getElementById('editAction_' + id);
    const saveButton = document.getElementById('saveAction_' + id);
    const nameInput = document.getElementById('name_' + id);
    const emailInput = document.getElementById('email_' + id);

    nameInput.disabled = false;
    emailInput.disabled = false;
    editButton.style.display = 'none';
    saveButton.style.display = 'inline-block';
}
//  save button action
function saveAction(id) {
    const editButton = document.getElementById('editAction_' + id);
    const saveButton = document.getElementById('saveAction_' + id);
    const nameInput = document.getElementById('name_' + id);
    const emailInput = document.getElementById('email_' + id);

    // call setData function
    setData(nameInput.value, emailInput.value, id);

    nameInput.disabled = true;
    emailInput.disabled = true;
    editButton.style.display = 'inline-block';
    saveButton.style.display = 'none';

    // Here, you can add your logic to save the changes (e.g., AJAX request to the server)
}

// name and email save
function setData(name, email, id) {
    console.log(id);
    $.ajax({
        url: "/updatedata",
        type: "POST",
        data: {
            name: name,
            email: email,
            id: id,
            _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
        },
        success: function (response) {
            console.log(response);
            alert('User updated successfully');
        },
        error: function (xhr, status, error) {
            console.error(error); // Handle error
        },
    });
}


// user delete function
function deleteAction(id, access) {
    if (access === 'write') {
        if (confirm("Are you sure you want to delete this item?")) {
            $.ajax({
                url: "/deletedata",
                type: "POST",
                data: {
                    id: id,
                    _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                },
                success: function (response) {
                    console.log(response);
                    location.reload(); // Reload the page
                },
                error: function (xhr, status, error) {
                    console.error(error); // Handle error
                },
            });
        } else {
            console.log("Deletion canceled");
        }
    } else {
        alert("Error: Access type could not reset pin.");
        // Optional: Handle access denied scenario as needed
    }
}

// reset pin
function resetAction(id, access) {
    if (access === 'write') {
        $.ajax({
            url: "/resetpin",
            type: "POST",
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                console.log(response);
                location.reload(); // Reload the page after successful reset
            },
            error: function (xhr, status, error) {
                console.error(error); // Handle error
            }
        });
    } else {
        alert("Error: Access type could not reset pin.");
        // Optional: Handle access denied scenario as needed
    }
}

// chane access type
function updateAccessType(checkbox, accessId, access) {
    if (access === 'write') { // Check if access type is 'write'
        const accessType = checkbox.checked ? 'read' : 'write';
        console.log(accessType);
        $.ajax({
            url: "/updateaccesstype",
            type: "POST",
            data: {
                id: accessId,
                access_type: accessType,
                _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
            },
            success: function (response) {
                console.log(response);
                if (response.message === 'Access type updated successfully') {
                    alert("Access type updated to: " + accessType);
                    location.reload(); // Reload the page
                }
            },
            error: function (xhr, status, error) {
                console.error(error); // Handle error
            },
        });
    } else {
        alert("Error: Access type could not be updated");
        location.reload();
    }
}


// change access page in users
function accesspage(id, name) {
    console.log(name);

    $.ajax({
        url: "/updateaccesspage",
        type: "POST",
        data: {
            id: id,
            name: name,
            _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
        },
        success: function (response) {
            console.log(response);
            if (response.message === 'Access type updated successfully') {
                alert("Access type updated to: " + response.access.access_type);
                // Optionally reload the page or update the checkbox state based on the new value
                location.reload();
            }
        },
        error: function (xhr, status, error) {
            console.error(error); // Handle error
        },
    });
}



// Workout Library page
// popup view +add
document.addEventListener('DOMContentLoaded', function () {
    var popup = document.getElementById('popupForm');
    var openBtn = document.getElementById('openPopupBtn');
    var closeBtn = document.querySelector('.close');

    if (openBtn) {
        openBtn.addEventListener('click', function () {
            popup.style.display = 'flex';
            document.querySelector('.popup-content').classList.add('show');
        });
    }

    closeBtn.addEventListener('click', function () {
        document.querySelector('.popup-content').classList.remove('show');
        document.querySelector('.popup-content').classList.add('hide');
        setTimeout(function () {
            popup.style.display = 'none';
            document.querySelector('.popup-content').classList.remove('hide');
        }, 400);
    });

    window.addEventListener('click', function (event) {
        if (event.target == popup) {
            document.querySelector('.popup-content').classList.remove('show');
            document.querySelector('.popup-content').classList.add('hide');
            setTimeout(function () {
                popup.style.display = 'none';
                document.querySelector('.popup-content').classList.remove('hide');
            }, 400);
        }
    });
});

// edit function 
function edit(id, categoryName, type, workout, link) {
    document.getElementById('workoutId').value = id;
    document.getElementById('workout').value = workout;
    document.getElementById('link').value = link;

    // Set the correct category option
    var categorySelect = document.getElementById('category');
    for (var i = 0; i < categorySelect.options.length; i++) {
        if (categorySelect.options[i].text === categoryName) {
            categorySelect.selectedIndex = i;
            break;
        }
    }

    // Set the correct type option
    var typeSelect = document.getElementById('type');
    for (var i = 0; i < typeSelect.options.length; i++) {
        if (typeSelect.options[i].value === type) {
            typeSelect.selectedIndex = i;
            break;
        }
    }

    var popup = document.getElementById('popupForm');

    var addButton = document.getElementById('addButton');
    var updateButton = document.getElementById('updateButton');

    addButton.hidden = true;
    updateButton.hidden = false;


    popup.style.display = 'flex';
    document.querySelector('.popup-content').classList.add('show');
}

//  close popup
function closePopup() {
    var popup = document.getElementById('popupForm');
    document.querySelector('.popup-content').classList.remove('show');
    document.querySelector('.popup-content').classList.add('hide');
    setTimeout(function () {
        popup.style.display = 'none';
        document.querySelector('.popup-content').classList.remove('hide');
    }, 400);
}

document.querySelector('.close').addEventListener('click', closePopup);