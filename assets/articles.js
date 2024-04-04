const MAX_ARTICLES_PER_PAGE = 10;
let currentPage = 1;

window.addEventListener("load", initializeApp);

function initializeApp() {
    showPage(currentPage);
    setupPaginationButtons();
    setupCreateArticleModal();
    setupSubmitButton();
}

function setupPaginationButtons() {
    document.getElementById("prev-button").addEventListener("click", () => {
        showPage(--currentPage);
    });

    document.getElementById("next-button").addEventListener("click", () => {
        showPage(++currentPage);
    });
}

function setupCreateArticleModal() {
    const createButton = document.getElementById("create-button");
    const dialog = document.getElementById("create-article-form");
    const cancelButton = document.getElementById("cancel-button");

    createButton.addEventListener("click", () => {
        const overlay = document.createElement('div');
        overlay.id = "veil";
        document.body.appendChild(overlay);
        dialog.style.display = "block";
    });

    cancelButton.addEventListener("click", () => {
        dialog.style.display = "none";
        document.body.removeChild(document.getElementById("veil"));
    });
}

function setupSubmitButton() {
    const submitButton = document.getElementById("submit-button");
    const nameInput = document.getElementById("name-input");
    submitButton.disabled = true;

    nameInput.addEventListener("input", () => {
        submitButton.disabled = nameInput.value.length === 0;
    });
}

function showPage(pageNumber) {
    clearArticleList();
    updatePaginationButtons(pageNumber);
    renderArticles(pageNumber);
    updatePageNumber(pageNumber);
}

function clearArticleList() {
    document.getElementById("articles").innerHTML = "";
}

function updatePaginationButtons(pageNumber) {
    const prevButton = document.getElementById("prev-button");
    const nextButton = document.getElementById("next-button");

    prevButton.disabled = pageNumber === 1;
    prevButton.style.border = pageNumber === 1 ? "2px solid #333" : "2px solid #000";

    const lastArticleIndex = (pageNumber * MAX_ARTICLES_PER_PAGE) <= articles.length ? pageNumber * MAX_ARTICLES_PER_PAGE : articles.length;
    nextButton.disabled = lastArticleIndex === articles.length;
    nextButton.style.border = lastArticleIndex === articles.length ? "2px solid #333" : "2px solid #000";
}

function renderArticles(pageNumber) {
    const articleList = document.getElementById("articles");
    const startIndex = (pageNumber - 1) * MAX_ARTICLES_PER_PAGE;
    const endIndex = startIndex + MAX_ARTICLES_PER_PAGE <= articles.length ? startIndex + MAX_ARTICLES_PER_PAGE : articles.length;

    for (let i = startIndex; i < endIndex; i++) {
        const article = articles[i];
        const item = createArticleListItem(article);
        articleList.appendChild(item);
    }
}

function createArticleListItem(article) {
    const item = document.createElement("li");

    const articleSpan = document.createElement("span");
    articleSpan.className = "button-span";

    const articleLink = document.createElement("a");
    articleLink.href = `/~80697138/cms/articles/${article.ID}`;
    articleLink.innerText = article.name;
    articleLink.className = "article-title";
    item.appendChild(articleLink);

    articleSpan.appendChild(createDeleteButton(article, item));
    articleSpan.appendChild(createEditButton(article));
    articleSpan.appendChild(createShowButton(article));

    item.appendChild(articleSpan);
    return item;
}

function createDeleteButton(article, listItem) {
    const deleteButton = document.createElement("button");
    deleteButton.className = "btn btn-right btn-red";
    deleteButton.innerText = "Delete";
    deleteButton.addEventListener("click", () => {
        if (confirm("Are you sure you want to delete this article?")) {
            fetch(`/~80697138/cms/articles/${article.ID}`, { method: "DELETE" });
            listItem.remove();
            articles = articles.filter(a => a.ID !== article.ID);
            if (articles.length % MAX_ARTICLES_PER_PAGE === 0 && currentPage > 1)
                currentPage--;
            showPage(currentPage);
        }
    });
    return deleteButton;
}

function createEditButton(article) {
    const editButton = document.createElement("button");
    editButton.className = "btn btn-right btn-orange";
    editButton.innerText = "Edit";
    editButton.addEventListener("click", () => {
        window.location.href = `/~80697138/cms/article-edit/${article.ID}`;
    });
    return editButton;
}

function createShowButton(article) {
    const showButton = document.createElement("button");
    showButton.className = "btn btn-right btn-yellow";
    showButton.innerText = "Show";
    showButton.addEventListener("click", () => {
        window.location.href = `/~80697138/cms/articles/${article.ID}`;
    });
    return showButton;
}

function updatePageNumber(pageNumber) {
    const page = document.getElementById("page-number");
    page.innerText = `${pageNumber}/${Math.ceil(articles.length / MAX_ARTICLES_PER_PAGE)}`;
}