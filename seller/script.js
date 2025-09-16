// script.js

document.addEventListener("DOMContentLoaded", function() {
    const uploadBtn = document.getElementById('uploadBtn');
    const deleteBtn = document.getElementById('deleteBtn');
    const updateBtn = document.getElementById('updateBtn');
    const uploadForm = document.getElementById('upload-form');
    const deleteForm = document.getElementById('delete-form');
    const updateForm = document.getElementById('update-form');
    const closeBtns = document.querySelectorAll('.close-btn');

    // Function to show form and close others
    function showForm(form) {
        [uploadForm, deleteForm, updateForm].forEach(f => {
            if (f !== form) {
                f.style.display = 'none';
            }
        });
        form.style.display = 'block';
    }

    // Event listeners for buttons
    uploadBtn.addEventListener('click', function(e) {
        e.preventDefault();
        showForm(uploadForm);
    });

    deleteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        showForm(deleteForm);
    });

    updateBtn.addEventListener('click', function(e) {
        e.preventDefault();
        showForm(updateForm);
    });

    // Event listener for close buttons
    closeBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            this.closest('.popup-form').style.display = 'none';
        });
    });
});
