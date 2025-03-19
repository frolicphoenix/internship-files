document.getElementById('unsubscribeForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
        first_name: document.getElementById('first_name').value,
        last_name: document.getElementById('last_name').value,
        email: document.getElementById('email').value,
        country: document.getElementById('country').value
    };

    try {
        const response = await fetch(`process_unsubscribe.php?pub=<?php echo $pub ?>`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();
        const messageDiv = document.getElementById('message');
        
        messageDiv.style.display = 'block';
        messageDiv.className = result.success ? 'success' : 'error';
        messageDiv.textContent = result.message;

        if (result.success) {
            document.getElementById('unsubscribeForm').reset();
        }
        
    } catch (error) {
        console.error('Error:', error);
    }
});