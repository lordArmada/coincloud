// Form submission handling
document.getElementById("depositForm").addEventListener("submit", function (e) {
  e.preventDefault(); // Prevent page refresh

  const gateway = document.getElementById("gateway").value;
  const amount = document.getElementById("amount").value;

  if (gateway && amount) {
    // Display the toast
    const toastElement = document.getElementById("liveToast");
    const toastBody = toastElement.querySelector(".toast-body");

    toastBody.textContent = `You selected ${gateway} and entered an amount of ${amount} USD. You will receive more information in your email.`;
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
  } else {
    alert("Please select a gateway and enter an amount.");
  }
});
console.log('working')