document.addEventListener("DOMContentLoaded", () => {
    const keywordInput = document.getElementById("keyword");
    const contractSelect = document.getElementById("contract");
    const offersContainer = document.getElementById("offersContainer");

    function fetchOffers() {
        const keyword = keywordInput.value;
        const contract = contractSelect.value;

        const params = new URLSearchParams({
            keyword: keyword,
            contract: contract
        });

        fetch(`/offers/search?${params.toString()}`)
            .then(response => response.text())
            .then(html => {
                offersContainer.innerHTML = html;
            });
    }

    keywordInput.addEventListener("keyup", fetchOffers);
    contractSelect.addEventListener("change", fetchOffers);
});

