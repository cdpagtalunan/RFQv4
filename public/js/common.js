// const errorHandler = function (errors,formInput){
//     if(errors === undefined){

//         formInput.classList.remove("is-invalid")
//         formInput.classList.add("is-valid")
//         // formInput.attr('title', '')
//     }else {
//         formInput.classList.add("is-invalid")
//         formInput.classList.remove("is-valid")

//     }
// }

const handleValidatorErrors = (errors) => {
    
    document.querySelectorAll('div input').forEach(function(input) {
        input.classList.remove('is-invalid');
    });
    document.querySelectorAll('div select').forEach(function(input) {
        input.classList.remove('is-invalid');
    });
    document.querySelectorAll('div textarea').forEach(function(input) {
        input.classList.remove('is-invalid');
    });
    // Loop through each field in the errors object
    for (let field in errors) {
        if (errors.hasOwnProperty(field)) {
            // Extract the error messages for the field
            let fieldErrorMessage = errors[field];

            // Add invalid class & title validation
            if(field){
                document.querySelector(`input[name="${field}"]`).classList.add('is-invalid');
                document.querySelector(`select[name="${field}"]`).classList.add('is-invalid');
                document.querySelector(`textarea[name="${field}"]`).classList.add('is-invalid');

            }
        }
    }
}