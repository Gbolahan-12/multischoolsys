
// document.addEventListener("DOMContentLoaded", function(){
//     console.log('testing app');
    

//     const schoolSelect = document.getElementById('schoolSelect');
//     const classSelect  = document.getElementById('classSelect');

//     const routeTemplate =
//         "{{ route('classes.by.school', ':id') }}";

//     schoolSelect.addEventListener('change', function(){

//         let schoolId = this.value;

//         classSelect.innerHTML =
//             '<option>Loading...</option>';
//         classSelect.disabled = true;

//         if(!schoolId){
//             classSelect.innerHTML =
//             '<option value="">Select Class</option>';
//             return;
//         }

//         let url = routeTemplate.replace(':id', schoolId);
        
//         console.log(url);

//         fetch(url)
//             .then(res => res.json())
//             .then(classes => {

//                 classSelect.innerHTML =
//                     '<option value="">Select Class</option>';

//                 classes.forEach(cls => {
//                     classSelect.innerHTML += `
//                         <option value="${cls.id}">
//                             ${cls.name}
//                         </option>
//                     `;
//                 });
//                 console.log(classes);

//                 classSelect.disabled = false;

//             })
//             .catch(() => {
//                 classSelect.innerHTML =
//                     '<option>Error loading classes</option>';
//             });

//     });

// });


// // @if(session('success') || session('error'))
// // 		<script>
// // 			window.addEventListener("load", function () {

// // 				if (typeof Toastify === "undefined") {
// // 					console.error("Toastify missing");
// // 					return;
// // 				}

// // 				@if(session('success'))
// // 					Toastify({
// // 						text: "{{ session('success') }}",
// // 						duration: 3500,
// // 						gravity: "top",
// // 						position: "right",
// // 						close: true
// // 					}).showToast();
// // 				@endif

// // 				@if(session('error'))
// // 					Toastify({
// // 						text: "{{ session('error') }}",
// // 						duration: 4000,
// // 						gravity: "top",
// // 						position: "right",
// // 						close: true,
// // 						style: { background: "#dc3545" }
// // 					}).showToast();
// // 				@endif

// // 			});
// // 		</script>
// // 	@endif

// // 	@if ($errors->any() && session('modal'))
// // 		<script>
// // 			window.addEventListener('load', function () {
// // 				new bootstrap.Modal(
// // 					document.getElementById('{{ session("modal") }}')
// // 				).show();
// // 			});
// // 		</script>
// // 	@endif