import "./bootstrap";
import "preline";
// import "flowbite";
document.addEventListener("livewire:navigated", () => {
    window.HSStaticMethods.autoInit();
});
