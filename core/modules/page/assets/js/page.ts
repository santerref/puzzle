import slugify from 'slugify'

document.addEventListener("DOMContentLoaded", function () {
    const titleInput = document.getElementById("title") as HTMLInputElement;
    const slugInput = document.getElementById("slug") as HTMLInputElement;

    if (titleInput && slugInput) {
        titleInput.addEventListener("input", function () {
            if (slugInput.dataset.edited !== 'true') {
                slugInput.value = slugify(titleInput.value).toLowerCase();
            }
        });

        slugInput.addEventListener("input", function () {
            slugInput.dataset.edited = 'true';
        });
    }
});
