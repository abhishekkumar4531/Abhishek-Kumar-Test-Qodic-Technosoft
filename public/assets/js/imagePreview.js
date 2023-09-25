const userImageInput = document.getElementById('userImage');
const imagePreview = document.getElementById('imagePreview');
const uploadButton = document.getElementById('uploadButton');
const deleteButton = document.getElementById('deleteButton');

userImageInput.addEventListener('change', function () {
  if (this.files && this.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      imagePreview.src = e.target.result;
      imagePreview.style.display = 'block';
      uploadButton.style.display = 'none';
      deleteButton.style.display = 'block';
    };
    reader.readAsDataURL(this.files[0]);
  }
});
deleteButton.addEventListener('click', function () {
  imagePreview.src = '';
  imagePreview.style.display = 'none';
  userImageInput.value = '';
  uploadButton.style.display = 'block';
  deleteButton.style.display = 'none';
});

// Function to trigger the file input when the "Upload" button is clicked
uploadButton.addEventListener('click', function () {
  userImageInput.click();
});
