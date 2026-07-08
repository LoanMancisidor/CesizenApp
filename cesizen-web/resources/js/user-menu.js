document.addEventListener("DOMContentLoaded", function () {
    const trigger = document.querySelector(".user-profile-trigger");
    const dropdown = document.getElementById("user-dropdown");

    if (!trigger || !dropdown) {
        return;
    }

    trigger.addEventListener("click", function (e) {
        e.stopPropagation();
        const isVisible = dropdown.style.display === "flex";
        dropdown.style.display = isVisible ? "none" : "flex";
    });

    window.addEventListener("click", function (e) {
        if (!trigger.contains(e.target)) {
            dropdown.style.display = "none";
        }
    });
});
