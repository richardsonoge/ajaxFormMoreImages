document.addEventListener("DOMContentLoaded", function(){
    document.forms["contact-form"].addEventListener("submit", postData);
});

function postData(formsubmission){
    formsubmission.preventDefault();

    var submitButton = document.getElementById("submit-button");
    submitButton.disabled = true;
    submitButton.textContent = "Wait";

    var formData = new FormData();
    
    // Add form data
    formData.append("first_name", document.getElementById("first_name").value);
    formData.append("last_name", document.getElementById("last_name").value);
    formData.append("email", document.getElementById("email").value);
    formData.append("website", document.getElementById("website").value);
    formData.append("phone", document.getElementById("phone").value);
    formData.append("message", document.getElementById("message").value);

    // Add image data
    var imageInputs = document.getElementById("images").files;
    for (var i = 0; i < imageInputs.length; i++) {
        formData.append("images[]", imageInputs[i]);
    }

    // Checks if fields are filled-in or not, returns response "<p>Please enter your details.</p>" if not.
    if(formData.get("first_name") == "" || formData.get("last_name") == "" || formData.get("email") == "" || formData.get("phone") == "" || formData.get("message") == ""){
        document.getElementById("response").innerHTML = "<p>Please enter your details.</p>";
        submitButton.disabled = false;
        submitButton.textContent = "Submit";
        return;
    }

    var http = new XMLHttpRequest();
    http.open("POST", "send.php", true);

    http.onreadystatechange = function(){
        if(http.readyState == 4 && http.status == 200){
            var response = JSON.parse(http.responseText);
            if (Array.isArray(response)) {
                var html = ''
                response.forEach( function(element, index) {
                    html += element
                });
                document.getElementById("response").innerHTML = response;
            } else {
                document.getElementById("response").innerHTML = response;
            }

            // Enable the submit button again only if there is an error message
            if (http.status !== 200 || response.includes("error")) {
                submitButton.disabled = false;
                submitButton.textContent = "Submit";
            }
        }
    }

    http.send(formData);
}