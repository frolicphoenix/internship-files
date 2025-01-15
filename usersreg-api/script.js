const API_BASE_URL = 'http://localhost/internship-files/usersreg-api/';

//function to load users
function loadUsers() {
    fetch(API_BASE_URL + 'api.php?action=read')

    .then(response => response.json())
    .then(data => {
        const userTableBody = document.getElementById('UserTableBody');

        userTableBody.innerHTML ='';

        data.forEach(user => {
            userTableBody.innerHTML += 
            `
                <tr>
                    <td>${user.first_name}</td>
                    <td>${user.last_name}</td>
                    <td>${user.email}</td>
                </tr>
            `
        });
    })
}