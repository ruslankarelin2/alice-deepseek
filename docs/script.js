"use strict";

const controls = document.querySelector(".catalog-controls");
const search = document.querySelector("#search");
const filters = [...document.querySelectorAll("[data-filter]")];
const cards = [...document.querySelectorAll(".tool-card")];
const resultCount = document.querySelector("#result-count");
const emptyState = document.querySelector("#empty-state");
let category = "all";

function updateResults() {
  const query = search.value.trim().toLocaleLowerCase("ru");
  let count = 0;

  for (const card of cards) {
    const matchesCategory =
      category === "all" || card.dataset.category === category;
    const matchesQuery = card.textContent
      .toLocaleLowerCase("ru")
      .includes(query);
    card.hidden = !(matchesCategory && matchesQuery);
    if (!card.hidden) count += 1;
  }

  for (const filter of filters) {
    const active = filter.dataset.filter === category;
    filter.classList.toggle("active", active);
    filter.setAttribute("aria-pressed", String(active));
  }

  resultCount.textContent = `Показано: ${count} из ${cards.length}`;
  emptyState.hidden = count !== 0;
}

for (const filter of filters) {
  filter.addEventListener("click", () => {
    category = filter.dataset.filter;
    updateResults();
  });
}

search.addEventListener("input", updateResults);
document.querySelector("#reset-filters").addEventListener("click", () => {
  category = "all";
  search.value = "";
  updateResults();
  search.focus();
});

controls.hidden = false;
