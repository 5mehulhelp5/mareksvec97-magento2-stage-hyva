define(["jquery", "fslightbox"], function ($) {
        $(document).ready(function () {
            (function () {
                let mediaCounter = 0;
                function createUploadedItem() {
                    function createContainer() {
                        const container = document.createElement("div");
                        container.classList.add("mw-upload__item", "mw-upload__item--hidden");
                        return container;
                    }
                    function createMedia(video) {
                        const media = document.createElement("div");
                        const image = document.createElement(video ? "video" : "img");
                        media.classList.add("mw-upload__item-media");
                        image.src = "";
                        image.alt = "";
                        media.appendChild(image);
                        return { media, image };
                    }
                    function createRemoveButton() {
                        const button = document.createElement("div");
                        button.classList.add("mw-upload__item-remove");
                        button.innerHTML =
                            '<svg width="12" height="12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 1L1 11M1 1l10 10" stroke="#000"></path></svg>';
                        return button;
                    }
                    function createFileInput() {
                        const input = document.createElement("input");
                        input.classList.add("mw-upload__control");
                        input.type = "file";
                        input.autocomplete = "off";
                        input.accept = "image/*,video/*";
                        input.name = `customer-image-${mediaCounter}`;
                        return input;
                    }
                    const input = createFileInput();
                    const container = createContainer();
                    const removeButton = createRemoveButton();
                    container.append(input, removeButton);
                    input.addEventListener("change", function (e) {
                        if (!e.target.files || !e.target.files[0]) {
                            return;
                        }
                        const { media, image } = createMedia(
                            e.target.files[0].type.includes("video")
                        );
                        container.append(media);
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            image.src = e.target.result;
                            container.classList.remove("mw-upload__item--hidden");
                        };
                        reader.readAsDataURL(input.files[0]);
                    });
                    removeButton.addEventListener("click", function () {
                        container.remove();
                    });
                    input.click();
                    mediaCounter += 1;
                    return container;
                }

                function handleUploadForm() {
                    const uploadZone = document.querySelector(".mw-upload__zone");
                    const uploadContainer = document.querySelector(".mw-upload");
                    if (!uploadZone) {
                        return;
                    }
                    uploadZone.addEventListener("click", function () {
                        const uploadedItem = createUploadedItem();
                        uploadContainer.prepend(uploadedItem);
                    });
                }

                handleUploadForm();
            })();
        })
    }
);
